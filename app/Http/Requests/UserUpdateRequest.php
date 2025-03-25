<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
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
                        'name'              => 'required',
                        'profile_avatar'    => 'mimes:jpg,jpeg,png,bmp,tiff',
                        'phone_number'      => 'required|numeric',
                        // 'timezone'          => 'required'
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
            'name.required'         => 'Name is required',
            'phone_number.required' => 'Phone number is required',
            // 'phone_number.min'      => 'Phone number should be between 10 to 12',
            'timezone.required'     => 'Please select timezone',
        ];
    }
}
