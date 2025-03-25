<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Course;
use App\Models\Student;
use App\Models\CourseMain;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use App\Models\StudentEnrolment;
use App\Services\StudentService;
use Illuminate\Support\Facades\DB;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Notification;
use App\Notifications\GenerateCertificateToAdmin;
use Illuminate\Support\Facades\Log;

class DailySendEmailNotification extends Command
{

    use Notifiable;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:adminnotification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for send generate course certificate notification to admin .';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info("Call Send Admin Notification Handle Function");
        $today = Carbon::today()->format('Y-m-d');
        //Course Run = Course Model - course table
        //$todaysAllCoursesRuns = Course::with('courseMain.programTypes')->whereDate('course_start_date', $today)->get();
        
        $todaysAllCoursesRuns = Course::with('courseMain.programTypes')->whereHas(
            'courseMain.programTypes', function ($query) {
                $query->where('program_types.status', '=', 1);
            }
        )->whereDate('course_start_date' , $today)->get();
        
        Log::info("Todays Course Runs Start");
        Log::info(print_r($todaysAllCoursesRuns, true));
        Log::info("Todays Course Runs End");

        $studentService = new StudentService;
        
        foreach($todaysAllCoursesRuns as $courseRun) {
            $courseData = [];
            $chkProgramType = $courseRun->courseMain->programTypes;
            
            if(!empty($chkProgramType))
            {
                foreach($chkProgramType as $programType){
                    $pId = $programType->pivot->program_type_id;
                    $totalCoursesSql = DB::table('course_main_program_type')
                    ->leftjoin('course_mains as cm', function($join) {
                        $join->on('course_main_program_type.course_main_id', '=','cm.id');
                    })
                    ->where('program_type_id', $pId)
                    ->groupBy('cm.skill_code')
                    ->get();
                    $totalCourses = $totalCoursesSql->count();
                    $courseName = $courseRun->courseMain->name;
                    $studentEnrollments = $studentService->getAllStudentsForCourseRun($courseRun->id);
                    $notifyStudents = [];
                    foreach($studentEnrollments as $enrollment) {
                        $studentId = $enrollment->student_id;
                        $currEnr = $enrollment->with('courseRun.courseMain.programTypes')->where('id',$enrollment->id)->first();
                        //$currentCourseReferenceNumber = $currEnr->courseRun->courseMain->reference_number;
                        $currentSkillCode = $currEnr->courseRun->courseMain->skill_code;
                        // $programType = $currEnr->courseRun->courseMain->programTypes->first();
                        $programType = $currEnr->courseRun->courseMain->programTypes;
                        $programType = $programType->find($pId);
                        
                        $programTypeId = $programType->pivot->program_type_id;
                        $otherAllEnrollments = StudentEnrolment::with('courseRun.courseMain.programTypes')->where(['student_id' => $studentId, 'assessment' => 'c']);
                    
                        $studentAllEnorollments = $otherAllEnrollments
                        ->whereHas('courseRun.courseMain', function ($query) use ($currentSkillCode) {
                            $query->where('course_mains.skill_code', '<>', $currentSkillCode);
                        })
                        ->whereHas('courseRun.courseMain.programTypes', function ($query) use ($programTypeId) {
                            $query->where('course_main_program_type.program_type_id', $programTypeId);
                        })->get()->groupBy('courseRun.courseMain.skill_code');
                        
                        Log::info("Student All Enorollments Count Start");
                        Log::info(print_r($studentAllEnorollments->count(), true));
                        Log::info("Student All Enorollments Count End");
                        
                        Log::info("Total Course Count Start");
                        Log::info(print_r($totalCourses, true));
                        Log::info("Total Course Count Count End");
                        
                        if($studentAllEnorollments->count() == ($totalCourses - 1))
                        {
                            $student = $enrollment->student;
                            $notifyStudents[] = array(
                                'id' => $student->id,
                                'name' => $student->name,
                                'email' => $student->email,
                                'student_nric' => $student->nric
                            );
                        }
                        
                        Log::info("Notify Students Start");
                        Log::info(print_r($notifyStudents, true));
                        Log::info("Notify Students End");
                    }
                    if(!empty($notifyStudents))
                    {
                        $courseData[] = array(
                            'course_name' => $courseName,
                            'course_start_date' => $courseRun->course_start_date,
                            'course_end_date' => $courseRun->course_end_date,
                            'students' => $notifyStudents,
                            'program_type_name' => $programType->name
                        );
                        
                        Log::info("Course Data Start");
                        Log::info(print_r($courseData, true));
                        Log::info("Course Data End");
                        
                        Notification::route('mail', getenv('ADMIN_NOTIFICATION_EMAIL'))->notify(new GenerateCertificateToAdmin($courseData));
                        
                        Log::info("Successfully sent notification to admin");
                        
                    }
                }
            }
        }
                
        $this->info('Successfully sent notification to admin.');
    }
}
