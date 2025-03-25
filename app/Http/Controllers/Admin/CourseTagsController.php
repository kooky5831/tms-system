<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseTagStoreRequest;
use App\Http\Requests\CourseTagUpdateRequest;
use App\Services\CourseTagService;
use Illuminate\Http\Request;
use DataTables;

class CourseTagsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(CourseTagService $courseTagService)
    {
        $this->middleware('auth');
        $this->courseTagService = $courseTagService;
    }

    /**
     * Show the list of SMS Templates.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        if (\Auth::user()->role != 'superadmin') { return abort(403); }
        return view('admin.coursetags.list');
    }

    public function listDatatable(Request $request)
    {
        if (\Auth::user()->role != 'superadmin') { return abort(403); }
        $records = $this->courseTagService->getAllCourseTags();
        return Datatables::of($records)
            ->addIndexColumn()
            ->addColumn('action', function($row) {
                $btn = '<a href="'.route('admin.coursetags.edit',$row->id).'" data-toggle="tooltip" data-placement="bottom" title="Edit" class="mr-2"><i class="fas fa-edit text-info font-16"></i></a>';
                return $btn;
            })
            ->editColumn('status', function($row) {
                if( $row->status ) { return '<span class="badge badge-soft-success">Active</span>'; }
                else { return '<span class="badge badge-soft-danger">Inactive</span>'; }
            })
            ->rawColumns(['action','status'])
            ->make(true);
    }

    public function courseTagsAdd(CourseTagStoreRequest $request)
    {
        if (\Auth::user()->role != 'superadmin') { return abort(403); }
        if( $request->method() == 'POST') {
            $record = $this->courseTagService->registerCourseTag($request);
            if( $record ) {
                setflashmsg(trans('msg.courseTagCreated'), 1);
                return redirect()->route('admin.coursetags.list');
            }
        }
        return view('admin.coursetags.add');
    }

    public function courseTagEdit($id, CourseTagUpdateRequest $request)
    {
        if (\Auth::user()->role != 'superadmin') { return abort(403); }
        if( $request->method() == 'POST') {
            $record = $this->courseTagService->updateCourseTag($id, $request);
            if( $record ) {
                setflashmsg(trans('msg.courseTagUpdated'), 1);
                return redirect()->route('admin.coursetags.list');
            }
        }

        $data = $this->courseTagService->getCourseTagById($id);
        return view('admin.coursetags.edit', compact('data'));
    }
}