<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VenueStoreRequest extends FormRequest
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
                $rule['floor'] = 'required';
                $rule['unit'] = 'required';
                $rule['postal_code'] = 'required|min:6';
                $rule['room'] = 'required';

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
            'floor.required'            => 'Floor is required',
            'unit.required'             => 'Unit is required',
            'postal_code.required'      => 'Postal Code is required',
            'room.required'             => 'Room is required',
            'status.required'           => 'Status is required',
        ];
    }
}
