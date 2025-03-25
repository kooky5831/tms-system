<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\CommonService;
use DataTables;

class HomeController extends Controller
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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $commonService = new CommonService;
        return view('trainer.home');
    }


    public function assessmentListDatatable(Request $request){
        $commonService = new CommonService;
        $records = $commonService->getListDatatableOfTrainer($request);
        return Datatables::of($records)
                ->addIndexColumn()
                ->addColumn('course_main_name', function($row) {
                    $courseNames = '';
                    if($row->courseMain){
                        $data = $row->courseMain->pluck('name')->toArray();
                        $courseNames = implode('<br/>', $data);
                    }
                    return $courseNames;
                })
                ->addColumn('assessment_count', function($row){
                    return count($row->assessment);
                })
                ->addColumn('action', function($row) {
                    $btn = '';
                    $btn .= '
                        <div class="dropdown dot-list">
                        <a href="#" class="dropdown-toggle rounded-bdr" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="list-dots"></span></a>
                            <ul  class="dropdown-menu">';
                            $btn .= '<li><a href="'.route('trainer.dashboard.edit', $row->id).'" ><i class="fas fa-eye font-16"></i>Edit Exam</a></li>';
                            $btn .= '<li><a href="'.route('trainer.dashboard.get-assessments', $row->id).'" ><i class="fas fa-eye font-16"></i>Main Assessments</a></li>
                        </ul>
                    </div>';
                return $btn;
                })
                ->rawColumns(['course_main_name', 'assessment_count', 'action'])
                ->make(true);
                // $btn .= '<li><a href="'.route('trainer.dashboard.all_assessments', $row->id).'" ><i class="fas fa-eye font-16"></i>Total Assessments</a></li>
    }

    public function courseListDatatable(Request $request){ 
        $commonService = new CommonService;
        $records = $commonService->getListDatatableOfTrainerCourseruns($request);
        return Datatables::of($records)
                ->addIndexColumn()
                ->addColumn('course_run_id', function($row) {
                    return $row->courseMain->reference_number;
                })
                ->addColumn('course_main_name', function($row) {
                    return $row->courseMain->name;
                })
                ->addColumn('course_start_date', function($row) {
                    return $row->course_start_date;
                })
                ->addColumn('course_end_date', function($row) {
                    return $row->course_end_date;
                })
                ->addColumn('pax', function($row){
                    $row->registeredusercount."/".$row->intakesize;
                })
                ->addColumn('action', function($row) {
                    $btn = '';
                    $btn .= '
                        <div class="dropdown dot-list">
                        <a href="#" class="dropdown-toggle rounded-bdr" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="list-dots"></span></a>
                            <ul  class="dropdown-menu">';
                            if($row->examCourse->is_assigned != 1) {
                                $btn .= '<li>
                                            <a href="" id="generate_student_assessment" data-courserunId ='. $row->id .'>
                                                <i class="fas fa-check-circle font-16"></i>Generate Assessment
                                            </a>
                                        </li>';
                            } else {
                                $btn .= '<li><span class="badge badge-success text-white p-2">Assessment Assigned</span></li>';
                            }
                            $btn .= '<li><a href="'.route('trainer.dashboard.mark_assessment', $row->id).'" ><i class="fas fa-eye font-16"></i>View Trainees</a></li>
                        </ul>
                    </div>';
                    return $btn;
                })
                ->rawColumns(['course_run_id', 'course_main_name', 'course_start_date', 'course_end_date', 'pax', 'action'])
                ->make(true);
    }
}
