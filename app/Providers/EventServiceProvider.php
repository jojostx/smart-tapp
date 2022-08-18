<?php

namespace App\Providers;

use Illuminate\Auth\Events\Verified;
use App\Events\UnverifiedTenantVerified;
use App\Events\UnverifiedTenantRegistered;
use App\Listeners\Tenant\LogAccessActivationNotification;
use App\Listeners\Tenant\TenantUserLoggedOut;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        Logout::class => [
            TenantUserLoggedOut::class,
        ],
        NotificationSent::class => [
            LogAccessActivationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
