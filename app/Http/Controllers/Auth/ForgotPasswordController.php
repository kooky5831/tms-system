<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    public function sendResetLinkEmail(Request $request)
    {
            $this->validate($request, ['username' => 'required'], ['username.required' => 'Please enter your username.']);

            $response = $this->broker()->sendResetLink(
                $request->only('username')
            );

            
            if ($response === Password::RESET_LINK_SENT) {
                return redirect()->back()->with('status', trans($response));
            }

            return back()->withErrors(
                ['username' => trans($response)]
            );
    }

}
