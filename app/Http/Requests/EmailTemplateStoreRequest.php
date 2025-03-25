<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmailTemplateStoreRequest extends FormRequest
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

                $rule['subject']               = 'required|unique:email_templates';
                $rule['template_text']         = 'required';
                $rule['template_for']          = 'required';
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
            'subject.required'                => 'Subject is required',
            'template_text.required'          => 'Body is required',
            'template_for.required'           => 'Please select Trigger Type'
        ];
    }
}
