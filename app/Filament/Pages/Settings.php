<?php

namespace App\Filament\Pages;

use App\Repositories\PlanRepository;
use Closure;
use Filament\Facades\Filament;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class Settings extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static string $view = 'filament::pages.settings';

    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static ?string $navigationGroup = 'Account';

    protected static ?int $navigationSort = 3;

    public function mount(): void
    {
        abort_unless(self::canAccessPage(), 403);

        $billing_info = $this->getFormModel();
        $this->billingInfoForm->fill([
            'organization' => $billing_info?->organization,
            'email' => $billing_info?->email,
            'name' => $billing_info?->name,
            'tax_number' => $billing_info?->tax_number,
            'address' => $billing_info?->address,
            'zip_code' => $billing_info?->zip_code,
        ]);
    }

    protected static function getAuthModel(): Model | null
    {
        return Filament::auth()->user();
    }

    protected function getFormModel(): Model | string | null
    {
        return tenant()->billingInfo()->first();
    }

    protected static function canAccessPage(): bool
    {
        return self::getAuthModel()->isSuperAdmin();
    }

    protected static function shouldRegisterNavigation(): bool
    {
        return self::canAccessPage();
    }

    public function getPlansProperty(PlanRepository $plans): Collection
    {
        $slug = tenant()->subscription?->plan?->slug ?? '';

        return $plans->getActiveExcept($slug);
    }

    public function getParamsProperty(): array
    {
        return ['tenant' => \tenant()->getTenantKey()];
    }

    protected function getBillingInfoFormSchema(): array
    {
        return [
            Grid::make([
                'default' => 1,
                'md' => 2,
            ])
                ->schema([
                    TextInput::make('organization')
                        ->label('Organization')
                        ->string()
                        ->maxLength(120),
                    TextInput::make('name')
                        ->label('Name')
                        ->string()
                        ->maxLength(120),
                    TextInput::make('email')
                        ->label('Email')
                        ->maxLength(255)
                        ->email(),
                    TextInput::make('tax_number')
                        ->label('Tax/VAT Number')
                        ->string()
                        ->maxLength(120),
                    TextInput::make('address')
                        ->label('Full Address')
                        ->string()
                        ->maxLength(255),
                    TextInput::make('zip_code')
                        ->label('Zip Code')
                        ->string()
                        ->maxLength(120),
                ]),
        ];
    }

    protected function getForms(): array
    {
        return [
            'billingInfoForm' => $this->makeForm()
                ->schema($this->getBillingInfoFormSchema())
                ->model($this->getFormModel()),
        ];
    }

    public function saveBillingInfo(): void
    {
        $data = $this->billingInfoForm->getState();
        $tenant = tenant();
        $billingInfo = $this->getFormModel();

        // save billing info to db replace name, organization, and email if blank
        $data = [
            'organization' => $data['organization'] ?? $tenant->organization,
            'name' => $data['name'] ?? $tenant->name,
            'email' => $data['email'] ?? $tenant->email,
            'tax_number' => $data['tax_number'],
            'address' => $data['address'],
            'zip_code' => $data['zip_code'],
        ];

        if ($billingInfo) {
            $saved = $billingInfo->fill($data)->save();
        } else {
            $saved = (bool) $tenant->billingInfo()->create($data);
        }

        $saved && $this->showSuccessNotification('Details saved successfully.');
    }

    protected function showSuccessNotification(string | Closure $body)
    {
        Notification::make('save-success-' . str()->random(5))
            ->body($body)
            ->success()
            ->seconds(10)
            ->send();
    }
}
