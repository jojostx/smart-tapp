<?php

namespace App\Traits;

use App\Enums\Models\ParkingLotStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait ParkingLotStatusManageable
{
  abstract public function accesses(): HasMany;

  /** @todo retrieve value from database based on the subscription plan the tenant is subscribed to */
  public function getMaxCapacity(): int
  {
    return 100;
  }

  /**
   * Scope a query to only query parking lots of the type indicated by the $type parameter
   * @method static whereStatus() get parking lots of the type indicated by the $type parameter
   * @param  \Illuminate\Database\Eloquent\Builder  $query
   * @param \App\Enums\Models\ParkingLotStatus $status
   * @throws \ValueError
   * 
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function scopeWhereStatus(Builder $query, string|ParkingLotStatus $status = '')
  {
    $status = is_string($status) ? ParkingLotStatus::from($status) : $status;

    $query = match ($status) {
      ParkingLotStatus::OPEN => $query->whereOpen(),
      ParkingLotStatus::CLOSED => $query->whereClosed(),
      ParkingLotStatus::FILLED => $query->whereFilled(),
      default => $query
    };

    return $query;
  }

  /**
   * Scope a query to only include OPEN parking lots.
   * open: where the total 'not inactive' (issued|active|expired) accesses 
   * is less than the parking lot's max_capacity and the status is set to OPEN
   * 
   * @param  \Illuminate\Database\Eloquent\Builder  $query
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function scopeWhereOpen(Builder $query)
  {
    return $query->where('status', ParkingLotStatus::OPEN);
  }

  /**
   * Scope a query to only include expired parking lots.
   * closed: where the total parking lot's status is set to CLOSED
   *
   * @param  \Illuminate\Database\Eloquent\Builder  $query
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function scopeWhereClosed(Builder $query)
  {
    return $query->where('status', ParkingLotStatus::CLOSED);
  }

  /**
   * Scope a query to only include expired parking lots.
   * filled: where the total 'not inactive' (issued|active|expired)
   * accesses is equal to the parking lot's max_capacity
   *
   * @param  \Illuminate\Database\Eloquent\Builder  $query
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function scopeWhereFilled(Builder $query)
  {
    return $query->whereHas('accesses', function (Builder $query) {
      $query->whereNotInactive();
    }, '>=', $this->getMaxCapacity());
  }

  /**
   * Get the current status of the parking lot
   * 
   * @return \Illuminate\Database\Eloquent\Casts\Attribute
   */
  protected function status(): Attribute
  {
    return Attribute::make(
      get: function ($value) {
        if ($this->isFilled()) {
          return ParkingLotStatus::FILLED;
        }

        return ParkingLotStatus::from($value);
      },
    );
  }

  /**
   * checks if the parking lot is open.
   *
   * @return bool
   */
  public function isOpen(): bool
  {
    return $this->status === ParkingLotStatus::OPEN;
  }

  /**
   * checks if the parking lot is closed || locked.
   *
   * @return bool
   */
  public function isClosed(): bool
  {
    return $this->status === ParkingLotStatus::CLOSED;
  }

  /**
   * checks if the parking lot is closed || locked.
   *
   * @return bool
   */
  public function isFilled(): bool
  {
    $this->loadCount(['accesses' => function ($query) {
      $query->whereNotInactive();
    }]);

    return $this->accesses_count >= $this->getMaxCapacity();
  }

  /**
   * open the parking lot by setting the status to OPEN.
   * 
   * @return bool
   */
  public function open(): bool
  {
    return false;
  }

  /**
   * close the parking lot by setting the status to CLOSED.
   * this prevents creation of parking lots for the parking lot
   * 
   * @param int $expiry_period
   * @param int $validity_period
   * 
   * @return bool
   */
  public function close(): bool
  {
    return false;
  }

  /**
   * close the parking lot by setting the status to CLOSED.
   * this prevents creation of parking lots for the parking lot
   * and deactivates all parking lots for this parking lot.
   *  
   * @return bool
   */
  public function lockdown(): bool
  {
    return false;
  }
}
