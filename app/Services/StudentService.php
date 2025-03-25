<?php

namespace App\Services;

use Auth;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Grant;
use App\Models\Course;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Student;
use App\Models\CourseMain;
use App\Models\Refreshers;
use App\Services\UserService;
use App\Services\XeroService;
use App\Models\CourseSessions;
use App\Models\StudentEnrolment;

use App\Models\CourseSoftBooking;
use App\Services\TPGatewayService;
use Illuminate\Support\Facades\DB;
use App\Models\XeroCourseLineItems;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Webfox\Xero\OauthCredentialManager;
use App\Services\GrantCalculationService;
use App\Http\Controllers\Admin\XeroController;

class StudentService
{
    protected $studentenrolment_model;
    protected $students_model;
    protected $grantCalculationService;
    protected $syncXeroService;
    protected $payment_model;
    protected $userService;

    public function __construct()
    {
        $this->studentenrolment_model = new StudentEnrolment;
        $this->students_model = new Student;
        $this->refresher_model = new Refreshers;
        $this->grantCalculationService = new GrantCalculationService;
        $this->payment_model = new Payment;
        $this->userService = new UserService;
    }

    public function getAllStudentEnrolment()
    {
        return $this->studentenrolment_model->with(['student']);
    }

    public function getAllRefresher($request){

        $refresher = $this->refresher_model->with(['course', 'student']);

        $coursemain = $request->get('coursemain');
        if(  !empty($coursemain) ){
            $refresher->WhereHas('course',function ($query) use ($coursemain) {
                $query->Where('courses.course_main_id', $coursemain);
            });
        }

        $status = $request->get('status');
        if(  !empty($status) ){
            if( $status == Refreshers::STATUS_PENDING ) {
                $refresher->where('refreshers.status', Refreshers::STATUS_PENDING);
            } else if( $status == Refreshers::STATUS_ACCEPTED ) {
                $refresher->where('refreshers.status', Refreshers::STATUS_ACCEPTED);
            } else if( $status == Refreshers::STATUS_CANCELLED ) {
                $refresher->where('refreshers.status', Refreshers::STATUS_CANCELLED);
            }
        }

        $course_run_id = $request->get('course_run_id');
        if( !empty($course_run_id) ){
            $refresher->WhereHas('course',function ($query) use ($course_run_id) {
                $query->Where('courses.tpgateway_id', $course_run_id);
            });
        }

        $startDate = $request->get('from');
        $endDate = $request->get('to');
        if( $startDate ) {
            $refresher->WhereHas('course',function ($query) use ($startDate) {
                $query->whereDate('courses.course_start_date', '>=', date("Y-m-d", strtotime($startDate)));
            });
        }
        if( $endDate ) {
            $refresher->WhereHas('course',function ($query) use ($endDate) {
                $query->whereDate('courses.course_end_date', '<=', date("Y-m-d", strtotime($endDate)));
            });
        }

        $trainer = $request->get('trainer');
        if(  !empty($trainer) ){
            $refresher->WhereHas('course.maintrainerUser',function ($query) use ($trainer) {
                $query->where('users.name', 'LIKE', '%'.$trainer.'%');
            });
        }

        return $refresher;

    }   
    public function getAllStudentEnrolmentWithFilter($request, $paymentReport = false)
    {
        // $s = $this->studentenrolment_model->with(['student']);
        if($paymentReport){
            $s = $this->payment_model->select('student_enrolments.*','students.name','students.nric',
                 'courses.tpgateway_id', 'courses.course_start_date', 'courses.course_end_date', 'course_mains.name as coursemainname', 'course_mains.course_type_id', 'payments.payment_mode', 'course_mains.id as course_main_id', 'student_enrolments.id AS student_enroll_id')
            ->groupBy('payments.student_enrolments_id')
            ->join('student_enrolments', 'student_enrolments.id', '=', 'payments.student_enrolments_id')
            ->join('students', 'students.id', '=', 'student_enrolments.student_id')
            ->join('courses', 'courses.id', '=', 'student_enrolments.course_id')
            ->join('course_mains', 'course_mains.id', '=', 'courses.course_main_id');
        }else{
            $s = $this->studentenrolment_model->select('student_enrolments.*','students.name as student_name','students.nric',
                'courses.tpgateway_id', 'courses.course_start_date', 'courses.course_end_date', 'course_mains.name as coursemainname', 'course_mains.course_type_id', 'students.id as student_id', 'course_mains.course_full_fees', 'student_enrolments.id AS student_enroll_id')
            ->join('students', 'students.id', '=', 'student_enrolments.student_id')
            ->join('courses', 'courses.id', '=', 'student_enrolments.course_id')
            ->join('course_mains', 'course_mains.id', '=', 'courses.course_main_id');
        }

        $thiscourseRunId = $request->get('courserun');
        $startDate = $request->get('from');
        $endDate = $request->get('to');

        $payment_status = $request->get('payment_status');
        $mincoursefee = $request->get('mincoursefee');
        $maxcoursefee = $request->get('maxcoursefee');
        $payment_status = $request->get('payment_status');
        //$maxpayment = $request->get('maxpayment');
        $mode = $request->get('course_mode_training');
        $nationality = $request->get('nationality'); 
        $company_name = $request->get('company_name');
        $company_uen = $request->get('company_uen');
        $xero_invoice_number = $request->get('xero_invoice_number');
        $sponsored_by_company = $request->get('sponsored_by_company');
        
        $minamountdue = $request->get('minamountdue');
        $maxamountdue = $request->get('maxamountdue');

        $remainingAmountFrom = $request->get('remaining_amount_from');
        $remainingAmountTo = $request->get('remaining_amount_to');
        $courseType = $request->get('course_type');
        $tpg_payment_status = $request->get('tpg_payment_status');

        if($request->has('dueDatestartDate') && !empty($request->get('dueDatestartDate'))){
            $dueStartDate = $request->get('dueDatestartDate');
        }
        if($request->has('dueDateendDate') && !empty($request->get('dueDateendDate'))){
            $dueDateendDate = $request->get('dueDateendDate');
        }
        
        if(!empty($dueStartDate) && !empty($dueDateendDate)){
            $s->whereBetween('student_enrolments.due_date', [$dueStartDate, $dueDateendDate]);
        }

        if($request->has('coursetartDate') && !empty($request->get('coursetartDate'))){
            $coursetartDate = $request->get('coursetartDate');
        }
        if($request->has('courseendDate') && !empty($request->get('courseendDate'))){
            $courseendDate = $request->get('courseendDate');
        }
        
        if(!empty($dueStartDate) && !empty($dueDateendDate)){
            $s->whereBetween('student_enrolments.due_date', [$dueStartDate, $dueDateendDate]);
        }

        $studentName = $request->get('student_name');
        if( !empty($studentName) ) {
            $s->where('students.name', 'LIKE', '%'.$studentName.'%');
        }
        
        $enrollStatus = $request->get('status');
        if( is_array($enrollStatus) ) {
            if(in_array(StudentEnrolment::REFRESHER_STATUS, $enrollStatus)){
                $s->join('refreshers' , function($query){
                    $query->on('refreshers.student_id', '=', 'students.id');
                    $query->on('refreshers.course_id', '=', 'courses.id');
                    $query->where('refreshers.status', '=', Refreshers::STATUS_ACCEPTED);
                });
            } else {
                $s->whereIn('student_enrolments.status', $enrollStatus);
            }
        } elseif(!is_array($enrollStatus) && !is_null($enrollStatus)) {
                if( $enrollStatus == StudentEnrolment::STATUS_ENROLLED ) {
                    $s->where('student_enrolments.status', StudentEnrolment::STATUS_ENROLLED);
                } else if( $enrollStatus == StudentEnrolment::STATUS_CANCELLED ) {
                    $s->where('student_enrolments.status', StudentEnrolment::STATUS_CANCELLED);
                } else if( $enrollStatus == StudentEnrolment::STATUS_HOLD ) {
                    $s->where('student_enrolments.status', StudentEnrolment::STATUS_HOLD);
                } else if( $enrollStatus == StudentEnrolment::STATUS_NOT_ENROLLED ) {
                    $s->where('student_enrolments.status', StudentEnrolment::STATUS_NOT_ENROLLED);
                }
        }


        if( $request->has('enrollment_no') && !empty($request->get('enrollment_no')) ) {
            $s->where('student_enrolments.tpgateway_refno', 'LIKE', '%'.$request->get('enrollment_no').'%');
        }

        $coursemainId = $request->get('coursemain');
        if( is_array($coursemainId) ) {
            $s->whereIn('courses.course_main_id', $coursemainId);
        } else if( $coursemainId > 0 ) {
            $s->where('courses.course_main_id', $coursemainId);
        }

        if( $startDate ) {
            $s->whereDate('courses.course_start_date', '>=', date("Y-m-d", strtotime($startDate)));
        }
        if( $endDate ) {
            $s->whereDate('courses.course_end_date', '<=', date("Y-m-d", strtotime($endDate)));
        }
        if( $thiscourseRunId > 0 ) {
            $s->where('course_id', $thiscourseRunId);
        }

        if(!empty($payment_status))
        {
            $s->where('payment_status', $payment_status);
        }

        if( isset($mincoursefee) ) {
            $s->where('student_enrolments.amount', '>=',$mincoursefee );
        }
        if( isset($maxcoursefee) ) {
            $s->where('student_enrolments.amount', '<=',$maxcoursefee );
        }

        if(!empty($mode))
        {
            $s->where('course_mains.course_mode_training', $mode);
        }

        if(!empty($nationality))
        {
            $s->where('student_enrolments.nationality', 'LIKE', '%'.$nationality.'%');
            
        }

        if(!empty($company_name))
        {
            $s->where('student_enrolments.company_name', 'LIKE', '%'.$company_name.'%');
            
        }

        if(!empty($company_uen))
        {
            $s->where('student_enrolments.company_uen', 'LIKE', '%'.$company_uen.'%');
            
        }

        if(!empty($xero_invoice_number))
        {
            $s->where('student_enrolments.xero_invoice_number',$xero_invoice_number);
            
        }

        if(!empty($sponsored_by_company))
        {
            $s->where('student_enrolments.sponsored_by_company','LIKE',$sponsored_by_company);
            
        }

        if(isset($remainingAmountFrom)){
            $s->whereRaw('(student_enrolments.amount - student_enrolments.amount_paid) >=?', [$remainingAmountFrom])->get();
        }

        if(isset($remainingAmountTo)){
            $s->whereRaw('(student_enrolments.amount - student_enrolments.amount_paid) <=?', [$remainingAmountTo])->get();
        }

        if( isset($minamountdue) ) {
            $s->whereRaw('(amount - amount_paid) >=?', [$minamountdue]);
        }
        
        if( isset($maxamountdue) ) {
            $s->whereRaw('(amount - amount_paid) <=?', [$maxamountdue]);
        }

        $course_type = $request->get('course_type');
        if( is_array($course_type) ) {
            $s->whereIn('course_mains.course_type',$course_type);
        }

        $sponsored_by_company = $request->get('sponsored_by_company');
        if( is_array($sponsored_by_company) ) {
            $s->whereIn('student_enrolments.sponsored_by_company',$sponsored_by_company);
        }

        $company_name = $request->get('company_name');
        if( !empty($company_name) ) {
            $s->where('student_enrolments.company_name', 'LIKE', '%'.$company_name.'%');
        }

        $company_uen = $request->get('company_uen');
        if( !empty($company_uen) ) {
            $s->where('student_enrolments.company_uen', 'LIKE', '%'.$company_uen.'%');
        }

        $company_contact_person = $request->get('company_contact_person');
        if( !empty($company_contact_person) ) {
            $s->where('student_enrolments.company_contact_person', 'LIKE', '%'.$company_contact_person.'%');
        }

        $company_contact_person_number = $request->get('company_contact_person_number');
        if( !empty($company_contact_person_number) ) {
            $s->where('student_enrolments.company_contact_person_number', 'LIKE', '%'.$company_contact_person_number.'%');
        }

        $company_contact_person_email = $request->get('company_contact_person_email');
        if( !empty($company_contact_person_email) ) {
            $s->where('student_enrolments.company_contact_person_email', 'LIKE', '%'.$company_contact_person_email.'%');
        }

        $billing_email = $request->get('billing_email');
        if( !empty($billing_email) ) {
            $s->where('student_enrolments.billing_email', 'LIKE', '%'.$billing_email.'%');
        }

        if( !empty($courseType) ) {
            $s->where('course_mains.course_type', '=', $courseType);
        }

        if( !empty($tpg_payment_status) ) {
            if( $tpg_payment_status == StudentEnrolment::TPG_STATUS_PENDING ) {
                $s->where('student_enrolments.payment_tpg_status', StudentEnrolment::TPG_STATUS_PENDING);
            } else if( $tpg_payment_status == StudentEnrolment::TPG_STATUS_PARTIAL ) {
                $s->where('student_enrolments.payment_tpg_status', StudentEnrolment::TPG_STATUS_PARTIAL);
            } else if( $tpg_payment_status == StudentEnrolment::TPG_STATUS_FULL ) {
                $s->where('student_enrolments.payment_tpg_status', StudentEnrolment::TPG_STATUS_FULL);
            } else if( $tpg_payment_status == StudentEnrolment::TPG_STATUS_CANCELLED ) {
                $s->where('student_enrolments.payment_tpg_status', StudentEnrolment::TPG_STATUS_CANCELLED);
            }
        }

        $pesa_refrerance_number = $request->get('pesa_refrerance_number');
        if( !empty($pesa_refrerance_number) ) {
            $s->where('student_enrolments.pesa_refrerance_number', 'LIKE', '%'.$pesa_refrerance_number.'%');
        }
        $skillfuture_credit = $request->get('skillfuture_credit');
        if( !empty($skillfuture_credit) ) {
            $s->where('student_enrolments.skillfuture_credit', 'LIKE', '%'.$skillfuture_credit.'%');
        }
        $vendor_gov = $request->get('vendor_gov');
        if( !empty($vendor_gov) ) {
            $s->where('student_enrolments.vendor_gov', 'LIKE', '%'.$vendor_gov.'%');
        }

        return $s;
    }

    public function getAllStudentEnrolmentPerMonth($request)
    {
        $s = $this->studentenrolment_model->select('course_mains.name as coursemainname', \DB::raw('count(student_enrolments.id) as `data`'), \DB::raw("DATE_FORMAT(student_enrolments.created_at, '%m-%Y') new_date"),  \DB::raw('YEAR(student_enrolments.created_at) year, MONTH(student_enrolments.created_at) month'))
        ->join('courses', 'courses.id', '=', 'student_enrolments.course_id')
        ->join('course_mains', 'course_mains.id', '=', 'courses.course_main_id');
        $coursemainId = $request->get('coursemain');
        $startDate = $request->get('from');
        $endDate = $request->get('to');
        if( is_array($coursemainId) ) {
            $s->whereIn('courses.course_main_id', $coursemainId);
        } else if( $coursemainId > 0 ) {
            $s->where('courses.course_main_id', $coursemainId);
        }

        if( $startDate ) {
            $s->whereDate('courses.course_start_date', '>=', date("Y-m-d", strtotime($startDate)));
        }
        if( $endDate ) {
            $s->whereDate('courses.course_end_date', '<=', date("Y-m-d", strtotime($endDate)));
        }
        $s->groupby('courses.course_main_id', 'year','month')->orderBy('month');
        return $s;
    }

    public function getAllStudentEnrolmentList()
    {
        return $this->studentenrolment_model->with(['courseRun'])->get();
    }

    public function getStudentEnrolmentById($id)
    {
        return $this->studentenrolment_model->find($id);
    }

    public function getStudentEnrolmentByTPGId($refNo)
    {
        return $this->studentenrolment_model->where('tpgateway_refno', $refNo)->first();
    }

    public function getStudentEnrolmentByIdWithRealtionData($id)
    {
        return $this->studentenrolment_model->where('id', $id)->with(['courseRun', 'student', 'payments', 'attendances', 'grants'])->first();
    }

    public function getStudentEnrolmentByStudentIdWithRealtionData($id)
    {
        return $this->studentenrolment_model->where('student_id', $id)->with(['courseRun', 'payments'])->get();
    }

    public function getAllStudentsForCourseRun($id, $relation = ['student', 'payments'])
    {
        return $this->studentenrolment_model->where('course_id', $id)->notCancelled()->holdListTrainee()->with($relation)->get();
    }

    public function getAllStudentIdsForCourseRun($id)
    {
        return $this->studentenrolment_model->where('course_id', $id)->notCancelled()->pluck('id');
    }

    public function getStudentEnrolmentByEntryId($id)
    {
        return $this->studentenrolment_model->where('entry_id', $id)->first();
    }

    public function hasGrantError($courseId)
    {
        return $this->studentenrolment_model->where('isGrantError', 1)->where('course_id', $courseId)->count();
    }

    public function hasEnrollmentError($courseId)
    {
        return $this->studentenrolment_model->where('status', 3)->where('course_id', $courseId)->count();
    }

    public function getAllStudents()
    {
        return $this->students_model->select();
    }

    public function getStudentById($id)
    {
        return $this->students_model->find($id);
    }

    public function getStudentByIds($ids)
    {
        return $this->students_model->whereIn('id', $ids)->get();
    }

    public function getStudentEnrollmentsByIds($ids)
    {
        return $this->studentenrolment_model->whereIn('id', $ids)->with(['student'])->get();
    }

    public function getStudentEnrollmentsByCourseRunId($courseRunId)
    {
        return $this->studentenrolment_model->select('students.name','students.nric','student_enrolments.email','student_enrolments.mobile_no','student_enrolments.dob',
            'student_enrolments.company_name','student_enrolments.remarks','student_enrolments.amount_paid',
            'student_enrolments.amount','student_enrolments.payment_mode_company',
            'student_enrolments.payment_mode_individual','student_enrolments.other_paying_by',
            'student_enrolments.xero_invoice_number')
        ->join('students', 'students.id', '=', 'student_enrolments.student_id')
        ->where('student_enrolments.course_id', $courseRunId)
        ->where('student_enrolments.status', '!=', StudentEnrolment::STATUS_CANCELLED)
        ->get();
    }

    public function updateStudentByDetails($request)
    {
        $nric = $request->get('nric');
        $student_id = $request->get('student_id');
        $password = $request->get('password');
        $student = Student::where('nric', $nric)->where('id', '!=', $student_id)->first();

        if( !empty($student->id) ) {
            return ['success' => false, 'message' => 'Nric already in used'];
        }
        $record = $this->getStudentById($student_id);
        $record->name                           = $request->get('name');
        $record->nric                           = $request->get('nric');
        $record->company_sme                    = $request->get('company_sme');
        $record->nationality                    = $request->get('nationality');
        $record->email                          = $request->get('email');
        $record->mobile_no                      = $request->get('mobile_no');
        $record->dob                            = $request->get('dob');
        $record->company_name                   = $request->get('company_name');
        $record->company_uen                    = $request->get('company_uen');
        $record->company_contact_person         = $request->get('company_contact_person');
        $record->company_contact_person_email   = $request->get('company_contact_person_email');
        $record->company_contact_person_number  = $request->get('company_contact_person_number');
        $record->billing_address                = $request->get('billing_address');
        $record->meal_restrictions              = $request->get('meal_restrictions');
        $record->meal_restrictions_type         = $request->get('meal_restrictions_type');
        $record->meal_restrictions_other        = $request->get('meal_restrictions_other');

        $record->updated_by     = Auth::Id();
        $record->save();

        $getStudentUserData = $this->userService->getUserById($record->user_id);

        if(!empty($getStudentUserData)){
            $studentPassword = Hash::make($password);
            $getStudentUserData->username = strtoupper(trim($request->get('nric')));
            $getStudentUserData->password = $studentPassword;
            $getStudentUserData->update();
        }

        if( $record ) {
            return ['success' => true, 'message' => 'Details updated successfully'];
        } else {
            return ['success' => false, 'message' => 'Details not updated'];
        }
    }

    public function checkstudentalreadyenrolled($request)
    {
        $nric = $request->get('nric');
        $student = Student::where('nric', $nric)->first();
        if( isset($student->id) ) {
            if( $request->get('learning_mode') == "f2f" ) {
                $course_id = $request->get('courses');
            } else {
                $course_id = $request->get('online_course_id');
            }
            if( is_array($course_id) ) {
                foreach( $course_id as $course ) {
                    $enrolled = StudentEnrolment::where('course_id', $course['f2f_course_id'])
                                ->where('status', StudentEnrolment::STATUS_ENROLLED)
                                ->where('student_id', $student->id)->first();
                    if( isset($enrolled->id) ) { return FALSE; }
                }
            } else {
                $enrolled = StudentEnrolment::where('course_id', $course_id)
                            ->where('status', StudentEnrolment::STATUS_ENROLLED)
                            ->where('student_id', $student->id)->first();
                if( isset($enrolled->id) ) { return FALSE; }
            }
        }
        return TRUE;
    }

    public function addStudentEnrolment($request, $xeroCredentials)
    {
        // check student is there in student table or not
        $nric = $request->get('nric');
        $student = Student::where('nric', $nric)->first();
        $studentId = null;
        $xeroServiceReq = new XeroService($xeroCredentials);
        $xeroId = NULL;
        // if( empty($student->id) || is_null($student->xero_id) ) {
        //     // add student/contact to xero account
        //     try {
        //         $resObj = $xeroServiceReq->createContactsXero(
        //             $request->get('name'),
        //             $request->get('mobile_no'),
        //             $request->get('email'));
        //         if( $resObj ) {
        //             $xeroId = $resObj->getContacts()[0]->getContactId();
        //         }
        //     } catch (Exception $e) {
        //         // log to error
        //     }
        // }
        if( isset($student->id) ) {
            $studentId = $student->id;
            // update student details
            $student->company_sme                 = $request->get('company_sme');
            $student->nationality                 = $request->get('nationality');
            $student->email                       = $request->get('email');
            $student->mobile_no                   = $request->get('mobile_no');
            $student->dob                         = $request->get('dob');
            if( $request->has('company_name') && !empty($request->get('company_name')) ) {
                $student->company_name            = $request->get('company_name');
            }
            if( $request->has('company_uen') && !empty($request->get('company_uen')) ) {
                $student->company_uen                 = $request->get('company_uen');
            }
            if( $request->has('company_contact_person') && !empty($request->get('company_contact_person')) ) {
                $student->company_contact_person      = $request->get('company_contact_person');
            }
            if( $request->has('company_contact_person_email') && !empty($request->get('company_contact_person_email')) ) {
                $student->company_contact_person_email = $request->get('company_contact_person_email');
            }
            if( $request->has('company_contact_person_number') && !empty($request->get('company_contact_person_number')) ) {
                $student->company_contact_person_number = $request->get('company_contact_person_number');
            }
            if( $request->has('billing_address') ) {
                $student->billing_address             = $request->get('billing_address');
            }
            if( is_null($student->xero_id) ) {
                $student->xero_id               = $xeroId;
            }

            // $student->save();

            $user = User::where('username', $student->nric)->first();
            if($user){
                $user->name          = $request->get('name');
                $user->email         = $request->get('email');
                $user->phone_number  = $request->get('phone_number');
                $user->role          = 'student';
                $user->status        = 1;
                $user->dob           = $request->get('dob');
                $user->save();
                $user->assignRole('student');
                // save user id after user creation
                $student->user_id = $user->id;
            }
            $student->save();

        } else {
            // add student in master table
            $student = new Student;
            $student->name                        = $request->get('name');
            $student->nric                        = $request->get('nric');
            $student->company_sme                 = $request->get('company_sme');
            $student->nationality                 = $request->get('nationality');
            $student->email                       = $request->get('email');
            $student->mobile_no                   = $request->get('mobile_no');
            $student->dob                         = $request->get('dob');
            if( $request->has('company_name') && !empty($request->get('company_name')) ) {
                $student->company_name            = $request->get('company_name');
            }
            if( $request->has('company_uen') && !empty($request->get('company_uen')) ) {
                $student->company_uen                 = $request->get('company_uen');
            }
            if( $request->has('company_contact_person') && !empty($request->get('company_contact_person')) ) {
                $student->company_contact_person      = $request->get('company_contact_person');
            }
            if( $request->has('company_contact_person_email') && !empty($request->get('company_contact_person_email')) ) {
                $student->company_contact_person_email = $request->get('company_contact_person_email');
            }
            if( $request->has('company_contact_person_number') && !empty($request->get('company_contact_person_number')) ) {
                $student->company_contact_person_number = $request->get('company_contact_person_number');
            }
            if( $request->has('billing_address') ) {
                $student->billing_address             = $request->get('billing_address');
            }
            $student->meal_restrictions          = $request->get('meal_restrictions');
            $student->meal_restrictions_type     = $request->get('meal_restrictions_type');
            $student->meal_restrictions_other    = $request->get('meal_restrictions_other');
            $student->xero_id                     = $xeroId;
            $student->created_by                  = 1;
            $student->updated_by                  = 1;
            // temporary comment
            $student->save();
            $studentId = $student->id;

            //Add student for assessment portal assessment-student table
            // $assessmentStudent              = new AssessmentStudent;
            // $assessmentStudent->student_id  = $studentId;
            // $assessmentStudent->user_id     = $request->get('email');
            // $assessmentStudent->password    = Hash::make($request->get('nric'));
            // $assessmentStudent->imp_data    = $request->get('nric');
            // $assessmentStudent->save();

            //Add student for assessment portal on user table
            /* $studentData = User::where('email', $request->get('email'))->first();
            if($studentData){
                $studentData->name          = $request->get('name');
                $studentData->role          = 'student';
                $studentData->status        = 1;
                $studentData->password      = Hash::make($request->get('nric'));
                $studentData->dob           = $request->get('dob');
                $studentData->update();
            } else {
            } */

            $studentUser = new User;
            $studentUser->name          = $request->get('name');
            $studentUser->email         = $request->get('email');
            $studentUser->username      = strtoupper(trim($request->get('nric')));
            $studentUser->phone_number  = $request->get('phone_number');
            $studentUser->role = 'student';
            $studentUser->status        = 1;
            $studentUser->password      = Hash::make(strtoupper(trim($request->get('nric'))));
            $studentUser->dob           = $request->get('dob');
            $studentUser->save();
            // save user id after user creation
            $student->user_id = $studentUser->id;
            $student->save();
            $studentUser->assignRole('student');
        }
        // get data from session table
        // $courseId = $request->get('training_course_dates');
        // $course = Course::findOrFail($courseId);
        // add it in enrolement table here
        if( $request->get('learning_mode') == "f2f" ) {
            $course_id = $request->get('courses');
        } else {
            $course_id = $request->get('online_course_id');
        }
        $tpgSyncStatus = true;
        if( is_array($course_id) ) {
            foreach( $course_id as $courseId ) {
                $record = new StudentEnrolment;
                $record->student_id                 = $studentId;
                $record->course_id                  = $courseId['f2f_course_id'];
                $record->sponsored_by_company       = $request->get('sponsored_by_company');
                $record->company_sme                = $request->get('company_sme');
                $record->nationality                = $request->get('nationality');
                $record->age                        = $request->get('age');
                $record->learning_mode              = $request->get('learning_mode');
                $record->email                      = $request->get('email');
                $record->mobile_no                  = $request->get('mobile_no');
                $record->dob                        = $request->get('dob');
                $record->education_qualification    = $request->get('education_qualification');
                $record->designation                = $request->get('designation');
                $record->salary                     = $request->get('salary');
                $record->company_name               = $request->get('company_name');
                $record->company_uen                = $request->get('company_uen');
                $record->company_contact_person     = $request->get('company_contact_person');
                $record->company_contact_person_email = $request->get('company_contact_person_email');
                $record->company_contact_person_number = $request->get('company_contact_person_number');
                $record->billing_email              = $request->get('billing_email');
                $record->billing_address            = $request->get('billing_address');
                $record->billing_zip                = $request->get('billing_zip');
                $record->billing_country            = $request->get('billing_country');
                $record->remarks                    = $request->get('remarks');
                /*$record->payment_mode               = $request->get('payment_mode');*/
                $record->payment_mode_company       = $request->get('payment_mode');
                $record->amount                     = cleanAmount($courseId['amount']);
                $record->xero_nett_course_fees      = cleanAmount($request->get('amount'));
                $record->discountAmount             = cleanAmount($request->get('discountAmount'));

                $record->meal_restrictions          = $request->get('meal_restrictions');
                $record->meal_restrictions_type     = $request->get('meal_restrictions_type');
                $record->meal_restrictions_other    = $request->get('meal_restrictions_other');
                $record->computer_navigation_skill  = $request->get('computer_navigation_skill');
                $record->course_brochure_determined = $request->get('course_brochure_determined');

                $record->payment_status             = $request->get('payment_status');
                if( $request->has('payment_tpg_status') ) {
                    $record->payment_tpg_status         = $request->get('payment_tpg_status');
                } else {
                    $record->payment_tpg_status         = $request->get('payment_status');
                }
                $record->entry_id                   = $request->get('entry_id');
                

                if( $request->has('payment_remark') && !empty($request->get('payment_remark'))) {
                    $record->payment_remark             = $request->get('payment_remark');
                }
                if( $request->has('due_date') && !empty($request->get('due_date'))) {
                    $record->due_date             = $request->get('due_date');
                }
                if( $request->has('reference') && !empty($request->get('reference'))) {
                    $record->reference             = $request->get('reference');
                }

                if( $request->has('pesa_refrerance_number') && !empty($request->get('pesa_refrerance_number')) ) {
                    $record->pesa_refrerance_number = $request->get('pesa_refrerance_number');
                }
                
                if( $request->has('skillfuture_credit') && !empty($request->get('skillfuture_credit')) ) {
                    $record->skillfuture_credit = $request->get('skillfuture_credit');
                }
                
                if( $request->has('vendor_gov') && !empty($request->get('vendor_gov')) ) {
                    $record->vendor_gov = $request->get('vendor_gov');
                }

                /* Add Xero Invoice Number - Added Manully */
                $record->xero_invoice_number  = $courseId['xero_invoice_number'];

                if(isset($courseId['xero_invoice_number'])){
                    $record->master_invoice = 0;
                }

                $record->created_by                 = Auth::id();
                $record->updated_by                 = Auth::id();
                
                //temporary comment
                $record->save();

                // increment the source schedule
                $course = Course::find($record->course_id);
                Log::info('registeredusercount increment');
                Log::info($record->id);
                $course->increment('registeredusercount');
                // check if is booster session than skip xero invoice and tpgateway sync
                $_needtoSkip = false;
                if( $course->courseMain->course_type_id != CourseMain::SINGLE_COURSE ) {
                    $_needtoSkip = true;
                    $record->status = StudentEnrolment::STATUS_ENROLLED;
                    $record->save();

                    /*$assessmentStudentData = AssessmentStudent::where('student_id', $record->student_id)->first();
                    $deletedAssessmentStudent = AssessmentStudent::where('student_id', $record->student_id)->withTrashed()->first();
                    if($deletedAssessmentStudent){
                        $deletedAssessmentStudent->restore();
                    } elseif ($assessmentStudentData == null){
                        $addForAssessment             = new AssessmentStudent;
                        $addForAssessment->student_id = $record->student_id;
                        $addForAssessment->user_id    = $record->email;
                        $addForAssessment->password   = Hash::make($request->get('nric'));
                        $addForAssessment->imp_data   = $request->get('nric');
                        $addForAssessment->save();
                    }*/
                }
                if( $course->courseMain->course_type == CourseMain::COURSE_TYPE_NONWSQ ) {
                    $_needtoSkip = true;
                    $record->status = StudentEnrolment::STATUS_ENROLLED;
                    $record->save();
                    
                    /*$assessmentStudentData = AssessmentStudent::where('student_id', $record->student_id)->first();
                    $deletedAssessmentStudent = AssessmentStudent::where('student_id', $record->student_id)->withTrashed()->first();
                    if($deletedAssessmentStudent){
                        $deletedAssessmentStudent->restore();
                    } elseif ($assessmentStudentData == null){
                        $addForAssessment             = new AssessmentStudent;
                        $addForAssessment->student_id = $record->student_id;
                        $addForAssessment->user_id    = $record->email;
                        $addForAssessment->password   = Hash::make($request->get('nric'));
                        $addForAssessment->imp_data   = $request->get('nric');
                        $addForAssessment->save();
                    }*/
                }

                // check if this trainee has soft booked then make it completed
                $softBooking = CourseSoftBooking::where('course_id', $record->course_id)
                                ->where('nric', $request->get('nric'))->first();
                if( isset($softBooking->id) && $softBooking->status == CourseSoftBooking::STATUS_PENDING ) {
                    $softBooking->status = CourseSoftBooking::STATUS_BOOKED;

                    // temporary comment
                    $softBooking->save();
                }

                if( !$_needtoSkip ) {
                    $tpgatewayReq = new TPGatewayService;
                    if( $course->tpgateway_id != "" && $course->tpgateway_id != "#N/A" ) {
                        // enrolment
                        $req_data = $tpgatewayReq->createEnrolmentRequest($course, $record);

                        // add to TP Gateway
                        $enrolRes = $tpgatewayReq->studentEnrolment($req_data);
                        $record->enrollmentResponse = json_encode($enrolRes);
                        $record->save();
                        if( isset($enrolRes->status) && $enrolRes->status == 200 ) {
                            $record->tpgateway_refno = $enrolRes->data->enrolment->referenceNumber;
                            $record->status = StudentEnrolment::STATUS_ENROLLED;
                            $record->save();
                            // grant calculator
                            $req_data_grant = $tpgatewayReq->createGrantRequest($course, $record);
                            // get data from tpgateway
                            $grantRes = $tpgatewayReq->checkGrantCalculator($req_data_grant);
                            $record->isGrantError = 1;
                            $record->grantResponse = json_encode($grantRes);
                            $record->save();
                            if( isset($grantRes->status) && $grantRes->status == 200 ) {
                                if( !empty($grantRes->data)) {
                                    // add grant for this enrollment
                                    foreach( $grantRes->data as $grant ){
                                        $grantRecord = new Grant;
                                        $grantRecord->student_enrolment_id      = $record->id;
                                        $grantRecord->grant_refno               = $grant->referenceNumber;
                                        $grantRecord->grant_status              = $grant->status;
                                        $grantRecord->scheme_code               = $grant->fundingScheme->code;
                                        $grantRecord->scheme_description        = $grant->fundingScheme->description;
                                        $grantRecord->component_code            = $grant->fundingComponent->code;
                                        $grantRecord->component_description     = $grant->fundingComponent->description;
                                        $grantRecord->amount_estimated          = $grant->grantAmount->estimated;
                                        $grantRecord->amount_paid               = $grant->grantAmount->paid;
                                        $grantRecord->amount_recovery           = $grant->grantAmount->recovery;
                                        $grantRecord->created_by                = Auth::Id();
                                        $grantRecord->updated_by                = Auth::Id();
                                        $grantRecord->save();
                                    }
                                }
                                // after grant approved we have to calculate nett fees
                                /*$record->grantEstimated = $grantRes->data[0]->grantAmount->estimated;
                                $record->grantRefNo = $grantRes->data[0]->referenceNumber;
                                $record->grantStatus = $grantRes->data[0]->status; */
                                $record->isGrantError = 0;
                                $record->save();
                            }

                        }else{
                            $tpgSyncStatus = false;
                        }
                        //correct name from TPG
                        $getName = $this->getNameOnNRIC($record->id);
                    }
                }

                //Manual invoice creation
                if($record->xero_invoice_number){
                    $xeroService = new XeroService($xeroCredentials);
                    $getUUID = $xeroService->getInvoiceFromXero($record->xero_invoice_number);
                    Log::info("Fetch the data");
                    $invoiceData = $xeroService->getInvoiceFromXeroAndSave($getUUID, $record->id);
                } else {
                    //Create invoice data
                    //Create contact id here
                    $grantCalculationService = new GrantCalculationService;
                    $invoiceData = $grantCalculationService->generateInvoice($record->id);
                    Log::info("Invoice created from TMS");
                    if($invoiceData != false){
                        if($request->get('create_xero_invoice')){
                            Log::info("Xero Syncing Call Start");
                            $returnType = false;
                            $xeroService = new XeroService($xeroCredentials);
                            $xeroData = $xeroService->createInvoiceFromXero($invoiceData, $returnType);
                            $record->xero_invoice_number = $xeroData;
                            $record->master_invoice = StudentEnrolment::XERO_SYNC;
                            $record->save();
                            Log::info("Xero Invoice Number ". $xeroData);
                            Log::info("Xero Syncing Call End");
                        }
                        Log::info("Invoice only created from TMS");
                    }
                }

                // add this invoice to xero
                /* try {
                    // create lineitems
                    $xeroLineItems = [];
                    $lineItems = XeroCourseLineItems::where('course_main_id', $course->course_main_id)->get();
                    if( !empty($lineItems) ) {
                        foreach( $lineItems as $item ) {
                            $lineItem = $xeroServiceReq->createLineItem($item);
                            array_push($xeroLineItems, $lineItem);
                        }
                        $resObj = $xeroServiceReq->createInvoiceXero(
                            $student->xero_id,
                            $xeroLineItems,
                            $course->courseMain->branding_theme_id);
                        \Log::error("Xero invoice obj wp, ", [$resObj]);
                        if( $resObj ) {
                            if( $resObj->getInvoices()[0]->getInvoiceId() != "00000000-0000-0000-0000-000000000000") {
                                $record->xero_invoice_id = $resObj->getInvoices()[0]->getInvoiceId();
                                $record->xero_invoice_number = $resObj->getInvoices()[0]->getInvoiceNumber();
                                $record->save();
                            }
                            // $xeroId = $resObj->getContacts()[0]->getContactId();
                        }
                    }
                } catch (Exception $e) {
                    // log to error
                    \Log::error("Xero invoice create wp, ".$e->getMessage(), [$e]);
                } */
            }
            if($tpgSyncStatus){
                return TRUE;
            } else{
                return FALSE;
            }
        }
        return FALSE;
        
    }

    public function doStudentEnrolmentAgainbyID($id, $enrolement_ids = [])
    {
        if($enrolement_ids){

            foreach($enrolement_ids as $id){
                $record = $this->getStudentEnrolmentById($id);
                $student = Student::find($record->student_id);
                $course = Course::find($record->course_id);
                if( $course->courseMain->course_type == CourseMain::COURSE_TYPE_NONWSQ ) {
                    $record->status = StudentEnrolment::STATUS_ENROLLED;
                    // var_dump('COURSE_TYPE_NONWSQ==> '.$record->status);
                    $record->save();
                    
                    /*$assessmentStudentData = AssessmentStudent::where('student_id', $record->student_id)->first();
                    $deletedAssessmentStudent = AssessmentStudent::where('student_id', $record->student_id)->withTrashed()->first();
                    if($deletedAssessmentStudent){
                        $deletedAssessmentStudent->restore();
                    } elseif ($assessmentStudentData == null){
                        $addForAssessment             = new AssessmentStudent;
                        $addForAssessment->student_id = $record->student_id;
                        $addForAssessment->user_id    = $record->email;
                        $addForAssessment->password   = Hash::make($student->nric);
                        $addForAssessment->imp_data   = $student->nric;
                        $addForAssessment->save();
                    }*/
                    // return [ 'status' => true, 'msg' => 'enrolment done' ];
                }
                if( $course->courseMain->course_type_id != CourseMain::SINGLE_COURSE ) {
                    $record->status = StudentEnrolment::STATUS_ENROLLED;
                    // var_dump('SINGLE_COURSE==> '.$record->status);
                    $record->save();
                    
                    /*$assessmentStudentData = AssessmentStudent::where('student_id', $record->student_id)->first();
                    $deletedAssessmentStudent = AssessmentStudent::where('student_id', $record->student_id)->withTrashed()->first();
                    if($deletedAssessmentStudent){
                        $deletedAssessmentStudent->restore();
                    } elseif ($assessmentStudentData == null){
                        $addForAssessment             = new AssessmentStudent;
                        $addForAssessment->student_id = $record->student_id;
                        $addForAssessment->user_id    = $record->email;
                        $addForAssessment->password   = Hash::make($student->nric);
                        $addForAssessment->imp_data   = $student->nric;
                        $addForAssessment->save();
                    }*/
                    // return [ 'status' => true, 'msg' => 'enrolment done' ];
                }
                // var_dump('tpgateway_id==> '.$record->status);
                if( $record->courseRun->tpgateway_id != "" && $record->courseRun->tpgateway_id != "#N/A" ) {
                    if( $record->status == 3  ) {
                        if( $course->courseMain->course_type_id == CourseMain::SINGLE_COURSE ) {
                            $tpgatewayReq = new TPGatewayService;
                            // enrolment
                            $req_data = $tpgatewayReq->createEnrolmentRequest($course, $record);
                            // add to TP Gateway
                            $enrolRes = $tpgatewayReq->studentEnrolment($req_data);
    
                            $record->enrollmentResponse = json_encode($enrolRes);
                            $record->save();
                            if( isset($enrolRes->status) && $enrolRes->status == 200 ) {
                                $record->tpgateway_refno = $enrolRes->data->enrolment->referenceNumber;
                                $record->status = StudentEnrolment::STATUS_ENROLLED;
                                $record->save();
                                
                                //If student enroll then access resources
                                /*$assessmentStudentData = AssessmentStudent::where('student_id', $record->student_id)->first();
                                $deletedAssessmentStudent = AssessmentStudent::where('student_id', $record->student_id)->withTrashed()->first();
                                if($deletedAssessmentStudent){
                                    $deletedAssessmentStudent->restore();
                                } elseif ($assessmentStudentData == null){
                                    $addForAssessment             = new AssessmentStudent;
                                    $addForAssessment->student_id = $record->student_id;
                                    $addForAssessment->user_id    = $record->email;
                                    $addForAssessment->password   = Hash::make($student->nric);
                                    $addForAssessment->imp_data   = $student->nric;
                                    $addForAssessment->save();
                                }*/
                                
                                // grant calculator
                                $req_data_grant = $tpgatewayReq->createGrantRequest($course, $record);
                                // get data from tpgateway
                                $grantRes = $tpgatewayReq->checkGrantCalculator($req_data_grant);
                                $record->isGrantError = 1;
                                $record->grantResponse = json_encode($grantRes);
                                $record->save();
                                if( isset($grantRes->status) && $grantRes->status == 200 ) {
                                    if( !empty($grantRes->data)) {
                                        // add/update grant for this enrollment
                                        foreach( $grantRes->data as $grant ){
    
                                            if($grant->status == 'Completed'){
                                                $disbursedDate = $grant->disbursementDate; 
                                                Grant::where('grant_refno', $grant->referenceNumber)->update(['disbursement_date' => $disbursedDate]);
                                            }
                                            else{
                                                $grantResByRef = $tpgatewayReq->checkGrantStatus($grant->referenceNumber);
                                                if( isset($grantResByRef->status) && $grantResByRef->status == 200 ) {
                                                    $disbursedDate = isset($grantResByRef->data->disbursementDate) ? $grantResByRef->data->disbursementDate : Carbon::parse($grantResByRef->meta->updatedOn)->format('Y-m-d');
                                                    Grant::where('grant_refno', $grant->referenceNumber)->update(['disbursement_date' => $disbursedDate]);
                                                }
                                            }
                                        
                                            $grantRecord = Grant::updateOrCreate(
                                                [
                                                    'grant_refno'   => $grant->referenceNumber,
                                                ],
                                                [
                                                    'student_enrolment_id' => $record->id,
                                                    'grant_refno' => $grant->referenceNumber,
                                                    'grant_status' => $grant->status,
                                                    'scheme_code' => $grant->fundingScheme->code,
                                                    'scheme_description' => $grant->fundingScheme->description,
                                                    'component_code' => $grant->fundingComponent->code,
                                                    'component_description' => $grant->fundingComponent->description,
                                                    'amount_estimated' => round($grant->grantAmount->estimated, 2),
                                                    'amount_paid' => round($grant->grantAmount->paid,2),
                                                    'amount_recovery' => round($grant->grantAmount->recovery,2),
                                                    //'disbursement_date' => $grant->disbursementDate ?? NULL,
                                                    'last_sync' => date('Y-m-d'),
                                                    'TPG_response' => 1,
                                                    'created_by' => Auth::Id(),
                                                    'updated_by' => Auth::Id()
                                                ]
                                            );
                                            
                                        }
                                    }
    
                                    // $record->grantEstimated = $grantRes->data[0]->grantAmount->estimated;
                                    // $record->grantRefNo = $grantRes->data[0]->referenceNumber;
                                    // $record->grantStatus = $grantRes->data[0]->status;
                                    $record->isGrantError = 0;
                                    $record->save();   
                                    
                                    //correct name from TPG
                                    $getName = $this->getNameOnNRIC($record->id);

                                }
                            }
                        }
                    }
                }
            }
            return [ 'status' => true, 'msg' => 'enrolment done' ];

        } else {
            $record = $this->getStudentEnrolmentById($id);
            $student = Student::find($record->student_id);
            if( $record->status == 1 ) {
                return [ 'status' => FALSE, 'msg' => 'enrolment previously cancelled' ];
            }
            $course = Course::find($record->course_id);
            if( $course->courseMain->course_type == CourseMain::COURSE_TYPE_NONWSQ ) {
                $record->status = StudentEnrolment::STATUS_ENROLLED;
                $record->save();
                
                /*$assessmentStudentData = AssessmentStudent::where('student_id', $record->student_id)->first();
                $deletedAssessmentStudent = AssessmentStudent::where('student_id', $record->student_id)->withTrashed()->first();
                if($deletedAssessmentStudent){
                    $deletedAssessmentStudent->restore();
                } elseif ($assessmentStudentData == null){
                    $addForAssessment             = new AssessmentStudent;
                    $addForAssessment->student_id = $record->student_id;
                    $addForAssessment->user_id    = $record->email;
                    $addForAssessment->password   = Hash::make($student->nric);
                    $addForAssessment->imp_data   = $student->nric;
                    $addForAssessment->save();
                }*/

                return [ 'status' => true, 'msg' => 'enrolment done' ];
            }
            if( $course->courseMain->course_type_id != CourseMain::SINGLE_COURSE ) {
                $record->status = StudentEnrolment::STATUS_ENROLLED;
                $record->save();
                
                /*$assessmentStudentData = AssessmentStudent::where('student_id', $record->student_id)->first();
                $deletedAssessmentStudent = AssessmentStudent::where('student_id', $record->student_id)->withTrashed()->first();
                if($deletedAssessmentStudent){
                    $deletedAssessmentStudent->restore();
                } elseif ($assessmentStudentData == null){
                    $addForAssessment             = new AssessmentStudent;
                    $addForAssessment->student_id = $record->student_id;
                    $addForAssessment->user_id    = $record->email;
                    $addForAssessment->password   = Hash::make($student->nric);
                    $addForAssessment->imp_data   = $student->nric;
                    $addForAssessment->save();
                }*/

                return [ 'status' => true, 'msg' => 'enrolment done' ];
            }
            if( $record->courseRun->tpgateway_id != "" && $record->courseRun->tpgateway_id != "#N/A" ) {
                if( $record->status == 3  ) {
                    if( $course->courseMain->course_type_id == CourseMain::SINGLE_COURSE ) {
                        $tpgatewayReq = new TPGatewayService;
                        // enrolment
                        $req_data = $tpgatewayReq->createEnrolmentRequest($course, $record);
                        // add to TP Gateway
                        $enrolRes = $tpgatewayReq->studentEnrolment($req_data);

                        $record->enrollmentResponse = json_encode($enrolRes);
                        $record->save();
                        if( isset($enrolRes->status) && $enrolRes->status == 200 ) {
                            $record->tpgateway_refno = $enrolRes->data->enrolment->referenceNumber;
                            $record->status = StudentEnrolment::STATUS_ENROLLED;
                            $record->save();
                            
                            //If student enroll then access resources
                            /*$assessmentStudentData = AssessmentStudent::where('student_id', $record->student_id)->first();
                            $deletedAssessmentStudent = AssessmentStudent::where('student_id', $record->student_id)->withTrashed()->first();
                            if($deletedAssessmentStudent){
                                $deletedAssessmentStudent->restore();
                            } elseif ($assessmentStudentData == null){
                                $addForAssessment             = new AssessmentStudent;
                                $addForAssessment->student_id = $record->student_id;
                                $addForAssessment->user_id    = $record->email;
                                $addForAssessment->password   = Hash::make($student->nric);
                                $addForAssessment->imp_data   = $student->nric;
                                $addForAssessment->save();
                            }*/

                            // grant calculator
                            $req_data_grant = $tpgatewayReq->createGrantRequest($course, $record);
                            // get data from tpgateway
                            $grantRes = $tpgatewayReq->checkGrantCalculator($req_data_grant);
                            $record->isGrantError = 1;
                            $record->grantResponse = json_encode($grantRes);
                            $record->save();
                            if( isset($grantRes->status) && $grantRes->status == 200 ) {
                                if( !empty($grantRes->data)) {
                                    // add/update grant for this enrollment
                                    foreach( $grantRes->data as $grant ){

                                        if($grant->status == 'Completed'){
                                            $disbursedDate = $grant->disbursementDate; 
                                            Grant::where('grant_refno', $grant->referenceNumber)->update(['disbursement_date' => $disbursedDate]);
                                        }
                                        else{
                                            $grantResByRef = $tpgatewayReq->checkGrantStatus($grant->referenceNumber);
                                            if( isset($grantResByRef->status) && $grantResByRef->status == 200 ) {
                                                $disbursedDate = isset($grantResByRef->data->disbursementDate) ? $grantResByRef->data->disbursementDate : Carbon::parse($grantResByRef->meta->updatedOn)->format('Y-m-d');
                                                Grant::where('grant_refno', $grant->referenceNumber)->update(['disbursement_date' => $disbursedDate]);
                                            }
                                        }
                                    
                                        $grantRecord = Grant::updateOrCreate(
                                            [
                                                'grant_refno'   => $grant->referenceNumber,
                                            ],
                                            [
                                                'student_enrolment_id' => $record->id,
                                                'grant_refno' => $grant->referenceNumber,
                                                'grant_status' => $grant->status,
                                                'scheme_code' => $grant->fundingScheme->code,
                                                'scheme_description' => $grant->fundingScheme->description,
                                                'component_code' => $grant->fundingComponent->code,
                                                'component_description' => $grant->fundingComponent->description,
                                                'amount_estimated' => round($grant->grantAmount->estimated, 2),
                                                'amount_paid' => round($grant->grantAmount->paid,2),
                                                'amount_recovery' => round($grant->grantAmount->recovery,2),
                                                //'disbursement_date' => $grant->disbursementDate ?? NULL,
                                                'last_sync' => date('Y-m-d'),
                                                'TPG_response' => 1,
                                                'created_by' => Auth::Id(),
                                                'updated_by' => Auth::Id()
                                            ]
                                        );
                                        
                                    }
                                }

                            // $record->grantEstimated = $grantRes->data[0]->grantAmount->estimated;
                            // $record->grantRefNo = $grantRes->data[0]->referenceNumber;
                            // $record->grantStatus = $grantRes->data[0]->status;
                            $record->isGrantError = 0;
                            $record->save();
                            //correct name from TPG
                            $getName = $this->getNameOnNRIC($record->id);
                        }
                            return [ 'status' => true, 'msg' => 'enrolment done' ];
                        }
                    }
                }
            }
            return [ 'status' => false, 'msg' => 'enrolment error' ];
        }

    }

    public function updateStudentEnrolment($id, $request, $xeroCerd)
    {
        $record = $this->getStudentEnrolmentById($id);
        //dd($record);
        if( $record ) {
            // $record->course_id                  = $request->get('course_id');
            $record->sponsored_by_company       = $request->get('sponsored_by_company');
            $record->company_sme                = $request->get('company_sme');
            $record->nationality                = $request->get('nationality');
            $record->age                        = $request->get('age');
            // $record->learning_mode              = $request->get('learning_mode');
            // $record->name                       = $request->get('name');
            // $record->nric                       = $request->get('nric');
            $record->email                      = $request->get('email');
            $record->mobile_no                  = $request->get('mobile_no');
            $record->dob                        = $request->get('dob');
            $record->education_qualification    = $request->get('education_qualification');
            // $record->designation                = $request->get('designation');
            // $record->salary                     = $request->get('salary');
            $record->company_uen                = $request->get('company_uen');
            $record->company_name               = $request->get('company_name');
            $record->company_contact_person     = $request->get('company_contact_person');
            $record->company_contact_person_email = $request->get('company_contact_person_email');
            $record->company_contact_person_number = $request->get('company_contact_person_number');
            $record->billing_email              = $request->get('billing_email');
            $record->billing_address            = $request->get('billing_address');
            $record->billing_zip                = $request->get('billing_zip');
            $record->billing_country            = $request->get('billing_country');
            $record->remarks                    = $request->get('remarks');
            /*$record->payment_mode               = $request->get('payment_mode');*/
            $record->payment_mode_company       = $request->get('payment_mode');
            $record->amount                     = cleanAmount($request->get('amount'));
            $record->xero_nett_course_fees      = cleanAmount($request->get('amount'));
            $record->discountAmount             = cleanAmount($request->get('discountAmount'));

            $record->meal_restrictions          = $request->get('meal_restrictions');
            $record->meal_restrictions_type     = $request->get('meal_restrictions_type');
            $record->meal_restrictions_other    = $request->get('meal_restrictions_other');
            $record->computer_navigation_skill  = $request->get('computer_navigation_skill');
            $record->course_brochure_determined = $request->get('course_brochure_determined');

            $record->payment_status             = $request->get('payment_status');
            $record->payment_tpg_status         = $request->get('payment_tpg_status');
            $record->xero_invoice_number        = $request->get('xero_invoice_number');
            $record->master_invoice             = $request->get('master_invoice');
            $record->updated_by                 = Auth::Id();

            if( $request->has('payment_remark')) {
                $record->payment_remark             = $request->get('payment_remark');
            }
            if( $request->has('due_date')) {
                $record->due_date             = $request->get('due_date');
            }
            if( $request->has('reference')) {
                $record->reference             = $request->get('reference');
            }

            if( $request->has('pesa_refrerance_number')) {
                $record->pesa_refrerance_number = $request->get('pesa_refrerance_number');
            }
            
            if( $request->has('skillfuture_credit')) {
                $record->skillfuture_credit = $request->get('skillfuture_credit');
            }
            
            if( $request->has('vendor_gov')) {
                $record->vendor_gov = $request->get('vendor_gov');
            }

            if($request->has('xero_nett_course_fees')){
                $record->xero_nett_course_fees = $request->get('xero_nett_course_fees');
            }

            // check if old data for reschedule
            $course_id = $request->get('f2f_course_id');
            if( $record->course_id != $course_id ) {
                $oldCourseId = $record->course_id;
                // get selected course tpgateway id
                $courseRun = Course::find($course_id);
                if( $courseRun->courseMain->course_type == CourseMain::COURSE_TYPE_NONWSQ ||
                    $courseRun->courseMain->course_type_id != CourseMain::SINGLE_COURSE ) {
                    $record->course_id                  = $request->get('f2f_course_id');
                    if( $record->status == StudentEnrolment::STATUS_HOLD ) {
                        // mark this as enrolled
                        $record->status = StudentEnrolment::STATUS_ENROLLED;
                    }
                    // update course registration count
                    else if( $record->status == 0 ) {
                        $oldCourseRun = Course::find($oldCourseId);
                        $oldCourseRun->decrement('registeredusercount');
                    }
                    else if($record->status == 3){
                        // mark this as enrolled
                        $record->status = StudentEnrolment::STATUS_ENROLLED;
                    }
                    Log::info('registeredusercount increment');
                    Log::info($record->id);
                    $courseRun->increment('registeredusercount');
                    $record->save();
                    return $record;
                }
                
                if( !empty($courseRun->tpgateway_id) && !empty($record->tpgateway_refno) ) {
                    $tpgatewayReq = new TPGatewayService;
                    // update this in tpgateway
                    // create update request
                    $req_data = $tpgatewayReq->createEnrollmentUpdateRequest($courseRun, $record);
                    // get data from tpgateway
                    $enrollRes = $tpgatewayReq->updateCancelStudentEnrolment($record->tpgateway_refno, $req_data);
                    $record->enrollmentResponse = json_encode($enrollRes);
                    if( isset($enrollRes->status) && $enrollRes->status == 200 ) {
                        $record->course_id                  = $request->get('f2f_course_id');
                        
                        if( $record->status == StudentEnrolment::STATUS_HOLD ) {
                            // mark this as enrolled
                            $record->status = StudentEnrolment::STATUS_ENROLLED;
                        }
                        // update course registration count
                        else if( $record->status == 0 ) {
                            $oldCourseRun = Course::find($oldCourseId);
                            $oldCourseRun->decrement('registeredusercount');
                        }
                        else if($record->status == 3){
                            // mark this as enrolled
                            $record->status = StudentEnrolment::STATUS_ENROLLED;
                        }
                        Log::info('registeredusercount increment');
                        Log::info($record->id);
                        $courseRun->increment('registeredusercount');
                    }
                }
            }

            $record->save();
            //Xero syncing flow start
                $invoiceData = Invoice::where(['student_enroll_id' => $id, 'invoice_number' => $record->xero_invoice_number])->first();
                $studentData = StudentEnrolment::findOrfail($id);
                $xeroServiceReq = new XeroService($xeroCerd);

                //data wil be empty on invoice and also not sync with xero
                if(empty($invoiceData) && $record->xero_invoice_number == null) {
                    $grantOfStudnet =  Grant::where('student_enrolment_id', $id)->get()->toArray();
                    if($record->master_invoice == StudentEnrolment::TMS_SYNC) {
                        if($grantOfStudnet) {
                            $invoiceData = $this->grantCalculationService->AddUpdateOldInvoices($id, $xeroCerd);
                            if($invoiceData){
                                $invoiceData = Invoice::where('student_enroll_id', $invoiceData)->first();
                            } else {
                                setflashmsg("Sorry your course setting is missing", 0);
                                return redirect()->back();
                            }
                        } else {
                            $invoiceData = $this->grantCalculationService->generateInvoice($id);
                            if($invoiceData){
                                $invoiceData = Invoice::where('student_enroll_id', $invoiceData)->first();
                            } else {
                                setflashmsg("Sorry your course setting is missing", 0);
                                return redirect()->back();
                            }
                        }
                    } elseif($record->master_invoice == StudentEnrolment::XERO_SYNC) {
                        $grantOfStudnet =  Grant::where('student_enrolment_id', $id)->get()->toArray();
                        if($grantOfStudnet) {
                            $returnType = false;
                            $invoiceData = $this->grantCalculationService->AddUpdateOldInvoices($id, $xeroCerd);
                            
                            $invoiceData = $xeroServiceReq->createInvoiceFromXero($invoiceData, $returnType);
                            
                            $invoiceData = Invoice::where('invoice_number', $invoiceData)->first();
                        } else {
                            $returnType = false;
                            $invoiceData = $this->grantCalculationService->generateInvoice($id);
                            $invoiceData = $xeroServiceReq->createInvoiceFromXero($invoiceData, $returnType);
                            $invoiceData = Invoice::where('invoice_number', $invoiceData)->first();
                        }
                    }
                } else if (!empty($invoiceData) && $record->xero_invoice_number == null) {
                    $grantOfStudnet =  Grant::where('student_enrolment_id', $id)->get()->toArray();
                    if($record->master_invoice == StudentEnrolment::TMS_SYNC) {
                        if($grantOfStudnet) {
                            if($invoiceData->xero_sync) {
                                $invoiceData = $this->grantCalculationService->AddUpdateOldInvoices($id, $xeroCerd);
                                $invoiceData = Invoice::where('student_enroll_id', $invoiceData)->first();
                            } else {
                                $invoiceData = $invoiceData;
                            }
                        } else {
                            if($invoiceData->xero_sync) {
                                $invoiceData = $this->grantCalculationService->updateTmsInvoice($id);
                                $invoiceData = Invoice::where('student_enroll_id', $invoiceData)->first();
                            } else {
                                $invoiceData = $invoiceData;
                            }
                        }
                    } elseif($record->master_invoice == StudentEnrolment::XERO_SYNC) {
                        $grantOfStudnet =  Grant::where('student_enrolment_id', $id)->get()->toArray();
                        if($grantOfStudnet) {
                            if(!empty($invoiceData->xero_sync) && !empty($invoiceData->xero_invoice_id)) {
                                $invoiceData = $invoiceData;
                            } else {
                                $returnType = false;
                                $invoiceData = $this->grantCalculationService->updateTmsInvoice($id);
                                $invoiceData = $xeroServiceReq->createInvoiceFromXero($invoiceData, $returnType);
                                $invoiceData = Invoice::where('invoice_number', $invoiceData)->first();
                            }
                        } else {
                            if(!empty($invoiceData->xero_sync) && !empty($invoiceData->xero_invoice_id)) {
                                $invoiceData = $invoiceData;
                            } else {
                                $returnType = false;
                                $invoiceData = $this->grantCalculationService->updateTmsInvoice($id);
                                $invoiceData = $xeroServiceReq->createInvoiceFromXero($invoiceData, $returnType);
                                $invoiceData = Invoice::where('invoice_number', $invoiceData)->first();
                            }
                        }
                    }
                } else if (!empty($invoiceData) && $record->xero_invoice_number != null){
                    $grantOfStudnet =  Grant::where('student_enrolment_id', $id)->get()->toArray();
                    if($record->master_invoice == StudentEnrolment::TMS_SYNC) {
                        if($grantOfStudnet) {
                            if($invoiceData->xero_sync == 1 && !empty($invoiceData->xero_invoice_id)) {
                                $invoiceData = $this->grantCalculationService->AddUpdateOldInvoices($id, $xeroCerd);
                                $invoiceData = Invoice::where('student_enroll_id', $invoiceData)->first();
                            } else {
                                $invoiceData = $invoiceData;
                            }
                        } else {
                            if(!empty($invoiceData->xero_sync) && !empty($invoiceData->xero_invoice_id)) {
                                $invoiceData = $this->grantCalculationService->updateTmsInvoice($id);
                                $invoiceData = Invoice::where('student_enroll_id', $invoiceData)->first();
                            } else {
                                $invoiceData = $invoiceData;
                            }
                        }
                    } elseif($record->master_invoice == StudentEnrolment::XERO_SYNC) {
                        if($grantOfStudnet) {
                            if($studentData->xero_invoice_number == $invoiceData->invoice_number){
                                if(!empty($invoiceData->xero_sync) && !empty($invoiceData->xero_invoice_id)) {
                                    $invoiceData = $invoiceData;
                                } else {
                                    $getUUID = $xeroServiceReq->getInvoiceFromXero($studentData->xero_invoice_number);
                                    if($getUUID) {
                                        $xeroInvoiceData = $xeroServiceReq->saveInvoiceFromTheXero($getUUID); 
                                        $invoiceData = $this->grantCalculationService->xeroToTmsInvoice($xeroInvoiceData, $invoiceData);
                                        $invoiceData = Invoice::where('student_enroll_id', $invoiceData)->first();
                                    } else {
                                        setflashmsg("Your invoice will be not available on xero", 0);
                                        return redirect()->back();
                                    }
                                }
                            } else {
                                $getUUID = $xeroServiceReq->getInvoiceFromXero($studentData->xero_invoice_number);
                                if($getUUID) {
                                    $xeroInvoiceData = $xeroServiceReq->saveInvoiceFromTheXero($getUUID); 
                                    $invoiceData = $this->grantCalculationService->xeroToTmsInvoice($xeroInvoiceData, $invoiceData);
                                    $invoiceData = Invoice::where('student_enroll_id', $invoiceData)->first();
                                } else {
                                    setflashmsg("Your invoice will be not available on xero", 0);
                                    return redirect()->back();
                                }
                            } 
                        } else {
                            if($studentData->xero_invoice_number == $invoiceData->invoice_number){
                                if(!empty($invoiceData->xero_sync) && !empty($invoiceData->xero_invoice_id)) {
                                    $invoiceData = $invoiceData;
                                } else {
                                    $getUUID = $xeroServiceReq->getInvoiceFromXero($studentData->xero_invoice_number);
                                    if($getUUID) {
                                        $xeroInvoiceData = $xeroServiceReq->saveInvoiceFromTheXero($getUUID); 
                                        $invoiceData = $this->grantCalculationService->xeroToTmsInvoice($xeroInvoiceData, $invoiceData);
                                        $invoiceData = Invoice::where('student_enroll_id', $invoiceData)->first();
                                    } else {
                                        setflashmsg("Your invoice will be not available on xero", 0);
                                        return redirect()->back();
                                    }
                                }
                            }else{
                                $getUUID = $xeroServiceReq->getInvoiceFromXero($studentData->xero_invoice_number);
                                if($getUUID) {
                                    $xeroInvoiceData = $xeroServiceReq->saveInvoiceFromTheXero($getUUID); 
                                    $invoiceData = $this->grantCalculationService->xeroToTmsInvoice($xeroInvoiceData, $invoiceData);
                                    $invoiceData = Invoice::where('student_enroll_id', $invoiceData)->first();
                                } else {
                                    setflashmsg("Your invoice will be not available on xero", 0);
                                    return redirect()->back();
                                }
                            }
                        }
                    }
                } else if (empty($invoiceData) && $record->xero_invoice_number != null) {
                    $grantOfStudnet =  Grant::where('student_enrolment_id', $id)->get()->toArray();
                    if($record->master_invoice == StudentEnrolment::TMS_SYNC) {
                        
                        if($grantOfStudnet) {
                            $invoiceData = $this->grantCalculationService->AddUpdateOldInvoices($id, $xeroCerd);
                            if($invoiceData){
                                $invoiceData = Invoice::where('student_enroll_id', $invoiceData)->first();
                            } else {
                                setflashmsg("Sorry your course setting is missing", 0);
                                return redirect()->back();
                            }
                        } else {
                            $invoiceData = $this->grantCalculationService->generateInvoice($id);
                            if($invoiceData){
                                $invoiceData = Invoice::where('student_enroll_id', $invoiceData)->first();
                            } else {
                                setflashmsg("Sorry your course setting is missing", 0);
                                return redirect()->back();
                            }
                        }
                    } else if ($record->master_invoice == StudentEnrolment::XERO_SYNC) {
                        $getUUID = $xeroServiceReq->getInvoiceFromXero($record->xero_invoice_number);
                        $invoiceData = $this->grantCalculationService->generateInvoice($id);
                        if($invoiceData){
                            if($getUUID) {
                                $xeroInvoiceData = $xeroServiceReq->saveInvoiceFromTheXero($getUUID);
                                $invoiceData = Invoice::where('student_enroll_id', $invoiceData)->first();
                                $invoiceData = $this->grantCalculationService->xeroToTmsInvoice($xeroInvoiceData, $invoiceData);
                                $invoiceData = Invoice::where('student_enroll_id', $invoiceData)->first();
                            } else {
                                setflashmsg("Your invoice will be not available on xero", 0);
                                return redirect()->back();
                            }
                        } elseif(empty($invoiceData) && !empty($getUUID)) {
                            $invoiceData = $xeroServiceReq->getInvoiceFromXeroAndSave($getUUID, $studentData->id);
                        }
                    }
                }
            //Xero syncing flow end
            if($studentData->xero_due_amount == 0){
                if($studentData->xero_paid_amount == $studentData->amount){
                    $studentData->payment_status = StudentEnrolment::PAYMENT_STATUS_FULL;
                    $studentData->save();
                }
            }
            
            return $record;
        }
        return false;
    }

    public function updateStudentEnrolmentByEntryID($request)
    {
        $entry_id = $request->get('entry_id');
        $record = $this->getStudentEnrolmentByEntryId($entry_id);

        $payment_status = StudentEnrolment::PAYMENT_STATUS_PENDING;
        if($request->get('payment_status') == 'Paid') {
            $payment_status = StudentEnrolment::PAYMENT_STATUS_FULL;
        }

        if( $record ) {
            $record->payment_status             = $payment_status;
            $record->payment_tpg_status         = $payment_status;
            $record->transaction_id             = $request->get('transaction_id');
            $record->save();
            return $record;
        }
        return false;
    }

    public function cancelStudentEnrolmentbyID($id= null, $ids = [])
    {
        if($ids){
            Log::info("Enr ids ");
            Log::info(print_r($ids, true));
            foreach($ids as $enrollId){
                $record = $this->getStudentEnrolmentById($enrollId);
                Log::info("record_id " . $record->id);
                Log::info("status before " . $record->status);
                $course = Course::find($record->course_id);
                
                // Need to verify multiple cancel enroll
                if( $record ) {
                    $oldStatus = $record->status;
                    if( $course->courseMain->course_type_id == CourseMain::SINGLE_COURSE ) {
                        if( !empty($record->tpgateway_refno) ) {
                            // return ['status' => FALSE, 'msg' => 'No Enrolment found'];
                            // remove from TP Gateway
                            Log::info("tpgateway_refno " . $record->tpgateway_refno);
                            $tpgatewayReq = new TPGatewayService;
                            // first check if in TP Gateway they directly removed this student
                            $enrollment = $tpgatewayReq->getTpgStudentEnrolmentById($record->tpgateway_refno);
                            if( $enrollment['status'] ) {
                                if( $enrollment['data']->status == "Cancelled" ) {
                                    // this means it already cancelled
                                    Log::info("already Cancelled" . $enrollment['data']->status);
                                    $record->status                     = StudentEnrolment::STATUS_CANCELLED;
                                    $record->save();
                                    
                                    /*$assessmentStudent = AssessmentStudent::where('student_id', $record->student_id)->first();
                                    if($assessmentStudent) {
                                        $assessmentStudent->delete();
                                    }*/

                                    if( $course->registeredusercount > 0 ) {
                                        if( $oldStatus != 2 ) {
                                            $course->decrement('registeredusercount');
                                        }
                                        Log::info('registeredusercount increment');
                                        Log::info($record->id);
                                        $course->increment('cancelusercount');
                                    }
                                    // return $record;
                                    // return ['status' => TRUE, 'msg' => 'Enrolment cancelled Successfully'];
                                }
                            }
                            // grant calculator
                            $req_data = $tpgatewayReq->createEnrollmentCancelRequest($record->courseRun, $record);
                            // get data from tpgateway
                            $enrollRes = $tpgatewayReq->updateCancelStudentEnrolment($record->tpgateway_refno, $req_data);
                            // $record->save();
                            if( isset($enrollRes->status) && $enrollRes->status == 200 ) {
                                Log::info("TPG enrollRes" . $enrollRes->status);
                                // $record->payment_status             = StudentEnrolment::PAYMENT_STATUS_REFUND;
                                $record->status                     = StudentEnrolment::STATUS_CANCELLED;
                                $record->save();

                                /*$assessmentStudent = AssessmentStudent::where('student_id', $record->student_id)->first();
                                if($assessmentStudent) {
                                    $assessmentStudent->restore();
                                }*/

                                if( $course->registeredusercount > 0 ) {
                                    if( $oldStatus != 2 ) {
                                        $course->decrement('registeredusercount');
                                    }
                                    Log::info('registeredusercount increment');
                                    Log::info($record->id);
                                    $course->increment('cancelusercount');
                                }
                                // return $record;
                                // return ['status' => TRUE, 'msg' => 'Enrolment cancelled Successfully'];
                            }
                        } else {
                            // $record->payment_status             = StudentEnrolment::PAYMENT_STATUS_REFUND;
                            $record->status                     = StudentEnrolment::STATUS_CANCELLED;
                            $record->save();
                            
                            /*$assessmentStudent = AssessmentStudent::where('student_id', $record->student_id)->first();
                            if($assessmentStudent) {
                                $assessmentStudent->restore();
                            }*/
                            Log::info("else status" . $record->status);
                            if( $course->registeredusercount > 0 ) {
                                if( $oldStatus != 2 ) {
                                    $course->decrement('registeredusercount');
                                }
                                Log::info('registeredusercount increment');
                                Log::info($record->id);
                                $course->increment('cancelusercount');
                            }
                            // return $record;
                            // return ['status' => TRUE, 'msg' => 'Enrolment cancelled Successfully'];
                        }
                    } else {
                        // $record->payment_status             = StudentEnrolment::PAYMENT_STATUS_REFUND;
                        $record->status                     = StudentEnrolment::STATUS_CANCELLED;
                        $record->save();

                        /*$assessmentStudent = AssessmentStudent::where('student_id', $record->student_id)->first();
                        if($assessmentStudent) {
                            $assessmentStudent->restore();
                        }*/

                        Log::info("main status" . $record->status);
                        // cancel enrollment for booster session
                        if( $course->registeredusercount > 0 ) {
                            if( $oldStatus != 2 ) {
                                $course->decrement('registeredusercount');
                            }
                            Log::info('registeredusercount increment');
                            Log::info($record->id);
                            $course->increment('cancelusercount');
                        }
                        // return $record;
                        // return ['status' => TRUE, 'msg' => 'Enrolment cancelled Successfully'];
                    }
                    // return ['status' => FALSE, 'msg' => 'Enrolment not cancelled'];
                }
                // return ['status' => FALSE, 'msg' => 'Enrolment data not found'];
            }
            return ['status' => TRUE, 'msg' => 'Enrolment cancelled Successfully'];
        }else {
            $record = $this->getStudentEnrolmentById($id);

            $course = Course::find($record->course_id);
            if( $record ) {
                $oldStatus = $record->status;
                // dd($record);
                if( $course->courseMain->course_type_id == CourseMain::SINGLE_COURSE ) {
                    if( !empty($record->tpgateway_refno) ) {
                        // return ['status' => FALSE, 'msg' => 'No Enrolment found'];
                        // remove from TP Gateway
                        $tpgatewayReq = new TPGatewayService;
                        // first check if in TP Gateway they directly removed this student
                        $enrollment = $tpgatewayReq->getTpgStudentEnrolmentById($record->tpgateway_refno);
                        if( $enrollment['status'] ) {
                            if( $enrollment['data']->status == "Cancelled" ) {
                                // this means it already cancelled
                                $record->status                     = StudentEnrolment::STATUS_CANCELLED;
                                $record->save();

                                /*$assessmentStudent = AssessmentStudent::where('student_id', $record->student_id)->first();
                                if($assessmentStudent) {
                                    $assessmentStudent->restore();
                                }*/

                                if( $course->registeredusercount > 0 ) {
                                    if( $oldStatus != 2 ) {
                                        $course->decrement('registeredusercount');
                                    }
                                    Log::info('registeredusercount increment');
                                    Log::info($record->id);
                                    $course->increment('cancelusercount');
                                }
                                // return $record;
                                return ['status' => TRUE, 'msg' => 'Enrolment cancelled Successfully'];
                            }
                        }
                        // grant calculator
                        $req_data = $tpgatewayReq->createEnrollmentCancelRequest($record->courseRun, $record);
                        // get data from tpgateway
                        $enrollRes = $tpgatewayReq->updateCancelStudentEnrolment($record->tpgateway_refno, $req_data);
                        // $record->save();
                        if( isset($enrollRes->status) && $enrollRes->status == 200 ) {
                            // $record->payment_status             = StudentEnrolment::PAYMENT_STATUS_REFUND;
                            $record->status                     = StudentEnrolment::STATUS_CANCELLED;
                            $record->save();
                            
                            /*$assessmentStudent = AssessmentStudent::where('student_id', $record->student_id)->first();
                            if($assessmentStudent) {
                                $assessmentStudent->restore();
                            }*/
                            
                            if( $course->registeredusercount > 0 ) {
                                if( $oldStatus != 2 ) {
                                    $course->decrement('registeredusercount');
                                }
                                Log::info('registeredusercount increment');
                                Log::info($record->id);
                                $course->increment('cancelusercount');
                            }
                            // return $record;
                            return ['status' => TRUE, 'msg' => 'Enrolment cancelled Successfully'];
                        }
                    } else {
                        // $record->payment_status             = StudentEnrolment::PAYMENT_STATUS_REFUND;
                        $record->status                     = StudentEnrolment::STATUS_CANCELLED;
                        $record->save();
                        
                        /*$assessmentStudent = AssessmentStudent::where('student_id', $record->student_id)->first();
                        if($assessmentStudent) {
                            $assessmentStudent->restore();
                        }*/
                        if( $course->registeredusercount > 0 ) {
                            if( $oldStatus != 2 ) {
                                $course->decrement('registeredusercount');
                            }
                            Log::info('registeredusercount increment');
                            Log::info($record->id);
                            $course->increment('cancelusercount');
                        }
                        // return $record;
                        return ['status' => TRUE, 'msg' => 'Enrolment cancelled Successfully'];
                    }
                } else {
                    // $record->payment_status             = StudentEnrolment::PAYMENT_STATUS_REFUND;
                    $record->status                     = StudentEnrolment::STATUS_CANCELLED;
                    $record->save();

                    /*$assessmentStudent = AssessmentStudent::where('student_id', $record->student_id)->first();
                    if($assessmentStudent) {
                        $assessmentStudent->restore();
                    }*/
                    // cancel enrollment for booster session
                    if( $course->registeredusercount > 0 ) {
                        if( $oldStatus != 2 ) {
                            $course->decrement('registeredusercount');
                        }
                        Log::info('registeredusercount increment');
                        Log::info($record->id);
                        $course->increment('cancelusercount');
                    }
                    // return $record;
                    return ['status' => TRUE, 'msg' => 'Enrolment cancelled Successfully'];
                }
                return ['status' => FALSE, 'msg' => 'Enrolment not cancelled'];
            }
            return ['status' => FALSE, 'msg' => 'Enrolment data not found'];
        }
    }

    public function holdStudentEnrolmentbyID($id)
    {
        $record = $this->getStudentEnrolmentById($id);

        $course = Course::find($record->course_id);
        if( $record ) {
            $record->status                     = StudentEnrolment::STATUS_HOLD;
            $record->save();
            if( $course->registeredusercount > 0 ) {
                $course->decrement('registeredusercount');
            }
            return ['status' => TRUE, 'msg' => 'Student moved to holdlist Successfully'];
        }
        return ['status' => FALSE, 'msg' => 'Enrolment data not found'];
    }


    public function updateAmountPaidByStudentEnrolmentID($id,$amount)
    {
        //$entry_id = $request->get('entry_id');
        $record = $this->getStudentEnrolmentById($id);
        $payment_status = $record->payment_status;
        if(($record->amount_paid + $amount) >= $record->amount) {
            $payment_status = StudentEnrolment::PAYMENT_STATUS_FULL;
        }
        if( $record ) {
            $record->amount_paid             = $record->amount_paid + $amount;
            $record->payment_status          = $payment_status;
            $record->save(); 
            return $record;
        }
        return false;
    }

    public function updateXeroAmountPaidByStudentEnrolmentID($id, $amount)
    {
        //$entry_id = $request->get('entry_id');
        $record = $this->getStudentEnrolmentById($id);
        $payment_status = $record->payment_status;
        if($record->amount_paid == $record->amount) {
            $payment_status = StudentEnrolment::PAYMENT_STATUS_FULL;
        } elseif($record->amount_paid != 0.00) {
            $payment_status = StudentEnrolment::PAYMENT_STATUS_PARTIAL;
        } else {
            $payment_status = StudentEnrolment::PAYMENT_STATUS_PENDING;
        }
        if( $record ) {
            $record->payment_status          = $payment_status;
            $record->save(); 
            return $record;
        } else {
            return false;
        }
    } 

    public function updateAmountPaidChangeByStudentEnrolmentID($id, $amount, $oldAmount)
    {
        $record = $this->getStudentEnrolmentById($id);
        $payment_status = $record->payment_status;
        $newAmt = $record->amount_paid - $oldAmount;
        if(($newAmt + $amount) >= $record->amount) {
            $payment_status = StudentEnrolment::PAYMENT_STATUS_FULL;
        } else {
            $payment_status = StudentEnrolment::PAYMENT_STATUS_PARTIAL;
        }
        if( $record ) {
            $record->amount_paid             = $newAmt + $amount;
            $record->payment_status          = $payment_status;
            $record->save();
            return $record;
        }
        return false;
    }
 
    public function searchStudentEnrolmentAjax($q)
    {
        $studentEnrolments = $this->studentenrolment_model
                            ->whereIn('payment_status', [StudentEnrolment::PAYMENT_STATUS_PENDING, StudentEnrolment::PAYMENT_STATUS_PARTIAL])
                            // ->where('students.name', 'like', '%'.$q.'%')
                            // ->where('email', 'like', '%'.$q.'%')
                            // ->orWhere('student.nric', 'like', '%'.$q.'%')
                            ->search($q)
                            ->limit(7)->get();
        $ret = [];
        foreach ($studentEnrolments as $enrolement) {
            $ret[] = [
                "id"                  => $enrolement->id,
                "text"                => $enrolement->courseRun->courseMain->name." (".$enrolement->courseRun->course_start_date.") ".$enrolement->courseRun->tpgateway_id." ".$enrolement->student->name.", ".$enrolement->student->nric.", ".$enrolement->email,
                "amount"              => $enrolement->amount,
                "paymens"             => $enrolement->payments,
                "xero_invoice_number" => $enrolement->xero_invoice_number,
                "xero_amount"         => $enrolement->xero_amount,
                "xero_paid_amount"    => $enrolement->xero_paid_amount,
                "xero_due_amount"     => $enrolement->xero_due_amount,
                "xero_nett_course_fees" => $enrolement->xero_nett_course_fees,
                "paid_amt"            => $enrolement->payments->where('status', '!=', 1)->sum('fee_amount')
            ];
        }
        return $ret;
    }

    public function searchStudentAjax($q)
    {
        $students = $this->students_model
                            ->where('name', 'like', '%'.$q.'%')
                            ->orWhere('email', 'like', '%'.$q.'%')
                            ->orWhere('nric', 'like', '%'.$q.'%')
                            ->orWhere('mobile_no', 'like', '%'.$q.'%')
                            ->limit(7)->get();
        $ret = [];
        foreach ($students as $student) {
            $ret[] = [
                "id"    => $student->id,
                "text"  => $student->name.", ".$student->nric.", ".$student->email
            ];
        }
        return $ret;
    }

    public function doStudentEnrolmentGrantCheckAgainbyID($id)
    {
        $record = $this->getStudentEnrolmentByIdWithRealtionData($id);
        if( $record->courseRun->courseMain->course_type == CourseMain::COURSE_TYPE_NONWSQ ) {
            return ['status' => FALSE, 'msg' => 'Non-WSQ Course. No grant found'];
        }
        if( $record->courseRun->courseMain->course_type_id != CourseMain::SINGLE_COURSE ) {
            return ['status' => FALSE, 'msg' => 'Not Sync to TPGateway.'];
        }
        //if( is_null($record->isGrantError) || $record->isGrantError == 1 ) {
            // add to TP Gateway
            $tpgatewayReq = new TPGatewayService;
            // grant calculator
            $req_data_grant = $tpgatewayReq->createGrantRequest($record->courseRun, $record);
            // get data from tpgateway
            $grantRes = $tpgatewayReq->checkGrantCalculator($req_data_grant);
            $record->isGrantError = 1;
            $record->grantResponse = json_encode($grantRes);
            $record->save();
            if( isset($grantRes->status) && $grantRes->status == 200 ) {
                if( !empty($grantRes->data)) {
                    // add/update grant for this enrollment
                    foreach( $grantRes->data as $grant ){

                        if($grant->status == 'Completed'){
                            $disbursedDate = $grant->disbursementDate; 
                            Grant::where('grant_refno', $grant->referenceNumber)->update(['disbursement_date' => $disbursedDate]);
                        }
                        else{
                            $grantResByRef = $tpgatewayReq->checkGrantStatus($grant->referenceNumber);
                            if( isset($grantResByRef->status) && $grantResByRef->status == 200 ) {
                                $disbursedDate = isset($grantResByRef->data->disbursementDate) ? $grantResByRef->data->disbursementDate : Carbon::parse($grantResByRef->meta->updatedOn)->format('Y-m-d');
                                Grant::where('grant_refno', $grant->referenceNumber)->update(['disbursement_date' => $disbursedDate]);
                            }
                        }

                        $grantRecord = Grant::updateOrCreate(
                            [
                                'grant_refno'   => $grant->referenceNumber,
                            ],
                            [
                                'student_enrolment_id' => $record->id,
                                'grant_refno' => $grant->referenceNumber,
                                'grant_status' => $grant->status,
                                'scheme_code' => $grant->fundingScheme->code,
                                'scheme_description' => $grant->fundingScheme->description,
                                'component_code' => $grant->fundingComponent->code,
                                'component_description' => $grant->fundingComponent->description,
                                'amount_estimated' => round($grant->grantAmount->estimated, 2),
                                'amount_paid' => round($grant->grantAmount->paid,2),
                                'amount_recovery' => round($grant->grantAmount->recovery,2),
                                //'disbursement_date' => $grant->disbursementDate ?? NULL,
                                'last_sync' => date('Y-m-d'),
                                'TPG_response' => 1,
                                'created_by' => Auth::Id(),
                                'updated_by' => Auth::Id()
                            ]
                        );   
                    }
                }

                //$record->grantEstimated = $grantRes->data[0]->grantAmount->estimated;
                //$record->grantRefNo = $grantRes->data[0]->referenceNumber;
                //$record->grantStatus = $grantRes->data[0]->status;
                $record->isGrantError = 0;
                $record->save();
            }
            if( $record->isGrantError == 1 ) { return ['status' => FALSE, 'msg' => 'Grant Fetch Error']; }
            return ['status' => TRUE, 'msg' => 'Grant Fetched Successfully'];
        //}
        //return ['status' => FALSE, 'msg' => 'Already Fetched'];
    }

    public function doStudentEnrolmentAttendanceAgainbyID($id)
    {
        $record = $this->getStudentEnrolmentByIdWithRealtionData($id);
        if( is_null($record->isAttendanceError) || $record->isAttendanceError == 1 ) {
            // add to TP Gateway
            $tpgatewayReq = new TPGatewayService;
            // $sessionRes = $tpgatewayReq->getCourseSessionsFromTpGateway($result->tpgateway_id,$result->courseMain->reference_number);
            // check all student attendance has been filled
            if( is_null($record->attendance) ) {
                return FALSE;
            }
            $req_data = [];
            $req_data = [
                "uen" => config('settings.tpgateway_uenno'),
                "corppassId" => config('settings.tpgateway_corppassId'),
            ];
            // pending this
            $req_data['course']['referenceNumber'] = $record->courseRun->courseMain->reference_number;
            try {
                $stuEnrol = $this->getStudentEnrolmentById($record->id);
                $isAttendanceError = false;
                $attendanceData = json_decode($record->attendance);
                $attendResData = json_decode($record->attendanceResponse, true);

                foreach( $attendanceData as $key => $att ) {
                    
                    if(!empty($att->att_sync) && $att->att_sync == 1)
                    {
                        continue;
                    }
                    $req_data['course']['sessionID'] = $att->tpgId;
                    $req_data['course']['attendance'] = [
                        "status" => [ "code" => $att->ispresent ? 1 : 2 ]
                    ];
                    $idType = "OT";
                    switch ($record->nationality) {
                        case "Singapore Permanent Resident":
                            $idType = "SB";
                        break;
                        case "Singapore Citizen":
                            $idType = "SP";
                        break;
                        case "Non-Singapore Citizen/PR":
                            $idType = "SO";
                        break;
                    }
                    $req_data['course']['attendance']['trainee'] = [
                        "id" => $record->student->nric,
                        "name" => $record->student->name,
                        "idType" => [ "code" => $idType ],
                        "email" => $record->email,
                        "contactNumber" => [
                            "mobile" => $record->mobile_no,
                            "areaCode" => null,
                            // "countryCode" => "+65"
                            "countryCode" => 65
                        ],
                        "surveyLanguage" => [ "code" => "EL" ],
                        "numberOfHours" => 4
                    ];
                    // submit to tpgateway
                    $attendRes = $tpgatewayReq->courseAttendance($record->courseRun->tpgateway_id, $req_data);
                    if( isset($attendRes->status) && $attendRes->status == 200 ) {
                        $attendanceData[$key]->att_sync = 1;
                    } else {
                        $isAttendanceError = true;
                        $attendanceData[$key]->att_sync = 2;
                    }

                    $attendResData[$att->tpgId] = $attendRes;
                }
                
                if($isAttendanceError){
                    $stuEnrol->isAttendanceError = 1;
                }
                else{
                    $stuEnrol->isAttendanceError = 0;
                }

                $stuEnrol->attendanceResponse = json_encode($attendResData);
                $stuEnrol->attendance = json_encode($attendanceData);
                // update the student enrolment data
                $stuEnrol->save();
                   

            } catch (\Exception $e) {
                \Log::info($e->getMessage());
            }
            if( $stuEnrol->isAttendanceError == 1 ) { return FALSE; }
            return TRUE;
        }
        return FALSE;
    }

    public function doStudentEnrolmentAssessmentAgainbyID($id)
    {
        $record = $this->getStudentEnrolmentByIdWithRealtionData($id);
        // add to TP Gateway
        $req_data = [];
        $req_data['assessment'] = [
            'trainingPartner' => [
                "code" => config('settings.tpgateway_code'),
                "uen" => config('settings.tpgateway_uenno')
            ],
            "course" => [
                "referenceNumber" => $record->courseRun->courseMain->reference_number,
                "run" => [ "id" => $record->courseRun->tpgateway_id ]
            ],
        ];
        // check all student Assessment has been filled
        if( is_null($record->assessment) ) {
            return FALSE;
        }
        try {
            $req_data['assessment']['trainee'] = [
                "id" => $record->student->nric,
                "idType" => $record->nationality == "Non-Singapore Citizen/PR" ? "Others" : "NRIC",
                "fullName" => $record->student->name,
            ];
            $req_data['assessment']['result'] = $record->assessment == 'c' ? 'Pass' : 'Fail';
            $req_data['assessment']['assessmentDate'] = $record->assessment_date;
            $req_data['assessment']['skillCode'] = $record->courseRun->courseMain->skill_code;
            $req_data['assessment']['conferringInstitute'] = [ "code" => config('settings.tpgateway_code')];
            // submit to tpgateway
            $tpgatewayReq = new TPGatewayService;
            $stuEnrol = $this->getStudentEnrolmentById($record->id);

            $assessRes = $tpgatewayReq->courseAssessments($req_data);
            if( isset($assessRes->status) && $assessRes->status == 200 ) {
                $stuEnrol->isAssessmentError = 0;
                $stuEnrol->assessment_sync = 2;
            } else {
                $stuEnrol->isAssessmentError = 1;
                $stuEnrol->assessment_sync = 1;
            }
            $stuEnrol->assessmentResponse = json_encode($assessRes);
            // update the student enrolment data
            $stuEnrol->save();
            if( $stuEnrol->isAssessmentError == 1 ) { return FALSE; }
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
        }
        return TRUE;

        return FALSE;
    }

    public function doStudentRefresherAttendanceAgainbyID($id)
    {
        $courseService = new \App\Services\CourseService;
        $record = $courseService->getRefreshersById($id);
        if( is_null($record->isAttendanceError) || $record->isAttendanceError == 1 ) {
            // add to TP Gateway
            $tpgatewayReq = new TPGatewayService;
            // $sessionRes = $tpgatewayReq->getCourseSessionsFromTpGateway($result->tpgateway_id,$result->courseMain->reference_number);
            // check all student attendance has been filled
            if( is_null($record->attendance) ) {
                return FALSE;
            }
            $req_data = [];
            $req_data = [
                "uen" => config('settings.tpgateway_uenno'),
                "corppassId" => config('settings.tpgateway_corppassId'),
            ];
            // pending this
            $req_data['course']['referenceNumber'] = $record->course->courseMain->reference_number;
            try {
                $isAttendanceError = false;
                $attendanceData = json_decode($record->attendance);
                $attendResData = json_decode($record->attendanceResponse, true);

                foreach( $attendanceData as $key => $att ) {

                    if(!empty($att->att_sync) && $att->att_sync == 1)
                    {
                        continue;
                    }
                    $req_data['course']['sessionID'] = $att->tpgId;
                    $req_data['course']['attendance'] = [
                        "status" => [ "code" => $att->ispresent ? 1 : 2 ]
                    ];
                    $idType = "OT";
                    switch ($record->student->nationality) {
                        case "Singapore Permanent Resident":
                            $idType = "SB";
                        break;
                        case "Singapore Citizen":
                            $idType = "SP";
                        break;
                        case "Non-Singapore Citizen/PR":
                            $idType = "SO";
                        break;
                    }
                    $req_data['course']['attendance']['trainee'] = [
                        "id" => $record->student->nric,
                        "name" => $record->student->name,
                        "idType" => [ "code" => $idType ],
                        "email" => $record->student->email,
                        "contactNumber" => [
                            "mobile" => $record->student->mobile_no,
                            "areaCode" => null,
                            // "countryCode" => "+65"
                            "countryCode" => 65
                        ],
                        "surveyLanguage" => [ "code" => "EL" ],
                        "numberOfHours" => 4
                    ];
                    // submit to tpgateway
                    $attendRes = $tpgatewayReq->courseAttendance($record->course->tpgateway_id, $req_data);
                    if( isset($attendRes->status) && $attendRes->status == 200 ) {
                        $attendanceData[$key]->att_sync = 1;
                    } else {
                        $isAttendanceError = true;
                        $attendanceData[$key]->att_sync = 2;
                    }

                    $attendResData[$att->tpgId] = $attendRes;
                }

                if($isAttendanceError) {
                    $record->isAttendanceError = 1;
                } else {
                    $record->isAttendanceError = 0;
                }

                $record->attendanceResponse = json_encode($attendResData);
                $record->attendance = json_encode($attendanceData);
                // update the student enrolment data
                $record->save();


            } catch (\Exception $e) {
                \Log::info($e->getMessage());
            }
            if( $record->isAttendanceError == 1 ) { return FALSE; }
            return TRUE;
        }
        return FALSE;
    }

    public function doStudentRefresherAssessmentAgainbyID($id)
    {
        $courseService = new \App\Services\CourseService;
        $record = $courseService->getRefreshersById($id);
        // add to TP Gateway
        $req_data = [];
        $req_data['assessment'] = [
            'trainingPartner' => [
                "code" => config('settings.tpgateway_code'),
                "uen" => config('settings.tpgateway_uenno')
            ],
            "course" => [
                "referenceNumber" => $record->course->courseMain->reference_number,
                "run" => [ "id" => $record->course->tpgateway_id ]
            ],
        ];
        // check all student Assessment has been filled
        if( is_null($record->assessment) ) {
            return FALSE;
        }
        try {
            $req_data['assessment']['trainee'] = [
                "id" => $record->student->nric,
                "idType" => $record->student->nationality == "Non-Singapore Citizen/PR" ? "Others" : "NRIC",
                "fullName" => $record->student->name,
            ];
            $req_data['assessment']['result'] = $record->assessment == 'c' ? 'Pass' : 'Fail';
            $req_data['assessment']['assessmentDate'] = $record->assessment_date;
            $req_data['assessment']['skillCode'] = $record->course->courseMain->skill_code;
            $req_data['assessment']['conferringInstitute'] = [ "code" => config('settings.tpgateway_code')];
            // submit to tpgateway
            $tpgatewayReq = new TPGatewayService;

            $assessRes = $tpgatewayReq->courseAssessments($req_data);
            if( isset($assessRes->status) && $assessRes->status == 200 ) {
                $record->isAssessmentError = 0;
                $record->assessment_sync = 2;
            } else {
                $record->isAssessmentError = 1;
                $record->assessment_sync = 1;
            }
            $record->assessmentResponse = json_encode($assessRes);
            // update the student enrolment data
            $record->save();
            if( $record->isAssessmentError == 1 ) { return FALSE; }
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
        }
        return TRUE;

        return FALSE;
    }

    // Api functions
    public function addStudentEnrolmentFromApi($request, $xeroCredentials)
    {
        \Log::info('add student from wp', [$request->all()]);
        // check student is there in student table or not
        $nric = $request->get('nric');
        $student = Student::where('nric', $nric)->first();
        $studentId = 0;
        $xeroServiceReq = new XeroService($xeroCredentials);
        $xeroId = NULL;
        // if( empty($student->id) || is_null($student->xero_id) ) {
        //     // add student/contact to xero account
        //     try {
        //         $resObj = $xeroServiceReq->createContactsXero(
        //             $request->get('name'),
        //             $request->get('mobile_no'),
        //             $request->get('email'));
        //         if( $resObj ) {
        //             $xeroId = $resObj->getContacts()[0]->getContactId();
        //         }
        //     } catch (Exception $e) {
        //         // log to error
        //     }
        // }
        if( isset($student->id) ) {
            $studentId             = $student->id;
            $student->company_sme                 = $request->get('company_sme');
            $student->nationality                 = $request->get('nationality');
            $student->email                       = $request->get('email');
            $student->mobile_no                   = $request->get('mobile_no');
            $student->dob                         = $request->get('dob');
            if( $request->has('company_name') ) {
                $student->company_name            = $request->get('company_name');
            }
            if( $request->has('company_uen') ) {
                $student->company_uen                 = $request->get('company_uen');
            }
            if( $request->has('company_contact_person') ) {
                $student->company_contact_person      = $request->get('company_contact_person');
            }
            if( $request->has('company_contact_person_email') ) {
                $student->company_contact_person_email = $request->get('company_contact_person_email');
            }
            if( $request->has('company_contact_person_number') ) {
                $student->company_contact_person_number = $request->get('company_contact_person_number');
            }
            if( $request->has('billing_address') ) {
                $student->billing_address             = $request->get('billing_address');
            }
            $student->meal_restrictions          = $request->get('meal_restrictions');
            $student->meal_restrictions_type     = $request->get('meal_restrictions_type');
            $student->meal_restrictions_other    = $request->get('meal_restrictions_other');
            if( is_null($student->xero_id) ) {
                // Temporary comment code of saved xero ID as 00000000-0000-0000-0000-000000000000
                // $student->xero_id               = $xeroId;
            }
            // $student->save();
            
            $user = User::where('username', $student->nric)->first();
            if($user){
                $user->name          = $request->get('name');
                $user->email         = $request->get('email');
                $user->phone_number  = $request->get('phone_number');
                $user->role          = 'student';
                $user->status        = 1;
                $user->dob           = $request->get('dob');
                $user->save();
                $user->assignRole('student');
                // save user id after user creation
                $student->user_id = $user->id;
            }
            $student->save();

        } else {
            // add student in master table
            $student = new Student;
            $student->name                        = $request->get('name');
            $student->nric                        = trim($request->get('nric'));
            $student->company_sme                 = $request->get('company_sme');
            $student->nationality                 = $request->get('nationality');
            $student->email                       = $request->get('email');
            $student->mobile_no                   = trim($request->get('mobile_no'));
            $student->dob                         = $request->get('dob');
            if( $request->has('company_name') && !empty($request->get('company_name')) ) {
                $student->company_name            = $request->get('company_name');
            }
            if( $request->has('company_uen') && !empty($request->get('company_uen')) ) {
                $student->company_uen                 = $request->get('company_uen');
            }
            if( $request->has('company_contact_person') && !empty($request->get('company_contact_person')) ) {
                $student->company_contact_person      = $request->get('company_contact_person');
            }
            if( $request->has('company_contact_person_email') && !empty($request->get('company_contact_person_email')) ) {
                $student->company_contact_person_email = $request->get('company_contact_person_email');
            }
            if( $request->has('company_contact_person_number') && !empty($request->get('company_contact_person_number')) ) {
                $student->company_contact_person_number = $request->get('company_contact_person_number');
            }
            if( $request->has('billing_address') ) {
                $student->billing_address             = $request->get('billing_address');
            }
            $student->meal_restrictions           = $request->get('meal_restrictions');
            $student->meal_restrictions_type      = $request->get('meal_restrictions_type');
            $student->meal_restrictions_other     = $request->get('meal_restrictions_other');
            // $student->xero_id                     = $xeroId;
            $student->created_by                  = 1;
            $student->updated_by                  = 1;
            $student->save();
            $studentId                            = $student->id;

            //Add student for assessment portal assessment-student table
            // $assessmentStudent              = new AssessmentStudent;
            // $assessmentStudent->student_id  = $studentId;
            // $assessmentStudent->user_id     = $request->get('email');
            // $assessmentStudent->password    = Hash::make(trim($request->get('nric')));
            // $assessmentStudent->imp_data    = trim($request->get('nric'));
            // $assessmentStudent->save();

            //Add student for assessment portal on user table
            
            $studentUser = new User;
            $studentUser->name          = $request->get('name');
            $studentUser->email         = $request->get('email');
            $studentUser->username      = strtoupper(trim($request->get('nric')));
            $studentUser->phone_number  = $request->get('phone_number');
            $studentUser->role          = 'student';
            $studentUser->status        = 1;
            $studentUser->password      = Hash::make(strtoupper(trim($request->get('nric'))));
            $studentUser->dob           = $request->get('dob');
            $studentUser->save();
            $studentUser->assignRole('student');
            // save user id after user creation
            $student->user_id = $studentUser->id;
            $student->update();
        }
        // get data from session table
        // $courseId = $request->get('training_course_dates');
        // $course = Course::findOrFail($courseId);
        $myinputs = $request->all();
        $courseRef = [];
        foreach( $myinputs as $inputkey => $input ) {
            $tmp = explode('_!!_', $inputkey);
            if( isset($tmp[1]) && !in_array($tmp[1], $courseRef) && !empty($tmp[1])) {
                array_push($courseRef, $tmp[1]);
            }
        }
        \Log::info('Course enrolment data start');
        \Log::info(print_r($courseRef, true));
        \Log::info('Course enrolment data end');
        // add it in enrolement table here
        foreach( $courseRef as $key => $courserefNo ) {
            $isCourseProgramType = false;
            // $isProgramApplicationFees = false;

            $record = new StudentEnrolment;
            $record->student_id             = $studentId;

            if( !empty($request->get('facetofaceclass_!!_'.$courserefNo)) ) {
                $course_id = $request->get('facetofaceclass_!!_'.$courserefNo);
            } else {
                $course_id = $request->get('onelineclass_!!_'.$courserefNo);
            }
            if( empty($course_id) ) {
                continue;
            }
            $record->course_id                  = $course_id;
            $record->sponsored_by_company       = $request->get('sponsored_by_company');
            $record->company_sme                = $request->get('company_sme');
            $record->nationality                = $request->get('nationality');
            $record->age                        = $request->get('age');
            $record->learning_mode              = $request->get('learning_mode');
            $record->email                      = $request->get('email');
            $record->mobile_no                  = $request->get('mobile_no');
            $record->dob                        = $request->get('dob');
            $record->education_qualification    = $request->get('education_qualification');
            // $record->designation                = $request->get('designation');
            // $record->salary                     = $request->get('salary');
            $record->company_name               = $request->get('company_name');
            $record->company_uen                = $request->get('company_uen');
            $record->company_contact_person     = $request->get('company_contact_person');
            $record->company_contact_person_email = $request->get('company_contact_person_email');
            $record->company_contact_person_number = $request->get('company_contact_person_number');
            $record->billing_email              = $request->get('billing_email');
            $record->billing_address            = $request->get('billing_address');
            $record->billing_zip                = $request->get('billing_zip');
            $record->billing_country            = $request->get('billing_country');
            $record->remarks                    = $request->get('remarks');
            /*$record->payment_mode               = $request->get('payment_mode');*/
            if( !empty($request->get('payment_mode_company')) ) {
                $record->payment_mode_company       = $request->get('payment_mode_company');
            }
            if( !empty($request->get('payment_mode_individual')) ) {
                $record->payment_mode_company       = $request->get('payment_mode_individual');
            }
            if( !empty($request->get('payment_mode_other_paying_by')) ) {
                $record->payment_mode_company       = $request->get('payment_mode_other_paying_by');
            }
            $record->payment_mode_individual    = $request->get('payment_mode_individual');
            $record->other_paying_by            = $request->get('payment_mode_other_paying_by');
            // $record->amount                     = cleanAmount($request->get('amount'));
            // $record->xero_nett_course_fees      = cleanAmount($request->get('amount'));

            if( !empty($request->get('coursefees_!!_'.$courserefNo)) ) {
                $record->amount                 = cleanAmount($request->get('coursefees_!!_'.$courserefNo));
                $record->xero_nett_course_fees  = cleanAmount($request->get('coursefees_!!_'.$courserefNo));
            } else {
                $record->amount                 = cleanAmount($request->get('amount'));
                $record->xero_nett_course_fees  = cleanAmount($request->get('amount'));
                // $course_id = $request->get('onelineclass_!!_'.$courserefNo);
            }

            if($request->has('payment_type')){
                $record->payment_type = $request->get('payment_type');
            }


            $record->meal_restrictions          = $request->get('meal_restrictions');
            $record->meal_restrictions_type     = $request->get('meal_restrictions_type');
            $record->computer_navigation_skill  = $request->get('computer_navigation_skill') ? 1 : 0;
            $record->course_brochure_determined = $request->get('course_brochure_determined') ? 1 : 0;

            $record->payment_status             = $request->get('payment_status');
            $record->gform_id                   = $request->get('gform_id');
            $record->entry_id                   = $request->get('entry_id');
            $record->due_date                    = Carbon::now()->addDays(7);
            $record->created_by                 = 1;
            $record->updated_by                 = 1;


            if(!empty($request->get('tms_platform_type'))){
                $record->program_type_id =  $request->get('tms_platform_type');
                $isCourseProgramType = true;
            }

            if($request->has('application_fee') && !$isCourseProgramType){
                $record->application_fee = cleanAmount($request->get('application_fee'));
            }

            $record->save();

            // increment the source schedule
            $course = Course::find($record->course_id);
            Log::info('registeredusercount increment');
            Log::info($record->id);
            $course->increment('registeredusercount');
            
            //Create invoice data
            //Create contact id here
            $grantCalculationService = new GrantCalculationService;
            $invoiceData = $grantCalculationService->generateInvoice($record->id, $isCourseProgramType, $record->entry_id);
            Log::info('=======================================================');
            Log::info('Course MAIN ID ==>> '.$courserefNo);
            Log::info('Course run id ==>> '.$course_id);
            Log::info('Enrolment Id ==>> '. $record->id);
            Log::info('Form ID ==>> '.$request->get('entry_id'));
            Log::info('Wordpress Check Contdition: ' . $request->get('create_xero_invoice'));
            Log::info('=======================================================');
            Log::info("Invoice created from TMS");
            if($invoiceData != false){
                Log::info("TMS->invoice_id ==> " . $record->id);
                // Log::info(print_r($invoiceData, true));
                Log::info("Now check code code is working or not");
                if($request->get('create_xero_invoice')){
                    Log::info("Xero Syncing Call Start");
                    $returnType = false;
                    $checkApi = true;
                    $xeroService = new XeroService($xeroCredentials);
                    $xeroData = $xeroService->createInvoiceFromXero($invoiceData, $returnType, $checkApi);
                    $record->xero_invoice_number = $xeroData;
                    $record->master_invoice = StudentEnrolment::XERO_SYNC;
                    $record->save();
                    Log::info("Xero Invoice Number ". $xeroData);
                    Log::info("Xero Syncing Call End");
                }
                Log::info("Invoice only created from TMS");
            }
            Log::info('=======================================================');

            // check if is booster session than skip xero invoice and tpgateway sync
            $_needtoSkip = false;
            if( $course->courseMain->course_type_id != CourseMain::SINGLE_COURSE ) {
                $_needtoSkip = true;
                $record->status = StudentEnrolment::STATUS_ENROLLED;
                $record->update();
                /*$assessmentStudentData = AssessmentStudent::where('student_id', $record->student_id)->first();
                $deletedAssessmentStudent = AssessmentStudent::where('student_id', $record->student_id)->withTrashed()->first();
                if($deletedAssessmentStudent){
                    $deletedAssessmentStudent->restore();
                } elseif ($assessmentStudentData == null){
                    $addForAssessment             = new AssessmentStudent;
                    $addForAssessment->student_id = $record->student_id;
                    $addForAssessment->user_id    = $record->email;
                    $addForAssessment->password   = Hash::make($student->nric);
                    $addForAssessment->imp_data   = $student->nric;
                    $addForAssessment->save();
                }*/
            }

            // check if this trainee has soft booked then make it completed
            $softBooking = CourseSoftBooking::where('course_id', $record->course_id)
                            ->where('nric', $request->get('nric'))->first();
            if( isset($softBooking->id) && $softBooking->status == CourseSoftBooking::STATUS_PENDING ) {
                $softBooking->status = CourseSoftBooking::STATUS_BOOKED;
                $softBooking->save();
            }

            if( !$_needtoSkip ) {
                /* try {
                    $tpgatewayReq = new TPGatewayService;
                    if( $course->tpgateway_id != "" && $course->tpgateway_id != "#N/A" ) {
                        // enrolment
                        $req_data = $tpgatewayReq->createEnrolmentRequest($course, $record);

                        // add to TP Gateway
                        $enrolRes = $tpgatewayReq->studentEnrolment($req_data);
                        if( $record->enrollToTPG != 1 ) {
                            $record->enrollToTPG = 0;
                        }
                        $record->enrollmentResponse = json_encode($enrolRes);
                        $record->save();
                        if( isset($enrolRes->status) && $enrolRes->status == 200 ) {
                            $record->tpgateway_refno = $enrolRes->data->enrolment->referenceNumber;
                            $record->enrollToTPG = 1;
                            $record->save();
                            // grant calculator
                            $req_data_grant = $tpgatewayReq->createGrantRequest($course, $record);
                            // get data from tpgateway
                            $grantRes = $tpgatewayReq->checkGrantCalculator($req_data_grant);
                            $record->isGrantError = 1;
                            $record->grantResponse = json_encode($grantRes);
                            $record->save();
                            if( isset($grantRes->status) && $grantRes->status == 200 ) {
                                $record->grantEstimated = $grantRes->data[0]->grantAmount->estimated;
                                $record->grantRefNo = $grantRes->data[0]->referenceNumber;
                                $record->grantStatus = $grantRes->data[0]->status;
                                $record->isGrantError = 0;
                                $record->save();
                            }
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error("TP Gateway error course Id: ".$record->course_id.", ".$e->getMessage(), [$e]);
                } */
            }

            // add this invoice to xero
            // try {
            //     // create lineitems
            //     $xeroLineItems = [];
            //     $lineItems = XeroCourseLineItems::where('course_main_id', $course->course_main_id)->get();
            //     if( !empty($lineItems) ) {
            //         foreach( $lineItems as $item ) {
            //             $lineItem = $xeroServiceReq->createLineItem($item);
            //             array_push($xeroLineItems, $lineItem);
            //         }
            //         $resObj = $xeroServiceReq->createInvoiceXero(
            //             // $student->xero_id,
            //             $xeroLineItems,
            //             $course->courseMain->branding_theme_id);
            //         \Log::error("Xero invoice obj wp, ", [$resObj]);
            //         if( $resObj ) {
            //             // Temporary comment code of saved xero ID
            //             // if( $resObj->getInvoices()[0]->getInvoiceId() != "00000000-0000-0000-0000-000000000000") {
            //             //     $record->xero_invoice_id = $resObj->getInvoices()[0]->getInvoiceId();
            //             //     $record->xero_invoice_number = $resObj->getInvoices()[0]->getInvoiceNumber();
            //             //     $record->save();
            //             // }
            //             // $xeroId = $resObj->getContacts()[0]->getContactId();
            //         }
            //     }
            // } catch (\Exception $e) {
            //     // log to error
            //     \Log::error("Xero invoice create wp, ".$e->getMessage(), [$e]);
            // }

        }

        return TRUE;
    }

    public function importStudentEnrolment($studentEnroll, $payment)
    {

        Log::info("In service function import student enrol");
        Log::info(print_r($studentEnroll, true));
        Log::info("Data end");
        // check student is there in student table or not
        $student = Student::where('nric', $studentEnroll['nric'])->first();
        $record = $this->studentenrolment_model;
        if( is_null($student) ) {
            Log::info("New student add into master student table");
            // add student in master table
            $student = new Student;
            $student->name                        = $studentEnroll['name'];
            $student->nric                        = $studentEnroll['nric'];
            $student->created_by                  = 1;
            $student->updated_by                  = 1;
        }
        // update student details
        $student->company_sme                 = $studentEnroll['company_sme'];
        $student->nationality                 = $studentEnroll['nationality'];
        $student->email                       = $studentEnroll['email'];
        $student->mobile_no                   = $studentEnroll['mobile_no'];
        $student->dob                         = $studentEnroll['dob'];

        if( !empty($studentEnroll['company_name']) ) {
            $student->company_name                  = $studentEnroll['company_name'];
        }
        if( !empty($studentEnroll['company_uen']) ) {
            $student->company_uen                   = $studentEnroll['company_uen'];
        }
        if( !empty($studentEnroll['company_contact_person']) ) {
            $student->company_contact_person        = $studentEnroll['company_contact_person'];
        }
        if( !empty($studentEnroll['company_contact_person_email']) ) {
            $student->company_contact_person_email  = $studentEnroll['company_contact_person_email'];
        }
        if( !empty($studentEnroll['company_contact_person_number']) ) {
            $student->company_contact_person_number = $studentEnroll['company_contact_person_number'];
        }
        if( !empty($studentEnroll['billing_address']) ) {
            $student->billing_address               = $studentEnroll['billing_address'];
        }
        // $student->save();

        $user = User::where('username', $studentEnroll['nric'])->first();
        if(!$user){
            $user = new User;
            $user->username      = strtoupper(trim($studentEnroll['nric']));
            $user->password      = Hash::make(strtoupper(trim($studentEnroll['nric'])));
        }
        $user->name          = $studentEnroll['name'];
        $user->email         = $studentEnroll['email'];
        $user->phone_number  = $studentEnroll['mobile_no'];
        $user->dob           = $studentEnroll['dob'];
        $user->status        = 1;
        $user->role          = 'student';
        $user->save();
        $user->assignRole('student');
        // save user id after user creation
        $student->user_id = $user->id;
        $student->save();
        // dd($student);

        // $courseId = $request->get('training_course_dates');
        // add it in enrolement table here
        $record->student_id                 = $student->id;
        $record->course_id                  = $studentEnroll['course_id'];
        $record->sponsored_by_company       = $studentEnroll['sponsored_by_company'];
        $record->company_sme                = $studentEnroll['company_sme'];
        $record->nationality                = $studentEnroll['nationality'];
        $record->learning_mode              = $studentEnroll['learning_mode'];
        $record->age                        = $studentEnroll['age'];
        $record->email                      = $studentEnroll['email'];
        $record->mobile_no                  = $studentEnroll['mobile_no'];
        $record->dob                        = $studentEnroll['dob'];
        // $record->education_qualification    = $request->get('education_qualification');
        $record->company_name               = $studentEnroll['company_name'];
        $record->company_uen                = $studentEnroll['company_uen'];
        $record->company_contact_person     = $studentEnroll['company_contact_person'];
        $record->company_contact_person_email = $studentEnroll['company_contact_person_email'];
        $record->company_contact_person_number = $studentEnroll['company_contact_person_number'];
        // $record->billing_email              = $request->get('billing_email');
        $record->billing_address            = $studentEnroll['billing_address'];
        // $record->billing_zip                = $request->get('billing_zip');
        // $record->billing_country            = $request->get('billing_country');
        $record->remarks                    = $studentEnroll['remarks'];
        $record->amount                     = cleanAmount($studentEnroll['amount']);
        // $record->xero_nett_course_fees      = cleanAmount($request->get('amount'));
        $record->assessment                 = $studentEnroll['assessment'];

        if( !empty($studentEnroll['billing_email']) ) {
            $record->billing_email = $studentEnroll['billing_email'];
        }
        if( !empty($studentEnroll['xero_invoice_number']) ) {
            $record->xero_invoice_number = $studentEnroll['xero_invoice_number'];
        }
        

        if( !empty($studentEnroll['student_enrollment_id']) ) {
            $record->tpgateway_refno        = $studentEnroll['student_enrollment_id'];
        }

        // $record->meal_restrictions          = $request->get('meal_restrictions');
        // $record->meal_restrictions_type     = $request->get('meal_restrictions_type');
        // $record->meal_restrictions_other    = $request->get('meal_restrictions_other');
        $record->computer_navigation_skill  = 1;
        $record->course_brochure_determined = 1;

        $record->payment_status             = $studentEnroll['payment_status'];
        $record->payment_tpg_status         = $studentEnroll['payment_status'];

        $record->created_by                 = 1;
        $record->updated_by                 = 1;
        $record->save();

        // increment the source schedule
        $course = Course::find($record->course_id);
        Log::info('registeredusercount increment');
        Log::info($record->id);
        $course->increment('registeredusercount');

        if( count($payment) > 0 ) {
            // add payment for this enrollment
            $paymetRecord = new \App\Models\Payment;
            $paymetRecord->student_enrolments_id      = $record->id;
            $paymetRecord->payment_mode               = $payment['payment_mode'];
            $paymetRecord->payment_method             = $payment['payment_mode'];
            $paymetRecord->payment_date               = $payment['payment_date'];
            $paymetRecord->fee_amount                 = $payment['fee_amount'];
            $paymetRecord->payment_remark             = $payment['payment_remark'];
            $paymetRecord->transaction_id             = \Illuminate\Support\Str::random(20);
            $paymetRecord->created_by                 = 1;
            $paymetRecord->updated_by                 = 1;
            $paymetRecord->save();
        }

        // Add Student Enrolment to TPG start
        $_needtoSkip = false; // check if is booster session than skip tpgateway sync

        if( $course->courseMain->course_type_id != CourseMain::SINGLE_COURSE ) {
            $_needtoSkip = true;
            $record->status = StudentEnrolment::STATUS_ENROLLED;
            $record->save();

            //Add student as assessment student
            /*$assessmentStudentData = AssessmentStudent::where('student_id', $record->student_id)->first();
            $deletedAssessmentStudent = AssessmentStudent::where('student_id', $record->student_id)->withTrashed()->first();
            if($deletedAssessmentStudent){
                $deletedAssessmentStudent->restore();
            } elseif ($assessmentStudentData == null){
                $addForAssessment             = new AssessmentStudent;
                $addForAssessment->student_id = $record->student_id;
                $addForAssessment->user_id    = $record->email;
                $addForAssessment->password   = Hash::make($student->nric);
                $addForAssessment->imp_data   = $student->nric;
                $addForAssessment->save();
            }*/
        }
        if( $course->courseMain->course_type == CourseMain::COURSE_TYPE_NONWSQ ) {
            $_needtoSkip = true;
            $record->status = StudentEnrolment::STATUS_ENROLLED;
            $record->save();

            //Add student as assessment student
            /*$assessmentStudentData = AssessmentStudent::where('student_id', $record->student_id)->first();
            $deletedAssessmentStudent = AssessmentStudent::where('student_id', $record->student_id)->withTrashed()->first();
            if($deletedAssessmentStudent){
                $deletedAssessmentStudent->restore();
            } elseif ($assessmentStudentData == null){
                $addForAssessment             = new AssessmentStudent;
                $addForAssessment->student_id = $record->student_id;
                $addForAssessment->user_id    = $record->email;
                $addForAssessment->password   = Hash::make($student->nric);
                $addForAssessment->imp_data   = $student->nric;
                $addForAssessment->save();
            }*/
        }

        if( !$_needtoSkip ) {
            $tpgatewayReq = new TPGatewayService;
            if( $course->tpgateway_id != "" && $course->tpgateway_id != "#N/A" ) {
                // enrolment
                $req_data = $tpgatewayReq->createEnrolmentRequest($course, $record);
                $enrolRes = $tpgatewayReq->studentEnrolment($req_data);
                $record->enrollmentResponse = json_encode($enrolRes);
                $record->save();

                if( isset($enrolRes->status) && $enrolRes->status == 200 ) {
                    $record->tpgateway_refno = $enrolRes->data->enrolment->referenceNumber;
                    $record->status = StudentEnrolment::STATUS_ENROLLED;
                    $record->save();

                    //Add student as assessment student
                    /*$assessmentStudentData = AssessmentStudent::where('student_id', $record->student_id)->first();
                    $deletedAssessmentStudent = AssessmentStudent::where('student_id', $record->student_id)->withTrashed()->first();
                    if($deletedAssessmentStudent){
                        $deletedAssessmentStudent->restore();
                    } elseif ($assessmentStudentData == null){
                        $addForAssessment             = new AssessmentStudent;
                        $addForAssessment->student_id = $record->student_id;
                        $addForAssessment->user_id    = $record->email;
                        $addForAssessment->password   = Hash::make($student->nric);
                        $addForAssessment->imp_data   = $student->nric;
                        $addForAssessment->save();
                    }*/

                    // grant calculator
                    $req_data_grant = $tpgatewayReq->createGrantRequest($course, $record);
                    // get data from tpgateway
                    $grantRes = $tpgatewayReq->checkGrantCalculator($req_data_grant);

                    $record->isGrantError = 1;
                    $record->grantResponse = json_encode($grantRes);
                    $record->save();
                    if( isset($grantRes->status) && $grantRes->status == 200 ) {
                        // after grant approved we have to calculate nett fees
                        // $record->grantEstimated = $grantRes->data[0]->grantAmount->estimated;
                        // $record->grantRefNo = $grantRes->data[0]->referenceNumber;
                        // $record->grantStatus = $grantRes->data[0]->status;
                        if( !empty($grantRes->data)) {
                            // add grant for this enrollment
                            foreach( $grantRes->data as $grant ){
                                $grantRecord = new Grant;
                                $grantRecord->student_enrolment_id      = $record->id;
                                $grantRecord->grant_refno               = $grant->referenceNumber;
                                $grantRecord->grant_status              = $grant->status;
                                $grantRecord->scheme_code               = $grant->fundingScheme->code;
                                $grantRecord->scheme_description        = $grant->fundingScheme->description;
                                $grantRecord->component_code            = $grant->fundingComponent->code;
                                $grantRecord->component_description     = $grant->fundingComponent->description;
                                $grantRecord->amount_estimated          = $grant->grantAmount->estimated;
                                $grantRecord->amount_paid               = $grant->grantAmount->paid;
                                $grantRecord->amount_recovery           = $grant->grantAmount->recovery;
                                $grantRecord->created_by                = Auth::Id();
                                $grantRecord->updated_by                = Auth::Id();
                                $grantRecord->save();
                            }
                        }
                        $record->isGrantError = 0;
                        $record->save();
                        //correct name from TPG
                        $getName = $this->getNameOnNRIC($record->id);
                    }
                }
            }
        }
        // Add Student Enrolment to TPG end
        return $record;
    }

    public function addStudentEnrolmentFromTPG($enrolment, $grant, $xeroCredentials)
    {
        // dd($enrolment);
        $res = [ 'status' => false ];
        $nationality = "";
        $sponsoredByCompany = "";
        if( strtolower($enrolment->trainee->idType->type) == "others" ) {
            $nationality = "Non-Singapore Citizen/PR";
        }
        if( strtolower($enrolment->trainee->sponsorshipType) == "individual" ) {
            $sponsoredByCompany = "No (I'm signing up as an individual)";
        }
        if( strtolower($enrolment->trainee->sponsorshipType) == "employer" ) {
            $sponsoredByCompany = "Yes";
        }
        // check student is there in student table or not
        $nric = $enrolment->trainee->id;
        $student = Student::where('nric', $nric)->first();
        $record = $this->studentenrolment_model;
        $xeroServiceReq = new XeroService($xeroCredentials);
        $xeroId = NULL;
        if( empty($student->id) || is_null($student->xero_id) ) {
            // add student/contact to xero account
            try {
                $resObj = $xeroServiceReq->createContactsXero(
                    $enrolment->trainee->fullName,
                    $enrolment->trainee->contactNumber->phoneNumber,
                    $enrolment->trainee->email->full);
                if( $resObj ) {
                    $xeroId = $resObj->getContacts()[0]->getContactId();
                }
            } catch (Exception $e) {
                // log to error
            }
        }
        if( isset($student->id) ) {
            $record->student_id             = $student->id;
            // update student details
            $student->company_sme                 = "";
            $student->nationality                 = $nationality;
            $student->email                       = $enrolment->trainee->email->full;
            $student->mobile_no                   = $enrolment->trainee->contactNumber->phoneNumber;
            $student->dob                         = $enrolment->trainee->dateOfBirth;
            if( !empty($enrolment->trainee->employer->name) ) {
                $student->company_name                 = $enrolment->trainee->employer->name;
            }
            if( !empty($enrolment->trainee->employer->uen) ) {
                $student->company_uen                 = $enrolment->trainee->employer->uen;
            }
            if( !empty($enrolment->trainee->employer->contact->fullName) ) {
                $student->company_contact_person      = $enrolment->trainee->employer->contact->fullName;
            }
            if( !empty($enrolment->trainee->employer->contact->email->full) ) {
                $student->company_contact_person_email = $enrolment->trainee->employer->contact->email->full;
            }
            if( !empty($enrolment->trainee->employer->contact->contactNumber->phoneNumber) ) {
                $student->company_contact_person_number = $enrolment->trainee->employer->contact->contactNumber->phoneNumber;
            }
            if( is_null($student->xero_id) ) {
                $student->xero_id               = $xeroId;
            }
            $student->save();
        } else {
            // add student in master table
            $student = new Student;
            $student->name                        = $enrolment->trainee->fullName;
            $student->nric                        = $nric;
            $student->company_sme                 = "";
            $student->nationality                 = $nationality;
            $student->email                       = $enrolment->trainee->email->full;
            $student->mobile_no                   = $enrolment->trainee->contactNumber->phoneNumber;
            $student->dob                         = $enrolment->trainee->dateOfBirth;
            if( !empty($enrolment->trainee->employer->name) ) {
                $student->company_name                 = $enrolment->trainee->employer->name;
            }
            if( !empty($enrolment->trainee->employer->uen) ) {
                $student->company_uen                 = $enrolment->trainee->employer->uen;
            }
            if( !empty($enrolment->trainee->employer->contact->fullName) ) {
                $student->company_contact_person      = $enrolment->trainee->employer->contact->fullName;
            }
            if( !empty($enrolment->trainee->employer->contact->email->full) ) {
                $student->company_contact_person_email = $enrolment->trainee->employer->contact->email->full;
            }
            if( !empty($enrolment->trainee->employer->contact->contactNumber->phoneNumber) ) {
                $student->company_contact_person_number = $enrolment->trainee->employer->contact->contactNumber->phoneNumber;
            }
            $student->xero_id                     = $xeroId;
            $student->created_by                  = Auth::Id();
            $student->updated_by                  = Auth::Id();
            $student->save();
            $record->student_id             = $student->id;
        }
        // get course data
        $course = Course::where('tpgateway_id', $enrolment->course->run->id)->first();
        if( empty($course->id) ) {
            $res['msg'] = 'Course run is not in our database. Please Sync Course run first: '.$enrolment->course->run->id;
            return $res;

        }
        // add it in enrolement table here
        $record->course_id                  = $course->id;
        if( !empty($enrolment->trainee->employer->name) ) {
            $record->company_name                 = $enrolment->trainee->employer->name;
            $record->company_sme                = "";
        }
        $record->sponsored_by_company       = $sponsoredByCompany;
        $record->nationality                = $nationality;
        if( !empty($enrolment->trainee->dateOfBirth) ) {
            $record->age                        = getAgeFromDOB($enrolment->trainee->dateOfBirth);
        }
        $record->learning_mode              = $course->courseMain->course_mode_training == "offline" ? "f2f" : "online";
        $record->email                      = $enrolment->trainee->email->full;
        $record->mobile_no                  = $enrolment->trainee->contactNumber->phoneNumber;
        $record->dob                        = $enrolment->trainee->dateOfBirth;
        // $record->education_qualification    = $request->get('education_qualification');
        if( !empty($enrolment->trainee->employer->name) ) {
            $record->company_name                 = $enrolment->trainee->employer->name;
        }
        if( !empty($enrolment->trainee->employer->uen) ) {
            $record->company_uen                 = $enrolment->trainee->employer->uen;
        }
        if( !empty($enrolment->trainee->employer->contact->fullName) ) {
            $record->company_contact_person      = $enrolment->trainee->employer->contact->fullName;
        }
        if( !empty($enrolment->trainee->employer->contact->email->full) ) {
            $record->company_contact_person_email = $enrolment->trainee->employer->contact->email->full;
        }
        if( !empty($enrolment->trainee->employer->contact->contactNumber->phoneNumber) ) {
            $record->company_contact_person_number = $enrolment->trainee->employer->contact->contactNumber->phoneNumber;
        }

        // get course main fees
        $courseMain = $course->courseMain;
        $totalFees = $courseMain->course_full_fees;
        // if there is discount then deduct from totalFees
        $discountAmount = $enrolment->trainee->fees->discountAmount;
        // add discount amount to field
        $record->discountAmount             = $discountAmount;
        $netFeesBeforeGst = $totalFees - $discountAmount;
        // now calculate the GST
        $gstAmt = config('settings.feesGST') * $netFeesBeforeGst;
        $netFees = $gstAmt + $netFeesBeforeGst;
        // deduct the grant if any
        $record->isGrantError               = 1;
        if( !is_null($grant) && is_array($grant)) {
            $grantRes = new \stdClass;
            $grantRes->data = $grant;
            $record->grantResponse              = json_encode($grantRes);
            $record->isGrantError = 0;
            foreach( $grant as $gt ) {
                $netFees -= $gt->grantAmount->estimated;
            }
        }
        $record->amount                     = $netFees;
        $record->xero_nett_course_fees      = $netFees;
        $record->amount_paid = 0;

        $record->computer_navigation_skill  = 1;
        $record->course_brochure_determined = 1;

        $record->payment_status             = getPaymentStatusFromTPG($enrolment->trainee->fees->collectionStatus);
        $record->payment_tpg_status         = getPaymentStatusFromTPG($enrolment->trainee->fees->collectionStatus);
        $record->enrollmentResponse         = json_encode($enrolment);
        $record->tpgateway_refno            = $enrolment->referenceNumber;
        $record->status                     = 0;


        $record->created_by                 = Auth::Id();
        $record->updated_by                 = Auth::Id();
        $record->save();

        // increment the source schedule
        Log::info('registeredusercount increment');
        Log::info($record->id);
        $course->increment('registeredusercount');

        // check if this trainee has soft booked then make it completed
        $softBooking = CourseSoftBooking::where('course_id', $record->course_id)
                        ->where('nric', $nric)->first();
        if( isset($softBooking->id) && $softBooking->status == CourseSoftBooking::STATUS_PENDING ) {
            $softBooking->status = CourseSoftBooking::STATUS_BOOKED;
            $softBooking->save();
        }
        $res['status'] = true;
        $res['msg'] = "Student enrolment added successfully";
        return $res;
    }

    public function updateOfStudentPaymentRecord($id, $request){
        $record = $this->getStudentEnrolmentById($id);
        if($record){
            if( $request->get('field') == "inline-payment-remark") {
                $record->payment_remark = $request->get('value');
            }
            if( $request->get('field') == "inline-dob") {
                $record->due_date       = $request->get('value');
            }
            $record->update();
            return $record;
        }
        return false;
    }

    public function getNameOnNRIC($id){
        if($id){
            $tpgatewayReq = new TPGatewayService;
            $enrolment = $this->studentenrolment_model->where('id', $id)->first();
            if($enrolment->tpgateway_refno) {
                $tpg_refno = $enrolment->tpgateway_refno;
                $enrolRes = $tpgatewayReq->getStudentEnrolmentFromTpGateway($tpg_refno);
                
                if( isset($enrolRes->status) && $enrolRes->status == 200 ) {
                    $studentDetails = $this->students_model->where('id', $enrolment->student_id)->first();
                    if($studentDetails->nirc_name != $enrolRes->data->enrolment->trainee->fullName){
                        $studentDetails->nirc_name = $enrolRes->data->enrolment->trainee->fullName;
                        $studentDetails->save();
                        return true;
                        // return response()->json(['success' => 'true', 'message' => 'name updated successfully']);
                    } else {
                        return false;
                        // return response()->json(['success' => 'false', 'message' => 'name can not be updated, please try again later']);
                    }
                }
            } else {
                return false;
                // return response()->json(['success' => 'false', 'message' => 'No enrolment found']);
            }
        } else {
            return false;
        }
    } 

}
