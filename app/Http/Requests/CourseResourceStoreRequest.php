<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseResourceStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // return false;
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        switch($this->method())
        {
            case 'GET': return []; break;
            case 'POST':
                $rule['course_main_id'] = 'required';
                $rule['resource_title'] = 'required';
                $rule['resource_file']  = 'required|mimes:pdf,doc,jpeg,png,docx,xlsx,xls,zip';


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
            'course_main_id.required'   => 'Please select course',
            'resource_title.required'   => 'Resource title is required',
            'resource_file.required'    => 'Resource file is required or invalid formate',
        ];
    }
}
