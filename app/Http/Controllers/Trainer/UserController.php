<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Requests\PasswordUpdateRequest;
use App\Services\UserService;
use App\Services\CommonService;
use Illuminate\Http\Request;


class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserService $userService)
    {
        $this->middleware('auth');
        $this->userService = $userService;
    }

    public function profile(UserUpdateRequest $request)
    {
        if( $request->method() == 'POST') {
            $user = $this->userService->updateTrainerProfile($request);
            if( $user ) {
                setflashmsg(trans('msg.profileUpdated'), 1);
                return redirect()->route('trainer.profile');
            }
        }
        $timezones = CommonService::timezoneList();
        return view('trainer.profile', compact('timezones'));
    }

    public function changePassword(PasswordUpdateRequest $request)
    {
        if( $request->method() == 'POST') {
            $data = $this->userService->updatePassword($request);
            if( !$data['success'] ) {
                setflashmsg($data['message'], 0);
            } else {
                setflashmsg($data['message'], 1);
            }
            return redirect()->route('trainer.changepassword');
        }
        return view('trainer.changepassword');
    }

}
