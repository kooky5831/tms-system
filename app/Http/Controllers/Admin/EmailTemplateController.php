<?php

namespace App\Http\Controllers\Admin;

use App\Models\EmailTemplate;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmailTemplateStoreRequest;
use App\Http\Requests\EmailTemplateUpdateRequest;
use Illuminate\Http\Request;
use App\Services\EmailTemplateService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Arr;
use DataTables;

class EmailTemplateController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(EmailTemplateService $emailTemplateService)
    {
        $this->middleware('auth');
        $this->emailTemplateService = $emailTemplateService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (\Auth::user()->role != 'superadmin') { return abort(403); }
        return view('admin.emailtemplate.list');
    }

    public function listDatatable(Request $request)
    {
        if (\Auth::user()->role != 'superadmin') { return abort(403); }
        $records = $this->emailTemplateService->getAllEmailTemplates();
        return Datatables::of($records)
                ->addIndexColumn()
                ->editColumn('template_for', function($row) {
                    return emailTemplateTriggerTypes($row->template_for);
                })
                ->addColumn('action', function($row) {
                    $btn = '';
                    $btn .= '
                        <div class="dropdown dot-list">
                        <a href="#" class="dropdown-toggle rounded-bdr" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="list-dots"></span></a>
                            <ul  class="dropdown-menu">';
                            $btn .= '<li><a href="'.route('admin.emailtemplates.edit',$row->id).'"><i class="fas fa-pencil-alt font-16"></i> Edit</a></li>
                            </ul>
                        </div>
                    ';
                    return $btn;
                })
                ->filterColumn('template_for', function($query, $keyword) {
                    $len = strlen($keyword);
                    if( (substr('admin', 0, $len) === strtolower($keyword)) ) {
                        $query->where('template_for', 1);
                    }
                    if( (substr('course', 0, $len) === strtolower($keyword)) ) {
                        $query->where('template_for', 2);
                    }
                })
                ->rawColumns(['action','status'])
                ->make(true);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function emailTemplateAdd(EmailTemplateStoreRequest $request)
    {
        if (\Auth::user()->role != 'superadmin') { return abort(403); }
        $emailTemplateTriggerTypes = Arr::except(emailTemplateTriggerTypes(), [1]);
        if( $request->method() == 'POST') {
            $record = $this->emailTemplateService->registerEmailTemplate($request);
            if( $record ) {
                setflashmsg(trans('msg.emailTemplateCreated'), 1);
                return redirect()->route('admin.emailtemplates.list');
            }
        }
        return view('admin.emailtemplate.add', compact('emailTemplateTriggerTypes'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\EmailTemplate  $emailTemplate
     * @return \Illuminate\Http\Response
     */
    public function show(EmailTemplate $emailTemplate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\EmailTemplate  $emailTemplate
     * @return \Illuminate\Http\Response
     */
    public function edit(EmailTemplate $emailTemplate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\EmailTemplate  $emailTemplate
     * @return \Illuminate\Http\Response
     */
    public function emailTemplateEdit($id, EmailTemplateUpdateRequest $request)
    {
        if (\Auth::user()->role != 'superadmin') { return abort(403); }
        
        if( $request->method() == 'POST') {
            
            $record = $this->emailTemplateService->updateEmailTemplate($id, $request);
            if( $record ) {
                setflashmsg(trans('msg.emailTemplateUpdated'), 1);
                return redirect()->route('admin.emailtemplates.list');
            }
        }

        $data = $this->emailTemplateService->getEmailTemplateById($id);
        $emailTemplateTriggerTypes = Arr::except(emailTemplateTriggerTypes(), [1]);
        return view('admin.emailtemplate.edit', compact('data', 'emailTemplateTriggerTypes'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\EmailTemplate  $emailTemplate
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmailTemplate $emailTemplate)
    {
        //
    }
}
