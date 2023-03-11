<?php

namespace App\Traits;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

trait Trackable
{
    public function getActivityCacheKey()
    {
        if (filled($tenant = tenant())) {
            return $tenant->getTenantKey() . ':last_active_at:' . $this->uuid;
        }

        return 'central:last_active_at:' . $this->uuid;
    }

    public function lastActiveAt()
    {
        $time = Cache::get($this->getActivityCacheKey());

        if (blank($time)) {
            return null;
        }

        try {
            return Carbon::createFromTimestamp($time);
        } catch (\Throwable $th) {
            return null;
        }
    }
}
