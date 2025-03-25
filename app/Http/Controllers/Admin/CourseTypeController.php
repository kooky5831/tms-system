<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseTypeStoreRequest;
use App\Services\CourseTypeService;
use Illuminate\Support\Facades\Gate;
use App\Services\CommonService;
use Illuminate\Http\Request;
use DataTables;


class CourseTypeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(CourseTypeService $courseTypeService)
    {
        $this->middleware('auth');
        $this->courseTypeService = $courseTypeService;
    }

    /**
     * Show the list of course types.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        if (! Gate::allows('coursetype-list')) { return abort(403); }

        if ($request->ajax()) {
            $admins = $this->courseTypeService->getAllCourseType();
            return Datatables::of($admins)
                    ->addIndexColumn()
                    ->editColumn('status', function($row) {
                        if( $row->status ) { return '<span class="badge badge-soft-success">Active</span>'; }
                        else { return '<span class="badge badge-soft-danger">Inactive</span>'; }
                    })
                    ->filterColumn('status', function($query, $keyword) {
                        $len = strlen($keyword);
                        if( (substr('active', 1, $len) === strtolower($keyword)) ) {
                            $query->where('status', 1);
                        }
                        if( (substr('inacive', 0, $len) === strtolower($keyword)) ) {
                            $query->where('status', 0);
                        }
                    })
                    ->addColumn('action', function($row) {
                        // $btn = '<a href="'.route('admin.doctor.view',$row->id).'" data-toggle="tooltip" data-placement="bottom" title="View" class="mr-2"><i class="fas fa-eye text-info font-16"></i></a>';
                        $btn = '<a href="'.route('admin.coursetype.edit',$row->id).'" data-toggle="tooltip" data-placement="bottom" title="Edit" class="mr-2"><i class="fas fa-edit text-info font-16"></i></a>';
                        return $btn;
                    })
                    ->rawColumns(['action','status'])
                    ->make(true);
        }
        return view('admin.coursetype.list');
    }

    public function courseTypeAdd(CourseTypeStoreRequest $request)
    {
        if (! Gate::allows('course-add')) { return abort(403); }
        if( $request->method() == 'POST') {
            $trainer = $this->courseTypeService->registerCourseType($request);
            if( $trainer ) {
                setflashmsg(trans('msg.courseTypeCreated'), 1);
                return redirect()->route('admin.coursetype.list');
            }
        }
        return view('admin.coursetype.add');
    }

    public function courseTypeEdit($id, CourseTypeStoreRequest $request)
    {
        if (! Gate::allows('coursetype-edit')) { return abort(403); }
        if( $request->method() == 'POST') {
            $allCourses = $this->courseTypeService->updateCourseType($id, $request);
            if( $allCourses ) {
                setflashmsg(trans('msg.courseTypeUpdated'), 1);
                return redirect()->route('admin.coursetype.list');
            }
        }

        $data = $this->courseTypeService->getCourseTypeById($id);
        return view('admin.coursetype.edit', compact('data'));
    }
}
