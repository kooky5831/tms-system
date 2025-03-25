<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseStoreRequest extends FormRequest
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

                $rule['course_main_id']             = 'required';
                $rule['registration_opening_date']  = 'required|date';
                $rule['registration_closing_date']  = 'required|date|after_or_equal:registration_opening_date';
                $rule['registration_closing_time']  = 'required';
                $rule['course_start_date']          = 'required|date';
                $rule['course_end_date']            = 'required|date|after_or_equal:course_start_date';
                // $rule['schinfotype_code']           = 'required';
                // $rule['schinfotype_desc']           = 'required';
                // $rule['sch_info']                   = 'required';
                $rule['modeoftraining']             = 'required';
                $rule['minintakesize']              = 'required';
                $rule['intakesize']                 = 'required';
                $rule['coursevacancy_code']         = 'required';
                // $rule['coursevacancy_desc']         = 'required';

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
            'course_main_id.required'                       => 'Please select course',
            'registration_opening_date.required'            => 'Registration opening date is required',
            'registration_closing_date.required'            => 'Registration closing date is required',
            'course_start_date.required'                    => 'Course start date is required',
            'course_end_date.required'                      => 'Course end date is required',
            'schinfotype_code.required'                     => 'Schedule infotype code is required',
            'schinfotype_desc.required'                     => 'Schedule infotype Description is required',
            'sch_info.required'                             => 'Schedule Info is required',
            'modeoftraining.required'                       => 'Mode of training is required',
            'minintakesize.required'                        => 'Minimum Intake is required',
            'intakesize.required'                           => 'Maximum Intake is required',
            'coursevacancy_code.required'                   => 'Course vacancy code is required',
            'coursevacancy_desc.required'                   => 'Course vacancy description is required',
        ];
    }
}
