<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WaitingListStoreRequest extends FormRequest
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
                $rule['course_id']          = 'required';
                // $rule['*.name']             = 'required|string';
                // $rule['*.nric']             = 'required|string|distinct';
                // $rule['*.email']            = 'required|email|distinct';
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
            'course_id.required'        => 'Course is required',
            'session_id.required'       => 'Session is required',
            'name.required'             => 'Name is required',
            'nric.required'             => 'NRIC is required',
            'email.required'            => 'Email is required',
            'number_of_seats.required'  => 'Number Of Seats is required',
            'deadline_date.required'    => 'Deadline Date is required',
        ];
    }
}
