<?php

namespace App\Filament\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait CanCleanupStaleRecords
{
    /**
     * cleans up records in the database that have no related models.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  array  $related
     * @param  int|false  $daysToConsiderStale
     * @return bool
     */
    public static function cleanupstaleRecords(Model $model, array $related = [], int | false $daysToConsiderStale = 1)
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

        if (intval($daysToConsiderStale) > 0) {
            $query = $query->whereDoesntHave('accesses', function (Builder $query) use ($daysToConsiderStale) {
                $query->where('created_at', '<', now()->subDays($daysToConsiderStale));
            });
        } else {
            $query = $query->doesntHave('accesses');
        }

        return boolval($query->delete());
    }
}
