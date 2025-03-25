<?php

namespace App\Http\Middleware;

use Auth;
use Closure;

class AdminAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        } else {
            if( !in_array(Auth::user()->role, ['superadmin', 'staff']) ) {
                Auth::logout();
                return redirect()->route('login');
            }
            if( !Auth::user()->hasRole('superadmin')) {
                Auth::logout();
                return redirect()->route('login')->withErrors('Your account is inactive');
            }
            return $next($request);
        }

        return $next($request);
    }
}
