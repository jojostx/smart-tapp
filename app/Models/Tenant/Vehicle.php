<?php

namespace App\Models\Tenant;

use Dyrynda\Database\Support\BindsOnUuid;
use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    use HasFactory, GeneratesUuid, BindsOnUuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'plate_number',
        'brand',
        'model',
        'color',
    ];

    /**
     * Get the name for the vehicle.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                return "Vehicle_{$attributes['plate_number']}";
            },
        )->shouldCache();
    }

    public function drivers(): BelongsToMany
    {
        return $this->belongsToMany(Driver::class, 'accesses');
    }

    public function accesses(): HasMany
    {
        return $this->HasMany(Access::class);
    }
}
