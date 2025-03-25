<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username(){
        return 'username';
    }

    public function authenticated($request , $user)
    {
        if( $user->isAdmin() ) {
            return redirect('/admin');
        }
        if( $user->isTrainer() ) {
            return redirect('/trainer');
        }
        if( $user->isStudent() ) {
            if ($user->hasAssociatedStudentData()) {
                return redirect('student/assessment/portal/');
            }
            // return redirect('/assessment/portal/');
        }
        Auth::logout();
    }
}
