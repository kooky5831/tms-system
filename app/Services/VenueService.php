<?php

namespace App\Services;

use App\Models\Venue;
use Auth;

class VenueService
{
    protected $venue_model;

    public function __construct()
    {
        $this->venue_model = new Venue;
    }

    public function getAllVenue()
    {
        return $this->venue_model->select();
        // return $this->venue_model->latest();
    }

    public function getAllVenuesList()
    {
        return $this->venue_model->active()->get();
    }

    public function getVenueById($id)
    {
        return $this->venue_model->find($id);
    }

    public function addVenue($request)
    {
        $record = $this->venue_model;
        $record->block = $request->get('block');
        $record->street = $request->get('street');
        $record->floor = $request->get('floor');
        $record->unit = $request->get('unit');
        $record->building = $request->get('building');
        $record->room = $request->get('room');
        $record->postal_code = $request->get('postal_code');
        $record->wheelchairaccess = $request->has('wheelchairaccess') ? 1 : 0;
        $record->status = $request->has('status') ? 1 : 0;

        $record->created_by            = Auth::Id();
        $record->updated_by            = Auth::Id();
        $record->save();
        return $record;
    }

    public function updateVenue($id, $request)
    {
        $record = $this->getVenueById($id);
        if( $record ) {
            $record->block = $request->get('block');
            $record->street = $request->get('street');
            $record->floor = $request->get('floor');
            $record->unit = $request->get('unit');
            $record->building = $request->get('building');
            $record->room = $request->get('room');
            $record->postal_code = $request->get('postal_code');
            $record->wheelchairaccess = $request->has('wheelchairaccess') ? 1 : 0;
            $record->status = $request->has('status') ? 1 : 0;

            $record->updated_by            = Auth::Id();
            $record->save();
            return $record;
        }
        return false;
    }

}
