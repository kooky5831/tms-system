<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title mt-0" id="myLargeModalLabel">{{$record->student->name}} - {{convertNricToView($record->student->nric)}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mt-0">{{$record->student->name}} - {{convertNricToView($record->student->nric)}} {{$type}} Response</h4>
                        @switch($type)
                            @case("attendance")
                                @if( !is_null($record->attendanceResponse) )
                                    <?php $attendanceRes = json_decode($record->attendanceResponse); ?>
                                    <?php dump($attendanceRes); ?>
                                @endif
                            @break
                            @case("assessment")
                                @if( !is_null($record->assessmentResponse) )
                                    <?php $assessmentRes = json_decode($record->assessmentResponse); ?>
                                    <?php dump($assessmentRes); ?>
                                @endif
                            @break
                            @default
                        @endswitch

                    </div><!--end card-body-->
                </div><!--end card-->
            </div> <!--end col-->
        </div><!--end row-->

    </div>
    <div class="modal-footer">
        @switch($type)
            @case("attendance")
            @if( is_null($record->isAttendanceError) || $record->isAttendanceError == 1 )
                <button type="button" class="btn btn-primary waves-effect" id="addAttendanceAgain" enrolement_id="{{$record->id}}">Submit Attendance Again</button>
            @endif
            @break
            @case("assessment")
            @if( is_null($record->isAssessmentError) || $record->isAssessmentError == 1 )
                <button type="button" class="btn btn-primary waves-effect" id="addAssessmentAgain" enrolement_id="{{$record->id}}">Submit Assessment Again</button>
            @endif
            @break
        @endswitch
        <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
    </div>
</div>
