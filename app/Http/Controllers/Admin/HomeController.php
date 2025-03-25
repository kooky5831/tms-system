<?php

namespace App\Http\Controllers\Admin;

use DataTables;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Refreshers;
use Illuminate\Http\Request;
use App\Services\CommonService;
use App\Services\CourseService;
use App\Models\StudentEnrolment;
use App\Services\StudentService;
use App\Services\TPGatewayService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(StudentService $studentService)
    {
        $this->middleware('auth');
        $this->studentService = $studentService;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $commonService = new CommonService;
        
        $data = $commonService->getAdminDashboardData();
        $courseCDMSData = StudentEnrolment::where('gform_id', '597')->groupBy('student_id')->simplePaginate(8);

        return view('admin.home', compact('data', 'courseCDMSData'));
    }

    public function examindex(){
        $commonService = new CommonService;
        $data = $commonService->getAdminDashboardData();
        return view('admin.examhome', compact('data'));
    }

    public function assessmentListDatatable(Request $request){
        $commonService = new CommonService;
        $records = $commonService->getListDatatableOfAdminAssessment($request);
        return Datatables::of($records)
            ->addIndexColumn()
            ->addColumn('course_name', function ($row) {
                return $row->courseMain->name;
            })
            ->addColumn('pax', function($row){
                return $row->registeredusercount.'/'.$row->intakesize;
            })
            ->filterColumn('course_name', function($query, $keyword) {
                $query->whereHas('courseMain', function($query) use ($keyword){
                    $query->where('course_mains.name', 'LIKE', '%'. strtolower($keyword) .'%');
               });   
            })
            ->addColumn('action', function ($row) {
                    $btn = '';
                        $btn .= '<div class="dropdown dot-list">
                                        <a href="#" class="dropdown-toggle rounded-bdr" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="list-dots"></span></a>
                                            <ul  class="dropdown-menu">';
                                                if($row->is_assigned != 1) {
                                                    $btn .= '<li>
                                                                <a href="" id="generate_student_assessment" data-courserunId ='. $row->id .'>
                                                                    <i class="fas fa-check-circle font-16"></i>Generate Assessment
                                                                </a>
                                                            </li>';
                                                } else {
                                                    $btn .= '<li><span class="badge badge-success text-white p-2">Assessment Assigned</span></li>';
                                                }
                                                
                                                $btn .= '<li><a href="'.route('admin.assessments.examdashboard.view_trainees',['courserunid' => $row->id]).'">
                                                <i class="fas fa-eye font-16"></i>View Trainees
                                            </a></li>';
                                            '</ul>
                                    </div>';
                        return $btn;
            })
            ->rawColumns(['course_name', 'action', 'pax'])
            ->make(true);
    }
    
    public function testTPGateway()
    {
        $tpgatewayReq = new TPGatewayService;
        // get course
        $res = $tpgatewayReq->getCourseFromRefNumber("TGS-2018503501");
        dd($res);

        // add to TP Gateway
        $req_data = [];
        $coursereferenceNumber = "TGS-2018503501";
        $courseRunId = "324157";
        $studentNric = "S9348650A";
        // enrolment
        // if(  )
        $req_data['enrolment'] = [
            'trainingPartner' => [
                "code" => config('settings.tpgateway_code'),
                "uen" => config('settings.tpgateway_uenno')
            ],
            "course" => [
                "referenceNumber" => $coursereferenceNumber,
                "run" => [ "id" => $courseRunId ]
            ],
            "trainee" => [
                "id" => $studentNric,
                "idType" => [ "type" => "NRIC" ],
                "dateOfBirth" => "1993-12-23",
                "fullName" => "Lim Xiu Yan",
                "contactNumber" => [
                    "countryCode" => "+65",
                    "areaCode" => "",
                    "phoneNumber" => "98333344"
                ],
                "emailAddress" => "Limxiuyan@gmail.com",
                "sponsorshipType" => "INDIVIDUAL",
                "employer" => [
                    "uen" => "",
                    "contact" => [
                        "fullName" => "",
                        "contactNumber" => [
                            "countryCode" => "+65",
                            "areaCode" => "",
                            "phoneNumber" => ""
                        ],
                        "emailAddress" => ""
                    ]
                ],
                "fees" => [
                    "discountAmount" => 0,
                    "collectionStatus" => "Pending Payment"
                ],
                "enrolmentDate" => Carbon::now(config('settings.tpgatewayTimezone'))->format('Y-m-d')
            ],
        ];

        // $res = $tpgatewayReq->studentEnrolment($req_data);
        // dd($res);
        $req_data_grant = [];
        // grant calculator
        $req_data_grant['grants'] = [
            "enrolment" => [
                "referenceNumber" => "ENR-2108-069510"
            ],
            "trainee" => [
                "id" => $studentNric
            ],
            "employer" => [
                "uen" => ""
            ],
            'trainingPartner' => [
                "code" => config('settings.tpgateway_code'),
                "uen" => config('settings.tpgateway_uenno')
            ],
            "course" => [
                "referenceNumber" => $coursereferenceNumber,
                "run" => [ 'id' => $courseRunId ]
            ]
        ];
        $req_data_grant['meta'] = [
            "lastUpdateDateFrom" => date('Y')."-01-01",
            "lastUpdateDateTo" => date('Y', strtotime('+1 year'))."-01-01"
        ];
        $req_data_grant['sortBy'] = [
            "field" => "updatedOn",
            "order" => "asc"
        ];
        $req_data_grant['parameters'] = [
            "page" => 0,
            "pageSize" => 20
        ];
        $grant = $tpgatewayReq->checkGrantCalculator($req_data_grant);
        dd($grant);
    }

    public function downloadLogFile(Request $request)
    {
        $file_dir = storage_path('/logs/schedulerlogs/');
        $fileName = $request->fname;
        return response()->download($file_dir.$fileName);      
    }

    public function getAllTrainers()
    {
        $tpgatewayReq = new TPGatewayService;
        // get course
        $res = $tpgatewayReq->getAllTrainersFromTPgateway();
        dd($res);

        
    }

    public function resetNricAsPassword($nric){
        if(!empty($nric)){
            $user = User::where('username', $nric)->first();

            
            if(!empty($user)){
                $resetPassword = Hash::make($nric);
                $user->password = $resetPassword;
                $user->update();
                dd('Password reset successfully');
            } else {
                dd('No user found with NRIC - ' . $nric);
            }
        }
    }

    public function getTraineeCourseRunModal(Request $request){

        $studentId = $request->get('student_id');
        $gformId = $request->get('gform_id');

        $getStudentData = StudentEnrolment::with(['courseRun'])->where(['student_id'=> $studentId, 'gform_id' => '597'])->get();
        $student = $this->studentService->getStudentById($studentId);

        $view = view('admin.partial.trainee-courserun', compact('student', 'getStudentData'))->render();
        $data = [ 'status' => true, 'html' => $view ];
        return response()->json($data);

    }
}
