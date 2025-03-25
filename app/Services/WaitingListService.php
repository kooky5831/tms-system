<?php

namespace App\Services;

use App\Models\WaitingList;
use Auth;

class WaitingListService
{
    protected $courseWaitingList_model;

    public function __construct()
    {
        $this->courseWaitingList_model = new WaitingList;
    }

    public function getAllWaitingList()
    {
        // return $this->courseWaitingList_model->with(['course']);
        $s = $this->courseWaitingList_model->select('waiting_lists.*','courses.course_start_date',
            'courses.course_end_date','courses.tpgateway_id','course_mains.name as coursermainname')
        ->join('courses', 'courses.id', '=', 'waiting_lists.course_id')
        ->join('course_mains', 'course_mains.id', '=', 'courses.course_main_id');
        return $s;
    }

    public function getWaitingListById($id)
    {
        return $this->courseWaitingList_model->find($id);
    }

    public function getAllWaitingListList()
    {
        return $this->courseWaitingList_model->get();
    }

    private function _findStudentForCourseRun($nric, $course_id)
    {
        return $this->courseWaitingList_model->where('nric', $nric)
                ->where('course_id', $course_id)->first();
    }

    public function registerWaitingList($request)
    {
        $students = $request->get('students');
        if( is_array($students) && count($students) > 0 ) {
            if( empty($students[0]['name']) ) {
                return FALSE;
            }
            $course_id = $request->get('course_id');
            foreach ($students as $student) {
                // check if user has already booked for this course run
                if( !$this->_findStudentForCourseRun($student['nric'], $course_id) ) {
                    $record = new $this->courseWaitingList_model;
                    $record->course_id                  = $course_id;
                    $record->name                       = $student['name'];
                    $record->nric                       = $student['nric'];
                    $record->email                      = $student['email'];
                    $record->mobile                     = $student['contact_number'];
                    $record->notes                      = $student['notes'];
                    $record->status                     = $student['status'];

                    $record->created_by                 = Auth::Id();
                    $record->updated_by                 = Auth::Id();

                    $record->save();
                }
            }
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function updateWaitingList($id, $request)
    {
        $record = $this->getWaitingListById($id);

        if( $record ) {
            $course_id = $request->get('course_id');
            if( $course_id != $record->course_id ) {
                // means course has been changed
                if( $this->_findStudentForCourseRun($request->get('nric'), $course_id) ) {
                    return ['success' => true, 'msg' => 'Student already in waiting list in this course run'];
                }
            }
            $record->course_id                  = $request->get('course_id');
            $record->name                       = $request->get('name');
            $record->nric                       = $request->get('nric');
            $record->email                      = $request->get('email');
            $record->mobile                     = $request->get('contact_number');
            $record->notes                      = $request->get('notes');
            $record->status                     = $request->get('status');
            $record->updated_by                 = Auth::Id();

            $record->save();
            return $record;
        }
        return false;
    }

}
