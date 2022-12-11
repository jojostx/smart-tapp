<?php

namespace App\Filament\Pages;

use App\Models\Tenant\User;
use App\Repositories\PlanRepository;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Jojostx\Larasubs\Models\Plan;

class Settings extends Page
{    
    protected static string $view = 'filament::pages.settings';
    
    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static ?string $navigationGroup = 'Account';

    protected static ?int $navigationSort = 3;

    protected static function canAccessPage(): bool
    {
        return User::find(auth()->guard('web')->id())?->isSuperAdmin();
    }

    protected static function shouldRegisterNavigation(): bool
    {
        return self::canAccessPage();
    }

    public function getPlansProperty(): Collection
    {
        return app(PlanRepository::class)->getActive();
    }

    public function getParamsProperty(): array
    {
        return ['tenant' => \tenant()->getTenantKey()];
    }

    public function mount(): void
    {
        abort_unless(self::canAccessPage(), 403);
    }
}
