<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use App\Services\StudentService;

class CourseRunExport implements FromCollection,WithMapping , WithStrictNullComparison ,ShouldAutoSize,WithHeadings
{
    use Exportable;

    public $courseRuns;

    public function __construct($courseRuns)
    {
        $this->courseRuns = $courseRuns;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->courseRuns;
    }

    public function headings(): array
    {
        return [
            'Internal Course Run ID',
            'Course Run',
            'Course Title',
            'Reference',
            'Type',
            'Start Date',
            'End Date',
            'Registered',
            'Intake',
            'Slot',
            'Cancelled',
            'Trainer',
            'Registration Date',
            'Status',
            'Total Paid Students',
            'Total Pending Students'
        ];
    }

    public function map($courseRun): array
    {        
        
        $studentService = new StudentService;
        $totalPaidStudents = $totalUnpaidStudents = 0; 

        $totalPaidStudents = $studentService->getAllStudentEnrolment()->where('course_id', $courseRun->id)->where('payment_status', 3)->count();
        $totalUnpaidStudents = $studentService->getAllStudentEnrolment()->where('course_id', $courseRun->id)->where('payment_status', 1)->count();
    
        $status = "Un Published";
        switch ($courseRun->is_published) {
            case 1: $status = "Published"; break;
            case 2: $status = "Cancelled"; break;
            default: break;
        }
        return [
            $courseRun->id,
            $courseRun->tpgateway_id,
            $courseRun->name,
            $courseRun->reference_number,
            getModeOfTraining($courseRun->modeoftraining),
            $courseRun->course_start_date,
            $courseRun->course_end_date,
            $courseRun->registeredusercount,
            $courseRun->intakesize,
            $courseRun->registeredusercount."/".$courseRun->intakesize,
            $courseRun->cancelusercount,
            $courseRun->trainername,
            $courseRun->created_at->format('d-m-y'),
            $status,
            $totalPaidStudents,
            $totalUnpaidStudents,
       ];

    }
}
