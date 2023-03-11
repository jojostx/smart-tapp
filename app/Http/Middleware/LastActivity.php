<?php

namespace App\Http\Middleware;

use App\Contracts\Models\Trackable;
use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class LastActivity
{
    public function handle(Request $request, Closure $next)
    {
        if (filled($user = $request->user()) && $user instanceof Trackable) {
            $key = $user->getActivityCacheKey();
            Cache::forever($key, time());
        }

        return $next($request);
    }
}
