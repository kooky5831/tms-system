<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class CourseRunRefresherExport implements FromCollection, WithMapping, WithStrictNullComparison, ShouldAutoSize, WithHeadings
{
    use Exportable;

    public $courseRun;
    public $courseRefreshers;

    public function __construct($courseRun, $courseRefreshers)
    {
        $this->courseRun = $courseRun;
        $this->courseRefreshers = $courseRefreshers;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->courseRefreshers;
    }

    public function headings(): array
    {
        return [
            'Name',
            'NRIC',
            'Email',
            'Phone Number',
            'Start Date',
            'End Date'
        ];
    }

    public function map($courseRefreshers): array
    {
        return [
            $courseRefreshers->student->name,
            $courseRefreshers->student->nric,
            $courseRefreshers->student->email,
            $courseRefreshers->student->mobile_no,
            $this->courseRun->course_start_date,
            $this->courseRun->course_end_date,
       ];

    }
}
