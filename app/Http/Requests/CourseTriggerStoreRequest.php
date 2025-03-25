<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseTriggerStoreRequest extends FormRequest
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
                $rule['triggerTitle']   = 'required';
                $rule['event_when']     = 'required';
                $rule['event_type']     = 'required';
                $rule['priority']       = 'integer|between:1,100|nullable';
                //
                if( $this->input('event_when') == 1 ) {
                    $rule['no_of_days']   = 'required';
                } else if( $this->input('event_when') == 2 ) {
                    $rule['date_in_month']   = 'required';
                } else if( $this->input('event_when') == 3 ) {
                    $rule['day_of_week']   = 'required';
                }
                // validation for event type
                if( $this->input('event_type') == 1 ) {
                    $rule['coursemain']     = 'required';
                    $rule['template_name']   = 'required';
                } else if( $this->input('event_type') == 2 ) {
                    $rule['coursemain']     = 'required';
                    $rule['sms_template']  = 'required';
                } else if( $this->input('event_type') == 3 ) {
                    $rule['task_text']  = 'required';
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
            'triggerTitle.required'      => 'Title is required',
            'coursemain.required'        => 'Please select atleast one course',
            'event_when.required'        => 'Please select when event should trigger',
            'event_type.required'        => 'Please select event type',
            'template_name.required'     => 'Please select Email Template',
            'sms_template.required'      => 'Please select SMS Template',
        ];
    }
}
