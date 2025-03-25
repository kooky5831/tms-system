<?php

namespace App\Services;

use PDF;
use Auth;
use DateTime;
use Mpdf\Mpdf;  
use DateTimeZone;
use Carbon\Carbon;
use App\Models\Course;
use App\Models\States;
use App\Models\Payment;
use App\Models\Student;
use App\Models\AdminTasks;
use App\Models\CourseMain;
use App\Jobs\CreateEmailJob;
use Illuminate\Http\Request;
use App\Models\EmailTemplate;
use App\Models\CourseResource;
use App\Services\CourseService;
use App\Models\StudentEnrolment;
use App\Models\AssessmentExamCourse;
use Illuminate\Support\Facades\File;
use App\Models\TrainerQualifications;
use Illuminate\Support\Facades\Storage;
use Assessments\Student\Models\ExamAssessment;
use Assessments\Student\Models\AssessmentMainExam;
use Assessments\Student\Models\AssessmentMainCourse;

class CommonService
{

    private $successStatus = 200;

    public function __construct(){

    }

    public function successResponse($msg)
    {
        return response()->json([
            'status'    => true,
            'msg'       => $msg
        ], $this->successStatus);
    }

    public function saveUploadedFile(Request $request) {
        $file = $request->file('file');

        //Move Uploaded File
        $destinationPath = public_path('assets/docs');
        $fileName = rand(1000,9999).'_'.time().$file->getClientOriginalName();

        $data = array();
        if( $file->move($destinationPath,$fileName) ){
            $data['status'] = true;
            $data['response']['filename'] = $fileName;
            $data['message'] = 'File uploaded successfully..!';
        } else {
            $data['code'] = false;
            $data['response'] = [];
            $data['message'] = 'Something went wrong..!';
        }
        return $data;
    }

    public static function trainerQualificationsList()
    {
        return TrainerQualifications::active()->get();
    }

    public function statesList()
    {
        return States::select(['id','code','name'])->active()->get();
    }

    public function makeSessionString($sessions, $showdays = false)
    {
        $start_time = $end_time = $month = $year = $lastMonth = NULL;
        if(!empty($sessions)){            
            $groupedDates = $sessions->groupBy(function ($session) {
                return Carbon::parse($session->start_date)->format('Y-m');
            });
            $getFirstSession = $sessions->groupBy(function ($session) {
                return date('g:ia', strtotime($session->start_time));
            });
            
            $lastMonth = $groupedDates->keys()->last();
            $startTime = $getFirstSession->keys()->first();
            $year = Carbon::parse($lastMonth)->format('Y');
            
            $sessionString = $groupedDates->map(function ($sessions, $key) use ($lastMonth, $year, $startTime) {
                $firstSession = $sessions->first();
                $lastSession = $sessions->last();
                $month = Carbon::parse($firstSession->start_date)->format('M');
    
                $days = $sessions->flatMap(function ($session) {
                    $startDate = Carbon::parse($session->start_date);
                    $endDate = Carbon::parse($session->end_date);
    
                    $dates = [];
                    for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
                        $dates[] = $date->format('d');
                    }
                    return $dates;
                })->unique()->implode(', ');
    
                $start_time = date('g:ia', strtotime($startTime));
                $end_time = date('g:ia', strtotime($lastSession->end_time));
    
                $lastYearInGroup = Carbon::parse($lastSession->start_date)->format('Y');
    
                return $key === $lastMonth 
                    ? "$days $month $lastYearInGroup ($start_time - $end_time)" 
                    : "$days $month" . ($lastYearInGroup !== $year ? $lastYearInGroup : '');
            })->implode(', ');    

            return $sessionString;
              
        }
        return '-';
    }

    public function makeSessionDateTime($sessions, $showdays = false, $showtime = false)
    {
        $sessionsStr = "";
        $sessionDays = "";
        $start_time = $end_time = $month = $_2month = $year = NULL;
        $firstmonth = $secondmonth = NULL;
        $monthChanged = false;
        $prevMonthDone = false;
        
        // check if it has same date
        $dateDone = [];
        foreach ($sessions as $session) {
            $nowDate = date('d', strtotime($session->start_date));
            if( !is_null($firstmonth) ) {
                $secondmonth = date('m', strtotime($session->start_date));
                if( $secondmonth != $firstmonth ) {
                    $monthChanged = true;
                    $_2month = date('M', strtotime($session->start_date));
                }
            }
            if( is_null($start_time) ) {
                $start_time = date('ga', strtotime($session->start_time));
                $month = date('M', strtotime($session->start_date));
                $year = date('Y', strtotime($session->start_date));
                $firstmonth = date('m', strtotime($session->start_date));
            }
            if( $monthChanged && !$prevMonthDone) {
                $prevMonthDone = true;
                $tmp = rtrim($sessionsStr, ", ")." ".$month;
                if( !in_array($nowDate, $dateDone) ) {
                    array_push($dateDone, $nowDate);
                    $sessionsStr = $tmp." - ".date('d', strtotime($session->start_date)).", ";
                    $sessionDays = date('D', strtotime($session->start_date)).", ";
                }
                
            } else {
                if( !in_array($nowDate, $dateDone) ) {
                    array_push($dateDone, $nowDate);
                    $sessionsStr .= date('d', strtotime($session->start_date)).", ";
                    $sessionDays .= date('D', strtotime($session->start_date)).", ";
                }
                
                
                
            }
            $end_time = date('ga', strtotime($session->end_time));
            
        }

        $ret = rtrim($sessionsStr, ", ");
        $days = rtrim($sessionDays, ", ");
        
        if($showtime){
            return $start_time." - ".$end_time;
            // $difference = round(abs(strtotime($start_time)-strtotime($end_time)));
        }

        
        if($showdays)
        {
            if( $monthChanged ) {
                return $ret." ".$_2month." ".$year. " (".$days."), (".$start_time." - ".$end_time.")";
            }
            return $ret." ".$month." ".$year." (".$days."), (".$start_time." - ".$end_time.")";
        }
        else
        {
            if( $monthChanged ) {
                return $ret." ".$_2month." ".$year." (".$start_time." - ".$end_time.")";
            }
            return $ret." ".$month." ".$year;
        }
        
    }

    public function makeSessionScheduleInfoString($sessions)
    {
        $sessionsStr = "";
        $start_time = $end_time = $month = NULL;
        foreach ($sessions as $session) {
            if( is_null($start_time) ) {
                $start_time = date('ha', strtotime($session->start_time));
                $end_time = date('ha', strtotime($session->end_time));
                $month = date('M Y', strtotime($session->start_date));
            }
            $sessionsStr .= date('d', strtotime($session->start_date)).", ";
        }

        $ret = rtrim($sessionsStr, ", ");
        return $ret." ".$month." (".$start_time." - ".$end_time.")";
    }

    public function makeAssessmentSessionString($startDate, $endDate)
    {
        $firstmonth = date('m', strtotime($startDate));
        $secondmonth = date('m', strtotime($endDate));
        $month = date('M', strtotime($startDate));
        $year = date('Y', strtotime($startDate));
        $lastDate = date('d M Y', strtotime($endDate));
        if( $secondmonth != $firstmonth ) {
            return date('d M', strtotime($startDate))." - ".date('d M', strtotime($endDate))." ".$year." (Assessment: ".$lastDate.")";
        }
        return date('d', strtotime($startDate))." - ".date('d', strtotime($endDate))." ".$month." ".$year." (Assessment: ".$lastDate.")";
    }

    public function getAttendanceSessionHours($sessions)
    {
        $totalHours = 0;
        foreach ($sessions as $session) {
            $date1 = new DateTime($session->start_date.'T'.$session->start_time);
            $date2 = new DateTime($session->start_date.'T'.$session->end_time);

            $diff = $date2->diff($date1);

            $hours = $diff->h;
            // $hours = $hours + ($diff->days*24);
            $totalHours += $hours;
        }
        return $totalHours;
    }

    public static function timezoneList()
    {
        $timezoneIdentifiers = DateTimeZone::listIdentifiers();
        $utcTime = new DateTime('now', new DateTimeZone('UTC'));

        $tempTimezones = array();
        foreach ($timezoneIdentifiers as $timezoneIdentifier) {
            $currentTimezone = new DateTimeZone($timezoneIdentifier);

            $tempTimezones[] = array(
                'offset' => (int)$currentTimezone->getOffset($utcTime),
                'identifier' => $timezoneIdentifier
            );
        }

        // Sort the array by offset,identifier ascending
        usort($tempTimezones, function($a, $b) {
            return ($a['offset'] == $b['offset'])
                ? strcmp($a['identifier'], $b['identifier'])
                : $a['offset'] - $b['offset'];
        });

        $timezoneList = array();
        foreach ($tempTimezones as $tz) {
            $sign = ($tz['offset'] > 0) ? '+' : '-';
            $offset = gmdate('H:i', abs($tz['offset']));
            $timezoneList[$tz['identifier']] = '(UTC ' . $sign . $offset . ') ' .
                $tz['identifier'];
        }

        return $timezoneList;
    }

    // get admin Dashboard Data
    public function getAdminDashboardData()
    {
        $_today = date('Y-m-d');

        // First day of the month.
        $startDate = date('Y-m-01', strtotime($_today));
        // Last day of the month.
        $endDate = date('Y-m-t', strtotime($_today));
        $data = [
            'newStudents' => Student::whereDate('created_at', '>=', $startDate)
                                ->whereDate('created_at', '<=', $endDate)
                                ->count(),
            'totalStudents' => Student::count(),
            'totalCourses' => Course::count(),
            'totalFees' => Payment::sum('fee_amount'),
            'oldPendingTask' => AdminTasks::with(['course'])->whereDate('created_at', '<', $_today)->where('status', '!=', AdminTasks::STATUS_COMPLETED)->orderByRaw('-priority DESC')->get(),
            'todaysTasks' => AdminTasks::with(['course', 'completedByUser'])
                                ->whereDate('created_at', $_today)
                                ->whereHas('course', function($q){
                                    $q->where('courses.is_published', '!=', AdminTasks::STATUS_PENDING);
                                }),
            'newstudentsList' => StudentEnrolment::with(['courseRun','student'])->latest()->take(5)->get(),
            'newcourserunList' => Course::simplePaginate(10),
        ];
        return $data;
    }

    // get trainer Dashboard Data
    public function getTrainerDashboardData()
    {
        $_today = date('Y-m-d');
        // // First day of the month.
        $startDate = date('Y-m-01', strtotime($_today));
        // // Last day of the month.
        $endDate = date('Y-m-t', strtotime($_today));

        $data = [
            'totalExams' => AssessmentMainExam::with('assessment')->count(),
            'exams' => AssessmentMainExam::with(['assessment'])->get(),
        ];
        //old-code
        /*$data = [
            'totalExams' => Course::where('maintrainer', \Auth::id())->count(),
            'exams' => Course::with(['courseMain','maintrainerUser','examCourse'])
                                        ->whereHas('examCourse', function($query){
                                            $query->groupBy('assessment_exam_courses.course_id');
                                        })
                                        ->where('maintrainer', \Auth::id())->simplePaginate(10),
        ];*/
        return $data;
    }

    public function getListDatatableOfTrainer($request){
        $courses = AssessmentMainExam::with(['assessment', 'courseMain'])
                                        ->whereHas('courseMain', function($query){
                                            $query->whereHas('trainers', function($query){
                                                $query->where('coursemain_trainer.trainer_id', \Auth::id());
                                            });
                                        });
        return $courses;
    }

    public function getListDatatableOfAdmin(){
        $courses = AssessmentMainExam::with(['assessment', 'courseMain']);
        return $courses;
    }


    public function getListDatatableOfTrainerCourseruns($request){
        $startDate = $request->get('from');
        $endDate = $request->get('to');
        // $courses = Course::with(['courseMain','maintrainerUser','examCourse'])
        //                 ->whereHas('examCourse', function($query){
        //                     $query->groupBy('assessment_exam_courses.course_id');
        //                 })
        //                 ->where('maintrainer', \Auth::id());
        $courses = Course::with('examCourse')->where('maintrainer', \Auth::id());
        if($startDate) {
            $courses->whereDate('course_start_date', '>=', date("Y-m-d", strtotime($startDate)));
        }
        if($endDate) {
            $courses->whereDate('course_end_date', '<=', date("Y-m-d", strtotime($endDate)));
        }
        return $courses;
    }

    public function getListDatatableOfAdminAssessment($request){
        $startDate = $request->get('from');
        $endDate = $request->get('to');
        $today = Carbon::today()->format('Y-m-d');
        // $courses = Course::with(['courseMain','maintrainerUser','examCourse'])
        //                 ->whereHas('examCourse', function($query){
        //                     $query->groupBy('assessment_exam_courses.course_id');
        //                 });
        $courses = Course::select('courses.*', 'tms_exam_assement_course_runs.is_assigned') // Select columns from the courses table
                            ->with('courseMain')
                            ->join('tms_exam_course_mains','tms_exam_course_mains.course_main_id','=','courses.course_main_id')
                            ->leftjoin('tms_exam_assement_course_runs','tms_exam_assement_course_runs.course_run_id','=','courses.id')
                            // ->where('courses.course_end_date', '>=', $today)    
                            ->groupBy('courses.id');
        if($startDate) {
            $courses->whereDate('course_start_date', '>=', date("Y-m-d", strtotime($startDate)));
        }
        if($endDate) {
            $courses->whereDate('course_end_date', '<=', date("Y-m-d", strtotime($endDate)));
        }
        return $courses;
    }

    public function generateCommonCertificate($id, $request, $function){
        $courseSevice = new CourseService;
        $adminTasksService = new AdminTasksService;
        if($function == "generateCertificateForAll"){
            $result = $courseSevice->getCourseByIdStudentEnrolment($id);
            $courseMain = $result->courseMain;
            if( $result->courseActiveEnrolments->isEmpty() ) {
                setflashmsg("No Student Enrolled in course", 0);
                return false;
            }
            if( empty($courseMain->certificate_file) ) {
                setflashmsg("No Certificate Found for course", 0);
                return false;
            }
            $certificate = Storage::path(config('uploadpath.blank_certificate')) . $courseMain->certificate_file;
            $zip_file = $result->tpgateway_id . "_courserun.zip";
            if( file_exists($certificate) ) {
                $certificateFolder = Storage::path(config('uploadpath.course_certificate')) . $result->id;
                // create zip and make it force download it
                $zip = new \ZipArchive();
                $zip->open(Storage::path($zip_file), \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
                foreach( $result->courseActiveEnrolments as $enrolment) {
                    //image processing 
                    $img = $this->imageProcessing($enrolment, $function, $result);
                    
                    $fileName = str_replace(' ','_',str_replace('/','-', $enrolment->student->name))."_".time().".png";
                    $fileNamePdf = str_replace(' ','_',str_replace('/','-', ucwords($enrolment->student->name)))."_".time().".pdf";

                    
                    $fullFilePath = $certificateFolder."/".$fileName;
                    imagepng($img, $fullFilePath);
                    unlink(Storage::path(config('uploadpath.blank_certificate')) . 'test.png');
                    
                    //convert Image to PDF
                    list($imageWidth, $imageHeight) = getimagesize($fullFilePath);
                    $orientation = $imageWidth > $imageHeight ? 'L' : 'P';
                    $pageWidth = $orientation === 'L' ? $imageWidth : $imageHeight;
                    $pageHeight = $orientation === 'L' ? $imageHeight : $imageWidth;
                    $pdf = new Mpdf();                    
                    $pdf->AddPage($orientation);
                    $pdf->Image($fullFilePath, 0, 0, 298, 210, 'jpg', '', true, false);
                    $fullFilePathPdf = $certificateFolder."/".$fileNamePdf;
                    $pdf->Output($fullFilePathPdf, 'F');
                    unlink($fullFilePath);

                    // extracting filename with substr/strlen
                    $relativePath = substr($fullFilePathPdf, strlen(Storage::path('public')) + 1);
                    $zip->addFile($fullFilePathPdf, $relativePath);
                }
                $zip->close();
                Storage::deleteDirectory(config('uploadpath.course_certificate') . $result->id);
                return $zip_file;
            }else{
                setflashmsg("No Certificate Found for course", 0);
                return false;
            }
        }else{
            $studentService = new \App\Services\StudentService;
            $students = $studentService->getStudentByIds($request->get('students_list'));
            if(!empty($request->get('refresher_list'))){
                $refresherStudent = $studentService->getStudentByIds($request->get('refresher_list'));
                $students = $students->merge($refresherStudent);
            }
            $emailTemplateMsg = $request->get('content');
            $task = $adminTasksService->getAdminTaskById($id);

            $emailTemplate = EmailTemplate::where('slug', $task->template_slug)->first();
            $sessionString = $this->makeSessionString($task->course->session);
            $sessionTime = $this->makeSessionDateTime($task->course->session, false, true);
            foreach( $students as $student ) {
                \Log::info('student Id ==> ' . $student->id);
                \Log::info('student email ==> ' . $student->email);
                if( !empty($student->email) ) {
                    // then create message
                    $msg = $emailTemplateMsg;
                    $msg = str_ireplace("{studentname}", $student->name, $msg);
                    $msg = str_ireplace("{coursename}", $task->course->courseMain->name, $msg);
                    $msg = str_ireplace("{coursedate}", $sessionString, $msg);
                    $msg = str_ireplace("{coursetime}", $sessionTime, $msg);
                    $msg = str_ireplace("{courseSession}", $sessionString, $msg);
                    $msg = str_ireplace("{staffname}", Auth::user()->name, $msg);
                    if( $task->course->course_link ) {
                        $msg = str_ireplace("{coursemeetinglink}", $task->course->course_link, $msg);
                    }
                    if( $task->course->meeting_id ) {
                        $msg = str_ireplace("{coursemeetingId}", $task->course->meeting_id, $msg);
                    }
                    if( $task->course->meeting_pwd ) {
                        $msg = str_ireplace("{coursemeetingPwd}", $task->course->meeting_pwd, $msg);
                    }
                    $certificateAttachment = null;
                    $courseId = null;
                    if (!empty($emailTemplate->is_send_certificate)) {
                        // $certificate = public_path('/').config('uploadpath.course_certificate') ."/". $task->course->courseMain->certificate_file;
                        $certificate = Storage::path(config('uploadpath.blank_certificate')) . $task->course->courseMain->certificate_file;
                        if( file_exists($certificate) ) {
                            $certificateFolder = Storage::path(config('uploadpath.course_certificate')) . $task->course->id;
                            $img = $this->imageProcessing($student, $function, $task);
                            // $fileName = $student->nric.".jpg";
                            $fileName = str_replace(' ','_',$student->name)."_".time().".png";
                            $fileNamePdf = str_replace(' ','_',ucwords($student->name)."_".time()).".pdf";
                            $fullFilePath = $certificateFolder."/".$fileName;
                            // $img->save($fullFilePath);
                            imagepng($img, $fullFilePath);
                            unlink(Storage::path(config('uploadpath.blank_certificate')) . 'test.png');

                            //convert Image to PDF
                            list($imageWidth, $imageHeight) = getimagesize($fullFilePath);
                            $orientation = $imageWidth > $imageHeight ? 'L' : 'P';
                            $pageWidth = $orientation === 'L' ? $imageWidth : $imageHeight;
                            $pageHeight = $orientation === 'L' ? $imageHeight : $imageWidth;
                            $pdf = new Mpdf();                    
                            $pdf->AddPage($orientation);
                            $pdf->Image($fullFilePath, 0, 0, 298, 210, 'jpg', '', true, false);
                            $fullFilePathPdf = $certificateFolder . "/" . $fileNamePdf;
                            $pdf->Output($fullFilePathPdf, 'F');
                            unlink($fullFilePath);

                            $certificateAttachment = substr($fullFilePathPdf, strlen(Storage::path('public')) + 1);
                            $courseId = $task->course->id;
                        }
                    }
                }
                CreateEmailJob::dispatch($student->email, $msg, "student", $certificateAttachment, $courseId);
            }
        }
    }

    public function imageProcessing($studentData, $function, $result){
        // $certificate = public_path('/') . config('uploadpath.course_certificate') . "/";
        $certificate = Storage::path(config('uploadpath.blank_certificate'));
        $certificateFolder = Storage::path(config('uploadpath.course_certificate'));
        
        if($function == "generateCertificateForAll"){
            $certificate = $certificate.$result->courseMain->certificate_file;
            $certificateFolder = $certificateFolder.$result->id; 
            $cords = json_decode($result->courseMain->cert_cordinates);
            $nameCords = $dateCords = NULL;
            if( !is_array($cords) ) {
                setflashmsg("No Certificate Found for course", 0);
                return redirect()->back();
            }
            foreach( $cords as $cord ) {
                if( $cord->label == "studentname" ) {
                    $nameCords = $cord;
                }
                if( $cord->label == "certificatedate" ) {
                    $dateCords = $cord;
                }
            }
            $studentName = str_replace('/', '-', strtoupper($studentData->student->name));
            $courseEnddate =  date('d M Y', strtotime($result->course_end_date));
            
            //CoreFunctions for 
            $temp = Storage::path(config('uploadpath.blank_certificate'));           
            $newwidth = 1400;
            $newheight = 1000;
            if(substr($certificate,-3) == "png"){
                $img = imagecreatefrompng($certificate);
                list($width, $height) = getimagesize($certificate);
                $thumb = imagecreatetruecolor($newwidth, $newheight);
                $source = imagecreatefrompng($certificate);
                imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
                imagepng($thumb, $temp . $result->courseMain->certificate_file);
                $img = imagecreatefrompng($temp . $result->courseMain->certificate_file);
            }else{
                $img = imagecreatefromjpeg($certificate);
                list($width, $height) = getimagesize($certificate);
                $thumb = imagecreatetruecolor($newwidth, $newheight);
                $source = imagecreatefromjpeg($certificate);
                imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
                imagejpeg($thumb, $temp . $result->courseMain->certificate_file);
                $img = imagecreatefromjpeg($temp . $result->courseMain->certificate_file); 
            }
        }else{
            $certificate = $certificate . $result->course->courseMain->certificate_file;
            $certificateFolder = $certificateFolder.$result->course->id;
            $cords = json_decode($result->course->courseMain->cert_cordinates);
            $nameCords = $dateCords = NULL;
            if( !is_array($cords) ) {
                setflashmsg("No Certificate Found for course", 0);
                return redirect()->back();
            }
            foreach( $cords as $cord ) {
                if( $cord->label == "studentname" ) {
                    $nameCords = $cord;
                }
                if( $cord->label == "certificatedate" ) {
                    $dateCords = $cord;
                }
            }

            $studentName = strtoupper($studentData->name);
            $courseEnddate =  date('d M Y', strtotime($result->course->course_end_date));
 
            $temp = Storage::path(config('uploadpath.blank_certificate'));           
            $newwidth = 1400;
            $newheight = 1000;
            if(substr($certificate,-3) == "png"){
                $img = imagecreatefrompng($certificate);
                list($width, $height) = getimagesize($certificate);
                $thumb = imagecreatetruecolor($newwidth, $newheight);
                $source = imagecreatefrompng($certificate);
                imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
                imagepng($thumb, $temp . $result->course->courseMain->certificate_file);
                $img = imagecreatefrompng($temp . $result->course->courseMain->certificate_file);
            }else{
                $img = imagecreatefromjpeg($certificate);
                list($width, $height) = getimagesize($certificate);
                $thumb = imagecreatetruecolor($newwidth, $newheight);
                $source = imagecreatefromjpeg($certificate);
                imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
                imagejpeg($thumb, $temp . $result->course->courseMain->certificate_file);
                $img = imagecreatefromjpeg($temp . $result->course->courseMain->certificate_file); 
            }
        }
        
        //Text Center using default GD library for Name
        $bbox = imagettfbbox($nameCords->font_size, 0, base_path('resources/fonts/Lato-Bold.ttf'), $studentName);
        $textWidth = $bbox[2] - $bbox[0];
        $offsetX = abs($bbox[0]);
        $centerX = $nameCords->left + ($nameCords->width / 2) - ($textWidth / 2) - $offsetX;
        $color = imagecolorallocate($img, 28, 58, 90);
        imagettftext($img, $nameCords->font_size, 0, $centerX, $nameCords->aCoords->br->y, $color, base_path('resources/fonts/Lato-Bold.ttf'), $studentName);

        //Text Center using default GD library for Date
        $bbox = imagettfbbox($dateCords->font_size, 0, base_path('resources/fonts/Lato-Regular.ttf'), $courseEnddate);
        $textWidth = $bbox[2] - $bbox[0];
        $offsetX = abs($bbox[0]);
        $centerX = $dateCords->left + ($dateCords->width / 2) - ($textWidth / 2) - $offsetX;
        $color = imagecolorallocate($img, 28, 58, 90);
        imagettftext($img, $dateCords->font_size, 0, $centerX, $dateCords->aCoords->br->y, $color, base_path('resources/fonts/Lato-Regular.ttf'), $courseEnddate);
        
        
        if( !File::exists($certificateFolder) ) {
            File::makeDirectory($certificateFolder, 0755, true, true);
        }
        imagepng($img, Storage::path(config('uploadpath.blank_certificate')).'test.png');
        return $img;
    }

    public function getPdfdata($invoiceData, $comapanyData, $invoiceSettings = null){
        $invoiceLineItems = json_decode($invoiceData->line_items, true);
        $pdfData = [];
        foreach($invoiceLineItems as $key => $value){
            $pdfData['itemlist'][] = [
                "line_item_id"  => "",
                    "description"   => $value['description'],
                    "quantity"      => $value['quantity'],
                    "unit_amount"   => $value['unit_amount'],
                    "tax_amount"    => $value['tax_amount'],
                    "line_amount"   => $value['line_amount'],
            ];
        }
        $pdfData['comapany'] = $comapanyData;
        $pdfData['amounts'] = [
            "sub_total" => $invoiceData->sub_total, 
            "total_tax" => $invoiceData->tax, 
            "invoice_total_sgd" => ($invoiceData->sub_total + $invoiceData->tax), 
            "total_net_payment_sgd" => $invoiceData->amount_paid, 
            "amount_sgd" => $invoiceData->amount_due
        ];
        $pdfData['dates'] = [
            "invoice_date" => $invoiceData->invoice_date,
            "due_date"  => $invoiceData->due_date,
        ];
        $pdfData['invoice_data'] = [
            "invoice_number" => $invoiceData->invoice_number,
            "reference" => $invoiceData->invoice_name,
            "student_enroll_id" => $invoiceData->student_enroll_id,
            "invoice_id"    =>  $invoiceData->id,
            "xero_invoice_id" => $invoiceData->xero_invoice_id,
            "xero_sync" => $invoiceData->xero_sync,
        ];
        if(!empty($invoiceSettings)){
            $getSetting = []; 
            foreach($invoiceSettings as $key => $value){
                $pdfData['invoice_setting'][$value->name] = $value->val;
            }  
        }
        return $pdfData;
    }

    public function storeCourseResource($resourceData){
        $fileName = "";
        $courseMainIds = $resourceData->get('course_main_id');
        
        if($resourceData->hasFile('resource_file')){
            
            $courseResource = new CourseResource;
            $courseResource->resource_title =  $resourceData->get('resource_title');
            $courseResource->created_by     =  Auth::Id();
            $courseResource->updated_by     =  Auth::Id();
            $courseResource->save();

            $uploadedFile = $resourceData->file('resource_file'); 
            $path = 'course-resources';
            
            // Use storage_path() to get the absolute path
            $absolutePath = storage_path('app/public/' . $path);
            
            if (!File::exists($absolutePath)) {
                // Create the directory with recursive flag
                File::makeDirectory($absolutePath, 0755, true, true);
            }
            
            $fileName = $courseResource->id . '_' . time() . '.' . $uploadedFile->getClientOriginalExtension();
            
            Storage::disk('public_course_resources')->putFileAs(
                $path,
                $uploadedFile, 
                $fileName
            );

            $courseResource->resource_file  =  $fileName;
            $courseResource->save();
        }

        
        foreach($courseMainIds as $courseId){
            $courseMainResource =  $courseResource->courseMain()->attach($courseId);
        }

        if($courseResource){
            return true;
        } else {
            return false;
        }
    }

    public function getAllCourseResources($id = null){
        if($id){
            // $getResources = CourseResource::with('courseMain')
            //                 ->whereHas('courseMain', function($query) use ($id) { 
            //                     $query->with('courseRun')
            //                         ->whereHas('courseRun', function($query) use ($id){
            //                             $query->where('courses.maintrainer', $id);
            //                         });
            //                 })
            //                 // ->groupBy('course_resources.course_main_id')
            //                 ->get();
            $getResources = CourseResource::select('course_mains.name',
                                                   'course_resources.resource_title', 
                                                   'course_resources.resource_file', 
                                                   'course_resources_coursemains.course_main_id',
                                                   'course_resources.id')
                                            ->join('course_resources_coursemains', function($join){
                                                $join->on('course_resources.id', '=', 'course_resources_coursemains.course_resource_id');
                                            })
                                            ->join('coursemain_trainer', function($join) use($id) {
                                                $join->on('course_resources_coursemains.course_main_id', '=', 'coursemain_trainer.coursemain_id')
                                                    ->where('coursemain_trainer.trainer_id' , $id);
                                            })
                                            ->join('course_mains', function($join){
                                                $join->on('course_resources_coursemains.course_main_id', '=', 'course_mains.id');
                                            })
                                            ->groupBy('course_resources_coursemains.course_main_id')
                                            ->get();
        } else {
            $getResources = CourseResource::select('course_mains.name',
                                                   'course_resources.resource_title', 
                                                   'course_resources.resource_file', 
                                                   'course_resources_coursemains.course_main_id',
                                                   'course_resources.id')
                                            ->join('course_resources_coursemains', function($join){
                                                $join->on('course_resources.id', '=', 'course_resources_coursemains.course_resource_id');
                                            })
                                            ->join('course_mains', function($join){
                                                $join->on('course_resources_coursemains.course_main_id', '=', 'course_mains.id');
                                            })
                                            ->groupBy('course_resources_coursemains.course_main_id')
                                            ->get();

        }
        if($getResources){
            return $getResources;
        } else {
            return false;
        }
    }

    public function getCourseResource($id){
        // $getResources = CourseResource::where('course_main_id', $id)->simplePaginate(10);
        $getResources = CourseResource::select( 'course_resources.resource_title', 
                                                'course_resources.resource_file', 
                                                'course_resources_coursemains.course_main_id',
                                                'course_resources.id')
                                            ->join('course_resources_coursemains', function($join){
                                                $join->on('course_resources.id', '=', 'course_resources_coursemains.course_resource_id');
                                            })                                           
                                            ->where('course_resources_coursemains.course_main_id', $id)
                                            ->groupBy('course_resources.id')
                                            ->simplePaginate(10);
        if($getResources){
            return $getResources;
        } else {
            return false;
        }
    }

    public function removeCourseResource($id){
        $resource = CourseResource::find($id);
        if($resource){
            $resource->courseMain()->detach();
            $resource->delete();
            return true;
        } else {
            return false;
        }
    }

    public function getResourceById($id){
        $resource = CourseResource::find($id);
        if($resource){
            return $resource;
        } else {
            return false;
        } 
    }

    public function updateCourseResource($request, $id){
        $resource = $this->getResourceById($id);
        $fileName = '';
        $courseMainId = $request->get('course_main_id');
        
        if($request->hasFile('resource_file')){
            $uploadedFile = $request->file('resource_file');
            $path = 'course-resources';

            // Use storage_path() to get the absolute path
            $absolutePath = storage_path('app/public/' . $path);

            if (!File::exists($absolutePath)) {
                // Create the directory with recursive flag
                File::makeDirectory($absolutePath, 0755, true, true);
            }


            $fileName = $id . '_' . time() . '.' . $uploadedFile->getClientOriginalExtension();

            Storage::disk('public_course_resources')->putFileAs(
                $path,
                $uploadedFile, 
                $fileName
            );
            $resource->resource_title = $request->get('resource_title');
            $resource->resource_file  = $fileName;
            $resource->updated_by     = Auth::Id();
            $resource->update();
        }

        if(!empty($courseMainId)){
            $updateCourseMain = $resource->courseMain()->sync($courseMainId);
            \Log::info('courseMain==> '. print_r($updateCourseMain, true));
        }
        
        if($resource){
            return true;
        } else {
            return false;
        }

    }

    public function getExamIdByRunId($courseMainId){
        // $examCourseRun = AssessmentExamCourse::where('course_run_id', $runId)->first();
        // $examAssessment = ExamAssessment::where('id', $examCourseRun->assessment_id)->first();
        $examCourseMain = AssessmentMainCourse::where('course_main_id', $courseMainId)->first();
        if(!empty($examCourseMain)){
            $exam = AssessmentMainExam::find($examCourseMain->exam_id);
            $getAssessmentTime = ExamAssessment::where(['exam_id' => $exam->id, 'date_option' => 1])->pluck('assessment_time')->toArray();

            if(empty($getAssessmentTime)){
                $getAssessmentTime = ExamAssessment::where(['exam_id' => $exam->id, 'date_option' => 2])->pluck('assessment_time')->toArray();
            }

            $time = array_map(function ($getAssessmentTime) {
                return Carbon::createFromFormat('H:i:s', $getAssessmentTime);
            }, $getAssessmentTime);

            $earliestTime = min($time);

            return $earliestTime->toTimeString();
        }
        return false;
    }
}
