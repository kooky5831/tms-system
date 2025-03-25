<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentStoreRequest extends FormRequest
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
                $rule['student_enrolments_id']  = 'required';
                $rule['payment_mode']           = 'required';
                $rule['payment_date']           = 'required';
                $rule['fee_amount']             = 'required|gt:0';
                // $rule['bankaccount_id']         = 'required';
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
            'student_enrolments_id.required'    => 'Student Enrolment Id is required',
            'payment_mode.required'             => 'Payment Mode is required',
            'payment_date.required'             => 'Payment Date is required',
            'bankaccount_id.required'           => 'Please select bank account',
        ];
    }
}
