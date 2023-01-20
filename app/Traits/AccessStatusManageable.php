<?php

namespace App\Traits;

use App\Enums\Models\AccessStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;

trait AccessStatusManageable
{
    use AccessActivationNotifiable;

    public static function getQueryFragmentForElapsedCheck(): string
    {
        return 'DATE_ADD(issued_at, INTERVAL IFNULL(expiry_period, 0) MINUTE)';
    }

    public static function getQueryFragmentForInvalidCheck(): string
    {
        return 'DATE_SUB(TIMESTAMPADD(DAY, `validity_period`, `issued_at`), INTERVAL IFNULL(`expiry_period`, 0) MINUTE)';
    }

    /**
     * Scope a query to only query access of the type indicated by the $type parameter
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \App\Enums\Models\AccessStatus  $type
     * @return \Illuminate\Database\Eloquent\Builder
     *
     * @throws \ValueError
     */
    public function scopeWhereStatus(Builder $query, string | AccessStatus $type = '')
    {
        $status = is_string($type) ? AccessStatus::from($type) : $type;

        $elapsed_fragment = static::getQueryFragmentForElapsedCheck();
        $invalid_fragment = static::getQueryFragmentForInvalidCheck();

        $query = match ($status) {
            AccessStatus::EXPIRED => $query->where('expiry_period', '!=', 0)
                ->whereRaw("now() > {$elapsed_fragment}")
                ->whereRaw("now() < {$invalid_fragment}"),

            AccessStatus::ISSUED => $query->where('expiry_period', '!=', 0)
                ->whereRaw("now() < {$elapsed_fragment}")
                ->whereRaw("now() < {$invalid_fragment}"),

            AccessStatus::ACTIVE => $query->where('expiry_period', '=', 0)
                ->whereRaw("now() < {$invalid_fragment}"),

            AccessStatus::INACTIVE => $query->where('expiry_period', '=', 0)
                ->whereRaw("now() > {$invalid_fragment}"),

            default => $query
        };

        return $query;
    }

    /**
     * Scope a query to only include issued accesses.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereIssued(Builder $query)
    {
        $elapsed_fragment = static::getQueryFragmentForElapsedCheck();
        $invalid_fragment = static::getQueryFragmentForInvalidCheck();

        return $query->where('expiry_period', '!=', 0)
            ->whereRaw("now() < {$elapsed_fragment}")
            ->whereRaw("now() < {$invalid_fragment}");
    }

    /**
     * Scope a query to only include expired accesses.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereExpired(Builder $query)
    {
        $elapsed_fragment = static::getQueryFragmentForElapsedCheck();
        $invalid_fragment = static::getQueryFragmentForInvalidCheck();

        return $query->where('expiry_period', '!=', 0)
            ->whereRaw("now() > {$elapsed_fragment}")
            ->whereRaw("now() < {$invalid_fragment}");
    }

    /**
     * Scope a query to only include active accesses.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereActive(Builder $query)
    {
        $invalid_fragment = static::getQueryFragmentForInvalidCheck();

        return $query->where('expiry_period', '=', 0)
            ->whereRaw("now() < {$invalid_fragment}");
    }

    /**
     * Scope a query to only include inactive/deactivated accesses.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereInactive(Builder $query)
    {
        $invalid_fragment = static::getQueryFragmentForInvalidCheck();

        return $query->where('expiry_period', '=', 0)
            ->whereRaw("now() > {$invalid_fragment}");
    }

    /**
     * Scope a query to only include accesses that are not inactive [only active, expired and issued].
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereNotInactive(Builder $query)
    {
        $invalid_fragment = static::getQueryFragmentForInvalidCheck();

        return $query->whereNot(function ($query) use ($invalid_fragment) {
            $query->where('expiry_period', '=', 0)
                ->whereRaw("now() > {$invalid_fragment}");
        });
    }

    /**
     * Get the current state of the access.
     * [expired, issued, active, inactive]
     * to specify when the access is expired, the 'expiry_period' attribute should be set and elapsed and the 'valid_until' is not in the past.
     * to specify when the access is issued the 'expiry_period' attribute should be set and not elapsed and the 'valid_until' attribute should not be in the past.
     * to specify when the access is active, the 'expiry_period' attribute should be 0 and the 'valid_until' attribute should not be in the past.
     * to specify when the access is inactive the expiry_period' attribute should be 0 and the 'valid_until' attribute should be in the past.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function status(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $issued_at = $this->asDateTime($attributes['issued_at']);

                $valid_until = $this->asDateTime($attributes['issued_at'])->addDays($attributes['validity_period']);

                $expiry_period = $attributes['expiry_period'];

                $elapsed = \elapsed($issued_at->addMinutes($expiry_period));

                $invalid = \elapsed($valid_until->subMinutes($expiry_period));

                // expired
                if (boolval($expiry_period) && $elapsed && ! $invalid) {
                    return AccessStatus::EXPIRED;
                }

                // issued
                if (boolval($expiry_period) && ! $elapsed && ! $invalid) {
                    return AccessStatus::ISSUED;
                }

                // active
                if (! boolval($expiry_period) && ! $invalid) {
                    return AccessStatus::ACTIVE;
                }

                // inactive | deactivated
                if (! boolval($expiry_period) && $invalid) {
                    return AccessStatus::INACTIVE;
                }

                return AccessStatus::INACTIVE;
            },
        );
    }

    /**
     * returns a collection of the count of all the accesses based on the status
     * e.g: ``['total_count' => 10, 'expired_count' => 2, 'issued_count' => 4, 'active_count' => 4, 'inactive_count' => 0]``
     *
     * @return Collection
     */
    public static function getStatusesCount()
    {
        $empty_expiry_fragment = '(expiry_period = 0)';
        $filled_expiry_fragment = '(expiry_period != 0)';
        $elapsed_fragment = static::getQueryFragmentForElapsedCheck();
        $invalid_fragment = static::getQueryFragmentForInvalidCheck();

        $expired_query = "(($filled_expiry_fragment) AND (NOW() > $elapsed_fragment) AND (NOW() < $invalid_fragment))";
        $issued_query = "(($filled_expiry_fragment) AND (NOW() < $elapsed_fragment) AND (NOW() < $invalid_fragment))";
        $active_query = "(($empty_expiry_fragment) AND (NOW() < $invalid_fragment))";
        $inactive_query = "(($empty_expiry_fragment) AND (NOW() > $invalid_fragment))";

        return static::toBase()
            ->selectRaw('count(*) as total_count')
            ->selectRaw("count(IF($expired_query, 1, null)) as expired_count")
            ->selectRaw("count(IF($issued_query, 1, null)) as issued_count")
            ->selectRaw("count(IF($active_query, 1, null)) as active_count")
            ->selectRaw("count(IF($inactive_query, 1, null)) as inactive_count")
            ->get();
    }

    /**
     * checks if the access is valid (not inactive or deactived).
     *
     * @return bool
     */
    public function isValid(): bool
    {
        return ! ($this->status === AccessStatus::INACTIVE);
    }

    /**
     * checks if the access is issued.
     *
     * @return bool
     */
    public function isIssued(): bool
    {
        return $this->status === AccessStatus::ISSUED;
    }

    /**
     * checks if the access is active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === AccessStatus::ACTIVE;
    }

    /**
     * checks if the access is expired.
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->status === AccessStatus::EXPIRED;
    }

    /**
     * checks if the access is inactive or deactivated.
     *
     * @return bool
     */
    public function isInactive(): bool
    {
        return $this->status === AccessStatus::INACTIVE;
    }

    /**
     * issue the access and send out the access activation notification.
     * issue sets 'expiry_period' to the expiry period set by tenant's Super Admin or admin.
     * 
     * @todo get the MAX_VALIDITY_PERIOD & MAX_EXPIRY_PERIOD from the tenant's setting
     * 
     * @param  int  $expiry_period
     * @param  int  $validity_period
     * @return bool
     */
    public function issue(?int $expiry_period = 0, ?int $validity_period = 0): bool
    {
        // $MAX_EXPIRY_PERIOD = 30;
        // $MAX_VALIDITY_PERIOD = 2;
        if (blank($expiry_period) || ! in_range($expiry_period, 10, 30, true)) {
            $expiry_period = $this->expiry_period ?? 30;
        }

        if (blank($validity_period) || ! in_range($validity_period, 1, 2, true)) {
            $validity_period = $this->validity_period ?? 2;
        }

        return $this->forceFill([
            'expiry_period' => $expiry_period,
            'validity_period' => $validity_period,
            'issued_at' => $this->freshTimestamp(),
        ])->save();
    }

    /**
     * activate the access.
     * activate sets 'expiry_period' attribute to 0, if and only if the access is issued or expired and not active.
     *
     * @return bool
     */
    public function activate(): string
    {
        if ($this->isActive()) {
            return false;
        }

        if (elapsed($this->valid_until?->subMinutes($this->expiry_period) ?? now())) {
            $this->forceFill([
                'issued_at' => now(),
            ]);
        }

        return $this->forceFill([
            'expiry_period' => 0,
        ])->save();
    }

    /**
     * deactivate the access.
     * deactivate sets 'expiry_period' attribute to 0 and the 'valid_until' to a period in the past.
     *
     * @return bool
     */
    public function deactivate(): bool
    {
        if ($this->isInactive()) {
            return true;
        }

        return $this->forceFill([
            'expiry_period' => 0,
            'issued_at' => $this->issued_at->subDays($this->validity_period),
        ])->save();
    }
}
