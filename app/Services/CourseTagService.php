<?php

namespace App\Services;

use App\Models\CourseTags;
use Auth;

class CourseTagService
{
    protected $courseTag_model;

    public function __construct()
    {
        $this->courseTag_model = new CourseTags;
    }

    public function getAllCourseTags()
    {
        return $this->courseTag_model->orderBy('id', 'desc')->get();
    }

    public function getActiveCourseTags()
    {
        return $this->courseTag_model->where('status', '=', 1)->get();
    }

    public function getCourseTagById($id)
    {
        return $this->courseTag_model->find($id);
    }

    public function registerCourseTag($request)
    {
        $record = $this->courseTag_model;

        $record->name                       = $request->get('name');
        $record->status                     = $request->has('status') ? 1 : 0;
        $record->created_by                 = Auth::Id();
        $record->updated_by                 = Auth::Id();
        $record->save();
        return $record;
    }

    public function updateCourseTag($id, $request)
    {
        $record = $this->getCourseTagById($id);
        if( $record ) {
            $record->name                       = $request->get('name');
            $record->status = $request->has('status') ? 1 : 0;
            $record->updated_by = Auth::Id();
            $record->save();
            return $record;
        }
        return false;
    }

}
