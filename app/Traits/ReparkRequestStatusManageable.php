<?php

namespace App\Traits;

use App\Enums\Models\ReparkRequestStatus;
use Illuminate\Database\Eloquent\Builder;

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
   * Checks if the repark request is being resolved (resolving).
   */
  public function isResolving(): bool
  {
    return $this->status == ReparkRequestStatus::RESOLVING;
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
   * @param \App\Enums\Models\ReparkRequestStatus $type
   * @throws \ValueError
   * 
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function scopeStatus(Builder $query, string|ReparkRequestStatus $type = '')
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
  public function scopeUnresolved(Builder $query)
  {
    return $query->where('status', ReparkRequestStatus::UNRESOLVED->value);
  }

  /**
   * Scope a query to only include resolving repark request.
   *
   * @param  \Illuminate\Database\Eloquent\Builder  $query
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function scopeResolving(Builder $query)
  {
    return $query->where('status', ReparkRequestStatus::RESOLVING->value);
  }

  /**
   * Scope a query to only include resolved repark request.
   *
   * @param  \Illuminate\Database\Eloquent\Builder  $query
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function scopeResolved(Builder $query)
  {
    return $query->where('status', ReparkRequestStatus::RESOLVED->value);
  }

  /**
   * Start Resolving the repark request
   *
   * @return bool
   */
  public function startResolving(): bool
  {
    if ($this->isResolving()) {
      return false;
    }

    return $this->forceFill([
      'status' => ReparkRequestStatus::RESOLVING,
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
