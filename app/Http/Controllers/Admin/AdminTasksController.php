<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminTasksService;
use App\Services\CourseMainService;
use App\Models\CourseMain;
use App\Models\AdminTasks;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use App\Jobs\CreateSMSJob;
use App\Jobs\CreateEmailJob;
use DataTables;
use Auth;
use Illuminate\Support\Facades\File;

use Illuminate\Support\Facades\Mail;
use App\Mail\EmailTriggersForCourse;

class AdminTasksController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(AdminTasksService $adminTasksService)
    {
        $this->middleware('auth');
        $this->adminTasksService = $adminTasksService;
    }

    /**
     * Show the list of Admin Tasks for Course Runs
     *
     * @return \Illuminate\Contracts\Support\Renderable
    */
    public function index(Request $request)
    {
        if (! Gate::allows('admintasks-list')) { return abort(403); }
        // $courseMainService = new CourseMainService;
        // $courseList = $courseMainService->getAllCourseMainListForRuns();
        // return view('admin.admintasks.list', compact('courseList'));
        return view('admin.admintasks.list');

    }

    public function listDatatable(Request $request)
    {
        if (! Gate::allows('admintasks-list')) { return abort(403); }
        $records = $this->adminTasksService->getAllAdminTasks($request);
        return Datatables::of($records)
                ->addIndexColumn()
                // ->orderBy('created_at', 'desc')
                ->editColumn('status', function($row) {
                    if( $row->status == AdminTasks::STATUS_CREATED ) { return '<span class="badge badge-soft-primary">Created</span>'; }
                    elseif( $row->status == AdminTasks::STATUS_PENDING ) { return '<span class="badge badge-soft-warning">Pending</span>'; }
                    else { return '<span class="badge badge-soft-success">Completed</span>'; }
                })
                ->filterColumn('status', function($query, $keyword) {
                    $len = strlen($keyword);
                    if( (substr('created', 0, $len) === strtolower($keyword)) ) {
                        $query->where('status', AdminTasks::STATUS_CREATED);
                    }
                    if( (substr('pending', 0, $len) === strtolower($keyword)) ) {
                        $query->where('status', AdminTasks::STATUS_PENDING);
                    }
                    if( (substr('completed', 0, $len) === strtolower($keyword)) ) {
                        $query->where('status', AdminTasks::STATUS_COMPLETED);
                    }
                })
                ->addColumn('coursedate', function($row) {
                    if( $row->course_id ) {
                        return $row->course->course_start_date;
                    }
                    return "-";
                })
                ->addColumn('coursename', function($row) {
                    if( $row->course_id ) {
                        return $row->course->courseMain->name;
                    }
                    return "-";
                })
                ->editColumn('task_type', function($row) {
                    return triggerEventTypes($row->task_type);
                })
                ->editColumn('template_name', function($row) {
                    if( $row->task_type == AdminTasks::TASK_TYPE_SMS && !is_null($row->sms_template_id) ) {
                        return $row->smsTemplate->name;
                    } else if( $row->task_type == AdminTasks::TASK_TYPE_EMAIL ) {
                        return $row->template_name;
                    } else if( $row->task_type == AdminTasks::TASK_TYPE_TEXT ) {
                        return $row->task_text;
                    }
                    return '';
                })
                ->filterColumn('template_name', function($query, $keyword) {
                    $len = strlen($keyword);
                    if( $len ) {
                        $query->whereHas('smsTemplate', function ($q) use ($keyword) {
                            return $q->where('name', 'LIKE', '%'. strtolower($keyword) .'%');
                        });
                        $query->orWhere('template_name', 'LIKE', '%'. strtolower($keyword) .'%');
                        $query->orWhere('task_text', 'LIKE', '%'. strtolower($keyword) .'%');
                    }
                })
                ->filterColumn('task_type', function($query, $keyword) {
                    $len = strlen($keyword);
                    if( (substr('sms', 0, $len) === strtolower($keyword)) ) {
                        $query->where('task_type', AdminTasks::TASK_TYPE_SMS);
                    }
                    if( (substr('email', 0, $len) === strtolower($keyword)) ) {
                        $query->where('task_type', AdminTasks::TASK_TYPE_EMAIL);
                    }
                    if( (substr('texttask', 0, $len) === strtolower($keyword)) ) {
                        $query->where('task_type', AdminTasks::TASK_TYPE_TEXT);
                    }
                })
                ->addColumn('action', function($row) {
                    $btn = '';
                    $btn .= '
                        <div class="dropdown dot-list">
                        <a href="#" class="dropdown-toggle rounded-bdr" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="list-dots"></span></a>
                            <ul  class="dropdown-menu">';
                            if( $row->course_id ) {
                                $btn .= '<li><a href="'.route('admin.course.courserunview',$row->course_id).'"><i class="fas fa-eye font-16"></i>View Course</a></li>';
                            }
                            $btn .= '<li><a href="javascript:void(0)" task_id="'.$row->id.'" class="viewtaskdetails"><i class="mdi mdi-note font-16"></i>View Task Details</a></li>';
                            $btn .= '<li><a href="javascript:void(0)" task_id="'.$row->id.'" class="updatetasknote"><i class="mdi mdi-note font-16"></i>Update/View Note</a></li>';
                            if( $row->task_type == AdminTasks::TASK_TYPE_EMAIL && $row->status != AdminTasks::STATUS_COMPLETED ) {
                                // give option for email sending
                                $btn .= '<li><a href="'.route('admin.tasks.sendTaskEmail', $row->id).'"><i class="mdi mdi-email-check-outline font-16"></i>Send Email</a></li>';
                            }
                            if( $row->task_type == AdminTasks::TASK_TYPE_SMS && $row->status != AdminTasks::STATUS_COMPLETED ) {
                                // give option for sms sending
                                $btn .= '<li><a href="'.route('admin.tasks.sendTasksms', $row->id).'"><i class="fas fa-sms font-16"></i>Send SMS</a></li>';
                            }
                            if( $row->status != AdminTasks::STATUS_COMPLETED ) {
                                $btn .= '<li><a href="javascript:void(0)" task_id="'.$row->id.'" class="marktaskComplete"><i class="fas fa-tasks font-16"></i>Mark as Completed</a></li>';
                            }
                        $btn .= '</ul>
                        </div>
                    ';
                    return $btn;
                })
                ->rawColumns(['action', 'coursedate', 'status'])
                ->make(true);

    }

    public function sendTaskSMS($id)
    {
        $data = $this->adminTasksService->getAdminTaskByIdWithRelation($id, ['course', 'smsTemplate']);
        // check if this task is already done or not
        if( $data->status == AdminTasks::STATUS_COMPLETED ) {
            setflashmsg(trans('msg.taskAlreadyCompleted'), 0);
            return redirect()->route('admin.dashboard');
        }
        // check if this task is for sms only
        if( $data->task_type != AdminTasks::TASK_TYPE_SMS ) {
            setflashmsg(trans('msg.notSMSTypeTask'), 0);
            return redirect()->route('admin.dashboard');
        }
        // dd($data);
        // get students mobile number enrolled in this course run
        $studentService = new \App\Services\StudentService;
        $studentEnrollments = $studentService->getAllStudentsForCourseRun($data->course_id, ['student']);
        $courseService = new \App\Services\CourseService;
        $refresherStudent = $courseService->getCourseRunFullDetailsById($data->course_id)->courseRefreshers;
        return view('admin.admintasks.send-sms', compact('data', 'studentEnrollments', 'refresherStudent'));
    }

    public function sendTaskSMSsubmit($id, Request $request)
    {
        $validated = $request->validate([
            'students_list' => 'required',
            'content' => 'required',
        ],[
            'students_list.required'   => 'Please select at least 1 student',
            'content.required'   => 'Content is required',
        ]);
        // get selected students list
        $studentService = new \App\Services\StudentService;
        $students = $studentService->getStudentByIds($request->get('students_list'));
        $smsTemplateMsg = $request->get('content');
        $task = $this->adminTasksService->getAdminTaskById($id);       
        $commonService = new \App\Services\CommonService;
        $sessionString = $commonService->makeSessionString($task->course->session);
        
        foreach( $students as $student ) {
            if( !empty($student->mobile_no) ) {
                // then create message
                $msg = $smsTemplateMsg;
                $msg = str_ireplace("{studentname}", $student->name, $msg);
                $msg = str_ireplace("{coursedate}", $sessionString, $msg);
                $msg = str_ireplace("{coursename}", $task->course->courseMain->name, $msg);
                $msg = str_ireplace("{staffname}", Auth::user()->name, $msg);
                $msg = str_ireplace("{courseSession}", $sessionString, $msg);

                if( $task->course->course_link ) {
                    $msg = str_ireplace("{coursemeetinglink}", $task->course->course_link, $msg);
                } else {
                    $msg = str_ireplace("{coursemeetinglink}", "-", $msg);
                }
                if( $task->course->meeting_id ) {
                    $msg = str_ireplace("{coursemeetingId}", $task->course->meeting_id, $msg);
                } else {
                    $msg = str_ireplace("{coursemeetingId}", "-", $msg);
                }
                if( $task->course->meeting_pwd ) {
                    $msg = str_ireplace("{coursemeetingPwd}", $task->course->meeting_pwd, $msg);
                } else {
                    $msg = str_ireplace("{coursemeetingPwd}", "-", $msg);
                }
                CreateSMSJob::dispatch($student->mobile_no, $msg);
            }
        }
        $task->status                     = AdminTasks::STATUS_COMPLETED;
        $task->completed_by               = Auth::Id();
        $task->completed_at               = date('Y-m-d H:i:s');
        $task->save();
        setflashmsg(trans('msg.taskCompleted'), 1);
        return redirect()->route('admin.dashboard');
    }

    public function sendTaskEmail($id)
    {
        $data = $this->adminTasksService->getAdminTaskByIdWithRelation($id, ['course']);
        // check if this task is already done or not
        if( $data->status == AdminTasks::STATUS_COMPLETED ) {
            setflashmsg(trans('msg.taskAlreadyCompleted'), 0);
            return redirect()->route('admin.dashboard');
        }
        // check if this task is for email only
        if( $data->task_type != AdminTasks::TASK_TYPE_EMAIL ) {
            setflashmsg(trans('msg.notEmailTypeTask'), 0);
            return redirect()->route('admin.dashboard');
        }

        // get email template
        $emailTemplate = EmailTemplate::where('slug', $data->template_slug)->first();
        // dd($emailTemplate);
        // get students mobile number enrolled in this course run
        $commonService = new \App\Services\CommonService;
        $sessionString = $commonService->makeSessionString($data->course->session);
        $sessionDateTime = $commonService->makeSessionDateTime($data->course->session);
        $sessionTime = $commonService->makeSessionDateTime($data->course->session, false, true);
        $studentService = new \App\Services\StudentService;
        $studentEnrollments = $studentService->getAllStudentsForCourseRun($data->course_id, ['student']);
        $courseService = new \App\Services\CourseService;
        $refresherStudent = $courseService->getCourseRunFullDetailsById($data->course_id)->courseRefreshers;
        $content = $emailTemplate['template_text'];
        $content = str_ireplace("{courseSession}", $sessionString, $content);
        $content = str_ireplace("{coursename}", $data->course->courseMain->name, $content);
        // $content = str_ireplace("{coursedate}", $sessionString, $content);
        $content = str_ireplace("{coursedate}", $sessionDateTime, $content);
        $content = str_ireplace("{coursetime}", $sessionTime, $content);
        $content = str_ireplace("{staffname}", Auth::user()->name, $content);
        $content = str_ireplace("{coursemeetinglink}", $data->course->course_link ? $data->course->course_link : '-', $content);
        $content = str_ireplace("{coursemeetingId}", $data->course->meeting_id ? $data->course->meeting_id : '-', $content);
        $content = str_ireplace("{coursemeetingPwd}", $data->course->meeting_pwd ? $data->course->meeting_pwd : '-', $content);

        return view('admin.admintasks.send-email', compact('data', 'sessionString', 'content', 'studentEnrollments', 'emailTemplate', 'refresherStudent'));
    }

    public function sendTaskEmailsubmit($id, Request $request)
    {
        $validated = $request->validate([
            'students_list' => 'required',
            'content' => 'required',
        ],[
            'students_list.required'   => 'Please select at least 1 student',
            'content.required'   => 'Content is required',
        ]);
        
        $commonService = new \App\Services\CommonService;
        $genrateCertificate = $commonService->generateCommonCertificate($id, $request, __FUNCTION__);
        $task = $this->adminTasksService->getAdminTaskById($id);

        if( !empty($request->get('company_list')) ) {
            \Log::info('Company List select');
            $certificateAttachment = null;
            $data = $this->adminTasksService->getAdminTaskByIdWithRelation($id, ['course']);
            $studentService = new \App\Services\StudentService;
            $sessionString = $commonService->makeSessionString($data->course->session);
            $sessionDateTime = $commonService->makeSessionDateTime($data->course->session);
            $sessionTime = $commonService->makeSessionDateTime($data->course->session, false, true);
            $companys = $studentService->getStudentEnrollmentsByIds($request->get('company_list'));
            foreach( $companys as $company ) {
                \Log::info('company Name ==> ' . $company->company_name);
                if( !empty($company->company_contact_person_email) ) {
                    // then create message
                    $emailTemplate = EmailTemplate::where('slug', $data->template_slug)->first();
                    $msg = $emailTemplate['template_text'];
                    $msg = str_ireplace("{studentname}", $company->company_name, $msg);
                    $msg = str_ireplace("{coursedate}", $sessionDateTime, $msg);
                    $msg = str_ireplace("{coursetime}", $sessionTime, $msg);
                    $msg = str_ireplace("{coursename}", $task->course->courseMain->name, $msg);
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
                    // Mail::to("vishal@equinetacademy.com")->send(new EmailTriggersForCourse($msg));
                    CreateEmailJob::dispatch($company->company_contact_person_email, $msg, "company", $certificateAttachment);
                }
            }
        }
        $task->status                     = AdminTasks::STATUS_COMPLETED;
        $task->completed_by               = Auth::Id();
        $task->completed_at               = date('Y-m-d H:i:s');
        $task->save();
        setflashmsg(trans('msg.taskCompleted'));
        return redirect()->route('admin.dashboard');
    }

    public function markTaskCompleted(Request $request)
    {
        $taskId = $request->get('id');
        $data = $this->adminTasksService->markTaskCompletedbyID($taskId);
        return response()->json($data);
    }

    public function markAllTaskCompleted(Request $request)
    {
        $taskIds = $request->get('ids');
        $data = $this->adminTasksService->markTaskCompletedbyIDs($taskIds);
        return response()->json($data);
    }


    public function marktaskUncomplete(Request $request)
    {
        $taskId = $request->get('id');
        $data = $this->adminTasksService->markTaskUncompletebyID($taskId);
        return response()->json($data);
    }

    public function updateNotesGetView(Request $request)
    {
        $taskId = $request->get('id');
        $data = $this->adminTasksService->getAdminTaskById($taskId);
        $view = view('admin.partial.update-task-notes', compact('data'))->render();
        $data = [ 'status' => true, 'html' => $view ];
        return response()->json($data);
    }

    public function updateNotes(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'task_id' => 'required',
            'notes' => 'required',
        ],[
            'task_id.required'   => 'No Task Found',
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
        $response = $this->adminTasksService->updateTaskNote($request);
        return response()->json($response, 200);
    }

    public function getTaskDetailsView(Request $request)
    {
        $taskId = $request->get('id');
        $data = $this->adminTasksService->getAdminTaskByIdWithRelation($taskId, ['course', 'completedByUser']);
        $view = view('admin.partial.get-task-details', compact('data'))->render();
        $data = [ 'status' => true, 'html' => $view ];
        return response()->json($data);
    }
}
