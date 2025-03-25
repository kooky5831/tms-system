<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DataTables;
use App\Models\Student;
use App\Models\CourseMain;
use App\Models\StudentEnrolment;
class AuditLogController extends Controller
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

    public function index(Request $request){
        return view('admin.activity.list');
    }

    public function searchStudent(Request $request)
    {
    	$students = [];
        if($request->has('q')){
            $search = $request->q;
            $students = Student::select("id", "name")
            		->where('name', 'LIKE', "%$search%")
            		->get();
        }
        return response()->json($students);
    }
    
    public function listDatatable(Request $request){
        
        $allAudits = \OwenIt\Auditing\Models\Audit::where('id', '!=', 0)
                                            ->where('auditable_type', '!=', 'App\Models\Grant')
                                            ->latest();

        $startDate = $request->get('from');
        $endDate = $request->get('to');
        $auditable_type = $request->get('auditable_type');
        $studentid = $request->get('studentid');
        $courseid = $request->get('courseid');
        $events = $request->get('action_type');

        if( is_array($auditable_type) ) {
            $allAudits->whereIn('auditable_type', $auditable_type);
        }

        if($events) {
            $eventsWithModel = explode('_', $events);
            if(isset($eventsWithModel[1])) {
                switch ($eventsWithModel[1]) {
                    case 'Course':
                        $eventType = $eventsWithModel[0] == 'created' ? 'created' : 'updated';
                        $allAudits->where('event', $eventType)->where('auditable_type', 'App\Models\CourseMain');        
                    break;
    
                    case 'Student Enrolmet':
                        if($eventsWithModel[0] == 'cancel') {
                            $allAudits->where('event', 'updated')
                                        ->where('auditable_type', 'App\Models\StudentEnrolment')
                                        ->where('new_values', 'not like', '%payment_tpg_status%')
                                        ->where('new_values', 'not like', '%payment_status%')
                                        ->where('new_values', 'not like', '%grantStatus%')
                                        ->where('new_values', 'not like', '%grantStatus%')
                                        ->where('new_values', 'not like', '%grantStatus%')
                                        ->where('new_values', 'not like', '%grantStatus%')
                                        ->where('new_values', 'not like', '%grantStatus%')
                                        ->where('new_values', 'not like', '%education_qualification%')
                                        ->where('new_values', 'not like', '%amount%')
                                        ->where('new_values', 'not like', '%company_uen%')
                                        ->where('new_values', 'not like', '%xero_invoice_number%')
                                        ->where('new_values', 'not like', '%nationality%')
                                        ->where('new_values', 'not like', '%company_sme%');
                        } else {
                            $eventType = $eventsWithModel[0] == 'created' ? 'created' : 'updated';
                            $allAudits->where('event', $eventType)->where('auditable_type', 'App\Models\StudentEnrolment');
                        }
                    break;
    
                    case 'Course Run':
                        $eventType = $eventsWithModel[0] == 'created' ? 'created' : 'updated';
                        $allAudits->where('event', $eventType)->where('auditable_type', 'App\Models\Course');
                    break;
    
                    case 'Payment':
                        if($eventsWithModel[0] == 'cancel') {
                            $allAudits->where('event', 'updated')
                                    ->where('auditable_type', 'App\Models\Payment')
                                    ->where('new_values', 'LIKE', '%status%');
                        } else {
                            $eventType = $eventsWithModel[0] == 'created' ? 'created' : 'updated';
                            $allAudits->where('event', $eventType)->where('auditable_type', 'App\Models\Payment');
                        }
                    break;
                    
                    case 'Student':
                        $eventType = $eventsWithModel[0] == 'created' ? 'created' : 'updated';
                        $allAudits->where('event', $eventType)->where('auditable_type', 'App\Models\Student');
                    break;
                }
            }
        }

        if( $startDate ) {
            $allAudits->whereDate('updated_at', '>=', date("Y-m-d", strtotime($startDate)));
        }
        if( $endDate ) {
            $allAudits->whereDate('updated_at', '<=', date("Y-m-d", strtotime($endDate)));
        }

        if($studentid)
        {
            $studentsId = StudentEnrolment::select('id')->where('student_id', $studentid)->get()->toArray();
            $allAudits = \OwenIt\Auditing\Models\Audit::whereIn('auditable_type',['App\Models\Student','App\Models\StudentEnrolment', 'App\Models\CourseMain', 'App\Models\Course', 'App\Models\Payment'])
                            ->whereIn('auditable_id', $studentsId);
        }

        if($courseid)
        {
            $allAudits = \OwenIt\Auditing\Models\Audit::whereIn('auditable_type',['App\Models\Course','App\Models\CourseMain'])->where('auditable_id', $courseid);
        }

        return Datatables::of($allAudits)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    if($row->event){
                        return $this->eventsType($row->event, $row->new_values, $row->auditable_type);
                    }
                })
                ->addColumn('description', function($row) {
                    if( $row->event ) {
                        return auditDescription($row->event, $row->old_values, $row->new_values, $row->user_id, $row->auditable_type, $row->auditable_id); 
                    }
                })
                ->addColumn('time', function($row) {
                    if( $row->created_at ) {
                        return date('d-m-Y h:i A', strtotime($row->created_at));
                    }
                })
                ->addColumn('user_id', function($row) {
                    if( $row->user_id ) {
                        return userName($row->user_id);
                    } else {
                        return "Registration from Equinet";
                    }
                })
                ->rawColumns(['description', 'action', 'user_id'])
                ->make(true);
    } 


    public function filterAudits(Request $request){
        // $mainCourseAudits =  CourseMain::with('audits')->get();
        // foreach($mainCourseAudits as $audit){
        //    foreach($audit->audits as $item){
        //         print_r($item);
        //    }
        //     // print_r($audit->audits()->latest()->first()->getMetadata());
        // }
    }

    public function eventsType($event, $new_values, $auditableType){
        $type = getModuleNameByType($auditableType);
        if(!empty($type)) {
            switch ($type) {
                case 'Course':
                    return  $event == 'updated' ? 'Edit Course' : 'Add Course';
                break;
                
                case 'Student Enrolmet':
                    if (isset($new_values['status'])) {
                        if($new_values['status'] == StudentEnrolment::STATUS_CANCELLED) {
                            return "Cancel Enrolment";
                        } else {
                            return  $event == 'updated' ? 'Edit Enrolment' : 'Add Enrolment';    
                        }               
                    } else{
                        return  $event == 'updated' ? 'Edit Enrolment' : 'Add Enrolment';
                    }
                break;
                
                case 'Course Run':
                    return  $event == 'updated' ? 'Edit Course Run' : 'Add Course Run';
                break;
                
                case "Payment":
                    if (isset($new_values['status'])) {
                        if($new_values['status'] == StudentEnrolment::STATUS_CANCELLED) {
                            return "Cancel Payment";
                        }               
                    } else {
                        return  $event == 'updated' ? 'Edit Payment' : 'Add Payment';
                    }
                break;
                
                case "Student":
                    return  $event == 'updated' ? 'Edit Student' : 'Add Student';
                break;
                
                default:
                break;
            }
        }
    }

    public function actionableDropdown(){
        $allModels = \OwenIt\Auditing\Models\Audit::select('auditable_type', 'new_values')
                                                ->groupBy('auditable_type')
                                                ->get();
        $options = [];
        $oprionStrings = "<option value='#'> Select Action <option>";
        foreach($allModels as $value) {
            $type = getModuleNameByType($value->auditable_type);
            if(!empty($type)) {
                switch ($type) {
                    case 'Course':
                        $oprionStrings .= "<option value='created_".getModuleNameByType($value->auditable_type)."'> Add " . getModuleNameByType($value->auditable_type) . " <option>";
                        $oprionStrings .= "<option value='updated_".getModuleNameByType($value->auditable_type)."'> Edit " . getModuleNameByType($value->auditable_type) . " <option>";
                    break;
                    
                    case 'Student Enrolmet':
                        $oprionStrings .= "<option value='created_".getModuleNameByType($value->auditable_type)."'> Add " . getModuleNameByType($value->auditable_type) . " <option>";
                        $oprionStrings .= "<option value='updated_".getModuleNameByType($value->auditable_type)."'> Edit " . getModuleNameByType($value->auditable_type) . " <option>";
                        $oprionStrings .= "<option value='cancel_".getModuleNameByType($value->auditable_type)."'> Cancel " . getModuleNameByType($value->auditable_type) . " <option>";
                    break;
                    
                    case 'Course Run':
                        $oprionStrings .= "<option value='created_".getModuleNameByType($value->auditable_type)."'> Add " . getModuleNameByType($value->auditable_type) . " <option>";
                        $oprionStrings .= "<option value='updated_".getModuleNameByType($value->auditable_type)."'> Edit " . getModuleNameByType($value->auditable_type) . " <option>";
                    break;
                    
                    case "Payment":
                        $oprionStrings .= "<option value='created_".getModuleNameByType($value->auditable_type)."'> Add " . getModuleNameByType($value->auditable_type) . " <option>";
                        $oprionStrings .= "<option value='updated_".getModuleNameByType($value->auditable_type)."'> Edit " . getModuleNameByType($value->auditable_type) . " <option>";
                        $oprionStrings .= "<option value='cancel_".getModuleNameByType($value->auditable_type)."'> Cancel " . getModuleNameByType($value->auditable_type) . " <option>";
                    break;
                    
                    case "Student":
                        $oprionStrings .= "<option value='created_".getModuleNameByType($value->auditable_type)."'> Add " . getModuleNameByType($value->auditable_type) . " <option>";
                        $oprionStrings .= "<option value='updated_".getModuleNameByType($value->auditable_type)."'> Edit " . getModuleNameByType($value->auditable_type) . " <option>";
                    break;
                    
                    default:
                    break;
                }
            }
        }
        return $oprionStrings;
    }


    public function searchCourse(Request $request){
        $students = [];
        if($request->has('q')){
            $search = $request->q;
            $students = CourseMain::select("id", "name")
            		->where('name', 'LIKE', "%$search%")
            		->get();
        }
        return response()->json($students);
    }
}
