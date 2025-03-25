<?php

namespace App\Exports;

use App\Models\Refreshers;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;


class RefresherReportExport implements FromCollection, WithMapping , WithStrictNullComparison , ShouldAutoSize, WithHeadings
{
    use Exportable;

    public $refresher;

    public function __construct($refresher)
    {
        $this->refresher = $refresher;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->refresher;
    }

    public function headings(): array
    {
        return [
            'Course Name',
            'TPG Course Run ID',
            'Internal Course Run ID',
            'Student Name',
            'Student NRIC',
            'Student Email',
            'Trainer',
            'Notes',
            'Course Start Date',
            'Course End Date',
            'Status',
        ];
    }

    public function map($refresher): array
    {
        if( $refresher->status == Refreshers::STATUS_PENDING) {
            $refresherStatus = "Pending";
        }else if( $refresher->status == Refreshers::STATUS_ACCEPTED ) {
            $refresherStatus = "Accepted";
        } else if( $refresher->status == Refreshers::STATUS_CANCELLED ) {
            $refresherStatus = "Cancelled";
        }
        return [ 
           $refresher->course->courseMain->name,
           $refresher->course->tpgateway_id,
           $refresher->course->id,
           $refresher->student->name,
           $refresher->student->nric,
           $refresher->student->email,
           $refresher->course->maintrainerUser->name,
           $refresher->notes,
           $refresher->course->course_start_date,
           $refresher->course->course_end_date,
           $refresherStatus
        ];
    }

}
