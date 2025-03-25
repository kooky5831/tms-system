<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use DataTables;
use App\Services\GrantService;
use Excel;
use App\Exports\GrantDetailsExport;

class GrantController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(GrantService $grantService)
    {
        $this->middleware('auth');
        $this->grantService = $grantService;
    }

    /**
     * Show the grants records.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {

        return view('admin.grants.list');
    }
    
    public function listDatatable(Request $request)
    {
        $records = $this->grantService->getAllGrantsWithStudentEnrolment($request);
        return Datatables::of($records)
            ->addColumn('funding_scheme', function($row) {
                $scheme = $row->scheme_code." - ".$row->scheme_description;
                return $scheme;
            })
            ->addColumn('funding_component', function($row) {
                $component = $row->component_code." - ".$row->component_description;
                return $component;
            })

            ->addColumn('student_name', function($row) {
                $student_name = $row->studentEnrolment->student->name;
                return $student_name;
            })

            ->addColumn('nric', function($row) {
                return convertNricToView($row->studentEnrolment->student->nric);
            })
            ->addColumn('email', function($row) {
                return $row->studentEnrolment->email;
            })

            ->addColumn('course_name', function($row) {
                return $row->tpgateway_id.' - '.$row->coursemainname;
            })

            ->addColumn('enrolment_ref_no', function($row) {
                return $row->enr_ref_no;
            })

            ->filterColumn('funding_scheme', function($query, $keyword) {
                $query->where('scheme_description', 'LIKE', '%'. strtolower($keyword) .'%');
            })

            ->filterColumn('funding_component', function($query, $keyword) {
                $query->where('component_description', 'LIKE', '%'. strtolower($keyword) .'%');
            })

            ->filterColumn('student_name', function($query, $keyword) {
                $query->where('students.name', 'LIKE', '%'. strtolower($keyword) .'%');
            })

            ->filterColumn('email', function($query, $keyword) {
                $query->where('student_enrolments.email', 'LIKE', '%'. strtolower($keyword) .'%');
            })

            ->filterColumn('course_name', function($query, $keyword) {
                $query->where('course_mains.name', 'LIKE', '%'. strtolower($keyword) .'%');
            })

            ->filterColumn('grant_refno', function($query, $keyword) {
                $query->where('grant_refno', 'LIKE', '%'. strtolower($keyword) .'%');
            })

            ->addColumn('action', function($row) {
                $btn = '';
                if( isset($row->grant_refno) && !empty($row->grant_refno)) {
                    $btn .= '<a class="fetchgrantstatus btn btn-primary" href="javascript:void(0)" grant_id="'.$row->id.'" ><i class="fas fa-download font-16 mr-2"></i>Fetch Details</a>';
                }
                return $btn;
            })

            ->addColumn('status', function($row) {
                return enrollStatusBadge($row->studentEnrolment->status);
            })

            ->addColumn('dates', function($row) {
                return $row->course_start_date ." / ".$row->course_end_date;
            })

            ->rawColumns(['funding_scheme','funding_component','student_name','nric','email','course_name','action','status','dates', 'enrolment_ref_no'])
            ->make(true);
        
    }

    public function fetchGrantStatus(Request $request){
        $grantId = $request->get('id');
        $data = $this->grantService->fetchGrantStatusFromTPG($grantId);
        return response()->json($data);
    }

    public function grantDetailsExportExcel(Request $request)
    {
        $recordsQuery = $this->grantService->getAllGrantsWithStudentEnrolment($request);
        $records = $recordsQuery->get();
        return Excel::download(new GrantDetailsExport($records), 'grantDetails-list.xlsx');
    }
    
}
