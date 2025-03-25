<?php

namespace App\Services;

use App\Models\CourseType;
use Auth;

class CourseTypeService
{
    protected $courseType_model;

    public function __construct()
    {
        $this->courseType_model = new CourseType;
    }

    public function getAllCourseType()
    {
        return $this->courseType_model->latest();
    }

    public function getCourseTypeById($id)
    {
        return $this->courseType_model->find($id);
    }

    public function getAllCourseTypeList()
    {
        return $this->courseType_model->active()->get();
    }

    public function registerCourseType($request)
    {
        $record = $this->courseType_model;

        $record->name                       = $request->get('name');
        $record->status                     = $request->has('status') ? 1 : 0;
        $record->created_by                 = Auth::Id();
        $record->updated_by                 = Auth::Id();

        $record->save();
        return $record;
    }

    public function updateCourseType($id, $request)
    {
        $record = $this->getCourseTypeById($id);

        if( $record ) {

            $record->name                          = $request->get('name');
            $record->status                        = $request->has('status') ? 1 : 0;
            $record->updated_by                    = Auth::Id();

            $record->save();
            return $record;
        }
        return false;
    }

}
