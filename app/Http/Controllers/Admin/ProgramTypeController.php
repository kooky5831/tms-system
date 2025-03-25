<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProgramTypeStoreRequest;
use App\Services\ProgramTypeService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use DataTables;


class ProgramTypeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ProgramTypeService $programTypeService)
    {
        $this->middleware('auth');
        $this->programTypeService = $programTypeService;
    }

    /**
     * Show the admin users.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        if (! Gate::allows('programtype-list')) { return abort(403); }
        if ($request->ajax()) {
            $records = $this->programTypeService->getAllProgramTypeList();
            return Datatables::of($records)
                    ->addIndexColumn()
                    ->editColumn('status', function($row) {
                        if( $row->status ) { return '<span class="badge badge-soft-success">Active</span>'; }
                        else { return '<span class="badge badge-soft-danger">Inactive</span>'; }
                    })
                    ->addColumn('action', function($row) {
                        $btn = '<a href="'.route('admin.programtype.edit',$row->id).'" data-toggle="tooltip" data-placement="bottom" title="Edit" class="mr-2"><i class="fas fa-edit text-info font-16"></i></a>';
                        return $btn;
                    })
                    ->rawColumns(['action','status'])
                    ->make(true);
        }
        return view('admin.programtype.list');
    }

    public function programTypeAdd(ProgramTypeStoreRequest $request)
    {
        if (! Gate::allows('programtype-add')) { return abort(403); }
        if( $request->method() == 'POST') {
            $record = $this->programTypeService->addProgramType($request);
            if( $record ) {
                setflashmsg(trans('msg.programTypeCreated'), 1);
                return redirect()->route('admin.programtype.list');
            }
        }
        return view('admin.programtype.add');
    }

    public function programTypeEdit($id, ProgramTypeStoreRequest $request)
    {
        if (! Gate::allows('programtype-edit')) { return abort(403); }
        if( $request->method() == 'POST') {
            $admin = $this->programTypeService->updateProgramType($id, $request);
            if( $admin ) {
                setflashmsg(trans('msg.programTypeUpdated'), 1);
                return redirect()->route('admin.programtype.list');
            }
        }
        $data = $this->programTypeService->getProgramTypeById($id);
        return view('admin.programtype.edit', compact('data'));
    }

}
