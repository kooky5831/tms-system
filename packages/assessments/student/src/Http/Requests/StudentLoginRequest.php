<?php

namespace Assessments\Student\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentLoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function rules(){
        
        switch($this->method()){
            
            case 'GET' : return[]; break;

            case 'POST' :
                $rule['user_id'] = 'required';
                $rule['password'] = 'required';
            return $rule;
            break;    
        }
    }

    public function message(){
        return [
        'user_id.required' => 'User Name is required',
        'password.required' => 'Password is required',
        ];
    }
}

