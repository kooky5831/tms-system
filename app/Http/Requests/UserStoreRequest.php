<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
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
        return [
            'profile_avatar' => 'mimes:jpg,jpeg,png,bmp,tiff',
            'phone_number'   => 'required|numeric',
            // 'timestamp' => 'required'
        ];
    }

    /**
     * Custom message for validation
     *
     * @return array
    */
    public function messages()
    {
        return [
            'phone_number.required' => 'Phone number is required',
            'phone_number.min' => 'Phone number should be between 10 to 12',
        ];
    }
}
