<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use DataTables;

class SettingController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }
    //Invoice settings
    public function getAllSettings(){
        $allSettings = Settings::all()->groupBy('group');
        $flattenedSettings = collect();
        foreach ($allSettings as $group => $settings) {
            if($group != 'feedback'){
                $flattenedSettings->push($settings->first());
            }
        }
        return $flattenedSettings;
        // return $allSettings;
    }

    public function invoiceSettings(){
        return view('admin.invoicesettings.list');
    }

    public function listDatatable(){
        $records = $this->getAllSettings();
        return Datatables::of(($records))
                ->addIndexColumn()
                ->editColumn('name', function($row) {
                    return $row->group;
                })
                ->addColumn('action', function($row) {
                    $btn = '';
                    $btn .= '
                        <div class="dropdown dot-list">
                        <a href="#" class="dropdown-toggle rounded-bdr" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="list-dots"></span></a>
                            <ul  class="dropdown-menu">';
                            $btn .= '<li><a href="'.route('admin.invoicesettings.edit.settings', "invoice").'"><i class="fas fa-pencil-alt font-16"></i> Edit</a></li>
                            </ul>
                        </div>
                    ';
                    return $btn;
                })
                ->rawColumns(['name', 'action'])
                // ->groupBy('group')
                ->make(true);
    }

    public function setInvoiceSetting(Request $request){
        if($request->hasFile('invoice_logo')){
            $invoiceLogo = $request->file('invoice_logo');
            $logoPath = 'invoice_logo.jpg';
            
            $storedLogoPath = Storage::putFileAs('', $invoiceLogo, 'public/invoice-image/'.$logoPath);
            
            if(!empty($invoiceLogo)){
                Settings::updateOrCreate(
                    ['name' => 'invoice_logo'],
                    [
                        'val' => $logoPath, 
                        'group' => 'invoice'
                    ]
                );
            }
        }
        if($request->hasFile('payment_qr')){
            $invoiceQr = $request->file('payment_qr');   
            $qrPath = 'invoice_qr.jpg';
            $storedQrPath = Storage::putFileAs('', $invoiceQr, 'public/invoice-image/'.$qrPath);
            if(!empty($qrPath)){
                Settings::updateOrCreate(
                    ['name' => 'invoice_qr'],
                    [
                        'val' => $qrPath, 
                        'group' => 'invoice'
                    ]
                );
            }

        }
        if($request->has('payment_terms')){
            Settings::updateOrCreate(
                ['name' => 'payment_terms'],
                [
                    'val' => $request->get('payment_terms'), 
                    'group' => 'invoice'
                ]
            );
        }
        if($request->has('payment_methods')){
            Settings::updateOrCreate(
                ['name' => 'payment_methods'],
                [
                    'val' => $request->get('payment_methods'), 
                    'group' => 'invoice'
                ]
            );
        }
        if($request->has('invoice_address')){
            Settings::updateOrCreate(
                ['name' => 'invoice_address'],
                [
                    'val' => $request->get('invoice_address'), 
                    'group' => 'invoice'
                ]
            );
        }
        $updateSettings = Settings::where('group', 'invoice')->get();
        if(!empty($updateSettings)){
            setflashmsg("Update setting successfully", 1);
            return redirect()->back();
        }else{
            setflashmsg("Something went wrong", 0);
            return redirect()->back();
        }
    }

    public function editSettings($group){
        $getSettings = Settings::where('group', $group)->get();
        $invoiceSettingData = [];
        foreach($getSettings as $value){
            $invoiceSettingData[$value->name] = $value->val;
        }
        return view('admin.invoicesettings.edit', compact('getSettings', 'invoiceSettingData'));
    }


    public function editFeedBackSettings(){
        $getSettings = Settings::where('group', 'feedback')->get();
        $feedbackSettingData = [];
        foreach($getSettings as $value){
            $feedbackSettingData[$value->name] = $value->val;
        }
        return view('admin.feedbacksettings.edit', compact('getSettings', 'feedbackSettingData'));
    }

    public function setFeedbackSetting(Request $request) {
        if($request->hasFile('feedback_qr')){
            $feedbackQr = $request->file('feedback_qr');   
            $qrPath = 'TRAQOM_QR.png';
            $storedQrPath = Storage::putFileAs('', $feedbackQr, 'public/feedback-qr-code/'.$qrPath);
            if(!empty($qrPath)){
                Settings::updateOrCreate(
                    ['name' => 'feedback_qr'],
                    [
                        'val' => $qrPath, 
                        'group' => 'feedback'
                    ]
                );
            }
        }
        if($request->has('feedback_text')){
            Settings::updateOrCreate(
                ['name' => 'feedback_text'],
                [
                    'val' => $request->get('feedback_text'), 
                    'group' => 'feedback'
                ]
            );
        }
        $updateSettings = Settings::where('group', 'feedback')->get();
        if(!empty($updateSettings)){
            setflashmsg("Update setting successfully", 1);
            return redirect()->back();
        }else{
            setflashmsg("Something went wrong", 0);
            return redirect()->back();
        }
    }
}
