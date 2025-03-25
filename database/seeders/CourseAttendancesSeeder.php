<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;
use App\Models\Course;
use App\Models\Student;
use App\Models\StudentEnrolment;
use App\Models\StudentCourseAttendance;
use App\Services\CourseService;

class CourseAttendancesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('student_course_attendances')->truncate();
        $enrolmets = StudentEnrolment::whereNotNull('attendance')->get();
        $courseService = new CourseService;
        if(!empty($enrolmets)){
            foreach($enrolmets as $enrolmet){
                $attendanceData = json_decode($enrolmet->attendance);
                $courseRun = $courseService->getCourseByIdStudentEnrolment($enrolmet->course_id);
                foreach ($courseRun->session as $session) {
                    foreach( $attendanceData as $key => $att ){
                        $tmp = [
                            'session_id' => $session->id,
                            'student_enrolment_id' => $enrolmet->id,
                            'course_id' => $courseRun->id,
                            'is_present' => ($att->ispresent) ? $att->ispresent : 0,
                            'attendance_sync' => ($att->att_sync) ? $att->att_sync : 0,
                            'assessment_sync' => 0,
                        ];
                    }
                    StudentCourseAttendance::create($tmp);
                }                
            }
        }
    }
}
