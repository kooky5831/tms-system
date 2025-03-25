<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Services\CommonService;
use Carbon\Carbon;

class CourseRunScheduleCollection extends ResourceCollection
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
                $softBooking = $this->_checkSoftBooking($data->courseSoftBooking);
                $totalBooking = $softBooking + $data->registeredusercount;
                if( $totalBooking < $data->intakesize ) {
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
                            // 'totalBooking' => $totalBooking,
                            'registration_opening_date' => $data->registration_opening_date,
                            'registration_closing_date' => $data->registration_closing_date,
                            'course_start_date' => $data->course_start_date,
                            'course_end_date' => $data->course_end_date,
                            // 'course_type' => $data->course_type,
                            'modeoftraining' => $data->modeoftraining,
                            // 'coursename' => ucwords(strtolower($data->courseMain->name)),
                            'coursename' => $data->courseMain->name,
                            'courserefno' => $data->courseMain->reference_number,
                            // 'course_type' => getCourseType($data->course_type),
                            'session'   => $this->common->makeSessionString($data->session)." - ",
                        ];
                    }
                }
            })
        ];
    }

    private function _checkSoftBooking($softBooking)
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
