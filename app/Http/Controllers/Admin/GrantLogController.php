<?php

namespace App\Http\Controllers\Admin;

use DataTables;
use App\Models\Grant;
use App\Models\Student;
use App\Models\GrantLog;
use App\Models\CourseMain;
use Illuminate\Http\Request;
use App\Models\StudentEnrolment;
use App\Services\StudentService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Exports\GrantLogsExport;
use Excel;

class GrantLogController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->studentService = new StudentService;
    }

    public function index(Request $request){
        return view('admin.grantactivity.list');
    }
    
    public function listDatatable(Request $request){

        $allGrnatLogs = GrantLog::select('grant_logs.*')->where('grant_logs.id', '!=', 0)->where('grant_logs.grant_notify', 0);

        $startDate = $request->get('from');
        $endDate = $request->get('to');
     
        if($startDate) {
            $allGrnatLogs->whereDate('grant_logs.created_at', '>=', date("Y-m-d", strtotime($startDate)));
        }
        if($endDate) {
            $allGrnatLogs->whereDate('grant_logs.created_at', '<=', date("Y-m-d", strtotime($endDate)));
        }

        $grantId = $request->get('grant_id');
        if($grantId){
            $allGrnatLogs->where('grant_logs.grant_refno', '=', $grantId);
        }

        $enrolmentId = $request->get('enrolment_id');
        if($enrolmentId){
            $allGrnatLogs->where('grant_logs.student_enrolment_id', '=', $enrolmentId);
        }

        $grantStatus = $request->get('grant_status');
        if($grantStatus){
            $allGrnatLogs->join('grants', function($join) use ($grantStatus){
                $join->on('grants.id', '=', 'grant_logs.grant_id')
                ->select('grants.grant_status', 'grant_logs.created_at')
                ->whereIn('grants.grant_status', $grantStatus);
            });
        }

        $studentName = $request->get('student_name');
        if($studentName){
            $allGrnatLogs->join('student_enrolments', function($join){
                $join->on('student_enrolments.id', '=', 'grant_logs.student_enrolment_id');
            })
            ->join('students', function($join) use ($studentName) {
                $join->on('student_enrolments.student_id', '=', 'students.id')
                ->select('students.name')
                ->where('students.name', 'LIKE', '%'.$studentName.'%');
            });            
        }

        $allGrnatLogs->get();
        
        return Datatables::of($allGrnatLogs)
        
            ->addColumn('user_id', function($row) {
                if( $row->user_id ) {
                    return 'By '.userName($row->user_id);
                }
                else{
                    return 'By Grant Status Cron Job';
                }
            })

            ->editColumn('grant_logs.created_at', function($row) {
                if( $row->created_at ) {
                    return date('d-m-Y h:i A', strtotime($row->created_at));
                }
            })

            ->addColumn('description', function($row) {
                $grant = Grant::where('grant_refno', $row->grant_refno)->first();
                if($row->event == 'Created'){
                    return auditDescription($row->event, "Grant Created", $grant->grant_status, $row->grant_refno, "App\Models\Grant", $row->student_enrolment_id);
                }else{
                    return auditDescription($row->event, "Grant Processing", $grant->grant_status, $row->grant_refno, "App\Models\Grant", $row->student_enrolment_id);
                }
            })

            ->addColumn('event', function($row) {
                return $row->event;
            })
        
            ->addColumn('action', function($row) {
                $btn = "<div class='d-flex'>"; 
                $btn .= "<a class='btn btn-success grant-remark mr-2' grantid='".$row->id."' href='javascript:void(0)'>Remarks</a>";
                $btn .= "<a class='btn btn-success grant-resolved' grantid='".$row->id."' href='javascript:void(0)'>Resolved</a>";
                $btn .= "</div>";
                return $btn;
            })
        
            ->rawColumns(['user_id', 'grant_logs.created_at', 'description', 'event', 'action'])
            ->make(true);
    }

    public function updateStatus(Request $request)
    {
        $grantId = $request->get('id');
        $data = GrantLog::find($grantId);
        $view = view('admin.partial.update-grant-log-status', compact('data'))->render();
        $data = [ 'status' => true, 'html' => $view ];
        return response()->json($data);
    }

    public function updateGrantLog(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'grant_id' => 'required',
            'notes' => 'required',
        ],[
            'grant_id.required'   => 'No Grant Log Found',
            'notes.required'   => 'Note is required',
        ]);
        if ($validator->fails()) {
            $error = $validator->errors()->first();
            $response = [
                'success'   => false,
                'message'   => $error,
            ];
            return response()->json($response, 200);
        }
        $response = $this->updateGrantLogStatus($request);
        return response()->json($response, 200);
    }

    public function updateGrantLogStatus($request)
    {
        $grant_id = $request->get('grant_id');
        
        $record = GrantLog::find($grant_id);
        if( empty($record->id) ) {
            return ['success' => false, 'message' => 'No Grant Log Found'];
        }

        $record->notes = $request->get('notes');
        $record->save();
        
        if( $record ) {
            return ['success' => true, 'message' => 'Grant Log Updated successfully'];
        } else {
            return ['success' => false, 'message' => 'Grant Log not updated'];
        }
    }

    public function resolvedGrantLog(Request $request)
    {
        $grant_id = $request->get('id');
        
        $record = GrantLog::find($grant_id);
        if( empty($record) ) {
            return ['success' => false, 'message' => 'No Grant Log Found'];
        }

        $record->grant_notify = 1;
        $record->save();
        
        if( $record ) {
            return ['success' => true, 'message' => 'Grant Mark as Resolved successfully'];
        } else {
            return ['success' => false, 'message' => 'Grant Log not Resolved'];
        }
    }

    public function grantLogExportExcel(Request $request){
        $allGrnatLogs = GrantLog::select('grant_logs.*')->where('grant_logs.id', '!=', 0)->where('grant_logs.grant_notify', 0);
        $startDate = $request->get('from');
        $endDate = $request->get('to');
     
        if($startDate) {
            $allGrnatLogs->whereDate('grant_logs.created_at', '>=', date("Y-m-d", strtotime($startDate)));
        }
        if($endDate) {
            $allGrnatLogs->whereDate('grant_logs.created_at', '<=', date("Y-m-d", strtotime($endDate)));
        }

        $grantId = $request->get('grant_id');
        if($grantId){
            $allGrnatLogs->where('grant_logs.grant_refno', '=', $grantId);
        }

        $enrolmentId = $request->get('enrolment_id');
        if($enrolmentId){
            $allGrnatLogs->where('grant_logs.student_enrolment_id', '=', $enrolmentId);
        }

        $grantStatus = $request->get('grant_status');
        if($grantStatus){
            $allGrnatLogs->join('grants', function($join) use ($grantStatus){
                $join->on('grants.id', '=', 'grant_logs.grant_id')
                ->select('grants.grant_status', 'grant_logs.created_at')
                ->whereIn('grants.grant_status', $grantStatus);
            });
        }

        $studentName = $request->get('student_name');
        if($studentName){
            $allGrnatLogs->join('student_enrolments', function($join){
                $join->on('student_enrolments.id', '=', 'grant_logs.student_enrolment_id');
            })
            ->join('students', function($join) use ($studentName) {
                $join->on('student_enrolments.student_id', '=', 'students.id')
                ->select('students.name')
                ->where('students.name', 'LIKE', '%'.$studentName.'%');
            });            
        }

        $data = $allGrnatLogs->get();

        return Excel::download(new GrantLogsExport($data), 'grantLog-list.xlsx');
    }
}
