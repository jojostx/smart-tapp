<?php

namespace App\Models\Tenant;

use App\Enums\Models\ParkingLotStatus;
use Dyrynda\Database\Support\BindsOnUuid;
use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'status' => ParkingLotStatus::class,
    ];

    /**
     * Get the parking lot's qrcode svg.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function qrcode(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                return replaceQrCodeAttributes(QrCode::generate(json_encode($attributes['uuid'])), 'w-full max-w-sm', 'parkinglot_qrcode');
            },
        )->shouldCache();
    }

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
