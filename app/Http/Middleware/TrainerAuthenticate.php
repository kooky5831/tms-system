<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Support\Facades\Route;

class TrainerAuthenticate
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
            if( !in_array(Auth::user()->role, ['trainer']) ) {
                return redirect()->route('login');
            }
            if(!Auth::user()->hasRole('trainer')) {
                Auth::logout();
                return redirect()->route('login')->withErrors('Your account is inactive');
            }
            return $next($request);
        }

        // if (!Auth::check() || !Auth::user()->hasRole('trainer') || Auth::user()->trainer->isBlocked()) {
            // if (!Auth::check() || !Auth::user()->hasRole('trainer')) {
            //     return redirect()->route('login');
            // }
            // if( !Auth::user()->status ) {
            //     Auth::logout();
            //     return redirect()->route('login')->withErrors('Your account is inactive');
            // }
        /*$currentRoute = Route::currentRouteName();
        $routesAllowed = [
            'member.terms',
            'member.termsagree',
            'member.login.destroy'
        ];
        if (!in_array($currentRoute, $routesAllowed)) {
            if( !Auth::user()->member->termsncon ) {
                return redirect()->route('member.terms')->with('error', 'Please accept terms and condition first.');
            }
        }*/

        return $next($request);
    }
}
