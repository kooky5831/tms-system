<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SMSTemplateStoreRequest;
use App\Http\Requests\SMSTemplateUpdateRequest;
use App\Services\SMSTemplateService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use DataTables;

class SMSTemplateController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(SMSTemplateService $smsTemplateService)
    {
        $this->middleware('auth');
        $this->smsTemplateService = $smsTemplateService;
    }

    /**
     * Show the list of SMS Templates.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        if (\Auth::user()->role != 'superadmin') { return abort(403); }
        return view('admin.smstemplate.list');

    }

    public function listDatatable(Request $request)
    {
        if (\Auth::user()->role != 'superadmin') { return abort(403); }
        $records = $this->smsTemplateService->getAllSMSTemplates();
        return Datatables::of($records)
                ->addIndexColumn()
                ->addColumn('action', function($row) {
                    $btn = '';
                    $btn .= '
                        <div class="dropdown dot-list">
                        <a href="#" class="dropdown-toggle rounded-bdr" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="list-dots"></span></a>
                            <ul  class="dropdown-menu">';
                            $btn .= '<li><a href="'.route('admin.smstemplates.edit',$row->id).'"><i class="fas fa-pencil-alt font-16"></i> Edit</a></li>
                            </ul>
                        </div>
                    ';
                    return $btn;
                })
                ->rawColumns(['action','status'])
                ->make(true);

    }

    public function smsTemplateAdd(SMSTemplateStoreRequest $request)
    {
        if (\Auth::user()->role != 'superadmin') { return abort(403); }
        if( $request->method() == 'POST') {
            $record = $this->smsTemplateService->registerSMSTemplate($request);
            if( $record ) {
                setflashmsg(trans('msg.smsTemplateCreated'), 1);
                return redirect()->route('admin.smstemplates.list');
            }
        }
        return view('admin.smstemplate.add');
    }

    public function smsTemplateEdit($id, SMSTemplateUpdateRequest $request)
    {
        if (\Auth::user()->role != 'superadmin') { return abort(403); }
        if( $request->method() == 'POST') {
            $record = $this->smsTemplateService->updateSMSTemplate($id, $request);
            if( $record ) {
                setflashmsg(trans('msg.smsTemplateUpdated'), 1);
                return redirect()->route('admin.smstemplates.list');
            }
        }

        $data = $this->smsTemplateService->getSMSTemplateById($id);
        return view('admin.smstemplate.edit', compact('data'));
    }

    public function searchSMSTemplates(Request $req)
    {
        $query = $req->get('q');
        $ret = $this->smsTemplateService->searchSMSTemplateAjax($query);
        return json_encode($ret);
    }
}
