<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\CourseMain;
use App\Models\Student;
use App\Models\StudentEnrolment;
use Webfox\Xero\OauthCredentialManager;
use App\Http\Requests\StudentEnrolmentApiStoreRequest;
use App\Http\Resources\CourseRunScheduleCollection;
use App\Http\Resources\CourseRunScheduleCollectionDate;
use App\Models\ProgramType;

class HomeController extends BaseController
{
    public function getCourseSchedule(Request $request)
    {
        $today = date('Y-m-d');
        $todayTime = date('H:i:s');
        $courscode = $request->get('coursecode');
        // $courseMain = CourseMain::where('id', $courscode)
        //             ->where('course_type_id', '!=', CourseMain::MODULAR_COURSE)->first();
        // if( !$courseMain ) {
        //     return $this->sendError( 'No Course Found.' );
        // }
        $courserun = Course::with(['session','courseMain', 'courseSoftBooking'])
                        ->whereDate('registration_opening_date', '<=', $today)
                        ->whereDate('registration_closing_date', '>=', $today)
                        ->whereColumn('registeredusercount', '<' , 'intakesize')
                        ->where('is_published', 1)
                        ->where('course_main_id', $courscode)
                        ->orderBy('course_start_date')
                        ->get();
        return $this->sendResponse(new CourseRunScheduleCollection($courserun), 'Schedule list');
    }

    /* API for getting Course Schedule Dates by Course Code with Specific Date */
    public function getCourseScheduleDates(Request $request)
    {
        $today = date('Y-m-d');
        $todayTime = date('H:i:s');
        $courscode = $request->get('coursecode');
        $showdate = $request->get('date') ? $request->get('date') : $today;
        // $courseMain = CourseMain::where('reference_number', $courscode)
        //             ->where('course_type_id', '!=', CourseMain::MODULAR_COURSE)->first();
        // if( !$courseMain ) {
        //     return $this->sendError( 'No Course Found.' );
        // }
        $courserun = Course::with(['session','courseMain', 'courseSoftBooking'])
                        ->whereDate('registration_opening_date', '<=', $today)
                        ->whereDate('registration_closing_date', '>=', $showdate)
                        ->where('is_published', '!=', 2)
                        ->where('course_main_id', $courscode)
                        ->orderBy('course_start_date')
                        ->get();
        return $this->sendResponse(new CourseRunScheduleCollectionDate($courserun), 'Schedule list');
    }

    public function getCourseNameById($id)
    {
        $courserun = Course::with(['session', 'courseMain'])->where('id', $id)->first();
        if( !$courserun ) {
            return $this->sendError("No course run found");
        }
        $common = new \App\Services\CommonService;
        $data['session'] = $common->makeSessionString($courserun->session)." - ".$courserun->courseMain->name;
        return $this->sendResponse($data, 'Course Run name');
    }

    public function validateCourseEnrolment(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'courserun_id' => 'required',
            'nric' => 'required',
        ],[
            'courserun_id.required'   => 'No Course Selected',
            'nric.required'   => 'NRIC is required',
        ]);
        if ($validator->fails()) {
            $error = $validator->errors()->first();
            return $this->sendError($error);
        }
        // get student details from nric
        $nric = $request->get('nric');
        $student = Student::where('nric', $nric)->first();
        if( $student ) {
            $courserunId = $request->get('courserun_id');
            // check for course run
            $enrollment = StudentEnrolment::where('student_id', $student->id)->where('course_id', $courserunId)->notCancelled()->first();
            if( $enrollment ) {
                return $this->sendError('Already enrolled to course');
            }
        }
        return $this->sendBlankResponse('No enrollment found to course');
    }

    public function studentEnrolment(StudentEnrolmentApiStoreRequest $request, OauthCredentialManager $xeroCredentials)
    {
        $studentService = new \App\Services\StudentService;
        \Log::info("student data", [$request]);
        $studentEnrolment = $studentService->addStudentEnrolmentFromApi($request, $xeroCredentials);
        \Log::info('Student enrolment done');
        \Log::info(print_r($studentEnrolment, true));
        \Log::info('Student enrolment done close function');
        //dd($studentEnrolment);
        if( $studentEnrolment ) {
            return $this->sendResponse($studentEnrolment, 'Student Enrollement Done.');
        } else {
            return $this->sendError( 'Enrolment cannot be created.' );
        }
    }

    public function addPayment(Request $request)
    {
        $paymentService = new \App\Services\PaymentService;
        $paymentData = $paymentService->addPaymentData($request);
        if( $paymentData ) {
            return $this->sendResponse($paymentData, 'Payment Data Done.');
        } else {
            return $this->sendError( 'Payment Data cannot be created.' );
        }
    }

    public function programTypes(){
        $programType = ProgramType::active()->get();
        if($programType){
            return $this->sendResponse($programType, 'Program Type Data.');
        } else {
            return $this->sendError( 'No program type found to course.' );
        }
    }

}

