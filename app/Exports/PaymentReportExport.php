<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class PaymentReportExport implements FromCollection, WithMapping, WithStrictNullComparison, ShouldAutoSize, WithHeadings
{
    use Exportable;

    public $students;
    
    public function __construct($students)
    {
        $this->students = $students;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->students;
    }

    public function headings(): array
    {
        return [
            'Student Name',
            'NRIC',
            'Nationality',
            'Phone No',
            'Email',
            'Course run id',
            'Course Name',
            'Course Start date',
            'Course End date',
            'Sponsord by company',
            'Company Name',
            'Company UEN',
            'Comapany contact person',
            'Comapany contact email',
            'Comapany contact number',
            'Billing email',
            'Remarks',
            'Payment Remarks',
            'payment mode',
            'Invoice #',
            'Due date',
            'Amount Remaining',
            'Payment Status',
            'Status',  
        ];
    }
    
    public function map($student): array
    {
        $enrollmentText = enrolledStatus($student->status);
        
        $paymentText = getPaymentStatus($student->payment_status);
        
        $paymentMode = "-";
        if( !is_null($student->payment_mode_company) ) {
            $paymentMode = $student->payment_mode_company;
        } elseif( !is_null($student->payment_mode_individual) ) {
            $paymentMode = $student->payment_mode_individual;
        } elseif( !is_null($student->other_paying_by) ) {
            $paymentMode = $student->other_paying_by;
        }

        
        return [
            $student->student_name,
            $student->nric,
            $student->nationality,
            $student->mobile_no,
            $student->email,
            $student->course_id,
            $student->coursemainname,
            $student->courseRun->course_start_date,
            $student->courseRun->course_end_date,
            $student->sponsored_by_company,
            $student->company_name,
            $student->company_uen,
            $student->company_contact_person,
            $student->company_contact_person_email,
            $student->company_contact_person_number,
            $student->billing_email,
            $student->remarks,
            $student->payment_remark,
            $paymentMode,
            $student->xero_invoice_number,
            $student->due_date,
            $student->amount - $student->amount_paid,
            $paymentText,
            $enrollmentText,
       ];
    }
}