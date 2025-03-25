<?php

namespace App\Services;

use App\Models\ProgramType;
use Auth;

class ProgramTypeService
{
    protected $program_type_model;

    public function __construct()
    {
        $this->program_type_model = new ProgramType;
    }

    public function getAllProgramTypeList()
    {
        return $this->program_type_model->get();
    }

    public function getAllProgramTypeAllList()
    {
        return $this->program_type_model->active()->pluck('name', 'id');
    }

    public function getProgramTypeById($id)
    {
        return $this->program_type_model->find($id);
    }

    public function addProgramType($request)
    {
        $record = $this->program_type_model;
        $record->name = $request->get('name');
        $record->status  = $request->has('status') ? 1 : 0;
        $record->created_by = Auth::Id();
        $record->updated_by = Auth::Id();

        $record->save();
        return $record;
    }

    public function updateProgramType($id, $request)
    {
        $record = $this->getProgramTypeById($id);
        if( $record ) {

            $record->name = $request->get('name');
            $record->is_discount = $request->has('is_discount') ? 1 : 0;
            $record->discount_percentage = $request->get('discount_percentage');
            $record->is_application_fee = $request->has('is_application_fee') ? 1 : 0;
            $record->application_fee = $request->get('application_fee');
            $record->is_absorb_gst = $request->has('is_absorb_gst') ? 1 : 0;
            $record->status = $request->has('status') ? 1 : 0;
            $record->updated_by = Auth::Id();

            $record->save();
            return $record;
        }
        return false;
    }
}
