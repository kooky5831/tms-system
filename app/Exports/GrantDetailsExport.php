<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class GrantDetailsExport implements FromCollection,WithMapping , WithStrictNullComparison ,ShouldAutoSize,WithHeadings
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
            'Enrolment Ref. No.',
            'Student Name',
            'NRIC',
            'Email',
            'Enrolment Status',
            'Course Name',
            'Course Start',
            'End Date',
            'Grant Ref. No.',
            'Grant Status',
            'Funding Scheme',
            'Funding Component',
            'Amount Estimated',
            'Amount Paid',
            'Amount Recovery',
            'Updated/ Disbursed Date'
        ];
    }

    public function map($student): array
    {
        $enrollmentText = "Not Enrolled";
        if( $student->enr_status == 0 ) {
            $enrollmentText = "Enrolled";
        } else if( $student->enr_status == 1 ) {
            $enrollmentText = "Enrolment Cancelled";
        }
        else if( $student->enr_status == 3 ) {
            $enrollmentText = "Not Enrolled";
        }
        else if( $student->enr_status == 2 ) {
            $enrollmentText = "Holding List";
        }
        
        return [
            $student->enr_ref_no,
            $student->name,
            $student->nric,
            $student->email,
            $enrollmentText,
            $student->coursemainname,
            $student->course_start_date ,
            $student->course_end_date,
            $student->grant_refno,
            $student->grant_status,
            $student->scheme_code ." - ".$student->scheme_description,
            $student->component_code ." - ".$student->component_description,
            $student->amount_estimated,
            $student->amount_paid,
            $student->amount_recovery,
            $student->disbursement_date,
       ];

    }
}
