<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\ErrorException;
use DataTables;
use Auth;

class AdminErrorsController extends Controller
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
     * Show the list of Admin Tasks for Course Runs
     *
     * @return \Illuminate\Contracts\Support\Renderable
    */
    public function index(Request $request)
    {
        // if (! Gate::allows('adminerrors-list')) { return abort(403); }
        // return view('admin.adminerrors.list');

    }

    /*public function listDatatable(Request $request)
    {
        if (! Gate::allows('adminerrors-list')) { return abort(403); }
        
        $records = $this->getAllErrors($request);
        
        return Datatables::of($records)
                ->addIndexColumn()
                ->editColumn('datetime', function($row) {
                    if( $row->datetime ) { 
                        return date('d-m-Y h:i a', strtotime($row->datetime));
                     }
                })
                ->editColumn('status', function($row) {
                    if( $row->status == ErrorException::PENDING ) { return '<span class="badge badge-soft-warning">Pending</span>'; }
                    else  { return '<span class="badge badge-soft-success">Resolved</span>'; }

                })

                ->addColumn('action', function($row) {
                    
                    return '<a href="javascript:void(0)" exp_id="'.$row->id.'" data-toggle="tooltip" data-placement="bottom" title="Mark as Resolved" class="btn btn-success btn-sm mr-2 updateexcpstatus"><i class="mdi mdi-check font-16"></i></a>';
                })
                
                ->rawColumns(['datetime', 'status', 'action'])
                ->make(true);

    }*/

    /*public function getAllErrors($request)
    {
        $s = ErrorException::where('id', '!=', 0);

        $startDate = $request->get('from');
        $endDate = $request->get('to');

        if( $startDate ) {
            $s->whereDate('datetime', '>=', date("Y-m-d", strtotime($startDate)));
        }
        if( $endDate ) {
            $s->whereDate('datetime', '<=', date("Y-m-d", strtotime($endDate)));
        }

        $status = $request->get('status');
        if( is_array($status) ) {
            $s->whereIn('status', $status);
        } else if( $status > 0 ) {
            $s->where('status', $status);
        }

        return $s;
    }*/

   /* public function updateStatus(Request $request)
    {
        $expId = $request->get('id');
        $data = ErrorException::find($expId);
        $view = view('admin.partial.update-exception-status', compact('data'))->render();
        $data = [ 'status' => true, 'html' => $view ];
        return response()->json($data);
    }

    public function updateException(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'exception_id' => 'required',
            'notes' => 'required',
        ],[
            'exception_id.required'   => 'No Exception Found',
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
        $response = $this->updateExceptionStatus($request);
        return response()->json($response, 200);
    }

    public function updateExceptionStatus($request)
    {
        $exception_id = $request->get('exception_id');
        $record = ErrorException::find($exception_id);
        if( empty($record->id) ) {
            return ['success' => false, 'message' => 'No Exception Found'];
        }
        $record->notes = $request->get('notes');

        $record->status = ErrorException::RESOLVED;
        $record->save();
        if( $record ) {
            return ['success' => true, 'message' => 'Exception Mark as Resolved successfully'];
        } else {
            return ['success' => false, 'message' => 'Exception not updated'];
        }
    }*/
}
