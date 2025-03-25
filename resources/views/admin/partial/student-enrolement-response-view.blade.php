<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title mt-0" id="myLargeModalLabel">{{$records->student->name}} - {{convertNricToView($records->student->nric)}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mt-0">{{$records->student->name}} - {{convertNricToView($records->student->nric)}} {{$type}} Response</h4>
                        @switch($type)
                            @case("enrolment")
                                @if( !is_null($records->enrollmentResponse) )
                                    <?php $enrolRes = json_decode($records->enrollmentResponse); ?>
                                    <?php dump($enrolRes); ?>
                                @endif
                            @break
                            @case("grant")
                                @if( !is_null($records->grantResponse) )
                                    <?php $grantRes = json_decode($records->grantResponse); ?>
                                    <?php dump($grantRes); ?>
                                @endif
                            @break
                            @case("attendance")
                                @if( !is_null($records->attendanceResponse) )
                                    <?php $attendanceRes = json_decode($records->attendanceResponse); ?>
                                    <?php dump($attendanceRes); ?>
                                @endif
                            @break
                            @case("assessment")
                                @if( !is_null($records->assessmentResponse) )
                                    <?php $assessmentRes = json_decode($records->assessmentResponse); ?>
                                    <?php dump($assessmentRes); ?>
                                @endif
                            @break
                            @case("payment")
                                @if( !is_null($records->tgp_payment_response) )
                                    <?php $paymentRes = json_decode($records->tgp_payment_response); ?>
                                    <?php dump($paymentRes); ?>
                                @endif
                            @break
                            @default
                                @if( !is_null($records->enrollmentResponse) )
                                    <?php $enrolRes = json_decode($records->enrollmentResponse); ?>
                                    <?php dump($enrolRes); ?>
                                @endif
                        @endswitch

                    </div><!--end card-body-->
                </div><!--end card-->
            </div> <!--end col-->
        </div><!--end row-->

    </div>
    <div class="modal-footer">
        @switch($type)
            @case("enrolment")
            @if( $records->status == 3)
                <button type="button" class="btn btn-primary waves-effect" id="enrolagain" enrolement_id="{{$records->id}}">Enroll Again</button>
            @endif
            @break
            @case("grant")
            @if( $records->tpgateway_refno)
                <button type="button" class="btn btn-primary waves-effect" id="searchgrantagain" enrolement_id="{{$records->id}}">Fetch Grant Data</button>
            @endif
            @break
            @case("attendance")
            @if( is_null($records->isAttendanceError) || $records->isAttendanceError == 1 )
                <button type="button" class="btn btn-primary waves-effect" id="addAttendanceAgain" enrolement_id="{{$records->id}}">Submit Attendance Again</button>
            @endif
            @break
            @case("assessment")
            @if( is_null($records->isAssessmentError) || $records->isAssessmentError == 1 )
                <button type="button" class="btn btn-primary waves-effect" id="addAssessmentAgain" enrolement_id="{{$records->id}}">Submit Assessment Again</button>
            @endif
            @break
        @endswitch
        <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
    </div>
</div>
