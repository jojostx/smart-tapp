<?php

namespace App\Traits;

use App\Enums\Models\ReparkRequestStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

trait ReparkRequestStatusManageable
{
    use ReparkRequestResolutionNotifiable;

    /**
     * Checks if the repark request has been resolved.
     */
    public function isResolved(): bool
    {
        return $this->status == ReparkRequestStatus::RESOLVED;
    }

    /**
     * Checks if the repark request is being resolved (pending).
     */
    public function isPendingConfirmation(): bool
    {
        return $this->status == ReparkRequestStatus::PENDING;
    }

    /**
     * Checks if the repark request is unresolved.
     */
    public function isUnresolved(): bool
    {
        return $this->status == ReparkRequestStatus::UNRESOLVED;
    }

    /**
     * Scope a query to only query access of the type indicated by the $type parameter
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \App\Enums\Models\ReparkRequestStatus  $type
     * @return \Illuminate\Database\Eloquent\Builder
     *
     * @throws \ValueError
     */
    public function scopeWhereStatus(Builder $query, string | ReparkRequestStatus $type = '')
    {
        $status = is_string($type) ? ReparkRequestStatus::from($type) : $type;

        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include unresolved repark request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereUnresolved(Builder $query)
    {
        return $query->where('status', ReparkRequestStatus::UNRESOLVED->value);
    }

    /**
     * Scope a query to only include pending repark request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWherePending(Builder $query)
    {
        return $query->where('status', ReparkRequestStatus::PENDING->value);
    }

    /**
     * Scope a query to only include resolved repark request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereResolved(Builder $query)
    {
        return $query->where('status', ReparkRequestStatus::RESOLVED->value);
    }

    /**
     * returns a collection of the count of all the repark request based on the status
     * e.g: ``['total_count' => 10, 'unresolved_count' => 2, 'pending_count' => 4, 'resolved_count' => 4]``
     *
     * @return Collection
     */
    public static function getStatusesCount(): Collection
    {
        $unresolved = ReparkRequestStatus::UNRESOLVED->value;
        $pending = ReparkRequestStatus::PENDING->value;
        $resolved = ReparkRequestStatus::RESOLVED->value;

        return static::toBase()
            ->selectRaw('count(*) as total_count')
            ->selectRaw("count(IF(status = '$unresolved', 1, null)) as unresolved_count")
            ->selectRaw("count(IF(status = '$pending', 1, null)) as pending_count")
            ->selectRaw("count(IF(status = '$resolved', 1, null)) as resolved_count")
            ->get();
    }

    /**
     * mark the repark request as pending
     *
     * @return bool
     */
    public function markAsPending(): bool
    {
        if ($this->isPendingConfirmation()) {
            return false;
        }

        return $this->forceFill([
            'status' => ReparkRequestStatus::PENDING,
        ])->save();
    }

    /**
     * Resolve the repark request
     *
     * @return bool
     */
    public function resolve(): bool
    {
        return $this->forceFill([
            'status' => ReparkRequestStatus::RESOLVED,
        ])->save();
    }
}
