<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class CourseRunWithIdExport implements FromCollection,WithMapping , WithStrictNullComparison ,ShouldAutoSize,WithHeadings
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
            'Id',
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
        ];
    }

    public function map($courseRun): array
    {
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
       ];

    }
}
