<?php

namespace App\Http\Controllers\Admin;
use Auth;
use App\Http\Controllers\Controller;
use App\Services\CourseMainService;
use App\Services\CourseService;
use App\Services\StudentService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use App\Imports\CourseRunsImport;
use App\Imports\StudentEnrolmentsImport;
use App\Services\TPGatewayService;
use Webfox\Xero\OauthCredentialManager;
use Excel;
use App\Models\Grant;

class DataImportController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function courseRunImport(Request $request)
    {
        if (\Auth::user()->role != 'superadmin') { return abort(403); }
        if( $request->method() == 'POST') {
            $validated = $request->validate([
                'coursemain' => 'required',
                'import_file' => 'required|max:10000|mimes:xlsx',
            ],[
                'coursemain.required'   => 'Please select course',
                'import_file.required'   => 'Please select file',
            ]);
            $courseMain = $request->get('coursemain');
            // Excel::import(new CourseRunsImport($courseMain), $request->file('import_file'));
            // Excel::import(new CourseRunsImport($courseMain), $request->file('import_file'), \Maatwebsite\Excel\Excel::XLSX);
            // show error in import
            $import =  new CourseRunsImport($courseMain);
            $import->import($request->file('import_file'));
            if( count($import->smerrors) > 0 ) {
                setflashmsg("Course run import has some error", 0);
                return redirect()->route('admin.dataImport.courseRun')->with('smerrors', $import->smerrors);
            }
            setflashmsg("Course run imported successfully", 1);
            return redirect()->route('admin.dataImport.courseRun');
        }

        $courseMainService = new CourseMainService;
        $courseMainList = $courseMainService->getAllCourseMainList();

        return view('admin.dataImport.course-runs', compact('courseMainList'));
    }

    public function studentEnrolmentImport(Request $request)
    {
        if (\Auth::user()->role != 'superadmin') { return abort(403); }
        if( $request->method() == 'POST') {
            $validated = $request->validate([
                'import_file' => 'required|max:10000|mimes:xlsx',
            ],[
                'import_file.required'   => 'Please select file',
            ]);
            // $courseRun = $request->get('courserun');
            // Excel::import(new StudentEnrolmentsImport($courseMain), $request->file('import_file'));
            // Excel::import(new StudentEnrolmentsImport, $request->file('import_file'), \Maatwebsite\Excel\Excel::XLSX);
            // show error in import
            $import =  new StudentEnrolmentsImport;
            $import->import($request->file('import_file'));
            if( count($import->smerrors) > 0 ) {
                setflashmsg("Student enrollment data has some error", 0);
                return redirect()->route('admin.dataImport.studentEnrolment')->with('smerrors', $import->smerrors);
            }
            setflashmsg("Student enrollment data imported successfully", 1);
            return redirect()->route('admin.dataImport.studentEnrolment');
        }

        // $courseService = new CourseService;
        // $courseList = $courseService->getAllCourseList();
        return view('admin.dataImport.student-enrolment');
    }

    public function syncTpgCourseRuns(Request $request)
    {
        if (\Auth::user()->role != 'superadmin') { return abort(403); }
        return view('admin.dataImport.sync-course-runs');
    }

    public function getTpgCourseRunsById(Request $request)
    {
        $courseRunId = $request->get('courserun_id');
        $tpgatewayReq = new TPGatewayService;
        $courseRes = $tpgatewayReq->getCourseRunFromTpGateway($courseRunId);
        if( $courseRes->status == 200 ) {
            $res = [ 'status' => true, 'data' => $courseRes->data, 'already' => false, 'msg' => 'Course run data fetched' ];
            // get session data as well
            $courseSession = $tpgatewayReq->getCourseSessionsFromTpGateway($courseRunId, $courseRes->data->course->referenceNumber);
            // check if this course run already exists in database
            $courseService = new CourseService;
            if( $courseService->getCourseByTPGRunId($courseRunId) ) {
                $res['already'] = true;
            }
            if( $courseSession->status == 200 ) {
                $res['sessions'] = $courseSession->data->sessions;
            }
        } else {
            $res = [ 'status' => false, 'data' => [], 'msg' => 'No Course run found' ];
        }
        return response()->json($res);
    }

    public function saveCourseRunTpGateway(Request $request)
    {
        $courseRunId = $request->get('courserun_id');
        $tpgatewayReq = new TPGatewayService;
        $courseRes = $tpgatewayReq->getCourseRunFromTpGateway($courseRunId);
        if( $courseRes->status == 200 ) {
            $res = [ 'status' => true, 'already' => false, 'msg' => 'Course run data fetched' ];
            // get session data as well
            $courseSession = $tpgatewayReq->getCourseSessionsFromTpGateway($courseRunId, $courseRes->data->course->referenceNumber);
            // check if this course run already exists in database
            $courseService = new CourseService;
            if( $courseService->getCourseByTPGRunId($courseRunId) ) {
                $res['already'] = true;
            }
            if( $courseSession->status == 200 && !$res['already'] ) {
                $sessions = $courseSession->data->sessions;
                // now save this to course run
                $res = $courseService->addCourseRunFromTPG($courseRes->data->course, $sessions);
            }
        } else {
            $res = [ 'status' => false, 'data' => [], 'msg' => 'No Course run found' ];
        }
        return response()->json($res);
    }

    public function syncTpgStudentEnrolment(Request $request)
    {
        if (\Auth::user()->role != 'superadmin') { return abort(403); }
        // $tpgatewayReq = new TPGatewayService;
        // $studentEnrollmentRes = $tpgatewayReq->getStudentEnrolmentFromTpGateway("ENR-2109-009332");
        // dd($studentEnrollmentRes);
        return view('admin.dataImport.sync-student-enrolment');
    }

    public function getTpgStudentEnrolmentById(Request $request)
    {
        $studentService = new StudentService;
        $enrolmentId = $request->get('enrolment_id');
        $studentEnrolment = $studentService->getStudentEnrolmentByTPGId($enrolmentId);
        $tpgatewayReq = new TPGatewayService;
        $enrolmentRes = $tpgatewayReq->getStudentEnrolmentFromTpGateway($enrolmentId);
        if( $enrolmentRes->status == 200 ) {
            $res = [ 'status' => true, 'data' => $enrolmentRes->data->enrolment, 'already' => false, 'courserun' => false, 'msg' => 'Student Enrolment data fetched' ];
            // check if this course run already exists in database
            $courseService = new CourseService;
            if( $courseService->getCourseByTPGRunId($enrolmentRes->data->enrolment->course->run->id) ) {
                $res['courserun'] = true;
            }
            // check if this students enrolment already exists in database
            if( $studentService->getStudentEnrolmentByTPGId($enrolmentId) ) {
                $res['already'] = true;
            }
            // get grant data
            $req_data_grant = $tpgatewayReq->createGrantRequestFromTPGateway($enrolmentRes->data->enrolment);
            $res['grantRes'] = [];
            $grantRes = $tpgatewayReq->checkGrantCalculator($req_data_grant);
            if( isset($grantRes->status) && $grantRes->status == 200 ) {
                $res['grantRes'] = $grantRes->data;
                if( !empty($grantRes->data)) {
                    // add/update grant for this enrollment
                    foreach( $grantRes->data as $grant ){
                    
                        $grantRecord = Grant::updateOrCreate(
                            [
                                'grant_refno'   => $grant->referenceNumber,
                            ],
                            [
                                'student_enrolment_id' => $studentEnrolment->id,
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
            }
            
        } else {
            $res = [ 'status' => false, 'data' => [], 'msg' => 'No Enrolment found' ];
        }
        return response()->json($res);
    }

    public function saveTpgStudentEnrolment(Request $request, OauthCredentialManager $xeroCredentials)
    {
        $enrolmentId = $request->get('enrolment_id');
        $tpgatewayReq = new TPGatewayService;
        $enrolmentRes = $tpgatewayReq->getStudentEnrolmentFromTpGateway($enrolmentId);
        if( $enrolmentRes->status == 200 ) {
            $res = [ 'status' => true, 'already' => false, 'msg' => 'Student Enrolment data fetched' ];
            // check if this students enrolment already exists in database
            $studentService = new StudentService;
            if( $studentService->getStudentEnrolmentByTPGId($enrolmentId) ) {
                $res['status'] = false;
                $res['already'] = true;
                $res['msg'] = 'Student Enrolment already exists in our system';
            } else {
                // get grant data
                $req_data_grant = $tpgatewayReq->createGrantRequestFromTPGateway($enrolmentRes->data->enrolment);
                $grant = null;
                $grantRes = $tpgatewayReq->checkGrantCalculator($req_data_grant);
                if( $grantRes->status == 200 ) {
                    $grant = $grantRes->data;
                }
                // now save this to student enrollment
                $res = $studentService->addStudentEnrolmentFromTPG($enrolmentRes->data->enrolment, $grant, $xeroCredentials);
            }
        } else {
            $res = [ 'status' => false, 'data' => [], 'msg' => 'No Course run found' ];
        }
        return response()->json($res);
    }

}
