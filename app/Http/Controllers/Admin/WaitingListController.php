<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\WaitingListStoreRequest;
use App\Services\WaitingListService;
use App\Services\CourseService;
use Illuminate\Support\Facades\Gate;
use App\Services\CommonService;
use Illuminate\Http\Request;
use DataTables;

class WaitingListController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(WaitingListService $waitingListService)
    {
        $this->middleware('auth');
        $this->waitingListService = $waitingListService;
    }

    /**
     * Show the list of course types.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        if (! Gate::allows('waitinglist-list')) { return abort(403); }
        return view('admin.waitinglist.list');
    }

    public function listDatatable(Request $request)
    {
        if (! Gate::allows('waitinglist-list')) { return abort(403); }
        $admins = $this->waitingListService->getAllWaitingList();
        return Datatables::of($admins)
                ->addIndexColumn()
                ->editColumn('course_id', function($row) {
                    return $row->tpgateway_id." (".$row->course_start_date. ") ".
                    " - ".$row->coursermainname;
                })
                ->editColumn('nric', function($row) {
                    return convertNricToView($row->nric);
                })
                ->editColumn('status', function($row) {
                    if( $row->status == 1 ) { return '<span class="badge badge-soft-success">'.getCourseWaitingListStatus($row->status).'</span>'; }
                    else { return '<span class="badge badge-soft-danger">'.getCourseWaitingListStatus($row->status).'</span>'; }
                })
                ->filterColumn('status', function($query, $keyword) {
                    $len = strlen($keyword);
                    if( (substr('accepted', 0, $len) === strtolower($keyword)) ) {
                        $query->where('status', 1);
                    }
                    if( (substr('pending', 0, $len) === strtolower($keyword)) ) {
                        $query->where('status', 0);
                    }
                    if( (substr('cancelled', 0, $len) === strtolower($keyword)) ) {
                        $query->where('status', 2);
                    }
                })
                ->addColumn('action', function($row) {
                    // $btn = '<a href="'.route('admin.waitinglist.edit',$row->id).'" data-toggle="tooltip" data-placement="bottom" title="Edit" class="mr-2"><i class="fas fa-edit text-info font-16"></i></a>';
                    $btn = '<div class="dropdown dot-list">
                            <a href="#" class="dropdown-toggle rounded-bdr" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="list-dots"></span></a>
                                <ul  class="dropdown-menu">';
                                if( $row->status == 0){
                                    $btn .= '<li><a href="'.route('admin.studentenrolment.add').'?waitinglist='.$row->id.'"><i class="fas fa-user font-16"></i> Enroll Student</a></li>';
                                }
                                $btn .= '<li><a href="'.route('admin.waitinglist.edit',$row->id).'"><i class="fas fa-pencil-alt font-16"></i> Edit</a></li>';
                                $btn .= '<li><a href="javascript:void(0)" class="viewnotes" main_id="'.$row->id.'"><i class="mdi mdi-note font-16"></i> View Note</a></li>';
                                // $btn .= '<li><a href="'.route('admin.waitinglist.edit',$row->id).'"><i class="far fa-trash-alt font-16"></i> Delete</a></li>';
                    $btn .= '</ul></div>';
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
    }

    public function waitingListAdd(WaitingListStoreRequest $request)
    {
        if (! Gate::allows('waitinglist-add')) { return abort(403); }
        if( $request->method() == 'POST') {
            $record = $this->waitingListService->registerWaitingList($request);
            if( $record ) {
                setflashmsg(trans('msg.waitingListCreated'), 1);
                return redirect()->route('admin.waitinglist.list');
            } else {
                setflashmsg(trans('msg.someError'), 0);
                return redirect()->route('admin.waitinglist.add')
                    ->withInput($request->input());
            }
        }

        $courseService = new CourseService;
        $courseList = $courseService->getAllCourseListWithRelationForBooking(['courseMain'], 'waitinglist');
        return view('admin.waitinglist.add', compact('courseList'));
    }

    public function waitingListEdit($id, WaitingListStoreRequest $request)
    {
        if (! Gate::allows('waitinglist-edit')) { return abort(403); }
        if( $request->method() == 'POST') {
            $oldRecord = $this->waitingListService->getWaitingListById($id);
            $record = $this->waitingListService->updateWaitingList($id, $request);
            if( isset($record['success']) ) {
                setflashmsg($record['msg'], 0);
                return redirect()->route('admin.waitinglist.list');
            } else {
                if( $oldRecord->status == \App\Models\WaitingList::STATUS_PENDING &&
                    $record->status == \App\Models\WaitingList::STATUS_ACCEPTED ) {
                    setflashmsg(trans('msg.waitingListAccepted'), 1);
                    return redirect()->route('admin.studentenrolment.add', ['waitinglist' => $record->id]);
                } else {
                    setflashmsg(trans('msg.waitingListUpdated'), 1);
                    return redirect()->route('admin.waitinglist.list');
                }
            }
        }

        $data = $this->waitingListService->getWaitingListById($id);
        $courseService = new CourseService;
        $courseList = $courseService->getAllCourseListWithRelationForBooking(['courseMain'], 'waitinglist');
        return view('admin.waitinglist.edit', compact('data','courseList'));
    }

    public function waitingNotesView(Request $request)
    {
        $main_id = $request->get('id');
        $data = $this->waitingListService->getWaitingListById($main_id);
        $view = view('admin.partial.view-notes', compact('data'))->render();
        $data = [ 'status' => true, 'html' => $view ];
        return response()->json($data);
    }
}
