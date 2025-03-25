<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class CourseSignupsCountExport implements FromCollection,WithMapping , WithStrictNullComparison ,ShouldAutoSize,WithHeadings
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
            'Month',
            'Course Name',
            'Count',
        ];
    }

    public function map($student): array
    {
        return [
            $student->new_date,
            $student->coursemainname,
            $student->data,
       ];

    }
}
