<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProgramTypeStoreRequest extends FormRequest
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

                $rule['name']               = 'required';
                if($this->has('is_discount')) {
                    $rule['discount_percentage']     = 'required';
                }
                if($this->has('is_application_fee')) {
                    $rule['application_fee']     = 'required';
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
            'discount_percentage.required'             => 'Discount percentage is required',
            'application_fee.required'             => 'Application fee is required',
        ];
    }
}
