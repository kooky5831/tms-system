<?php

namespace App\Services;

use App\Models\AdminTasks;
use Auth;
use Log;

class AdminTasksService
{
    protected $adminTasks_model;

    public function __construct()
    {
        $this->adminTasks_model = new AdminTasks;
    }

    public function getAllAdminTasks($request)
    {
        $s = $this->adminTasks_model->with(['course']);

        /*$coursemainId = $request->get('coursemain');
        if( is_array($coursemainId) ) {
            $s->whereIn('courses.course_main_id', $coursemainId);
        } else if( $coursemainId > 0 ) {
            $s->where('courses.course_main_id', $coursemainId);
        }*/


        $eventType = $request->get('event_type');
        if( is_array($eventType) ) {
            $s->whereIn('admin_tasks.task_type', $eventType);
        } else if( $eventType > 0 ) {
            $s->where('admin_tasks.task_type', $eventType);
        }

        $startDate = $request->get('from');
        $endDate = $request->get('to');

        if( $startDate ) {
            $s->whereDate('admin_tasks.created_at', '>=', date("Y-m-d", strtotime($startDate)));
        }
        if( $endDate ) {
            $s->whereDate('admin_tasks.created_at', '<=', date("Y-m-d", strtotime($endDate)));
        }

        $s->orderBy('status', 'desc');

        return $s;

        /*$s = $this->adminTasks_model->select('admin_tasks.*','courses.course_main_id','courses.course_start_date','course_mains.name as course_mainname','course_mains.reference_number', 'sms_templates.name as sms_templates_name')
        ->join('sms_templates', 'sms_templates.id', '=', 'admin_tasks.sms_template_id')
        ->join('courses', 'courses.id', '=', 'admin_tasks.course_id')
        ->join('course_mains', 'course_mains.id', '=', 'courses.course_main_id');
        $thiscourseRunId = $request->get('courserun');

        $courserunid = $request->get('courserunid');

        if( $thiscourseRunId > 0 ) {
            $s->where('courses.id', $thiscourseRunId);
        }

        if( $courserunid > 0 ) {
            $s->where('courses.tpgateway_id', $courserunid);
        }
        return $s;*/
    }

    public function getAllAdminTasksList()
    {
        return $this->adminTasks_model->get();
    }

    public function getAdminTaskById($id)
    {
        return $this->adminTasks_model->with(['course'])->find($id);
    }

    public function getAdminTaskByIdWithRelation($id, $relation)
    {
        return $this->adminTasks_model->with($relation)->find($id);
    }

    public function registerAdminTask($request)
    {
        $record = $this->adminTasks_model;

        $record->course_type_id             = $request->get('course_type_id');
        $record->name                       = $request->get('name');
        $record->reference_number           = $request->get('reference_number');
        if( $request->has('skill_code') ) {
            $record->skill_code                 = $request->get('skill_code');
        }
        $record->course_full_fees           = $request->get('course_full_fees');
        $record->course_type                = $request->get('course_type');
        $record->course_mode_training       = $request->get('course_mode_training');

        if( $request->has('coursesmain') && count($request->get('coursesmain')) ) {
            $record->single_course_ids      = implode (",", $request->get('coursesmain'));
        }

        if( $request->has('brandingTheme') ) {
            $record->branding_theme_id = $request->get('brandingTheme');
        }

        $record->created_by                 = Auth::Id();
        $record->updated_by                 = Auth::Id();

        $record->save();
        return $record;
    }

    public function updateAdminTask($id, $request)
    {
        $record = $this->getAdminTaskById($id);

        if( $record ) {

            $record->course_type_id             = $request->get('course_type_id');
            $record->name                       = $request->get('name');
            $record->reference_number           = $request->get('reference_number');
            if( $request->has('skill_code') ) {
                $record->skill_code                 = $request->get('skill_code');
            }
            $record->course_full_fees           = $request->get('course_full_fees');
            $record->course_type                = $request->get('course_type');
            $record->course_mode_training       = $request->get('course_mode_training');
            return $record;
        }
        return false;
    }

    public function markTaskCompletedbyID($id)
    {
        $record = $this->getAdminTaskById($id);

        if( $record ) {
            if( $record->status == AdminTasks::STATUS_COMPLETED ) {
                return ['status' => FALSE, 'msg' => trans('msg.taskAlreadyCompleted')];
            }
            $record->status                     = AdminTasks::STATUS_COMPLETED;
            $record->completed_by               = Auth::Id();
            $record->completed_at               = date('Y-m-d H:i:s');
            $record->save();
            return ['status' => TRUE, 'msg' => trans('msg.taskCompleted')];
        }
        return ['status' => FALSE, 'msg' => 'Task not found'];
    }

    public function markTaskCompletedbyIDs($ids){
        foreach($ids as $id){
            $record = $this->getAdminTaskById($id);
            if( $record ) {
                Log::info("Completed task" . $record->id);
                if( $record->status == AdminTasks::STATUS_COMPLETED ) {
                    // return ['status' => FALSE, 'msg' => trans('msg.taskAlreadyCompleted')];
                }
                $record->status                     = AdminTasks::STATUS_COMPLETED;
                $record->completed_by               = Auth::Id();
                $record->completed_at               = date('Y-m-d H:i:s');
                $record->save();
                // return ['status' => TRUE, 'msg' => trans('msg.taskCompleted')];
            }
            // return ['status' => FALSE, 'msg' => 'Task not found'];
        }
        return ['status' => TRUE, 'msg' => trans('msg.taskCompleted')];
    }


    public function markTaskUncompletebyID($id)
    {
        $record = $this->getAdminTaskById($id);
        if( $record ) {
            if( $record->status == AdminTasks::STATUS_COMPLETED ) {
                $record->status                     = AdminTasks::STATUS_PENDING;
                $record->completed_by               = Auth::Id();
                $record->completed_at               = date('Y-m-d H:i:s');
                $record->save();
                return ['status' => TRUE, 'msg' => trans('msg.taskCompleted')];
            }
            return ['status' => FALSE, 'msg' => 'Task remove from the complete'];
        }
        return ['status' => FALSE, 'msg' => 'Task not found'];
    }


    public function updateTaskNote($request)
    {
        $taskId = $request->get('task_id');
        $record = $this->getAdminTaskById($taskId);
        if( empty($record->id) ) {
            return ['success' => false, 'message' => 'No Task Found'];
        }
        $record->notes                          = $request->get('notes');

        $record->updated_by     = Auth::Id();
        $record->save();
        if( $record ) {
            return ['success' => true, 'message' => 'Task Notes updated successfully'];
        } else {
            return ['success' => false, 'message' => 'Task Notes not updated'];
        }
    }

}
