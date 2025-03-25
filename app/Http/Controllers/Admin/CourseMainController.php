<?php

namespace App\Http\Controllers\Admin;

use DataTables;
use App\Models\Course;
use App\Models\AdminTasks;
use App\Models\CourseMain;
use App\Jobs\CreateEmailJob;
use Illuminate\Http\Request;
use App\Services\UserService;
use App\Services\XeroService;
use App\Models\CourseResource;
use App\Services\CommonService;
use App\Models\CourseRunTriggers;
use App\Services\CourseTagService;
use App\Services\CourseMainService;
use App\Services\CourseTypeService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\ProgramTypeService;

use Illuminate\Support\Facades\Gate;
use App\Services\CourseTriggersService;
use Webfox\Xero\OauthCredentialManager;
use App\Models\CourseResourceCourseMain;
use App\Http\Requests\CourseMainStoreRequest;
use App\Http\Requests\CourseMainUpdateRequest;
use App\Http\Requests\CourseTriggerStoreRequest;
use App\Http\Requests\CourseResourceStoreRequest;


class CourseMainController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(CourseMainService $courseMainService)
    {
        $this->middleware('auth');
        $this->courseMainService = $courseMainService;
    }

    /**
     * Show the list of course types.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        if (! Gate::allows('coursemain-list')) { return abort(403); }
        return view('admin.coursemain.list');

    }

    public function listDatatable(Request $request)
    {
        if (! Gate::allows('coursemain-list')) { return abort(403); }
        $records = $this->courseMainService->getAllCourseMain($request);
        return Datatables::of($records)
                ->addIndexColumn()
                ->editColumn('coursetype', function($row) {
                    return $row->courseType->name;
                })
                ->editColumn('name', function($row) {
                    return '<a href="'.route('admin.coursemain.edit',$row->id).'">' . $row->name . '</a>';
                })
                ->editColumn('course_type', function($row) {
                    if( $row->course_type == 1 ) { return '<span class="badge badge-soft-success">'.getCourseType($row->course_type).'</span>'; }
                    else { return '<span class="badge badge-soft-danger">'.getCourseType($row->course_type).'</span>'; }
                })
                ->filterColumn('coursetype', function($query, $keyword) {
                    $len = strlen($keyword);
                    if( $len ) {
                        $query->whereHas('coursetype', function ($q) use ($keyword) {
                            return $q->where('name', 'LIKE', '%'. strtolower($keyword) .'%');
                        });
                    }
                })
                ->filterColumn('course_type', function($query, $keyword) {
                    $len = strlen($keyword);
                    if( (substr('wsq', 0, $len) === strtolower($keyword)) ) {
                        $query->where('course_type', CourseMain::COURSE_TYPE_WSQ);
                    }
                    if( (substr('non-wsq', 0, $len) === strtolower($keyword)) ) {
                        $query->where('course_type', CourseMain::COURSE_TYPE_NONWSQ);
                    }
                })
                ->addColumn('course_tags', function($row) {
                    $tags = implode(', ', $row->courseTags->pluck('name')->toArray());
                    return $tags; 
                })
                ->filterColumn('course_tags', function($query, $keyword) {
                    $query->whereHas('courseTags', function ($query) use ($keyword) {
                        $query->where('name', 'like', "%{$keyword}%");
                    });
                })
                // ->orderColumn('course_tags', function ($query, $orderDirection) {
                //     $query->whereHas('courseTags', function ($query) use ($orderDirection) {
                //         $query->orderBy('name', $orderDirection);
                //     });
                // })
                ->addColumn('action', function($row) {
                    $btn = '';
                    // if( $row->course_type_id != CourseMain::MODULAR_COURSE ) {
                    //     if( Gate::allows('course-list') ) {
                    //         $btn .= '<a href="'.route('admin.course.list', $row->id).'" data-toggle="tooltip" data-placement="bottom" title="View Course Run" class="mr-2"><i class="fas fa-eye text-info font-16"></i></a>';
                    //     }
                    //     $btn .= '<a href="'.route('admin.course.add', $row->id).'" data-toggle="tooltip" data-placement="bottom" title="Add Course Run" class="mr-2"><i class="fas fa-plus text-info font-16"></i></a>';
                    // }
                    // $btn .= '<a href="'.route('admin.coursemain.edit', $row->id).'" data-toggle="tooltip" data-placement="bottom" title="Edit" class="mr-2"><i class="fas fa-edit text-info font-16"></i></a>';

                    $btn .= '
                        <div class="dropdown dot-list">
                        <a href="#" class="dropdown-toggle rounded-bdr" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="list-dots"></span></a>
                            <ul  class="dropdown-menu">';
                            if( $row->course_type_id != CourseMain::MODULAR_COURSE ) {
                                if( Gate::allows('course-list') ) {
                                    $btn .= ' <li><a href="'.route('admin.course.list',$row->id).'"><i class="fas fa-eye font-16"></i> View Course Run</a></li>';
                                }
                                if( Gate::allows('course-add') ) {
                                    $btn .= '<li><a href="'.route('admin.course.add',$row->id).'"><i class="fas fa-plus font-16"></i> Add Course Run</a></li>';
                                }
                            }
                            $btn .= '<li><a href="'.route('admin.coursemain.edit',$row->id).'"><i class="fas fa-pencil-alt font-16"></i> Edit</a></li>
                            </ul>
                        </div>
                    ';
                    return $btn;
                })
                ->rawColumns(['action', 'course_type', 'name','status'])
                ->make(true);

    }

    public function courseMainAdd(CourseMainStoreRequest $request)
    {
        if (! Gate::allows('coursemain-add')) { return abort(403); }
        if( $request->method() == 'POST') {
            // $xeroSer = new XeroService($xeroCredentials);
            // $xeroitems = $xeroSer->getItemsList();
            $trainer = $this->courseMainService->registerCourseMain($request);
            if( $trainer ) {
                setflashmsg(trans('msg.courseMainCreated'), 1);
                return redirect()->route('admin.coursemain.list');
            }
        }

        $courseTypeService = new CourseTypeService;
        $programTypeService = new ProgramTypeService;
        $courseTypelist = $courseTypeService->getAllCourseTypeList();
        $programTypelist = $programTypeService->getAllProgramTypeAllList();
        $userService = new UserService;
        $trainers = $userService->getAllTrainersList();

        // $xeroSer = new XeroService($xeroCredentials);
        // $connection = $xeroSer->checkConnection();
        // $xero = [
        //     'connection' => $connection
        // ];
        // if( $connection['status'] && !$connection['data']['error'] ) {
        //     // get lists
        //     $xero['items'] = $xeroSer->getItemsList();
        //     $xero['brandingThemes'] = $xeroSer->getBrandingThemesList();
        // }

        $courseTagService = new CourseTagService;
        $courseTags = $courseTagService->getActiveCourseTags();

        return view('admin.coursemain.add',compact('courseTypelist', 'trainers', 'programTypelist', 'courseTags'));
    }

    public function courseMainEdit($id, CourseMainUpdateRequest $request)
    {
        $programTypeService = new ProgramTypeService;
        if (! Gate::allows('coursemain-edit')) { return abort(403); }
        if( $request->method() == 'POST') {
            // $xeroSer = new XeroService($xeroCredentials);
            // $xeroitems = $xeroSer->getItemsList();
            $allCourses = $this->courseMainService->updateCourseMain($id, $request);
            if( $allCourses ) {
                setflashmsg(trans('msg.courseMainUpdated'), 1);
                return redirect()->route('admin.coursemain.list');
            }
        }

        $data = $this->courseMainService->getCourseMainById($id);
        $selectedcourses = [];
        if( !empty($data->single_course_ids) && $data->course_type_id == CourseMain::MODULAR_COURSE ) {
            $courseIds = explode(",", $data->single_course_ids);
            $selectedcourses = $this->courseMainService->getCourseMainByIds($courseIds);
        }
        $selectedTrainers = $data->trainers->pluck('id')->all();
        $courseTypeService = new CourseTypeService;
        $courseTypelist = $courseTypeService->getAllCourseTypeList();
        $programTypelist = $programTypeService->getAllProgramTypeAllList();
        $userService = new UserService;
        $trainers = $userService->getAllTrainersList();

        // $xeroSer = new XeroService($xeroCredentials);
        // $connection = $xeroSer->checkConnection();
        // $xero = [
        //     'connection' => $connection
        // ];
        // if( $connection['status'] && !$connection['data']['error'] ) {
        //     // get lists
        //     $xero['items'] = $xeroSer->getItemsList();
        //     $xero['brandingThemes'] = $xeroSer->getBrandingThemesList();
        // }

        $courseTagService = new CourseTagService;
        $courseTags = $courseTagService->getActiveCourseTags();

        return view('admin.coursemain.edit', compact('data','courseTypelist', 'selectedTrainers', 'trainers', 'selectedcourses', 'programTypelist', 'courseTags'));
    }

    public function searchMainCourses(Request $req)
    {
        $query = $req->get('q');
        $ret = $this->courseMainService->searchCourseMainAjax($query);
        return json_encode($ret);
    }

    public function getCourseMainList(Request $request)
    {
        $records = $this->courseMainService->getAllCourseMainListForRuns();
        $view = view('admin.partial.coursemain-dropdown', compact('records'))->render();
        $data = [ 'status' => true, 'html' => $view ];
        return response()->json($data);
    }

    public function courseMainCheck()
    {
        $tpgatewayReq = new \App\Services\TPGatewayService;
        $res = $tpgatewayReq->retrieveCourses();
        dd($res);
    }

    /**
     * Show the list of Triggers for Course Runs
     *
     * @return \Illuminate\Contracts\Support\Renderable
    */
    public function triggersListIndex(Request $request)
    {
        if (! Gate::allows('coursetriggers-list')) { return abort(403); }
        // get main course list
        $courseMainList = $this->courseMainService->getAllCourseMainListForRuns();
        $courseTagService = new CourseTagService;
        $courseTags = $courseTagService->getActiveCourseTags();
        return view('admin.coursetriggers.list', compact('courseMainList', 'courseTags'));

    }

    public function triggersListDatatable(Request $request)
    {
        if (! Gate::allows('coursetriggers-list')) { return abort(403); }
        $courseTriggersService = new CourseTriggersService;
        $records = $courseTriggersService->getAllCourseTriggers($request);
        return Datatables::of($records)
                ->addIndexColumn()
                ->smart(false)
                // ->orderBy('created_at', 'desc')
                ->filterColumn('triggerTitle', function($query, $keyword){
                    return $query->where('triggerTitle','LIKE', '%'. strtolower($keyword) .'%');
                })
                ->editColumn('triggerTitle', function($row) {
                    return '<a href="'.route('admin.coursetrigger.edit',$row->id).'">' . $row->triggerTitle . '</a>';
                })
                ->editColumn('status', function($row) {
                    return courseTriggerStatus($row->status);
                })
                ->filterColumn('status', function($query, $keyword) {
                    $len = strlen($keyword);
                    if( (substr('active', 0, $len) === strtolower($keyword)) ) {
                        $query->where('status', 1);
                    }
                    if( (substr('inactive', 0, $len) === strtolower($keyword)) ) {
                        $query->where('status', 0);
                    }
                })
                ->addColumn('coursename', function ($row) {
                    return $row->courseMains->map(function($courseMain) {
                        return $courseMain->name;
                    })->implode(', ');
                })
                ->filterColumn('coursename', function($query, $keyword) {
                    $query->whereHas('courseMains', function ($q) use ($keyword) {
                        return $q->where('name', 'LIKE', '%'. strtolower($keyword) .'%');
                    });
                })
                ->addColumn('tags', function($row){
                    return $row->courseTags->map(function($courseTags){
                        return $courseTags->name;
                    })->implode(', ');
                })
                ->filterColumn('tags', function($query, $keyword){
                    $query->whereHas('courseTags', function ($q) use ($keyword) {
                        return $q->where('name', 'LIKE', '%'. strtolower($keyword) .'%');
                    });
                })
                ->addColumn('priority', function($row){
                    return $row->priority ?? "-";
                })
                ->editColumn('event_when', function($row) {
                    $num = " (";
                    if( in_array($row->event_when, [1,4]) ) {
                        $num .= $row->no_of_days;
                    } else if ( $row->event_when == 2 ) {
                        $num .= $row->date_in_month;
                    } else if ( $row->event_when == 3 ) {
                        $num .= getDaysOfWeek($row->day_of_week);
                    }
                    $num .= ")";
                    return triggerEventWhen($row->event_when).$num;
                })
                ->editColumn('event_type', function($row) {
                    return triggerEventTypes($row->event_type);
                })
                ->editColumn('template_name', function($row) {
                    if( $row->event_type == 2 && !is_null($row->sms_template_id) ) {
                        return $row->smsTemplate->name;
                    } else if($row->event_type == 1 && !is_null($row->template_name)) {
                        return $row->template_name;
                    }
                })
                ->filterColumn('template_name', function($query, $keyword) {
                    $len = strlen($keyword);
                    if( $len ) {
                        $query->whereHas('smsTemplate', function ($q) use ($keyword) {
                            return $q->where('name', 'LIKE', '%'. strtolower($keyword) .'%');
                        })->orWhere('template_name', 'LIKE', '%'. strtolower($keyword) .'%');
                    }
                })
                ->filterColumn('event_when', function($query, $keyword) {
                    $len = strlen($keyword);
                    if( (substr('day', 0, $len) === strtolower($keyword)) ) {
                        $query->whereIn('event_when', [1,3]);
                    } else {
                        if( (substr('daysbeforecourse', 0, $len) === strtolower($keyword)) ) {
                            $query->where('event_when', 1);
                        }
                        if( (substr('dayofweek', 0, $len) === strtolower($keyword)) ) {
                            $query->where('event_when', 3);
                        }
                    }
                    if( (substr('timeofmonth', 0, $len) === strtolower($keyword)) ) {
                        $query->where('event_when', 2);
                    }
                })
                ->filterColumn('event_type', function($query, $keyword) {
                    $len = strlen($keyword);
                    if( (substr('sms', 0, $len) === strtolower($keyword)) ) {
                        $query->where('event_type', 2);
                    }
                    if( (substr('email', 0, $len) === strtolower($keyword)) ) {
                        $query->where('event_type', 1);
                    }
                    if( (substr('texttask', 0, $len) === strtolower($keyword)) ) {
                        $query->where('event_type', 3);
                    }
                })
               
                ->filterColumn('course_type', function($query, $keyword) {
                    $len = strlen($keyword);
                    if( (substr('wsq', 0, $len) === strtolower($keyword)) ) {
                        $query->where('courseMain.course_type', CourseMain::COURSE_TYPE_WSQ);
                    }
                    if( (substr('non-wsq', 0, $len) === strtolower($keyword)) ) {
                        $query->where('courseMain.course_type', CourseMain::COURSE_TYPE_NONWSQ);
                    }
                })
                ->addColumn('action', function($row) {
                    $btn = '';
                    $btn .= '
                        <div class="dropdown dot-list">
                        <a href="#" class="dropdown-toggle rounded-bdr" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="list-dots"></span></a>
                            <ul  class="dropdown-menu">';
                            $btn .= '<li><a href="'.route('admin.coursetrigger.edit',$row->id).'"><i class="fas fa-pencil-alt font-16"></i> Edit</a></li>
                            </ul>
                        </div>
                    ';
                    return $btn;
                })
                ->orderColumn('priority', function ($query, $order) {
                    $query->orderBy('priority', $order);
                })
                ->rawColumns(['action', 'course_type', 'status', 'triggerTitle'])
                ->make(true);

    }

    public function addTrigger(CourseTriggerStoreRequest $request)
    {
        if (! Gate::allows('coursetriggers-add')) { return abort(403); }
        $courseTriggersService = new CourseTriggersService;
        if( $request->method() == 'POST') {
            $record = $courseTriggersService->registerCourseTrigger($request);
            if( $record ) {
                setflashmsg(trans('msg.courseTriggerCreated'), 1);
                return redirect()->route('admin.coursetrigger.list');
            }
        }

        $templates = $courseTriggersService->getEmailTemplatesList();
        $smsTemplateSer = new \App\Services\SMSTemplateService;
        $smsTemplates = $smsTemplateSer->getAllSMSTemplatesList();
        $courseMains = $this->courseMainService->getAllCourseMainListForRuns();
        $courseTagService = new CourseTagService;
        $courseTags = $courseTagService->getActiveCourseTags();
        return view('admin.coursetriggers.add', compact('templates', 'smsTemplates', 'courseMains', 'courseTags'));
    }

    public function editTrigger($id, CourseTriggerStoreRequest $request)
    {
        if (! Gate::allows('coursetriggers-edit')) { return abort(403); }
        $courseTriggersService = new CourseTriggersService;
        if( $request->method() == 'POST') {
            $record = $courseTriggersService->updateCourseTrigger($id, $request);
            if( $record ) {
                setflashmsg(trans('msg.courseTriggerUpdated'), 1);
                return redirect()->route('admin.coursetrigger.list');
            }
        }

        $data = $courseTriggersService->getCourseTriggerById($id);
        // if it has course main id then get all course main if for this group
        $templates = $courseTriggersService->getEmailTemplatesList();
        $smsTemplateSer = new \App\Services\SMSTemplateService;
        $smsTemplates = $smsTemplateSer->getAllSMSTemplatesList();
        $courseMains = $this->courseMainService->getAllCourseMainListForRuns();
        $courseTagService = new CourseTagService;
        $courseTags = $courseTagService->getActiveCourseTags();

        return view('admin.coursetriggers.edit', compact('data','templates', 'smsTemplates', 'courseMains', 'courseTags'));
    }

    public function testsmsTriggers()
    {
        $courseTriggersService = new CourseTriggersService;
        $courseMains = $courseTriggersService->getAllCourseTriggersForEventWhen(4, NULL);

        // loop through it and get course run for specific date
        foreach ($courseMains as $courseMain) {
            $courses = $courseMain->courseMains->pluck('id')->toArray();
            $tag_courses = $courseMain->courseTags->pluck('courseMains.*.id')->flatten()->toArray();
            $all_courses = array_unique(array_merge($tag_courses, $courses));

            $searchDate = \Carbon\Carbon::now()->addDays($courseMain->no_of_days);
            // check if is there any course for today for this main course
            $courseRuns = Course::whereIn('course_main_id', $all_courses)
                            ->whereDate('course_end_date', $searchDate)->with(['courseActiveEnrolmentsWithStudent'])->get();
            // loop through course run if found any
            if( !$courseRuns->isEmpty() ) {
                foreach($courseRuns as $courseRun) {
                    $adminTask = new AdminTasks;
                    $adminTask->course_id = $courseRun->id;
                    $adminTask->task_type = $courseMain->event_type;

                    // check for task Type
                    if( $courseMain->event_type == CourseRunTriggers::EVENT_TYPE_EMAIL ) {
                        $adminTask->template_name = $courseMain->template_name;
                        $adminTask->template_slug = $courseMain->template_slug;
                        // split Template name and slug
                    } else if( $courseMain->event_type == CourseRunTriggers::EVENT_TYPE_SMS ) {
                        // template for SMS
                        $adminTask->sms_template_id = $courseMain->sms_template_id;
                    } else if( $courseMain->event_type == CourseRunTriggers::EVENT_TYPE_TEXT ) {
                        $adminTask->task_text = $courseMain->task_text;
                    }
                    $adminTask->priority = $courseMain->priority;
                    $adminTask->created_by = 1;
                    $adminTask->updated_by = 1;
                    $adminTask->save();
                }
            }
        }
    }

    public function courseResourceIndex(){
        return view('admin.courseresource.list');
    }

    public function resourceListDatatable(Request $request)
    {
        $commonService = new CommonService;
        $records = $commonService->getAllCourseResources();
        return Datatables::of($records)
                    ->addIndexColumn()
                    ->addColumn('course_main_name', function($row) {
                        $name = $row->name;
                        return $name; 
                    })
                    ->addColumn('resource_count', function($row) {
                        $count = CourseResourceCourseMain::where('course_main_id', $row->course_main_id)
                        ->join('course_resources', 'course_resources_coursemains.course_resource_id', '=', 'course_resources.id')
                        ->whereNull('course_resources.deleted_at')->count();
                        return $count;
                    })
                    ->addColumn('action', function($row) {
                        $btn = '';      
                        $btn .= '
                            <div class="d-flex">';
                            $btn .= '<a class="btn btn-success"  href="'.route('admin.course-resources.get-resources', ['id' => $row->course_main_id,'resourceId' => $row->id] ).'">View Resources</a>';
                            $btn .= "</div>";
                        return $btn;
                    })
                ->rawColumns(['course_main_name', 'resource_count', 'action'])
                ->make(true);

    }

    public function courseResourceAdd(CourseResourceStoreRequest $request){
        if( $request->method() == 'POST') {
            // dd($request->all());
            $commonService = new CommonService;
            $resourcesData = $commonService->storeCourseResource($request);
            if($resourcesData){
                setflashmsg(trans('msg.CourseResourceAdd'), 1);
                return redirect()->route('admin.course-resources.index');
            } else {
                setflashmsg(trans('msg.CourseResourceError'), 0);
                return redirect()->route('admin.course-resources.index');
            }
        }
        $allCources = CourseMain::whereNot('course_type_id', CourseMain::BOOSTER_SESSIONS )->get();
        return view('admin.courseresource.add', compact('allCources'));
    }

    public function courseResourceEdit(Request $request, $id){
        $commonService = new CommonService;
        if( $request->method() == 'POST') {
            $editResource = $commonService->updateCourseResource($request, $id);
            if( $editResource ) {
                setflashmsg(trans('Resource updated successfully'), 1);
                return redirect()->route('admin.course-resources.index');
            }
        }
        $resource = $commonService->getResourceById($id);
        $allCources = CourseMain::whereNot('course_type_id', CourseMain::BOOSTER_SESSIONS )->get();
        $getCourseMainResource = CourseResourceCourseMain::where('course_resource_id', $id)->pluck('course_main_id')->toArray();
        return view('admin.courseresource.edit', compact('resource', 'allCources', 'getCourseMainResource'));
    }

    public function getResourceById(Request $request, $id, $resourceId){
        $commonService = new CommonService;
        $resourcesData = $commonService->getCourseResource($id);
        $mainCourse = CourseResource::select('course_mains.name')
                                            ->join('course_resources_coursemains', function($join){
                                                $join->on('course_resources.id', '=', 'course_resources_coursemains.course_resource_id');
                                            })
                                            ->join('course_mains', function($join){
                                                $join->on('course_resources_coursemains.course_main_id', '=', 'course_mains.id');
                                            })
                                            ->where('course_resources.id', $resourceId)
                                            ->get();
        return view('admin.courseresource.preview', compact('resourcesData', 'mainCourse'));
    }

    public function removeResourceById($id){
        $commonService = new CommonService;
        $removeResource = $commonService->removeCourseResource($id);
        if($removeResource) {
            setflashmsg("Resource deleted successfully", 1);
            return redirect()->back();
        } else {
            setflashmsg("Resource not deleted something went wrong", 0);
            return redirect()->back();
        }
    }


}
