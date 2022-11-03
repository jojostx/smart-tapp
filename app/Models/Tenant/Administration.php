<?php

namespace App\Models\Tenant;

use ALajusticia\Expirable\Traits\Expirable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Administration extends Model
{
    use HasFactory, Expirable;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'expires_at',
    ];

    public static function defaultExpiresAt()
    {
        return Carbon::now()->addWeek();
    }

    /**
     * Get the admin for the administration.
     */
    public function administrator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the parking lot for the administration.
     */
    public function parkingLot(): BelongsTo
    {
        return $this->belongsTo(ParkingLot::class);
    }
}
