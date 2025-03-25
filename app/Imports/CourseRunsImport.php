<?php

namespace App\Imports;

use App\Models\User;
use App\Services\CourseService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;

class CourseRunsImport implements ToCollection, WithHeadingRow, SkipsOnError, SkipsOnFailure
{
    use Importable, SkipsErrors, SkipsFailures;

    protected $courseMain;

    public $smerrors = [];

    public function __construct($courseMain)
    {
        $this->courseMain = $courseMain;
    }

    /**
     * Transform a date value into a Carbon object.
     *
     * @return \Carbon\Carbon|null
    */
    public function transformDate($value, $format = 'Y-m-d')
    {
        try {
            return \Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value));
        } catch (\ErrorException $e) {
            return \Carbon\Carbon::createFromFormat($format, $value);
        }
    }

    public function transformTime($sessionTime)
    {
        $time = explode("to", $sessionTime);
        $ret['start_time'] = date('H:i', strtotime(trim($time[0]).":00"));
        $ret['end_time'] = date('H:i', strtotime(trim($time[1]).":00"));
        return $ret;
    }

    public function transformScheduleTime($sessionDate, $sessionTime)
    {
        $start = date('Y/m/d h:i A', strtotime($sessionDate." ".$sessionTime['start_time']));
        $end = date('Y/m/d h:i A', strtotime($sessionDate." ".$sessionTime['end_time']));
        return $start . " - " . $end;
    }

    public function collection(Collection $rows)
    {
        $courseRuns = [];
        // $courseSession = [];
        foreach ($rows as $k => $row)
        {
            // dd(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['registration_start_date']));
            // dd($row);
            if( empty($row['trainer']) || empty($row['course_start_date']) ) continue;
            // find course trainer with name
            $trainer = User::where('name', $row['trainer'])->userTrainer()->first();
            if( !empty($trainer) ) {
                try {
                    $courseRun = [
                        'venue_id' => $row['venue'],
                        'coursetrainers' => $trainer->id,
                        'registration_opening_date' => $this->transformDate($row['registration_start_date'])->format('Y-m-d'),
                        'registration_closing_date' => $this->transformDate($row['registration_end_date'])->format('Y-m-d'),
                        'course_start_date' => $this->transformDate($row['course_start_date'])->format('Y-m-d'),
                        'course_end_date' => $this->transformDate($row['course_end_date'])->format('Y-m-d'),
                        'intakesize' => $row['intake_size'],
                        'modeoftraining' => $row['mode_of_training'],
                    ];
                    if( !empty($row['tpgateway_id']) ) {
                        $courseRun['tpgateway_id']      = $row['tpgateway_id'];
                    }
                    if ( !array_key_exists($row['course_run_id'], $courseRuns) ) {
                        // now add this to course run table
                        $courseService = new CourseService;
                        $courseId = $courseService->importCourseRun($this->courseMain, $courseRun);
                        $courseRuns[$row['course_run_id']] = $courseId;
                    }
                    $sessionTime = $this->transformTime($row['session_time']);
                    $courseSession = [
                        'course_run' => $courseRuns[$row['course_run_id']],
                        'start_date' => $this->transformDate($row['session_date'])->format('Y-m-d'),
                        'end_date' => $this->transformDate($row['session_date'])->format('Y-m-d'),
                        'start_time' => $sessionTime['start_time'],
                        'end_time' => $sessionTime['end_time'],
                        'session_schedule' => $this->transformScheduleTime($this->transformDate($row['session_date'])->format('Y-m-d'), $sessionTime),
                        'session_mode' => $row['mode_of_training']
                    ];
                    $courseService = new CourseService;
                    $courseSessionRet = $courseService->importCourseRunSession($courseSession);
                } catch (\Exception $e) {
                    $this->smerrors[] = [
                        'message' => $e->getMessage(),
                        'column_no' => ($k + 2)
                    ];
                }
            } else {
                $this->smerrors[] = [
                    'message' => 'Trainer Not Found',
                    'column_no' => ($k + 2)
                ];
            }
        }
    }
}