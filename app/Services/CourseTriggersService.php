<?php

namespace App\Services;

use App\Models\CourseRunTriggers;
use App\Models\EmailTemplate;
use Auth;
use Log;

class CourseTriggersService
{
    protected $courseTrigger_model;

    public function __construct()
    {
        $this->courseTrigger_model = new CourseRunTriggers;
    }

    public function getAllCourseTriggers($request)
    {
        $courseTriggers = $this->courseTrigger_model->with(['smsTemplate', 'courseTags']);
        $coursemainId = $request->get('coursemain');
        if( is_array($coursemainId) ) {
            $courseTriggers->whereHas('courseMains', function ($q) use ($coursemainId) {
                return $q->whereIn('course_mains.id', $coursemainId);
            });
        }
        $eventType = $request->get('event_type');
        if( is_array($eventType) ) {
            $courseTriggers->whereIn('event_type', $eventType);
        } else if( $eventType > 0 ) {
            $courseTriggers->where('event_type', $eventType);
        }

        $eventWhen = $request->get('event_when');
        if( is_array($eventWhen) ) {
            $courseTriggers->whereIn('event_when', $eventWhen);
        } else if( $eventWhen > 0 ) {
            $courseTriggers->where('event_when', $eventWhen);
        }

        $status = $request->get('status');
        if(isset($status)){
            $courseTriggers->where('status', $status);
        }

        $courseTags = $request->get('coursetag');

        if (is_array($courseTags)) {
            $courseTriggers->whereHas('courseTags', function ($query) use ($courseTags) {
                $query->whereIn('course_tags.id', $courseTags);
            });
        }
                
        $from  =  $request->get('from');
        $to    =  $request->get('to');

        if(!empty($from) || empty($from) && !empty($to)) {
            if(in_array(1, $eventWhen) && $from == 0 && $to == 2) {
                $courseTriggers->where('no_of_days', '>', $from)->where('no_of_days', '<', $to);
            } else {
                if($from == 0 && $to == 4) {
                    $courseTriggers->where('no_of_days', '=>', $from)->where('no_of_days', '=<', $to);
                } elseif ($from == 6 && $to == 8) {
                    $courseTriggers->where('no_of_days', '>', $from)->where('no_of_days', '<', $to);
                } elseif ($from > 15 && $to < 30) {
                    $courseTriggers->where('no_of_days', '>', $from)->where('no_of_days', '<', $to);
                } elseif ($from >= 0 && $to <= 15) {
                    $courseTriggers->where('no_of_days', '>', $from)->where('no_of_days', '<', $to);
                }
            }
        }
                
        return $courseTriggers;
    }

    public function getAllCourseTriggersList()
    {
        return $this->courseTrigger_model->get();
    }

    public function getCourseTriggerById($id)
    {
        return $this->courseTrigger_model->find($id);
    }

    public function registerCourseTrigger($request)
    {
        $coursemainIds = $request->get('coursemain');
        $coursetagsIds = $request->get('coursetag');
        // get last group number
        if( is_null($coursemainIds) && $request->get('event_type') == CourseRunTriggers::EVENT_TYPE_TEXT ) {
            // add single record and it should be text type only
            $record = new CourseRunTriggers;
            $record->triggerTitle               = $request->get('triggerTitle');
            $record->event_when                 = $request->get('event_when');
            $record->event_type                 = $request->get('event_type');
            if( !empty($request->get('no_of_days')) ) {
                $record->no_of_days             = $request->get('no_of_days');
            }
            if( !empty($request->get('date_in_month')) ) {
                $record->date_in_month          = $request->get('date_in_month');
            }
            if( !empty($request->get('day_of_week')) ) {
                $record->day_of_week            = $request->get('day_of_week');
            }
            $record->task_text                  = $request->get('task_text');
            $record->status                     = $request->has('status') ? 1 : 0;
            $record->priority                   = $request->get('priority');
            $record->created_by                 = Auth::Id();
            $record->updated_by                 = Auth::Id();

            $record->save();
            $record->courseTags()->sync($coursetagsIds);
        } else {
            if( is_array($coursemainIds) ) {
                $record = new CourseRunTriggers;

                $record->triggerTitle               = $request->get('triggerTitle');
                $record->event_when                 = $request->get('event_when');
                $record->event_type                 = $request->get('event_type');
                if( !empty($request->get('no_of_days')) ) {
                    $record->no_of_days             = $request->get('no_of_days');
                }
                if( !empty($request->get('date_in_month')) ) {
                    $record->date_in_month             = $request->get('date_in_month');
                }
                if( !empty($request->get('day_of_week')) ) {
                    $record->day_of_week             = $request->get('day_of_week');
                }
                // for Email Templates
                if( $record->event_type == CourseRunTriggers::EVENT_TYPE_EMAIL ) {
                    // split Template name and slug
                    $temp = explode( "__!!__", $request->get('template_name') );
                    if( !empty($temp[0]) ) {
                        $record->template_name  = $temp[0];
                    }
                    if( !empty($temp[1]) ) {
                        $record->template_slug  = $temp[1];
                    }
                } else if( $record->event_type == CourseRunTriggers::EVENT_TYPE_SMS ) {
                    // template for SMS
                    $record->sms_template_id    = $request->get('sms_template');
                } else if( $record->event_type == CourseRunTriggers::EVENT_TYPE_TEXT ) {
                    $record->task_text = $request->get('task_text');
                }
                $record->status                     = $request->has('status') ? 1 : 0;
                $record->created_by                 = Auth::Id();
                $record->updated_by                 = Auth::Id();

                $record->save();
                $record->courseMains()->attach($coursemainIds);
                $record->courseTags()->attach($coursetagsIds);
            }
        }
        return TRUE;
    }

    public function updateCourseTrigger($id, $request)
    {
        $record = $this->getCourseTriggerById($id);
        $coursemainIds = $request->get('coursemain');
        $coursetagsIds = $request->get('coursetag');

        if( $record && is_null($coursemainIds) && $request->get('event_type') == CourseRunTriggers::EVENT_TYPE_TEXT ) {
            $record->triggerTitle               = $request->get('triggerTitle');
            $record->event_when                 = $request->get('event_when');
            $record->event_type                 = $request->get('event_type');
            if( !empty($request->get('no_of_days')) ) {
                $record->no_of_days             = $request->get('no_of_days');
            }
            if( !empty($request->get('date_in_month')) ) {
                $record->date_in_month             = $request->get('date_in_month');
            }
            if( !empty($request->get('day_of_week')) ) {
                $record->day_of_week             = $request->get('day_of_week');
            }
            $record->template_name = null;
            $record->template_slug = null;
            $record->sms_template_id = null;
            $record->task_text = $request->get('task_text');
            $record->status                     = $request->has('status') ? 1 : 0;
            $record->priority                   = $request->get('priority');
            $record->updated_by = Auth::Id();
            $record->save();
            $record->courseMains()->sync($coursemainIds);
            $record->courseTags()->sync($coursetagsIds);

            return $record;
        } else {
            // now get the coursemainids which are already there in this group
            if( is_array($coursemainIds) ) {
                // check if this record exists or not
                $courseTrigger = CourseRunTriggers::where('id', $id)->first();
                $courseTrigger->triggerTitle               = $request->get('triggerTitle');
                $courseTrigger->event_when                 = $request->get('event_when');
                $courseTrigger->event_type                 = $request->get('event_type');
                if( !empty($request->get('no_of_days')) ) {
                    $courseTrigger->no_of_days             = $request->get('no_of_days');
                }
                if( !empty($request->get('date_in_month')) ) {
                    $courseTrigger->date_in_month             = $request->get('date_in_month');
                }
                if( !empty($request->get('day_of_week')) ) {
                    $courseTrigger->day_of_week             = $request->get('day_of_week');
                }
                // for Email Templates
                if( $courseTrigger->event_type == CourseRunTriggers::EVENT_TYPE_EMAIL ) {
                    // split Template name and slug
                    $temp = explode( "__!!__", $request->get('template_name') );
                    if( !empty($temp[0]) ) {
                        $courseTrigger->template_name  = $temp[0];
                    }
                    if( !empty($temp[1]) ) {
                        $courseTrigger->template_slug  = $temp[1];
                    }
                    $courseTrigger->sms_template_id = null;
                    $courseTrigger->task_text = null;
                } else if( $courseTrigger->event_type == CourseRunTriggers::EVENT_TYPE_SMS ) {
                    // template for SMS
                    $courseTrigger->sms_template_id    = $request->get('sms_template');
                    $courseTrigger->template_name = null;
                    $courseTrigger->template_slug = null;
                    $courseTrigger->task_text = null;
                } else if( $courseTrigger->event_type == CourseRunTriggers::EVENT_TYPE_TEXT ) {
                    $courseTrigger->task_text = $request->get('task_text');
                    $courseTrigger->template_name = null;
                    $courseTrigger->template_slug = null;
                    $courseTrigger->sms_template_id = null;
                }
                $courseTrigger->status                     = $request->has('status') ? 1 : 0;
                $courseTrigger->priority                   = $request->get('priority');
                $courseTrigger->updated_by                 = Auth::Id();

                $courseTrigger->save();
                $courseTrigger->courseMains()->sync($coursemainIds);
                $courseTrigger->courseTags()->sync($coursetagsIds);
            }

            return true;
        }

        return false;
    }

    public function getEmailTemplatesList()
    {
        $emailTemplate = EmailTemplate::whereIn('template_for', [EmailTemplate::TRIGGER_TYPE_COURSE])->get();

        return $emailTemplate;
    }

    public function getAllCourseTriggersForSMS($eventWhen)
    {
        return $this->courseTrigger_model->active()
            ->with('courseMains', 'courseTags.courseMains')
            ->where('event_when', $eventWhen)->where('event_type', CourseRunTriggers::EVENT_TYPE_SMS)->with(['smsTemplate'])->get();
    }

    public function getAllCourseTriggersForEmail($eventWhen)
    {
        return $this->courseTrigger_model->active()->where('event_when', $eventWhen)->where('event_type', CourseRunTriggers::EVENT_TYPE_EMAIL)->get();
    }

    public function getAllCourseTriggersForEventWhen($eventWhen, $eventData)
    {
        $courseTriggers = $this->courseTrigger_model->active()->where('event_when', $eventWhen);
        $courseTriggers->with('courseMains', 'courseTags.courseMains');
        //
        if( $eventWhen === CourseRunTriggers::EVENT_WHEN_DAYS_BEFORE ) {
            // $courseTriggers->where('no_of_days', $eventData);
        } else if ( $eventWhen === CourseRunTriggers::EVENT_WHEN_TIME_OF_MONTH ) {
            $courseTriggers->where('date_in_month', $eventData);
        } else if ( $eventWhen === CourseRunTriggers::EVENT_WHEN_DAY_OF_WEEK ) {
            $courseTriggers->where('day_of_week', $eventData);
        } else if ( $eventWhen === CourseRunTriggers::EVENT_WHEN_DAYS_AFTER ) {
            // $courseTriggers->where('day_of_week', $eventData);
        }

        return $courseTriggers->get();
    }

}
