<?php

namespace App\Filament\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait CanCleanupStaleRecords
{
  /**
   * cleans up records in the database that have no related models.
   *
   * @param \Illuminate\Database\Eloquent\Model $model
   * @param array $related
   * @param int|false $timeToConsiderStale
   *  
   * @return bool
   */
  public static function cleanupstaleRecords(Model $model, array $related = [], int|false $timeToConsiderStale = 1)
  {
    if (blank($related)) {
      return false;
    }

    $query = $model->query();

    foreach ($related as $value) {
      if (is_string($value)) {
        $query = $query->doesntHave($value);
      }
    }

    if (intval($timeToConsiderStale) > 0) {
      $query = $query->whereDoesntHave('accesses', function (Builder $query) use ($timeToConsiderStale) {
        $query->where('created_at', '<', now()->subDays($timeToConsiderStale));
      });
    } else {
      $query = $query->doesntHave('accesses');
    }

    return boolval($query->delete());
  }
}
