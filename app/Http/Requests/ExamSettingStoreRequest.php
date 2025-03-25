<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExamSettingStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        switch($this->method()){
            case 'GET': return []; break;
            case 'POST':
                $rule['course_main_id'] = 'required';
                // $rule['exam_time'] = 'required';
                // $rule['exam_duration'] = 'required';
                // $rule['exam_date'] = 'required';

            return $rule;
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
            'course_main_id.required' => 'Please select main course',
            'exam_time.required'   => 'Exam time is required',
            'exam_duration.required'      => 'Exam duration is required',
            // 'exam_date.required' => 'Exam date is required',
        ];
    }
}
