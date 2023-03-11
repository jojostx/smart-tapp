<?php

namespace App\Filament\Components;

use App\Models\Tenant\Access;
use App\Models\Tenant\Driver;
use App\Models\Tenant\Message;
use App\Models\Tenant\User;
use Illuminate\Database\Eloquent\Collection;
use Filament\Forms;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Livewire\Component;

class Chat extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    public Access $access;

    public User $issuer;

    public Driver $driver;

    public $message = '';

    public function mount(Access $access)
    {
        $this->access = $access->load(['driver', 'issuer']);

        $this->issuer = $this->access->issuer;

        $this->driver = $this->access->driver;
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

    public function getMessagesProperty(): Collection
    {
        return $this->driver->messages()
            ->oldest()
            ->get();
    }

    public function getReceivedMessagesCountProperty()
    {
        $this->driver->loadCount(['receivedMessages' => function (Builder $query) {
            $query->whereNull('seen_at');
        }]);

        return $this->driver->received_messages_count;
    }
    /**
     * checks if a message is the oldest unseen message received
     */
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
                ->filter(fn (Message $message) => $message->unseen() && $message->receiver->is($this->driver))
                ->sortBy(fn (Message $message) => $message->created_at->getTimestamp())
                ->first();
        }

        return null;
    }

    /**
     * checks if a message is seen
     */
    public function messageIsSeen(Message $message)
    {
        return $message->sender->is($this->driver) && $message->seen();
    }

    /**
     * checks if a message is received
     * - A message is considered received if
     * the receiver is the current auth user
     */
    public function messageIsReceived(Message $message): bool
    {
        return $message->receiver->is($this->driver);
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

    public function sendMessage()
    {
        $message = trim($this->form->getState()['message']);

        if (blank($this->driver) || blank($this->issuer)) return;

        $new_message = new Message();
        $new_message->body = $message;
        $new_message->sender()->associate($this->driver);
        $new_message->receiver()->associate($this->issuer);

        $new_message->save();

        $this->reset('message'); // Clear the message after it's sent
        $this->dispatchBrowserEvent('scroll-chat-to-bottom');
    }

    public function render()
    {
        return view('filament::components.chat');
    }
}
