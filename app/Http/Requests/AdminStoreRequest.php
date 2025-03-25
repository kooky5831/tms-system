<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminStoreRequest extends FormRequest
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch($this->method())
        {
            case 'GET': return []; break;
            case 'POST':
                $rule['name'] = 'required';
                $rule['username'] = 'required';
                $rule['phone_number'] = 'required';
                $rule['timezone'] = 'required';
                $rule['profile_avatar'] = 'mimes:jpg,jpeg,png,bmp,tiff';

                $name = \Route::currentRouteName();
                $rule['email'] = 'required|email|unique:users';
                if( $name == "admin.admin.edit" || $name == "admin.user.superadmin.edit" ) {
                    $rule['email'] = '';
                }

                return $rule;
            break;
            default: break;
        }
    }

    /**
     * Custom message for validation
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required'             => 'Name is required',
            'email.required'            => 'Email is required',
            'username.required'            => 'NRIC or ID No. is required',
            'phone_number.required'     => 'Phone number is required',
            'timezone.required'         => 'Please select timezone',
            'status.required'           => 'Status is required',
            'profile_avatar.mimes'      => 'Image type is not valid. Please upload jpeg,jpg,png or bmp entension file',
        ];
    }
}
