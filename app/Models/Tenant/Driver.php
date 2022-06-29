<?php

namespace App\Models\Tenant;

use Dyrynda\Database\Support\BindsOnUuid;
use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Driver extends Model
{
    use HasFactory, GeneratesUuid, BindsOnUuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'phone_number',
        'email',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
    ];

    /**
     * Get the name for the vehicle.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function identifierforfilament(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                return str("Driver-{$attributes['phone_number']}")->kebab();
            },
        )->shouldCache();
    }

    public function vehicles(): BelongsToMany
    {
        return $this->belongsToMany(Vehicle::class, 'accesses');
    }
}
