<?php

namespace Database\Seeders;

use App\Enums\Roles\UserRole;
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
            foreach (self::$roles as $role_) {
                Role::query()
                    ->firstOrCreate([
                        'name' => $role_->value,
                        'guard_name' => 'web',
                    ]);
            }
        }
    }
}
