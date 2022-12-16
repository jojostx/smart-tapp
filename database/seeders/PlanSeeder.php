<?php

namespace Database\Seeders;

use App\Enums\Models\FeatureResources;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Jojostx\Larasubs\Enums\IntervalType;
use Jojostx\Larasubs\Models\Feature;
use Jojostx\Larasubs\Models\Plan;

class PlanSeeder extends Seeder
{
  /**
   * Seed the application's database.
   *
   * @return void
   */
  public function run()
  {
    $standard = Plan::create([
      'name'                    => 'free',
      'description'             => [
        'tag' => 'Ideal for testing out our services',
        'body' => 'Ideal for individuals and low traffic organizations that need to control parking efficiently.',
        'icon' => 'heroicon-o-calculator',
        'highlight' => false,
      ],
      'active'                  => true,
      'price'                   => 0, // price in the lowest currency value (kobo)
      'currency'                => 'NGN',
      'interval'                => 12,
      'interval_type'           => IntervalType::MONTH,
      'trial_interval'          => 0,
      'trial_interval_type'     => IntervalType::DAY,
      'grace_interval'          => 0,
      'grace_interval_type'     => IntervalType::DAY,
      'sort_order' => 0,
    ]);
  
    $standard = Plan::create([
      'name'                    => 'standard',
      'description'             => [
        'tag' => 'Ideal for smaller organizations that need to control parking efficiently.',
        'body' => 'Ideal for smaller organizations that need to control parking efficiently.',
        'icon' => 'heroicon-o-scale',
        'highlight' => false,
      ],
      'active'                  => true,
      'price'                   => 3000000, // price in the lowest currency value (kobo)
      'currency'                => 'NGN',
      'interval'                => 12,
      'interval_type'           => IntervalType::MONTH,
      'trial_interval'          => 2,
      'trial_interval_type'     => IntervalType::WEEK,
      'grace_interval'          => 1,
      'grace_interval_type'     => IntervalType::WEEK,
      'sort_order' => 1,
    ]);

    $premium = Plan::create([
      'name'                    => 'premium',
      'description'             => [
        'tag' => 'For larger organizations that need reliable and scalable solutions',
        'body' => 'For larger organizations that need reliable and scalable solutions',
        'icon' => 'heroicon-o-fire',
        'highlight' => true,
      ],
      'active'                  => true,
      'price'                   => 5400000, // price in the lowest currency value (kobo)
      'currency'                => 'NGN',
      'interval'                => 12,
      'interval_type'           => IntervalType::MONTH,
      'trial_interval'          => 2,
      'trial_interval_type'     => IntervalType::WEEK,
      'grace_interval'          => 1,
      'grace_interval_type'     => IntervalType::WEEK
    ]);

    $enterprise = Plan::create([
      'name'                    => 'enterprise',
      'description'             => [
        'tag' => 'Available for establishments with large parking traffic, customized or unique business models',
        'body' => 'Available for establishments with large parking traffic, customized or unique business models',
        'icon' => 'heroicon-o-globe',
        'highlight' => false,
      ],
      'active'                  => true,
      'price'                   => 10200000, // price in the lowest currency value (kobo)
      'currency'                => 'NGN',
      'interval'                => 12,
      'interval_type'           => IntervalType::MONTH,
      'trial_interval'          => 2,
      'trial_interval_type'     => IntervalType::WEEK,
      'grace_interval'          => 1,
      'grace_interval_type'     => IntervalType::WEEK
    ]);

    $teamMembers = Feature::create([
      'name'             => FeatureResources::TEAM_MEMBERS->value,
      'description' => [
        'tag' => "Up to %d Team members",
        'body' => "Ability to add team members"
      ],
      'consumable'       => true,
      'active' => true,
      'interval'      => 6,
      'interval_type' => IntervalType::MONTH,
    ]);

    $parkingLots = Feature::create([
      'name'       => FeatureResources::PARKING_LOTS->value,
      'description' => [
        'tag' => "Up to %d Parking Lots",
        'body' => "Ability to create, administer and assign management of parking lots to team members"
      ],
      'consumable' => true,
      'active' => true,
      'interval'      => 6,
      'interval_type' => IntervalType::MONTH,
    ]);

    $accesses_per_parking_lot = Feature::create([
      'name'       => FeatureResources::ACCESSES_PER_PARKING_LOT->value,
      'description' => [
        'tag' => "Up to %d Accesses per parking lot",
        'body' => "Ability to issue access for vehicles on a per parking lot basis"
      ],
      'consumable' => true,
      'active' => true,
      'interval'      => 6,
      'interval_type' => IntervalType::MONTH,
    ]);

    $dedicated_support = Feature::create([
      'name'       => FeatureResources::DEDICATED_SUPPORT->value,
      'description' => [
        'tag' => 'Dedicated Support',
        'body' => 'Recieve support on issue and question with a direct contact person via mail, slack and other channels.'
      ],
      'consumable' => false,
      'active' => true,
    ]);

    $standard->features()->attach($teamMembers, ['units' => 1]);
    $standard->features()->attach($parkingLots, ['units' => 1]);
    $standard->features()->attach($accesses_per_parking_lot, ['units' => 50]);
    $standard->features()->attach($dedicated_support);

    $standard->features()->attach($teamMembers, ['units' => 2]);
    $standard->features()->attach($parkingLots, ['units' => 5]);
    $standard->features()->attach($accesses_per_parking_lot, ['units' => 50]);
    $standard->features()->attach($dedicated_support);

    $premium->features()->attach($teamMembers, ['units' => 5]);
    $premium->features()->attach($parkingLots, ['units' => 10]);
    $premium->features()->attach($accesses_per_parking_lot, ['units' => 100]);
    $premium->features()->attach($dedicated_support);

    $enterprise->features()->attach($teamMembers, ['units' => 10]);
    $enterprise->features()->attach($parkingLots, ['units' => 20]);
    $enterprise->features()->attach($accesses_per_parking_lot, ['units' => 200]);
    $enterprise->features()->attach($dedicated_support);
  }
}
