<?php

namespace App\Exports;

use App\Models\Refreshers;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class EachCourseRunExport implements FromCollection, WithMapping , WithStrictNullComparison ,ShouldAutoSize,WithHeadings
{
    use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
    public $records;

    public function __construct($records){
        $this->records = $records;
    }

    public function collection()
    {
        return $this->records;
    }

    public function headings(): array
    {
        return [
            'Email Address',
            'Name',
            'Organization',
            'Phone',
            // 'NRIC',
            // 'Course',
            // 'Status',
        ];
    }

    public function map($records): array{

        /*$enrollmentText = "Not Enrolled";
        if( $records->status == 0 ) {
            $refresherData = Refreshers::where('course_id', $records->course_id)->where('student_id', $records->student_id)->where('status', Refreshers::STATUS_ACCEPTED)->first();
            if( $refresherData ){
                $enrollmentText = "Refreshers";
            } else {
                $enrollmentText = "Enrolled";
            }
        } else if( $records->status == 1 ) {
            $enrollmentText = "Enrolment Cancelled";
        }*/

        return [
            $records->student->email,
            $records->student->name,
            $records->company_name,
            $records->student->mobile_no,
            // $records->student->nric,
            // $records->courseRun->courseMain->name,
            // $enrollmentText,
        ];
    }
}
