<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SoftBookingStoreRequest;
use App\Services\SoftBookingService;
use App\Services\CourseService;
use Illuminate\Support\Facades\Gate;
use App\Services\CommonService;
use Illuminate\Http\Request;
use DataTables;

class SoftBookingController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(SoftBookingService $softBookingService)
    {
        $this->middleware('auth');
        $this->softBookingService = $softBookingService;
    }

    /**
     * Show the list of course types.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        if (! Gate::allows('softbooking-list')) { return abort(403); }
        return view('admin.softbooking.list');
    }

    public function listDatatable(Request $request)
    {
        if (! Gate::allows('softbooking-list')) { return abort(403); }
        $admins = $this->softBookingService->getAllSoftBooking();
        return  Datatables::of($admins)
                ->addIndexColumn()
                ->editColumn('course_id', function($row) {
                    return $row->tpgateway_id." (".$row->course_start_date. ") ".
                    " - ".$row->coursermainname;
                })
                ->filterColumn('course_id', function($query, $keyword) {
                    $query->where('tpgateway_id', 'LIKE', '%'.strtolower($keyword).'%');
                })
                ->editColumn('nric', function($row) {
                    return convertNricToView($row->nric);
                })
                ->editColumn('status', function($row) {
                    if( $row->status == 0 ) { return '<span class="badge badge-soft-primary">'.getCourseSoftBookingStatus($row->status).'</span>'; }
                    elseif( $row->status == 1 ) { return '<span class="badge badge-soft-success">'.getCourseSoftBookingStatus($row->status).'</span>'; }
                    else { return '<span class="badge badge-soft-danger">'.getCourseSoftBookingStatus($row->status).'</span>'; }
                })
                ->editColumn('deadline_date', function($row) {
                    return $row->deadline_date->format('d M Y');
                })
                ->filterColumn('status', function($query, $keyword) {
                    $len = strlen($keyword);
                    if( (substr('booked', 0, $len) === strtolower($keyword)) ) {
                        $query->where('status', 1);
                    }
                    if( (substr('pending', 0, $len) === strtolower($keyword)) ) {
                        $query->where('status', 0);
                    }
                    if( (substr('cancelled', 0, $len) === strtolower($keyword)) ) {
                        $query->where('status', 2);
                    }
                    if( (substr('expired', 0, $len) === strtolower($keyword)) ) {
                        $query->where('status', 3);
                    }
                })
                ->addColumn('action', function($row) {
                    $btn = '
                    <div class="dropdown dot-list">
                        <a href="#" class="dropdown-toggle rounded-bdr" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="list-dots"></span></a>
                        <ul  class="dropdown-menu">';
                        if( $row->status == 0){
                            $btn .= '<li><a href="'.route('admin.studentenrolment.add').'?softbooking='.$row->id.'"><i class="fas fa-user font-16"></i> Enroll Student</a></li>';
                        }
                        $btn .= '<li><a href="'.route('admin.softbooking.edit',$row->id).'"><i class="fas fa-pencil-alt font-16"></i> Edit</a></li>';
                        $btn .= '<li><a href="javascript:void(0)" class="viewnotes" main_id="'.$row->id.'"><i class="mdi mdi-note font-16"></i> View Note</a></li>';
                        // $btn .= '<li><a href="javascript:void(0)" class="viewnotes" main_id="'.$row->id.'"><i class="far fa-trash-alt font-16"></i> Delete</a></li>';
                    $btn .= '</ul></div>';
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
    }

    public function softBookingAdd(SoftBookingStoreRequest $request)
    {
        if (! Gate::allows('softbooking-add')) { return abort(403); }
        if( $request->method() == 'POST') {
            $record = $this->softBookingService->registerSoftBooking($request);
            if( $record ) {
                setflashmsg(trans('msg.softBookingCreated'), 1);
                return redirect()->route('admin.softbooking.list');
            } else {
                setflashmsg(trans('msg.someError'), 0);
                return redirect()->route('admin.softbooking.add')
                    ->withInput($request->input());
            }
        }

        $courseService = new CourseService;
        $courseList = $courseService->getAllCourseListWithRelationForBooking(['courseMain']);
        return view('admin.softbooking.add', compact('courseList'));
    }

    public function softBookingEdit($id, SoftBookingStoreRequest $request)
    {
        if (! Gate::allows('softbooking-edit')) { return abort(403); }
        if( $request->method() == 'POST') {
            $oldRecord = $this->softBookingService->getSoftBookingById($id);
            $record = $this->softBookingService->updateSoftBooking($id, $request);
            if( isset($record['success']) ) {
                setflashmsg($record['msg'], 0);
                return redirect()->route('admin.softbooking.list');
            } else {
                if( $oldRecord->status == \App\Models\CourseSoftBooking::STATUS_PENDING &&
                    $record->status == \App\Models\CourseSoftBooking::STATUS_BOOKED ) {
                    setflashmsg(trans('msg.softBookingAccepted'), 1);
                    return redirect()->route('admin.studentenrolment.add', ['softbooking' => $record->id]);
                } else {
                    setflashmsg(trans('msg.softBookingUpdated'), 1);
                    return redirect()->route('admin.softbooking.list');
                }
            }
        }

        $data = $this->softBookingService->getSoftBookingById($id);
        $courseService = new CourseService;
        $courseList = $courseService->getAllCourseListWithRelationForBooking(['courseMain']);
        return view('admin.softbooking.edit', compact('data','courseList'));
    }

    public function softNotesView(Request $request)
    {
        $main_id = $request->get('id');
        $data = $this->softBookingService->getSoftBookingById($main_id);
        $view = view('admin.partial.view-notes', compact('data'))->render();
        $data = [ 'status' => true, 'html' => $view ];
        return response()->json($data);
    }
}
