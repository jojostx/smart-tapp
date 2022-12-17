<?php

namespace Database\Seeders;

use App\Enums\Roles\UserRole;
use App\Models\Tenant\Access;
use App\Models\Tenant\Driver;
use App\Models\Tenant\ParkingLot;
use App\Models\Tenant\User;
use App\Models\Tenant\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

class TenantSeeder extends Seeder
{
    protected static array $roles = [
        UserRole::SUPER_ADMIN,
        UserRole::ADMIN,
        UserRole::SUPPORT
    ];

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        if (Schema::hasTable('users')) {
            // $user = User::find(1);

            foreach (self::$roles as $role_) {
                Role::create([
                    'name' => $role_->value,
                    'guard_name' => 'web',
                ]);
            }

            // Access::factory()
            //     ->for(ParkingLot::factory()->create())
            //     ->for(Driver::factory()->create())
            //     ->for(Vehicle::factory()->create())
            //     ->for($user, 'creator')
            //     ->for($user, 'issuer')
            //     ->create();
        }
    }
}
