@extends('admin.layouts.master')
@section('title', 'Refresher View')
@section('content')
<div class="container-fluid">
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">View Refresher</a></li>
                        <li class="breadcrumb-item active">View</li>
                    </ol>
                </div>
                <h4 class="page-title">View Refresher
                    <a href="{{ route('admin.refreshers.edit', $data->id) }}" class="btn btn-info btn-sm float-right mr-3">Edit</a>
                </h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <div class="row view-all-rec">
        <div class="col-12">
            <div class="card">
                <h5 class="card-header bg-secondary text-white mt-0">View Refresher Data</h5>
                    <div class="card-body">
                        <div class="row">

                            <div class="col-md-4">
                                <label>Course Name / Course Run Id</label>
                                <p><strong>{{$data->course->courseMain->name}} / {{$data->course->tpgateway_id}}</strong></p>
                            </div>
                            <div class="col-md-3">
                                <label>Selected Course Run </label>
                                <p><strong>{{$data->course->course_start_date}} - {{$data->course->course_end_date}}</strong></p>
                            </div>
                            <div class="col-md-2">
                                <label>Attendance Required </label>
                                <p><strong>{{$data->isAttendanceRequired == 1 ? 'Yes' : 'No'}}</strong></p>
                            </div>
                            <div class="col-md-2">
                                <label>Assessment Required </label>
                                <p><strong>{{$data->isAssessmentRequired == 1 ? 'Yes' : 'No'}}</strong></p>
                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-4">
                                <label>Name </label>
                                <p><strong>{{ $data->student->name }}</strong></p>
                            </div>

                            <div class="col-md-4">
                                <label>NRIC</label>
                                <p><strong>{{ convertNricToView($data->student->nric) }}</strong></p>
                            </div>

                            @if( $singleCourse)
                                <div class="col-md-4">
                                    <label>Nationality </label>
                                    <p><strong>{{ $data->student->nationality }}</strong></p>
                                </div>
                            @endif

                        </div>

                        <div class="row">

                            <div class="col-md-4">
                                <label>Email </label>
                                <p><strong>{{ $data->student->email }}</strong></p>
                            </div>

                            <div class="col-md-4">
                                <label>Mobile No </label>
                                <p><strong>{{ $data->student->mobile_no }}</strong></p>
                            </div>

                            <div class="col-md-2">
                                <label>Date Of Birth </label>
                                <p><strong>{{ $data->student->dob }}</strong></p>
                            </div>

                            @if( $singleCourse)
                                @if( !empty($data->student->dob) )
                                <div class="col-md-2">
                                    <label>Age </label>
                                    <p><strong>{{ getAgeFromDOB($data->student->dob) }}</strong></p>
                                </div>
                                @endif
                            @endif

                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <label>Notes</label>
                                <p><strong>{{ $data->notes }}</strong></p>
                            </div>
                        </div>

                        @if( $singleCourse)

                        <div class="row">
                            <div class="col-md-3">
                                <label>Attendance Status</label>
                                <p>
                                    @if( is_null($data->isAttendanceError) )
                                    <span class="badge badge-info text-white">Not Submitted</span>
                                    @elseif( $data->isAttendanceError == 0 )
                                    <span class="badge badge-success text-white">Submitted</span>
                                    @else
                                    <span class="badge badge-danger text-white">Failed</span>
                                    @endif
                                    @if( \Carbon\Carbon::parse($data->course->course_end_date." 23:59:59")->isPast() && $data->isAttendanceRequired )
                                    <button class="btn btn-secondary viewenrolmentresponse" type="attendance" enrolement_id="{{$data->id}}">Response</button>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-3">
                                <label>Assessment Status - TP Gateway</label>
                                <p>
                                    @if( is_null($data->isAssessmentError) )
                                    <span class="badge badge-info text-white">Not Submitted</span>
                                    @elseif( $data->isAssessmentError == 0 )
                                    <span class="badge badge-success text-white">Submitted</span>
                                    @else
                                    <span class="badge badge-danger text-white">Failed</span>
                                    @endif
                                    @if( \Carbon\Carbon::parse($data->course->course_end_date." 23:59:59")->isPast() && $data->isAssessmentRequired )
                                    <button class="btn btn-secondary viewenrolmentresponse" type="assessment" enrolement_id="{{$data->id}}">Response</button>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-3">
                                <label>Assessment Status</label>
                                <p>
                                    @if( $data->assessment == 'nyc' )
                                    <span class="badge badge-danger text-white">{{getAssessmentName('nyc')}}</span>
                                    @elseif( $data->assessment == 'c' )
                                    <span class="badge badge-success text-white">{{getAssessmentName('c')}}</span>
                                    @else
                                    <span class="badge badge-info text-white">Not Submitted</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        @endif

                    </div><!--end card-body-->
            </div><!--end card-->
        </div> <!--end col-->
    </div><!--end row-->

</div><!-- container -->
@endsection
@push('scripts')
<script type="text/javascript">
    $(function () {

        @include('admin.partial.actions.viewenrolmentresponse');

        // attendance again
        $(document).on('click', '#addAttendanceAgain', function(e) {
            e.preventDefault();
            var btn = $('#addAttendanceAgain');
            BITBYTE.progress(btn);
            let _enrolement_id = $(this).attr('enrolement_id');
            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                url: '{{ route('admin.ajax.studentRefresherAttendanceAgain') }}',
                type: "POST",
                dataType: "JSON",
                data: {
                    id: _enrolement_id
                },
                success: function(res) {
                    BITBYTE.unprogress(btn);
                    if( res.status == true ) {
                        showToast(res.msg, 1);
                    } else {
                        showToast(res.msg, 0);
                    }
                    setTimeout(function(){
                        location.reload();
                    }, 2000);
                },
                error: function(err) {
                    BITBYTE.unprogress(btn);
                    if( err.status == 422 ) {
                        // display error
                        showToast(err.responseJSON.message, 0);
                        return false;
                    }
                }
            }); // end ajax
        });

        // assessment again
        $(document).on('click', '#addAssessmentAgain', function(e) {
            e.preventDefault();
            var btn = $('#addAssessmentAgain');
            BITBYTE.progress(btn);
            let _enrolement_id = $(this).attr('enrolement_id');
            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                url: '{{ route('admin.ajax.studentRefresherAssessmentAgain') }}',
                type: "POST",
                dataType: "JSON",
                data: {
                    id: _enrolement_id
                },
                success: function(res) {
                    BITBYTE.unprogress(btn);
                    if( res.status == true ) {
                        showToast(res.msg, 1);
                    } else {
                        showToast(res.msg, 0);
                    }
                    location.reload();
                },
                error: function(err) {
                    BITBYTE.unprogress(btn);
                    if( err.status == 422 ) {
                        // display error
                        showToast(err.responseJSON.message, 0);
                        return false;
                    }
                }
            }); // end ajax
        });

    });
</script>
@endpush
