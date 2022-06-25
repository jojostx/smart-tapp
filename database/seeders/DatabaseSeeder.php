<?php

namespace Database\Seeders;

use App\Models\Tenant\Access;
use App\Models\Tenant\Driver;
use App\Models\Tenant\ParkingLot;
use App\Models\Tenant\User;
use App\Models\Tenant\Vehicle;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user = User::find(1);

        Access::factory()
            ->for(ParkingLot::factory()->create())
            ->for(Driver::factory()->create())
            ->for(Vehicle::factory()->create())
            ->for($user, 'creator')
            ->for($user, 'issuer')
            ->create();
    }
}
