<?php

namespace App\Services;

use App\Models\SMSTemplates;
use Auth;

class SMSTemplateService
{
    protected $smsTemplate_model;

    public function __construct()
    {
        $this->smsTemplate_model = new SMSTemplates;
    }

    public function getAllSMSTemplates()
    {
        return $this->smsTemplate_model->orderBy('id', 'desc');
    }

    public function getAllSMSTemplatesList()
    {
        return $this->smsTemplate_model->active()->get();
    }

    public function getSMSTemplateById($id)
    {
        return $this->smsTemplate_model->find($id);
    }

    public function registerSMSTemplate($request)
    {
        $record = $this->smsTemplate_model;

        $record->name                       = $request->get('name');
        $record->description                = $request->get('description');
        $record->content                    = $request->get('content');
        // $record->status                     = $request->has('status') ? 1 : 0;

        $record->created_by                 = Auth::Id();
        $record->updated_by                 = Auth::Id();
        $record->save();
        return $record;
    }

    public function updateSMSTemplate($id, $request)
    {
        $record = $this->getSMSTemplateById($id);
        if( $record ) {
            $record->name                       = $request->get('name');
            $record->description                = $request->get('description');
            $record->content                    = $request->get('content');
            // $record->status                     = $request->has('status') ? 1 : 0;
            $record->updated_by = Auth::Id();
            $record->save();
            return $record;
        }
        return false;
    }

    public function searchSMSTemplateAjax($q)
    {
        $smsTemplates = $this->smsTemplate_model->where('name', 'like', '%'.$q.'%')
                            ->orWhere('description', 'like', '%'.$q.'%')
                            ->active()
                            ->limit(7)->get();
        $ret = [];
        foreach ($smsTemplates as $template) {
            $ret[] = [
                "id"    => $template->id,
                "text"  => $template->name." - ".$template->description,
            ];
        }
        return $ret;
    }

}
