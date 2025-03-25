<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use App\Services\CommonService;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CourseRunScheduleCollectionDate extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $this->common = new CommonService;
        // return parent::toArray($request);
        return [
            'data' => $this->collection->transform( function($data) {
                $softBooking = $this->_checkSoftBookingStatus($data->courseSoftBooking);
                $totalBooking = $softBooking + $data->registeredusercount;
                
                $availability = '';
                if($totalBooking >= $data->threshold)
                {
                    $availability = 'Limited seats';
                }
                else if($totalBooking >= $data->intakesize)
                {
                    $availability = 'Full house';
                }
                
                if( $totalBooking <= $data->intakesize ) {
                    $checkDate = Carbon::parse($data->registration_closing_date);
                    // dd($checkDate);
                    $isReturn = true;
                    if( $checkDate->isToday() ) {
                        // check for time as well
                        if( $data->registration_closing_time < Carbon::now()->format('H:i:s') ) {
                            $isReturn = false;
                        }
                    }
                    if( $isReturn ) {
                        return [
                            'id' => $data->id,
                            'registration_opening_date' => $data->registration_opening_date,
                            'registration_closing_date' => $data->registration_closing_date,
                            'course_start_date' => $data->course_start_date,
                            'course_end_date' => $data->course_end_date,
                            'modeoftraining' => $data->modeoftraining,
                            'coursename' => $data->courseMain->name,
                            'courserefno' => $data->courseMain->reference_number,
                            'session'   => $this->common->makeSessionString($data->session, $showdays = true)." - ",
                            'status' => $data->is_published,
                            'availability' => $availability,
                        ];
                    }
                }
            })
        ];
    }

    private function _checkSoftBookingStatus($softBooking)
    {
        $bookings = 0;
        if( $softBooking ) {
            foreach ($softBooking as $book) {
                if( $book->status == 0 ) {
                    $bookings += 1;
                }
            }
        }
        return $bookings;
    }
}
