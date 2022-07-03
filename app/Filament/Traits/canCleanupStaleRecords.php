<?php

namespace App\Filament\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait canCleanupStaleRecords
{
  /**
   * cleans up records in the database that have no related models.
   *
   * @param Illuminate\Database\Eloquent\Model $model
   * @param array $related
   *  
   * @return bool
   */
  public static function cleanupstaleRecords(Model $model, array $related, int $timeToConsiderStale = 1)
  {
    if (blank($related)) {
      return false;
    }

    if ($timeToConsiderStale <= 0) {
      return false;
    }

    $query = $model->query();

    foreach ($related as $value) {
      if (is_string($value)) {
        $query = $query->doesntHave($value);
      }
    }

    $result = $query->whereDoesntHave('accesses', function (Builder $query) use ($timeToConsiderStale) {
      $query->where('created_at', '<', now()->subDays($timeToConsiderStale));
    })->delete();

    return boolval($result);
  }
}
