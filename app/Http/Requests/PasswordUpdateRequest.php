<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PasswordUpdateRequest extends FormRequest
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
                return [
                    'old_password' => 'required',
                    'new_password' => 'required|min:6|max:30',
                    'confirm_new_password' => 'required|same:new_password',
                ];
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
            'old_password.required' => 'Old Password is required',
            'new_password.required' => 'New Password is required',
            'confirm_new_password.required' => 'Confirm Password is required',
            'confirm_new_password.same' => 'New password and confirm password does not match'
        ];
    }
}
