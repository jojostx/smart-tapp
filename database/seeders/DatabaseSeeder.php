<?php

namespace Database\Seeders;

use App\Enums\Models\FeatureResources;
use Illuminate\Database\Seeder;
use Jojostx\Larasubs\Enums\IntervalType;
use Jojostx\Larasubs\Models\Feature;
use Jojostx\Larasubs\Models\Plan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->seedPlans();
    }

    public function seedPlans()
    {
        /** yearly */
        $free = Plan::create([
            'name' => 'free',
            'description' => [
                'tag' => 'Ideal for individuals and low traffic organizations testing out our services.',
                'body' => 'Ideal for individuals and low traffic organizations testing out our services.',
                'icon' => 'heroicon-o-calculator',
                'highlight' => false,
            ],
            'active' => true,
            'price' => 0, // price in the lowest currency value (kobo)
            'currency' => 'NGN',
            'interval' => 12,
            'interval_type' => IntervalType::MONTH,
            'trial_interval' => 0,
            'trial_interval_type' => IntervalType::DAY,
            'grace_interval' => 0,
            'grace_interval_type' => IntervalType::DAY,
            'sort_order' => 0,
        ]);

        $standard_yearly = Plan::create([
            'name' => 'standard',
            'slug' => 'standard-yearly',
            'description' => [
                'tag' => 'Ideal for medium organizations that need to control parking efficiently.',
                'body' => 'Ideal for medium organizations that need to control parking efficiently.',
                'icon' => 'heroicon-o-scale',
                'highlight' => false,
            ],
            'active' => true,
            'price' => 3000000, // price in the lowest currency value (kobo)
            'currency' => 'NGN',
            'interval' => 12,
            'interval_type' => IntervalType::MONTH,
            'trial_interval' => 2,
            'trial_interval_type' => IntervalType::WEEK,
            'grace_interval' => 1,
            'grace_interval_type' => IntervalType::WEEK,
            'sort_order' => 1,
        ]);

        $premium_yearly = Plan::create([
            'name' => 'premium',
            'slug' => 'premium-yearly',
            'description' => [
                'tag' => 'For larger organizations that need reliable and scalable solutions',
                'body' => 'For larger organizations that need reliable and scalable solutions',
                'icon' => 'heroicon-o-fire',
                'highlight' => true,
            ],
            'active' => true,
            'price' => 5400000, // price in the lowest currency value (kobo)
            'currency' => 'NGN',
            'interval' => 12,
            'interval_type' => IntervalType::MONTH,
            'trial_interval' => 2,
            'trial_interval_type' => IntervalType::WEEK,
            'grace_interval' => 1,
            'grace_interval_type' => IntervalType::WEEK,
            'sort_order' => 2,
        ]);

        $enterprise_yearly = Plan::create([
            'name' => 'enterprise',
            'slug' => 'enterprise-yearly',
            'description' => [
                'tag' => 'Ideal for establishments with specialized and large parking traffic',
                'body' => 'Ideal for establishments with specialized and large parking traffic',
                'icon' => 'heroicon-o-globe',
                'highlight' => false,
            ],
            'active' => true,
            'price' => 10200000, // price in the lowest currency value (kobo)
            'currency' => 'NGN',
            'interval' => 12,
            'interval_type' => IntervalType::MONTH,
            'trial_interval' => 2,
            'trial_interval_type' => IntervalType::WEEK,
            'grace_interval' => 1,
            'grace_interval_type' => IntervalType::WEEK,
            'sort_order' => 3,
        ]);

        /** monthly */
        $standard_monthly = Plan::create([
            'name' => 'standard',
            'slug' => 'standard-monthly',
            'description' => [
                'tag' => 'Ideal for medium organizations that need to control parking efficiently.',
                'body' => 'Ideal for medium organizations that need to control parking efficiently.',
                'icon' => 'heroicon-o-scale',
                'highlight' => false,
            ],
            'active' => true,
            'price' => 350000, // price in the lowest currency value (kobo)
            'currency' => 'NGN',
            'interval' => 1,
            'interval_type' => IntervalType::MONTH,
            'trial_interval' => 1,
            'trial_interval_type' => IntervalType::WEEK,
            'grace_interval' => 1,
            'grace_interval_type' => IntervalType::WEEK,
            'sort_order' => 1,
        ]);

        $premium_monthly = Plan::create([
            'name' => 'premium',
            'slug' => 'premium-monthly',
            'description' => [
                'tag' => 'For larger organizations that need reliable and scalable solutions',
                'body' => 'For larger organizations that need reliable and scalable solutions',
                'icon' => 'heroicon-o-fire',
                'highlight' => true,
            ],
            'active' => true,
            'price' => 600000, // price in the lowest currency value (kobo)
            'currency' => 'NGN',
            'interval' => 1,
            'interval_type' => IntervalType::MONTH,
            'trial_interval' => 1,
            'trial_interval_type' => IntervalType::WEEK,
            'grace_interval' => 1,
            'grace_interval_type' => IntervalType::WEEK,
            'sort_order' => 2,
        ]);

        $enterprise_monthly = Plan::create([
            'name' => 'enterprise',
            'slug' => 'enterprise-monthly',
            'description' => [
                'tag' => 'Ideal for establishments with specialized and large parking traffic',
                'body' => 'Ideal for establishments with specialized and large parking traffic',
                'icon' => 'heroicon-o-globe',
                'highlight' => false,
            ],
            'active' => true,
            'price' => 1000000, // price in the lowest currency value (kobo)
            'currency' => 'NGN',
            'interval' => 1,
            'interval_type' => IntervalType::MONTH,
            'trial_interval' => 1,
            'trial_interval_type' => IntervalType::WEEK,
            'grace_interval' => 1,
            'grace_interval_type' => IntervalType::WEEK,
            'sort_order' => 3,
        ]);

        /* features */
        $teamMembers = Feature::create([
            'name' => FeatureResources::TEAM_MEMBERS->value,
            'description' => [
                'tag' => 'Up to %d Team members',
                'body' => 'Ability to add team members',
            ],
            'consumable' => true,
            'active' => true,
            'interval' => 1,
            'interval_type' => IntervalType::MONTH,
        ]);

        $parkingLots = Feature::create([
            'name' => FeatureResources::PARKING_LOTS->value,
            'description' => [
                'tag' => 'Up to %d Parking Lots',
                'body' => 'Ability to create, administer and assign management of parking lots to team members',
            ],
            'consumable' => true,
            'active' => true,
            'interval' => 1,
            'interval_type' => IntervalType::MONTH,
        ]);

        $accesses_per_parking_lot = Feature::create([
            'name' => FeatureResources::ACCESSES_PER_PARKING_LOT->value,
            'description' => [
                'tag' => 'Up to %d Accesses per parking lot',
                'body' => 'Ability to issue access for vehicles on a per parking lot basis',
            ],
            'consumable' => true,
            'active' => true,
            'interval' => 1,
            'interval_type' => IntervalType::MONTH,
        ]);

        $dedicated_support = Feature::create([
            'name' => FeatureResources::DEDICATED_SUPPORT->value,
            'description' => [
                'tag' => 'Dedicated Support',
                'body' => 'Recieve support on issue and question with a direct contact person via mail, slack and other channels.',
            ],
            'consumable' => false,
            'active' => true,
        ]);

        /** associations */
        $free->features()->attach($teamMembers, ['units' => 2]);
        $free->features()->attach($parkingLots, ['units' => 1]);
        $free->features()->attach($accesses_per_parking_lot, ['units' => 50]);
        $free->features()->attach($dedicated_support);

        $standard_yearly->features()->attach($teamMembers, ['units' => 4]);
        $standard_yearly->features()->attach($parkingLots, ['units' => 5]);
        $standard_yearly->features()->attach($accesses_per_parking_lot, ['units' => 60]);
        $standard_yearly->features()->attach($dedicated_support);

        $premium_yearly->features()->attach($teamMembers, ['units' => 5]);
        $premium_yearly->features()->attach($parkingLots, ['units' => 10]);
        $premium_yearly->features()->attach($accesses_per_parking_lot, ['units' => 120]);
        $premium_yearly->features()->attach($dedicated_support);

        $enterprise_yearly->features()->attach($teamMembers, ['units' => 10]);
        $enterprise_yearly->features()->attach($parkingLots, ['units' => 20]);
        $enterprise_yearly->features()->attach($accesses_per_parking_lot, ['units' => 200]);
        $enterprise_yearly->features()->attach($dedicated_support);

        $standard_monthly->features()->attach($teamMembers, ['units' => 4]);
        $standard_monthly->features()->attach($parkingLots, ['units' => 5]);
        $standard_monthly->features()->attach($accesses_per_parking_lot, ['units' => 60]);
        $standard_monthly->features()->attach($dedicated_support);

        $premium_monthly->features()->attach($teamMembers, ['units' => 5]);
        $premium_monthly->features()->attach($parkingLots, ['units' => 10]);
        $premium_monthly->features()->attach($accesses_per_parking_lot, ['units' => 120]);
        $premium_monthly->features()->attach($dedicated_support);

        $enterprise_monthly->features()->attach($teamMembers, ['units' => 10]);
        $enterprise_monthly->features()->attach($parkingLots, ['units' => 20]);
        $enterprise_monthly->features()->attach($accesses_per_parking_lot, ['units' => 200]);
        $enterprise_monthly->features()->attach($dedicated_support);
    }
}
