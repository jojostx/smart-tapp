<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    protected function redirectTo($request)
    {
        if ($request->route()->named('access.dashboard')) {
            if ($request->route()->hasParameter('access')) {
                return route('access.scan', ['access' => $request->route()->access]);
            } else {
                abort(404);
            }
        }

        if (! $request->expectsJson()) {
            return route('login');
        }
    }
}
