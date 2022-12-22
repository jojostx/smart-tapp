<?php

namespace App\Models;

use Dyrynda\Database\Support\BindsOnUuid;
use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class CreditCard extends Model implements Sortable
{
    use HasFactory;
    use GeneratesUuid;
    use BindsOnUuid;
    use SortableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'is_enabled',
        'first_6',
        'last_4',
        'issuer',
        'country',
        'type',
        'token',
        'expiry',
        'sort_order',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'token',
    ];

    public $sortable = [
        'order_column_name' => 'sort_order',
        'sort_when_creating' => true,
    ];

    public function cardNumber(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                return $attributes['first_6'] . "*******" . $attributes['last_4'];
            },
        );
    }

    public function tenant()
    {
        return $this->belongsTo(config('tenancy.tenant_model'));
    }

    /**
     * Scope a query to only include enabled cards.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereEnabled(Builder $query)
    {
        return $query->where('is_enabled', '=', true);
    }

    /**
     * Scope a query to only include disabled cards.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereDisabled(Builder $query)
    {
        return $query->where('is_enabled', '=', false);
    }

    /**
     * Mark the credit card as default.
     */
    public function makeDefault()
    {
        return tenancy()->central(function () {
            return $this->moveToStart()->refresh();
        });
    }

    /**
     * Mark the credit card as enabled
     *
     * @return bool
     */
    public function enable()
    {
        return tenancy()->central(function () {
            return $this->forceFill([
                'is_enabled' => true,
            ])->save();
        });
    }

    /**
     * Mark the credit card as disabled
     *
     * @return bool
     */
    public function disable()
    {
        return tenancy()->central(function () {
            return $this->forceFill([
                'is_enabled' => false,
            ])->save();
        });
    }

    /**
     * check if the credit card is disabled.
     */
    public function isEnabled()
    {
        return (bool) $this->is_enabled;
    }

    /**
     * check if the credit card is disabled.
     */
    public function isDisabled()
    {
        return !$this->isEnabled();
    }

    /**
     * check if the credit card is expired.
     */
    public function isExpired()
    {
        if (Carbon::canBeCreatedFromFormat($this->expiry, 'm/Y')) {
            $expiry = Carbon::createFromFormat('m/Y', $this->expiry);

            return $expiry->isCurrentWeek();
        }

        return false;
    }

    /**
     * check if the credit card is not expired.
     */
    public function isNotExpired()
    {
        return !$this->isExpired();
    }

    /**
     * check if the credit card is default.
     */
    public function isDefault(): bool
    {
        return tenancy()->central(function () {
            return $this->isFirstInOrder();
        });
    }

    /**
     * check if the credit card is not default.
     */
    public function isNotDefault()
    {
        return !$this->isDefault();
    }
}
