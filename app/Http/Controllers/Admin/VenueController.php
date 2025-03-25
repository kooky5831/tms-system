<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\VenueStoreRequest;
use App\Services\VenueService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use DataTables;


class VenueController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(VenueService $venueService)
    {
        $this->middleware('auth');
        $this->venueService = $venueService;
    }

    /**
     * Show the admin users.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        if (! Gate::allows('venue-list')) { return abort(403); }
        return view('admin.venue.list');
    }

    public function listDatatable(Request $request)
    {
        if (! Gate::allows('venue-list')) { return abort(403); }
        $records = $this->venueService->getAllVenue();
        return Datatables::of($records)
                ->addIndexColumn()
                ->editColumn('status', function($row) {
                    if( $row->status ) { return '<span class="badge badge-soft-success">Active</span>'; }
                    else { return '<span class="badge badge-soft-danger">Inactive</span>'; }
                })
                ->editColumn('wheelchairaccess', function($row) {
                    if( $row->wheelchairaccess ) { return '<span class="badge badge-soft-success">Yes</span>'; }
                    else { return '<span class="badge badge-soft-danger">No</span>'; }
                })
                ->filterColumn('wheelchairaccess', function($query, $keyword) {
                    $len = strlen($keyword);
                    if( (substr('yes', 0, $len) === strtolower($keyword)) ) {
                        $query->where('wheelchairaccess', 1);
                    }
                    if( (substr('no', 0, $len) === strtolower($keyword)) ) {
                        $query->where('wheelchairaccess', 0);
                    }
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
                ->addColumn('action', function($row) {
                    // $btn = '<a href="'.route('admin.doctor.view',$row->id).'" data-toggle="tooltip" data-placement="bottom" title="View" class="mr-2"><i class="fas fa-eye text-info font-16"></i></a>
                    //<a href="'.route('admin.venue.edit',$row->id).'" data-toggle="tooltip" data-placement="bottom" title="Edit" class="mr-2"><i class="fas fa-edit text-info font-16"></i></a>';
                    $btn = '<div class="dropdown dot-list">
                            <a href="#" class="dropdown-toggle rounded-bdr" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="list-dots"></span></a>
                                <ul  class="dropdown-menu">';
                                    $btn .= '<li><a href="'.route('admin.venue.edit',$row->id).'"><i class="fas fa-pencil-alt font-16"></i> Edit</a></li>';
                                    // $btn .= '<li><a href="'.route('admin.venue.edit',$row->id).'"><i class="far fa-trash-alt font-16"></i> Delete</a></li>';
                    $btn .= '</ul></div>';
                    return $btn;
                })
                ->rawColumns(['action','wheelchairaccess','status'])
                ->make(true);

    }

    public function venueAdd(VenueStoreRequest $request)
    {
        if (! Gate::allows('venue-add')) { return abort(403); }
        if( $request->method() == 'POST') {
            $record = $this->venueService->addVenue($request);
            if( $record ) {
                setflashmsg(trans('msg.venueCreated'), 1);
                return redirect()->route('admin.venue.list');
            }
        }
        return view('admin.venue.add');
    }

    public function venueEdit($id, VenueStoreRequest $request)
    {
        if ( \Auth::user()->role != 'superadmin' ) { return abort(403); }
        if( $request->method() == 'POST') {
            $admin = $this->venueService->updateVenue($id, $request);
            if( $admin ) {
                setflashmsg(trans('msg.venueUpdated'), 1);
                return redirect()->route('admin.venue.list');
            }
        }
        $data = $this->venueService->getVenueById($id);
        return view('admin.venue.edit', compact('data'));
    }

    /*public function trainerView($id)
    {
        if (! Gate::allows('trainer-view')) { return abort(403); }
        dd('pending');
        $doctor = $this->doctorService->getDoctorById($id);
        return view('backend.doctors.view', compact('doctor'));
    }*/

}
