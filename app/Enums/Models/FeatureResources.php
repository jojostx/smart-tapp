<?php

namespace App\Enums\Models;

use Illuminate\Support\Arr;

enum FeatureResources: string
{
    case TEAM_MEMBERS = 'team-members';
    case PARKING_LOTS = 'parking-lots';
    case ACCESSES_PER_PARKING_LOT = 'accesses-per-parking-lot';
    case DEDICATED_SUPPORT = 'dedicated_support';

    public static function getResourceByFeature(string $feature): string
    {
        $relatedResources = [
            self::TEAM_MEMBERS->value => \App\Models\Tenant\User::class,
            self::PARKING_LOTS->value => \App\Models\Tenant\ParkingLot::class,
            self::ACCESSES_PER_PARKING_LOT->value => \App\Models\Tenant\Access::class,
            self::DEDICATED_SUPPORT->value => '',
        ];

        return Arr::get($relatedResources, $feature, '');
    }

    public static function toArray(): array
    {
        return collect(self::cases())->flatMap(function ($status) {
            return [$status->value => ucfirst($status->value)];
        })->toArray();
    }

    public static function toDescriptionArray(): array
    {
        return [
            self::TEAM_MEMBERS->value => 'Access for the parking lot can be created',
            self::PARKING_LOTS->value => 'Access for the Parking Lot can not be created.',
            self::ACCESSES_PER_PARKING_LOT->value => 'All Accesses for the Parking Lot will be deactivated.',
        ];
    }
}
