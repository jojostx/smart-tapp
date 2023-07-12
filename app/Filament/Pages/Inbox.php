<?php

namespace App\Filament\Pages;

use App\Contracts\Models\Messageable;
use App\DTOs\InboxSearchResults;
use App\Enums\Roles\UserRole;
use App\Models\Tenant\Driver;
use App\Models\Tenant\Message;
use App\Models\Tenant\User;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use ReflectionMethod;

class Inbox extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-inbox';

    protected static string $view = 'filament::pages.inbox';

    public $search = '';

    public $message = '';

    public ?Model $selectedMessageable = null;

    protected array $messageableModels = [
        'drivers' => Driver::class,
        'admins' => User::class,
    ];

    protected $listeners = ['getMessageable', 'refreshInbox' => '$refresh'];

    public function mount()
    {
        // set the user to the most recent message's
        // messageable model that is not the current auth model
        $this->selectedMessageable = $this->mostRecentMessageable;
    }

    protected static function getAuthModel(): Model | null
    {
        return Filament::auth()->user();
    }

    protected function getViewData(): array
    {
        return [
            'drivers' => $this->drivers,
            'admins' => $this->admins,
            'activeMenu' => $this->activeMenu,
            'results' => $this->getResults(),
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('message')
                ->disableLabel()
                ->required()
                ->minLength(2)
                ->maxLength(255)
                ->autofocus()
                ->placeholder('Type message...')
                ->extraInputAttributes(['class' => 'placeholder-gray-500 transition duration-75 border rounded-lg bg-gray-400/10 focus:bg-white focus:placeholder-gray-400 focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500'])
        ];
    }

    protected function getSearchResults(string $query): ?InboxSearchResults
    {
        $builder = InboxSearchResults::make();

        foreach ($this->getMessageableModels() as $messageableType => $messageableClass) {
            $messageableResults = $messageableClass::getMessageableSearchResults($query);

            if (!$messageableResults->count()) {
                continue;
            }

            $builder->category($messageableType, $messageableResults);
        }

        return $builder;
    }

    public function getResults(): ?InboxSearchResults
    {
        $search = trim($this->search);

        if (blank($search)) {
            return null;
        }

        $results = $this->getSearchResults($this->search);

        if ($results === null) {
            return $results;
        }

        $this->dispatchBrowserEvent('open-inbox-search-results');

        return $results;
    }

    public function getMessageableModels(): array
    {
        return $this->messageableModels;
    }

    public function getMessageablesProperty(): array
    {
        return [
            'drivers' => $this->drivers,
            'admins' => $this->admins,
        ];
    }

    public function getActiveMenuProperty(): string
    {
        if (blank($this->selectedMessageable) || blank($this->messageables)) {
          return 'none';
        }

        if ($this->selectedMessageable && $this->isAdmin($this->selectedMessageable)) {
            return 'admins';
        }

        return 'drivers';
    }

    public function getDriversProperty(): EloquentCollection
    {
        // all drivers who have conversations with the support
        return Driver::query()
            ->whereHas('sentMessages')
            ->orWhereHas('receivedMessages')
            ->withCount(['sentMessages' => function (Builder $query) {
                $query->whereNull('seen_at');
            }])
            ->limit(20)
            ->get();
    }

    public function getAdminsProperty(): EloquentCollection
    {
        $auth = $this->getAuthModel();

        return User::query()
            ->whereNot('id', $auth->getKey())
            ->withCount([
                'sentMessages' => fn (Builder $query) => $query->whereMorphedTo('receiver', $auth)->whereNull('seen_at')
            ])
            ->limit(10)
            ->get();
    }

    public function getMostRecentMessageableProperty(): ?Messageable
    {
        $auth = $this->getAuthModel();

        $message = Message::query()
            ->whereSender($auth)
            ->orWhere(fn ($query) => $query->whereReceiver($auth))
            ->latest()
            ->first();

        if ($message?->sender->is($auth)) {
            return $message->receiver;
        } else if ($message?->receiver->is($auth)) {
            return $message->sender;
        } else {
            return null;
        }
    }

    public function isOldestUnseenMessage(Message $message)
    {
        return $this->oldestUnseenMessage?->is($message);
    }

    /**
     * get the oldest unseen message from the sender who is not the current auth model
     */
    public function getOldestUnseenMessageProperty(): ?Message
    {
        if ($this->messages->isNotEmpty()) {
            return $this->messages
                ->filter(fn (Message $message) => $this->messageIsReceived($message) && $message->unseen())
                ->sortBy(fn (Message $message) => $message->created_at->getTimestamp())
                ->first();
        }

        return null;
    }

    /**
     * checks if a message is seen
     * - message is considered seen if the message is seen
     * and [
     * the sender is the current auth user or 
     * (the receiver is a driver and the current auth user is a super admin or support admin)
     * ]
     */
    public function messageIsSeen(Message $message)
    {
        $auth = $this->getAuthModel();

        return ($message->sender->is($auth) ||
            ($this->isDriver($message->receiver) &&
                $auth->hasRole([UserRole::SUPER_ADMIN->value, UserRole::SUPPORT->value]))
        ) && $message->seen();
    }

    /**
     * checks if a message is received
     * - A message is considered received if
     * the receiver is the current auth user or
     * [the sender is of type **Driver**
     * and the receiver is of type **User**
     * and the current user is a super admin or a support admin]
     */
    public function messageIsReceived(Message $message): bool
    {
        /** @var \App\Models\Tenant\User */
        $auth = $this->getAuthModel();

        return
            $message->receiver()->is($auth) ||
            ($this->isDriver($message->sender) &&
                $this->isAdmin($message->receiver) &&
                $auth->hasRole([UserRole::SUPER_ADMIN->value, UserRole::SUPPORT->value])
            );
    }

    public function markMessagesAsSeen(string|int|array $key = "all")
    {
        /** @var \Illuminate\Database\Eloquent\Collection */
        $messages = $this->messages
            ->filter(fn (Message $message) => $this->messageIsReceived($message))
            ->filter(fn (Message $message) => $message->unseen());

        $messages
            ->when(
                $messages->isNotEmpty() && \filled($key),
                fn (Collection $messages) => Message::markMessagesAsSeen(
                    $key === 'all' ?
                        $messages->modelKeys() :
                        $messages->find(Arr::wrap($key))->modelKeys()
                )
            );
    }

    public function getMessagesProperty(): EloquentCollection
    {
        $auth = $this->getAuthModel();

        if (
            blank($this->selectedMessageable) ||
            !($this->selectedMessageable instanceof Model) ||
            $this->selectedMessageable->is($auth)
        ) {
            return EloquentCollection::make([]);
        }

        if ($this->isDriver($this->selectedMessageable)) {
            return $this
                ->selectedMessageable
                ->messages()
                ->get();
        }

        return Message::query()
            ->whereBetween($auth, $this->selectedMessageable)
            ->oldest()
            ->get();
    }

    public function getLastSeenAtProperty()
    {
        if (blank($this->selectedMessageable)) {
            return '';
        }

        return $this->selectedMessageable->lastActiveAt() ??
            $this->selectedMessageable->lastSentMessage?->created_at;
    }

    public function getOnlineStatusProperty()
    {
        if (blank($this->lastSeenAt)) {
            return 'offline';
        }

        $isOnline = $this->lastSeenAt->gte(\now()->subMinutes(20));

        return $isOnline ?
            'online' :
            'last seen ' . $this->lastSeenAt->diffForHumans();
    }

    public function getMessageable(string|int $messageable_uuid, string $type = '')
    {
        $this->reset('search'); // reset search to prevent rerendering search component

        if ($this->getAuthModel()->uuid === $messageable_uuid) {
            return;
        }

        $model = $this->messageableModels[$type] ?? null;

        if (blank($model)) {
            foreach ($this->messageableModels as $type => $class) {
                $this->selectedMessageable = $class::query()->where('uuid', $messageable_uuid)->first();

                if (filled($this->selectedMessageable)) {
                    return;
                }
            }
        }

        $this->selectedMessageable = $model::query()->where('uuid', $messageable_uuid)->first();
    }

    public function isAdmin(string|Model $type): bool
    {
        return $this->messageableModels['admins'] === (is_string($type) ? $type : $type::class);
    }

    public function isDriver(string|Model $type): bool
    {
        return $this->messageableModels['drivers'] === (is_string($type) ? $type : $type::class);
    }

    public function mountInboxAction(string $action, ?string $messageable = null)
    {
        if (method_exists($this, $action)) {
            $reflection = new ReflectionMethod($this, $action);
            if (!$reflection->isPublic()) {
                return;
            }

            $result = $this->$action($messageable);

            $this->dispatchBrowserEvent('scroll-chat-to-bottom');

            return $result;
        }
    }

    public function sendMessage()
    {
        $message = trim($this->form->getState()['message']);

        if (
            blank($this->selectedMessageable) ||
            $this->selectedMessageable->is($this->getAuthModel())
        ) {
            return;
        }

        $new_message = new Message();
        $new_message->body = $message;
        $new_message->sender()->associate($this->getAuthModel());
        $new_message->receiver()->associate($this->selectedMessageable);

        $new_message->save();

        $this->reset('message'); // Clear the message after it's sent
        $this->dispatchBrowserEvent('scroll-chat-to-bottom');
    }
}
