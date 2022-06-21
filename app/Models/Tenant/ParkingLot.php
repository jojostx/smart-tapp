<?php

namespace App\Models\Tenant;

use Dyrynda\Database\Support\BindsOnUuid;
use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParkingLot extends Model
{
    use HasFactory, GeneratesUuid, BindsOnUuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Get the accesses for the parking lot.
     */
    public function accesses(): HasMany
    {
        return $this->hasMany(Access::class, 'parking_lot_id');
    }

    /**
     * The users that belong to the role.
     */
    public function vehicles()
    {
        return $this->belongsToMany(Vehicle::class, 'accesses');
    }
}
