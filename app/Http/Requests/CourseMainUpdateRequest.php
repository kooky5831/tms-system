<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseMainUpdateRequest extends FormRequest
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
                $rule['name'] = 'required';
                $rule['course_type_id']     = 'required';
                $rule['course_type']        = 'required';
                // $rule['reference_number']   = 'required|unique:course_mains,reference_number,'.$this->route('id');
                $rule['reference_number'] = 'required';
                if( $this->input('course_type') != "2" ) {
                    $rule['skill_code']   = 'required';
                }
                if( $this->input('course_type_id') !== "2" ) {
                    $rule['course_mode_training']  = 'required';
                }
                if($this->has('is_discount')) {
                    $rule['discount_amount'] = 'required';
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
            'course_type_id.required'   => 'Please select course module',
            'course_type.required'      => 'Please select course type',
            'reference_number.required' => 'Reference Number is required',
            'skill_code.required'       => 'Competency Code / Skill Code is required',
            'course_mode_training.required' => 'Please select mode of training',
            'discount_amount.required' => 'Discount amount is required',
        ];
    }
}
