<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuestionAnsStoreRequest extends FormRequest
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
        switch($this->method())
        {
            case 'GET': return []; break;
            case 'POST': 
                $rule['assessment[0][question]'] = 'required';
                $rule['assessment[0][question_weightage]'] = 'required';
                $rule['assessment[0][template_text]'] = 'required';

               return $rule;
               break;
               default: break; 
        }
    }

    public function message(){
        return [
        'assessment[0][question]' => 'All fields are required',
        'assessment[0][question_weightage]' => 'All fields are required',
        'assessment[0][template_text]' => 'All fields are required'
        ];
    }
}
