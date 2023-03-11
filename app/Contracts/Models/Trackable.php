<?php

namespace App\Contracts\Models;

interface Trackable
{
    /**
     * Get the entity's activity tracking key for use with cache.
     */
    public function getActivityCacheKey(): string;
}