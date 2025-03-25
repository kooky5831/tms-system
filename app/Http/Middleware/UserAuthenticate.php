<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        } else {  
            if(Auth::user()->hasRole('superadmin')){
                if($request->route()->getPrefix() != "/admin" ){
                    if(strpos($request->route()->getPrefix(), 'admin/') === false)
                         return redirect()->route('admin.dashboard');
                }                   
            } else if (Auth::user()->hasRole('trainer')) {
                if($request->route()->getPrefix() != "/trainer" ){
                    if(strpos($request->route()->getPrefix(), 'trainer/') === false)
                         return redirect()->route('trainer.dashboard');
                }                   
            } else if(Auth::user()->role == 'student'){
                Auth::user()->assignRole('student');
                if(Auth::user()->hasRole('student')){
                    if($request->route()->getPrefix() != "student" ){
                        if(strpos($request->route()->getPrefix(), 'student') === false)
                             return redirect()->route('student.assessment.dashboard');
                    }   
                }
            }
            return $next($request);
        }
    }
}
