<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SMSTemplateStoreRequest extends FormRequest
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

                $rule['name']               = 'required|unique:sms_templates';
                $rule['content']            = 'required';
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
            'content.required'          => 'Content is required',
            'course_type_id.required'   => 'Please select course module',
            'reference_number.required' => 'Reference Number is required',
            'skill_code.required'       => 'Competency Code / Skill Code is required',
            'course_mode_training.required' => 'Please select mode of training',
        ];
    }
}
