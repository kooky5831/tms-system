<?php

namespace Assessments\Student\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Assessments\Student\Services\AssessmentExamService;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;

class StudentLoginController
{
    protected $tokenService;
    
    public function __construct(AssessmentExamService $tokenService){
        $this->tokenService = $tokenService;
    }
    
    public function login(Request $request){
        $request->validate([
            'user_id' => 'required',
            'password' => 'required',
        ]);
        $credentials = $request->only('user_id', 'password');

        if (Auth::guard('webassessment_students')->attempt($request->only(['user_id', 'password']))) {
            $userId = Crypt::encryptString($request->user_id);
            session(['encryptId' => $userId, 'userId' => $request->user_id]);
            return redirect()->route('assessment.dashboard', $userId);
        } else {

        }
    }

    public function logout(){
        Auth::logout();
    }
}