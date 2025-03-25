<?php

namespace App\Exports;

use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class AssessmentExport implements FromCollection, WithMapping , WithStrictNullComparison , ShouldAutoSize, WithHeadings
{
    use Exportable;

    public $assessment;

    public function __construct($assessment)
    {
        $this->assessment = $assessment;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->assessment;
    }

    public function headings(): array
    {
        return [
            'CourseRun ID',
            'CourseRun TPG ID',
            'NRIC',
            'Name',
            'Email Address',
            'Contact Number',
            'Assessment Method',
            'Assessment Outcome',
            'Date Assessed',
            'Time Assessed',
            'Marked Assessment Date/Time',
        ];
    }

    public function map($assessment): array
    {
        if(!empty($assessment->exam_time)){
            $examTime = Carbon::parse($assessment->exam_time)->format('g:i a' );
        }
        if(!is_null($assessment->updated_at)){
            $markedAssessmentDate = Carbon::parse($assessment->updated_at)->format('Y-m-d g:i a' );
        } else {
            $markedAssessmentDate = "";
        }
        return [
            $assessment->courserun_id,
            $assessment->courserun_tpg_id,
            $assessment->nric,
            $assessment->name,
            $assessment->email,
            $assessment->mobile_no,
            $assessment->assessment_name,
            $assessment->is_passed,
            $assessment->exam_date,
            $examTime,
            $markedAssessmentDate,
        ];
    }
}
