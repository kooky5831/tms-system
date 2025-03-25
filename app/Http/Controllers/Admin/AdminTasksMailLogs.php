<?php

namespace App\Http\Controllers\Admin;

use DataTables;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\AdminTasksMailLog;
use App\Http\Controllers\Controller;
use App\Services\AdminTaskMailLogService;

class AdminTasksMailLogs extends Controller
{
        /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(AdminTaskMailLogService $adminTaskMailLogService){
        $this->middleware('auth');
        $this->adminTaskMailLogService = $adminTaskMailLogService;
    }

    public function index(){
        return view('admin.maillogs.list');
    }

    public function listDatatable(Request $request){

        $records = $this->adminTaskMailLogService->getAllEmailLogsData($request);

        return DataTables::of($records)
                            ->editColumn('mail_logs_subject', function($row){
                                return '<a href="'.route('admin.maillogs.viewmaillogs',$row->id).'" target="_blank">' . $row->mail_logs_subject . '</a>'; 
                            })
                            ->editColumn('mail_logs_time', function($row){
                                return Carbon::parse($row->mail_logs_time)->format('g:i a' ); 
                            })
                            ->editColumn('created_at', function($row){
                                return Carbon::parse($row->created_at)->format('Y-m-d' ); 
                            })
                            ->addColumn('action', function($row) {
                                $btn = '';
                                $btn .= '
                                    <div class="dropdown dot-list">
                                    <a href="#" class="dropdown-toggle rounded-bdr" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="list-dots"></span></a>
                                        <ul  class="dropdown-menu">';
                                            $btn .= '<li><a href="'.route('admin.maillogs.viewmaillogs',$row->id).'"><i class="fas fa-eye font-16"></i>View</a></li>';
                                    $btn .= '</ul>
                                    </div>
                                ';
                                return $btn;
                            })
                            ->rawColumns(['action', 'mail_logs_subject'])
                            ->make(true);

    }

    public function viewMailLogs($id){
        $data = AdminTasksMailLog::find($id);
        return view('admin.maillogs.view', compact('data'));
    }


}
