<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class CourseRunTraineeExport implements FromCollection, WithMapping, WithStrictNullComparison, ShouldAutoSize, WithHeadings
{
    use Exportable;

    public $courseRun;
    public $students;

    public function __construct($courseRun, $students)
    {
        $this->courseRun = $courseRun;
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
            'Name',
            'NRIC',
            'Email',
            'Phone Number',
            'Company Name',
            'Payment Mode',
            'Remarks',
            'Payment Status',
            'Start Date',
            'End Date'
        ];
    }

    public function map($students): array
    {
        $paymentMode = "-";
        if( !is_null($students->payment_mode_company) ) {
            $paymentMode = $students->payment_mode_company;
        } elseif( !is_null($students->payment_mode_individual) ) {
            $paymentMode = $students->payment_mode_individual;
        } elseif( !is_null($students->other_paying_by) ) {
            $paymentMode = $students->other_paying_by;
        }
        return [
            $students->name,
            $students->nric,
            $students->email,
            $students->mobile_no,
            $students->company_name,
            $paymentMode,
            $students->remarks,
            "$".$students->amount_paid."/$".$students->amount,
            $this->courseRun->course_start_date,
            $this->courseRun->course_end_date,
       ];

    }
}
