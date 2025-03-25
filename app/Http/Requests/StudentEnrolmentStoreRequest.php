<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentEnrolmentStoreRequest extends FormRequest
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
                // f2f    online
                if( $this->input('learning_mode') == "f2f" ) {
                    $rule['courses']        = 'required';
                } else {
                    $rule['online_course_id']     = 'required';
                }
                $rule['name']                       = 'required';
                $rule['nric']                       = 'required';
                $rule['email']                      = 'required|email';
                $rule['mobile_no']                  = 'required';
                $rule['dob']                        = 'required|date';
                // $rule['billing_email']              = 'required|email';
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
            'course_id.required'                            => 'Please select course date',
            'name.required'                                 => 'Fullname is required',
            'nric.required'                                 => 'NRIC or ID is required',
            'email.required'                                => 'Email is required',
            'mobile_no.required'                            => 'Mobile No is required',
            'dob.required'                                  => 'Date of Birth is required',
        ];
    }
}
