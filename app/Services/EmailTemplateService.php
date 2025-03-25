<?php

namespace App\Services;

use App\Models\EmailTemplate;
use Illuminate\Support\Str;

class EmailTemplateService
{
    protected $emailTemplate_model;

    public function __construct()
    {
        $this->emailTemplate_model = new EmailTemplate;
    }

    public function getAllEmailTemplates()
    {
        return $this->emailTemplate_model->orderBy('id', 'desc');
    }

    public function getAllEmailTemplatesList()
    {
        return $this->emailTemplate_model->active()->get();
    }

    public function getEmailTemplateById($id)
    {
        return $this->emailTemplate_model->find($id);
    }

    public function registerEmailTemplate($request)
    {
        $record = $this->emailTemplate_model;

        $record->description                = $request->get('description');
        $record->subject                    = $request->get('subject');
        $record->slug                       = Str::slug($request->get('subject'), '-');
        $record->template_for               = $request->get('template_for');
        $record->template_text              = $request->get('template_text');
        $record->keywords                   = $request->get('keywords');
        $record->is_send_certificate        = $request->get('is_send_certificate') ? 1 : 0;
        // $record->status                     = $request->has('status') ? 1 : 0;

        $record->save();
        return $record;
    }

    public function updateEmailTemplate($id, $request)
    {
        $record = $this->getEmailTemplateById($id);
        if( $record ) {
            $record->description                = $request->get('description');
            $record->subject                    = $request->get('subject');
            $record->template_for               = $request->get('template_for');
            $record->template_text              = $request->get('template_text');
            $record->is_send_certificate        = $request->get('is_send_certificate') ? 1 : 0;
            $record->save();
            return $record;
        }
        return false;
    }

    public function searchEmailTemplateAjax($q)
    {
        $emailTemplates = $this->emailTemplate_model->where('subject', 'like', '%'.$q.'%')
                            ->orWhere('description', 'like', '%'.$q.'%')
                            ->orWhere('slug', 'like', '%'.$q.'%')
                            ->active()
                            ->limit(7)->get();
        $ret = [];
        foreach ($emailTemplates as $template) {
            $ret[] = [
                "id"    => $template->id,
                "text"  => $template->name." - ".$template->description,
            ];
        }
        return $ret;
    }

}
