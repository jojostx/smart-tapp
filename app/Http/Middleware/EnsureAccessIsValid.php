<?php

namespace App\Http\Middleware;

use App\Models\Tenant\Access;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class EnsureAccessIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $redirectToRoute = 'access.home')
    {
        if (! $request->route()->hasParameter('access')) {
            throw new Exception("Missing required route parameter definition. The Route must have a required parameter definition that includes '{access}' ");
        }

        abort_if(is_null($request->route('access')), 404, "Missing required route parameter definition. The Route must have a required parameter definition that includes '{access}'.");

        $access = Access::whereUuid($request->route('access'))->first();

        if (!$access->isValid() || blank($access)) {
            if (Auth::guard('driver')->check()) {
                Auth::guard('driver')->logout();
            }

            return $request->expectsJson()
            ? abort(403, 'The Access is invalid or expired.')
            : Redirect::route($redirectToRoute ?: 'access.home')->with(compact($access));
        }

        return $next($request);
    }
}
