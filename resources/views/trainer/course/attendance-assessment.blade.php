@extends('trainer.layouts.master')
@section('title', 'Attendance Assessment List - Course Run')
@section('content')
<div class="container-fluid">
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Courses Runs - Attendance & Assessment</a></li>
                        <li class="breadcrumb-item active">List</li>
                    </ol>
                </div>
                <h4 class="page-title">Courses Run - Attendance & Assessment</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('trainer.course.save-attendance-assessment', $result->id) }}" id="courserun_attendance_save" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row float-right">
                            <a class="btn btn-primary px-4 btn-rounded mt-0 mb-3" type="button" href="{{ route('trainer.course.list', $result->courseMain->id) }}">Go Back</a>
                        </div>
                        <h4 class="header-title mt-0">Attendance & Assessment List - {{$result->courseMain->name}}</h4>
                        <h4 class="header-title mt-0">Course Date - {{$result->course_start_date}} to {{$result->course_end_date}}</h4>
                        <div class="table-responsive dash-social">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>NRIC</th>
                                    <th>Email</th>
                                    <th>Payment Status</th>
                                    @foreach ($result->session as $session)
                                        <th>{{$session->start_date}}:{{$session->start_time}}</th>
                                    @endforeach
                                    <th>Assessment</th>
                                    <th>Action</th>
                                </tr><!--end tr-->
                                </thead>

                                <tbody>
                                    @foreach ($result->courseActiveEnrolments as $s => $student)
                                        @php
                                        $studentAttendances = is_null($student->attendance) ? NULL : json_decode($student->attendance);
                                        @endphp
                                        <tr>
                                            <td>{{++$s}}</td>
                                            <td>{{$student->student->name}}</td>
                                            <td>{{convertNricToView($student->student->nric)}}</td>
                                            <td>{{$student->email}}</td>
                                            <td>{{getPaymentStatus($student->payment_status)}}</td>
                                            @foreach ($result->session as $session)
                                                <?php $currentSession = NULL; ?>
                                                @if( !is_null($studentAttendances) )
                                                    @foreach ($studentAttendances as $att)
                                                        @if( $att->start_date == $session->start_date && $att->start_time == $session->start_time )
                                                            <?php $currentSession = $att->ispresent; ?>
                                                        @endif
                                                    @endforeach
                                                @endif
                                                <td>
                                                    <div class="col-md-9">
                                                        <div class="form-check-inline my-1">
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" id="attendance_{{$student->id}}_{{$session->id}}_present" name="attendance_{{$student->id}}_{{$session->id}}" value="1" class="custom-control-input" {{ is_null($currentSession) ? 'checked' : '' }} {{ !is_null($currentSession) && $currentSession == 1 ? 'checked' : '' }}>
                                                                <label class="custom-control-label" for="attendance_{{$student->id}}_{{$session->id}}_present">Present</label>
                                                            </div>
                                                        </div>
                                                        <div class="form-check-inline my-1">
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" id="attendance_{{$student->id}}_{{$session->id}}_absent" name="attendance_{{$student->id}}_{{$session->id}}" value="0" class="custom-control-input" {{ !is_null($currentSession) && $currentSession == 0 ? 'checked' : '' }} />
                                                                <label class="custom-control-label" for="attendance_{{$student->id}}_{{$session->id}}_absent">Absent</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            @endforeach
                                            <td>
                                                <div class="col-md-9">
                                                    <div class="form-check-inline my-1">
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" id="assessment_{{$student->id}}_c" name="assessment_{{$student->id}}" class="custom-control-input" value="c" {{ $student->assessment == 'nyc' ? '' : 'checked' }}>
                                                            <label class="custom-control-label" for="assessment_{{$student->id}}_c">C</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-check-inline my-1">
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" id="assessment_{{$student->id}}_nyc" name="assessment_{{$student->id}}" class="custom-control-input" value="nyc" {{ $student->assessment == 'nyc' ? 'checked' : '' }} />
                                                            <label class="custom-control-label" for="assessment_{{$student->id}}_nyc">NYC</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="{{route('trainer.studentenrolment.view', $student->id)}}" target="_blank" data-toggle="tooltip" data-placement="bottom" title="View" class="mr-2"><i class="fas fa-eye text-info font-16"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="row float-right">
                            <button type="submit" class="btn btn-success px-4 btn-rounded mt-0 mb-3">Save</button>
                        </div>
                    </form>
                </div><!--end card-body-->
            </div><!--end card-->
        </div> <!--end col-->
    </div><!--end row-->

</div><!-- container -->
@endsection

@push('scripts')
<script type="text/javascript">
    $(function () {

    });
</script>
@endpush
