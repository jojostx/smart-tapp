<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\Tenant\TenantAdminUserCreated;
use App\Events\Tenant\TenantVerified;
use App\Http\Middleware\InitializeTenancyByDomain;
use App\Jobs\Tenant;
use App\Jobs\Tenant\SendTenantVerificationEmail;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Stancl\JobPipeline\JobPipeline;
use Stancl\Tenancy\Controllers\TenantAssetsController;
use Stancl\Tenancy\Events;
use Stancl\Tenancy\Jobs;
use Stancl\Tenancy\Listeners;
use Stancl\Tenancy\Middleware;

class TenancyServiceProvider extends ServiceProvider
{
    // By default, no namespace is used to support the callable array syntax.
    public static string $controllerNamespace = '';

    public function events()
    {
        return [
            // Tenant events
            Events\CreatingTenant::class => [],
            Events\TenantCreated::class => [
                JobPipeline::make([
                    SendTenantVerificationEmail::class,
                ])->send(function (Events\TenantCreated $event) {
                    return $event->tenant;
                })->shouldBeQueued(true),
            ],

            // fired when a tenant is verified (email or phone number)
            TenantVerified::class => [
                JobPipeline::make([
                    Jobs\CreateDatabase::class,
                ])->send(function (TenantVerified $event) {
                    return $event->tenant;
                })->shouldBeQueued(true), // `false` by default, but you probably want to make this `true` for production.
            ],

            // fired when a tenant admin user has been created
            TenantAdminUserCreated::class => [
                JobPipeline::make([
                    // create assign super admin role to tenant user
                    Tenant\AssignSuperAdminRoleToTenantUser::class,
                ])->send(function (TenantAdminUserCreated $event) {
                    return $event->tenant;
                })->shouldBeQueued(true), // `false` by default, but you probably want to make this `true` for production.
            ],

            Events\SavingTenant::class => [],
            Events\TenantSaved::class => [],
            Events\UpdatingTenant::class => [],
            Events\TenantUpdated::class => [],
            Events\DeletingTenant::class => [],

            Events\TenantDeleted::class => [
                JobPipeline::make([
                    Tenant\DeleteTenantDatabase::class,
                ])->send(function (Events\TenantDeleted $event) {
                    return $event->tenant;
                })->shouldBeQueued(true), // `false` by default, but you probably want to make this `true` for production.
            ],

            // Domain events
            Events\CreatingDomain::class => [],

            Events\DomainCreated::class => [],

            Events\SavingDomain::class => [],
            Events\DomainSaved::class => [],
            Events\UpdatingDomain::class => [],
            Events\DomainUpdated::class => [],
            Events\DeletingDomain::class => [],
            Events\DomainDeleted::class => [],

            // Database events
            Events\DatabaseCreated::class => [
                JobPipeline::make([
                    Jobs\MigrateDatabase::class,
                ])->send(function (Events\DatabaseCreated $event) {
                    return $event->tenant;
                })->shouldBeQueued(true),
            ],

            Events\DatabaseMigrated::class => [
                JobPipeline::make([
                    Jobs\SeedDatabase::class,
                ])->send(function (Events\DatabaseMigrated $event) {
                    return $event->tenant;
                })->shouldBeQueued(true),
            ],

            Events\DatabaseSeeded::class => [
                JobPipeline::make([
                    // create Tenant Subdomain
                    Tenant\CreateTenantSubdomain::class,

                    // create Tenant Super Admin user
                    Tenant\CreateTenantAdminUser::class,
                ])->send(function (Events\DatabaseSeeded $event) {
                    return $event->tenant;
                })->shouldBeQueued(true),
            ],
            Events\DatabaseRolledBack::class => [],
            Events\DatabaseDeleted::class => [],

            // Tenancy events
            Events\InitializingTenancy::class => [],
            Events\TenancyInitialized::class => [
                Listeners\BootstrapTenancy::class,
            ],

            Events\EndingTenancy::class => [],
            Events\TenancyEnded::class => [
                Listeners\RevertToCentralContext::class,
            ],

            Events\BootstrappingTenancy::class => [],
            Events\TenancyBootstrapped::class => [],
            Events\RevertingToCentralContext::class => [],
            Events\RevertedToCentralContext::class => [],

            // Resource syncing
            Events\SyncedResourceSaved::class => [
                Listeners\UpdateSyncedResource::class,
            ],

            // Fired only when a synced resource is changed in a different DB than the origin DB (to avoid infinite loops)
            Events\SyncedResourceChangedInForeignDatabase::class => [],
        ];
    }

    public function register()
    {
        //
    }

    public function boot()
    {
        $this->bootEvents();

        $this->mapRoutes();

        $this->makeTenancyMiddlewareHighestPriority();

        $this->setTenantAssetMiddleware();

        $this->redirectToCentralDomainOnFail();
    }

    protected function bootEvents()
    {
        foreach ($this->events() as $event => $listeners) {
            foreach ($listeners as $listener) {
                if ($listener instanceof JobPipeline) {
                    $listener = $listener->toListener();
                }

                Event::listen($event, $listener);
            }
        }
    }

    protected function mapRoutes()
    {
        if (file_exists(base_path('routes/tenant.php'))) {
            Route::namespace(static::$controllerNamespace)
                ->group(base_path('routes/tenant.php'));
        }
    }

    protected function makeTenancyMiddlewareHighestPriority()
    {
        $tenancyMiddleware = [
            // Even higher priority than the initialization middleware
            Middleware\PreventAccessFromCentralDomains::class,

            InitializeTenancyByDomain::class,
            Middleware\InitializeTenancyBySubdomain::class,
            Middleware\InitializeTenancyByDomainOrSubdomain::class,
            Middleware\InitializeTenancyByPath::class,
            Middleware\InitializeTenancyByRequestData::class,
        ];

        foreach (array_reverse($tenancyMiddleware) as $middleware) {
            $this->app[\Illuminate\Contracts\Http\Kernel::class]->prependToMiddlewarePriority($middleware);
        }
    }

    protected function redirectToCentralDomainOnFail()
    {
        InitializeTenancyByDomain::$onFail = function () {
            return redirect(config('app.url'));
        };
    }

    protected function setTenantAssetMiddleware()
    {
        TenantAssetsController::$tenancyMiddleware = InitializeTenancyByDomain::class;
    }
}
