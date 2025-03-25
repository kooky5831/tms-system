<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrainerStoreRequest extends FormRequest
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
                $rule['phone_number'] = 'required';
                $rule['id_number'] = 'required';
                $rule['timezone'] = 'required';
                $rule['experience'] = 'required';
                $rule['linkedInURL'] = 'required';
                // $rule['type'] = 'required';
                $rule['domainArea'] = 'required';
                $rule['profile_avatar'] = 'mimes:jpg,png,gif,pdf';
                $rule['trainer_signature'] = 'mimes:jpg,jpeg,png,bmp,tiff';

                $name = \Route::currentRouteName();
                if( $name != "admin.trainer.edit" ) {
                    $rule['email'] = 'required|email|unique:users';
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
            'phone_number.required'     => 'Phone number is required',
            'id_number.required'        => 'NRIC or ID No. is required',
            'experience.required'       => 'Experience is required',
            'linkedInURL.required'      => 'LinkedIn URL is required',
            'type.required'             => 'Please select type',
            'domainArea.required'       => 'Domain Area of Practice is required',
            'timezone.required'         => 'Please select timezone',
            'status.required'           => 'Status is required',
            'profile_avatar.mimes'      => 'Image type is not valid. Please upload jpg, png, gif or pdf entension file',
            'trainer_signature.mimes'      => 'Image type is not valid. Please upload jpeg,jpg,png or bmp entension file',
        ];
    }
}
