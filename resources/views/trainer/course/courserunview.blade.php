@extends('trainer.layouts.master')
@section('title', 'Courses Run View')
@push('css')
<!-- DataTables -->
<link href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<!-- Responsive datatable examples -->
<link href="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
@endpush
@section('content')
<div class="container-fluid">
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Courses Runs View</a></li>
                        <li class="breadcrumb-item active">List</li>
                    </ol>
                </div>
                <h4 class="page-title">Courses Runs View</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <div class="row blue-header-box marlr0">
        <div class="col-9">
            <div class="">
                <h2>
                    @if(!is_null($data->tpgateway_id))
                    {{$data->tpgateway_id}} -
                    @endif
                    {{$data->courseMain->name}}
                </h2>
                <div class="blue-box-btns">
                    <!-- <button type="button" class="dis-in btn btn-danger mar-r-10">Publish</button> -->
                    @if($data->is_published == 1)
                    <button type="button" class="dis-in btn btn-disable mar-r-10">Published</button>
                    @elseif($data->is_published == 2)
                    <button type="button" class="dis-in btn btn-danger mar-r-10">Cancelled</button>
                    @else
                    <button type="button" class="dis-in btn btn-primary mar-r-10">Un Published</button>
                    @endif
                    <div class="dropdown dot-list dis-in">
                        <a href="#" class="dropdown-toggle rounded-bdr" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="thr-wht-dots"></span></a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{{ route('trainer.course.generate-attendance', $data->id) }}" target="_blank" id="courserun_generateAttendance"><i class="fas fa-download font-16"></i>Attendence Sheet</a>
                            </li>
                            <li>
                                <a href="{{ route('trainer.course.generate-assessment', $data->id) }}" target="_blank" id="courserun_generateAssessment"><i class="fas fa-download font-16"></i>Assessment Sheet</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">

        <div class="col-md-12">

            <nav class="page-title-box mt-4">
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-details-tab" data-toggle="tab" data-target="#nav-details" type="button" role="tab" aria-controls="nav-details" aria-selected="true">Details</button>
                    <button class="nav-link" id="nav-trainee-tab" data-toggle="tab" data-target="#nav-trainee" type="button" role="tab" aria-controls="nav-trainee" aria-selected="false">Trainee List &nbsp;<span class="badge badge-primary text-white">{{count($data->courseEnrolledNotEnrolledEnrolments)}}</span></button>
                    <button class="nav-link" id="nav-trainee-cancelled-tab" data-toggle="tab" data-target="#nav-trainee-cancelled" type="button" role="tab" aria-controls="nav-trainee-cancelled" aria-selected="false">Cancelled Trainee List &nbsp;<span class="badge badge-primary text-white">{{count($data->courseCancelledEnrolments)}}</span></button>
                    <button class="nav-link" id="nav-trainee-holdlist-tab" data-toggle="tab" data-target="#nav-trainee-holdlist" type="button" role="tab" aria-controls="nav-trainee-holdlist" aria-selected="false">Holdlist Trainee List &nbsp;<span class="badge badge-primary text-white">{{count($data->courseHoldingEnrolments)}}</span></button>
                    <button class="nav-link" id="nav-refresher-tab" data-toggle="tab" data-target="#nav-refresher-list" type="button" role="tab" aria-controls="nav-refresher-list" aria-selected="false">Refreshers &nbsp;<span class="badge badge-primary text-white">{{count($data->courseRefreshers->where('status', '!=', 2))}}</span></button>
                </div>
            </nav>

        </div>

        <div class="col-md-12">

            <div class="tab-content" id="nav-tabContent">
                <div class="row marlr0 tab-pane fade show active" id="nav-details" role="tabpanel" aria-labelledby="nav-details-tab">
                    <div class="page-title-box">
                        <h4 class="header-title mt-0">{{$data->courseMain->name}}</h4>
                        @if($data->course_link)
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="course_link">Course Link</label>
                                    <input type="text" readonly="" class="form-control" value="{{ $data->course_link }}" />
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Reference Code</label>
                                    <input type="text" readonly="" class="form-control" value="{{ $data->courseMain->reference_number }}" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Mode of Training</label>
                                    <input type="text" readonly="" class="form-control" value="{{ getModeOfTraining($data->modeoftraining) }}" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Maximum Student</label>
                                    <input type="text" readonly="" class="form-control" value="{{ $data->intakesize }}" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Main Trainer</label>
                                    <input type="text" readonly="" class="  form-control" value="{{ $data->maintrainerUser->name }}" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Venue</label>
                                    <input type="text" readonly="" class="form-control" value="{{ $data->venue->block.",".$data->venue->street }}" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Number Of Sessions</label>
                                    <input type="text" readonly="" class="form-control" value="{{ count($data->session) }}" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Minimum Intake</label>
                                    <input type="text" readonly="" class="form-control" value="{{ $data->minintakesize }}" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Registration Opening Date</label>
                                    <input type="text" readonly="" class="form-control" value="{{ $data->registration_opening_date }}" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Registration Closing Date</label>
                                    <input type="text" readonly="" class="form-control" value="{{ $data->registration_closing_date }}" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Course Start Date</label>
                                    <input type="text" readonly="" class="form-control" value="{{ $data->course_start_date }}" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Course End Date</label>
                                    <input type="text" readonly="" class="form-control" value="{{ $data->course_end_date }}" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Schedule Info Type Code</label>
                                    <input type="text" readonly="" class="form-control" value="{{ $data->schinfotype_code }}" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Schedule Info Type Description</label>
                                    <textarea type="text" readonly="" class="form-control">{{ $data->schinfotype_desc }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Course Vacancy Code</label>
                                    <input type="text" readonly="" class="form-control" value="{{ $data->coursevacancy_code }}" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Course Vacancy Description</label>
                                    <textarea type="text" readonly="" class="form-control">{{ $data->coursevacancy_desc }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Schedule Info</label>
                                    <input type="text" readonly="" class="form-control" value="{{ $data->sch_info }}" />
                                </div>
                            </div>
                        </div>
                        @if( $data->trainers )
                            <div class="row mt-2">
                                <div class="col-md-12">
                                    <h4 class="header-title mt-0">Extra Trainers</h4>
                                </div>
                                <div class="col-md-12">
                                    @foreach( $data->trainers as $trainer )
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Trainer Name</label>
                                                    <input type="text" readonly="" class="form-control" value="{{ $trainer->name }}" />
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Email</label>
                                                    <input type="text" readonly="" class="form-control" value="{{ $trainer->email }}" />
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Contact No.</label>
                                                    <input type="text" readonly="" class="form-control" value="{{ $trainer->phone_number }}" />
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <h4 class="header-title mt-0">Session: Date & Time Details</h4>
                            </div>
                            <div class="col-md-12">
                                @foreach( $data->session as $s => $ses )
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Session {{++$s}}</label>
                                                <input type="text" readonly="" class="form-control" value="{{ $ses->session_schedule }}" />
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    {{-- checklist --}}
                    @if( $data->courseMain->course_type_id == 1)
                    <div class="row mt-4 chk-list page-title-box marlr0">
                        <div class="col-md-12">
                            <h4 class="header-title mt-0">Course Run Checklist Updates</h4>
                        </div>
                        <div class="row marlr0" style="width: 100%;">
                            <div class="col-md-3">
                                <div class="checkbox checkbox-primary">
                                    <input id="chk-enrollment" class="courserun_check" disabled="" value="Enrollment" name="courserun_check[]" type="checkbox" {{$isEnrollmentError == 0 ? "checked=''" : ''}}>
                                    <label for="chk-enrollment">Enrollment</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="checkbox checkbox-primary">
                                    <input id="chk-payment" class="courserun_check" disabled="" value="Grant" name="courserun_check[]" type="checkbox" {{$isGrantError == 0 ? "checked=''" : ''}}>
                                    <label for="chk-payment">Grant</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="checkbox checkbox-primary">
                                    <input id="chk-attendance" class="courserun_check" disabled="" value="Attendance" name="courserun_check[]" type="checkbox" {{$data->isAttendanceSubmitedTPG == 1 ? "checked=''" : ''}}>
                                    <label for="chk-attendance">Attendance</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="checkbox checkbox-primary">
                                    <input id="chk-assessment" class="courserun_check" disabled="" value="Assessment" name="courserun_check[]" type="checkbox" {{$data->isAssessmentSubmitedTPG == 1 ? "checked=''" : ''}}>
                                    <label for="chk-assessment">Assessment</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <a class="btn btn-primary px-4 btn-rounded float-right mt-0 mb-3" id="add_documents" courserunid="{{$data->id}}" href="javascript:void(0)"><i class="add-new"></i> Add New</a>
                                    <h4 class="header-title mt-0">Assessment & Attendance</h4>
                                    <div class="table-responsive dash-social">
                                        <table class="table sm-datatable">
                                            <thead>
                                            <tr>
                                                <th>File Name</th>
                                                <th>Category</th>
                                                <th>Upload Date</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                @foreach( $data->courseDocuments as $docs )
                                                <tr>
                                                    <td>{{$docs->file_name}}</td>
                                                    <td>{{getAttAssCategory($docs->category)}}</td>
                                                    <td>{{$docs->created_at->format('d M Y')}}</td>
                                                    <td>
                                                        <a class="mr-2 eye-back" href="{{url(config('uploadpath.course_document'))}}/{{$docs->file_name}}" download data-toggle="tooltip" data-placement="bottom" title="Download"><i class="fas fa-download text-info font-16"></i></a>
                                                        <a class="editdoc mr-2 edit-back" href="javascript:void(0)" docs_id="{{$docs->id}}" data-toggle="tooltip" data-placement="bottom" title="Edit Document"><i class="fas fa-edit text-info font-16"></i></a>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row marlr0 tab-pane fade" id="nav-trainee" role="tabpanel" aria-labelledby="nav-trainee-tab">
                    <div class="page-title-box">
                        <form method="POST" action={{route('trainer.course.courseRunTrainee.export.excel', $data->id)}}>
                            @csrf
                            <button type="submit" class="btn btn-warning float-right mt-3 mb-3 mr-2">Download Trainee List</button>
                        </form>
                        <h4 class="header-title mt-0">Trainee List</h4>
                        <div class="table-responsive dash-social">
                            <table class="table sm-datatable">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th>NRIC</th>
                                    <th>Trainee Name</th>
                                    <th>Email Address</th>
                                    <th>Contact No.</th>
                                    <th>Assessment</th>
                                    <th>Invoice #</th>
                                    <th>Payment Status</th>
                                    <th>Action</th>
                                </tr><!--end tr-->
                                </thead>
                                <tbody>
                                    @foreach( $data->courseEnrolledNotEnrolledEnrolments as $enrolment )
                                        <tr>
                                            <td></td>
                                            <td>{{ convertNricToView($enrolment->student->nric) }}</td>
                                            <td>{{ $enrolment->student->name }}</td>
                                            <td>{{ $enrolment->email }}</td>
                                            <td>{{ $enrolment->mobile_no }}</td>
                                            <td>
                                                @if(!is_null($enrolment->assessment))
                                                <span class="badge badge-soft-{{$enrolment->assessment == 'c' ? 'success' : 'danger' }}">{{ getAssessmentName($enrolment->assessment) }}</span>
                                                @else
                                                -
                                                @endif
                                            </td>
                                            <td>{{ $enrolment->xero_invoice_number }}</td>
                                            <td>{{ getPaymentStatus($enrolment->payment_status) }}</td>
                                            <td>
                                                <div class="dropdown dot-list">
                                                    <a href="#" class="dropdown-toggle rounded-bdr" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="list-dots"></span></a>
                                                    <ul  class="dropdown-menu">
                                                        <li><a href="{{route('trainer.studentenrolment.view',$enrolment->id)}}"><i class="fas fa-eye font-16"></i> View</a></li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
                <div class="row marlr0 tab-pane fade" id="nav-trainee-cancelled" role="tabpanel" aria-labelledby="nav-trainee-cancelled-tab">
                    <div class="page-title-box">
                        <h4 class="header-title mt-0">Cancelled Trainee List</h4>
                        <div class="table-responsive dash-social">
                            <table class="table sm-datatable">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th>NRIC</th>
                                    <th>Trainee Name</th>
                                    <th>Email Address</th>
                                    <th>Contact No.</th>
                                    <th>Assessment</th>
                                    <th>Invoice #</th>
                                    <th>Payment Status</th>
                                    <th>Action</th>
                                </tr><!--end tr-->
                                </thead>
                                <tbody>
                                    @foreach( $data->courseCancelledEnrolments as $enrolment )
                                        <tr>
                                            <td></td>
                                            <td>{{ convertNricToView($enrolment->student->nric) }}</td>
                                            <td>{{ $enrolment->student->name }}</td>
                                            <td>{{ $enrolment->email }}</td>
                                            <td>{{ $enrolment->mobile_no }}</td>
                                            <td>
                                                @if(!is_null($enrolment->assessment))
                                                <span class="badge badge-soft-{{$enrolment->assessment == 'c' ? 'success' : 'danger' }}">{{ getAssessmentName($enrolment->assessment) }}</span>
                                                @else
                                                -
                                                @endif
                                            </td>
                                            <td>{{ $enrolment->xero_invoice_number }}</td>
                                            <td>{{ getPaymentStatus($enrolment->payment_status) }}</td>
                                            <td>
                                                <div class="dropdown dot-list">
                                                    <a href="#" class="dropdown-toggle rounded-bdr" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="list-dots"></span></a>
                                                    <ul  class="dropdown-menu">
                                                        <li><a href="{{route('trainer.studentenrolment.view',$enrolment->id)}}"><i class="fas fa-eye font-16"></i> View</a></li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
                <div class="row marlr0 tab-pane fade" id="nav-trainee-holdlist" role="tabpanel" aria-labelledby="nav-trainee-holdlist-tab">
                    <div class="page-title-box">
                        <h4 class="header-title mt-0">Holdlist Trainee List</h4>
                        <div class="table-responsive dash-social">
                            <table class="table sm-datatable">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th>NRIC</th>
                                    <th>Trainee Name</th>
                                    <th>Email Address</th>
                                    <th>Contact No.</th>
                                    <th>Assessment</th>
                                    <th>Invoice #</th>
                                    <th>Payment Status</th>
                                    <th>Action</th>
                                </tr><!--end tr-->
                                </thead>
                                <tbody>
                                    @foreach( $data->courseHoldingEnrolments as $enrolment )
                                        <tr>
                                            <td></td>
                                            <td>{{ convertNricToView($enrolment->student->nric) }}</td>
                                            <td>{{ $enrolment->student->name }}</td>
                                            <td>{{ $enrolment->email }}</td>
                                            <td>{{ $enrolment->mobile_no }}</td>
                                            <td>
                                                @if(!is_null($enrolment->assessment))
                                                <span class="badge badge-soft-{{$enrolment->assessment == 'c' ? 'success' : 'danger' }}">{{ getAssessmentName($enrolment->assessment) }}</span>
                                                @else
                                                -
                                                @endif
                                            </td>
                                            <td>{{ $enrolment->xero_invoice_number }}</td>
                                            <td>${{ $enrolment->amount_paid }}/${{ $enrolment->amount }}</td>
                                            {{-- <td>{{ getPaymentStatus($enrolment->payment_status) }}</td> --}}
                                            <td>
                                                <div class="dropdown dot-list">
                                                    <a href="#" class="dropdown-toggle rounded-bdr" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="list-dots"></span></a>
                                                    <ul  class="dropdown-menu">
                                                        <li><a href="{{route('trainer.studentenrolment.view',$enrolment->id)}}"><i class="fas fa-eye font-16"></i> View</a></li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
                <div class="row marlr0 tab-pane fade" id="nav-refresher-list" role="tabpanel" aria-labelledby="nav-refresher-tab">
                    <div class="page-title-box">
                        @if( \Carbon\Carbon::parse($data->course_start_date)->isFuture() )
                        <a class="btn btn-primary px-4 btn-rounded float-right mt-0 mb-3" href="{{ route('trainer.refreshers.add', $data->id) }}"><i class="add-new"></i> Add New</a>
                        @endif
                        @if( count($data->courseRefreshers->where('status', '!=', 2)) > 0 )
                        <form method="POST" action={{route('trainer.course.courseRunRefreshers.export.excel', $data->id)}}>
                            @csrf
                            <button type="submit" class="btn btn-warning float-right mt-3 mb-3 mr-2">Download Refreshers List</button>
                        </form>
                        @endif
                        <h4 class="header-title mt-0">Refreshers List</h4>
                        <div class="table-responsive dash-social">
                            <table class="table sm-datatable">
                                <thead>
                                <tr>
                                    <th>NRIC</th>
                                    <th>Trainee Name</th>
                                    <th>Email Address</th>
                                    <th>Contact No.</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr><!--end tr-->
                                </thead>
                                <tbody>
                                    @foreach( $data->courseRefreshers as $refresher )
                                        <tr>
                                            <td>{{ convertNricToView($refresher->student->nric) }}</td>
                                            <td>{{ $refresher->student->name }}</td>
                                            <td>{{ $refresher->student->email }}</td>
                                            <td>{{ $refresher->student->mobile_no }}</td>
                                            <td>{{ getCourseWaitingListStatus($refresher->status) }}</td>
                                            <td>
                                                <div class="dropdown dot-list">
                                                    <a href="#" class="dropdown-toggle rounded-bdr" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="list-dots"></span></a>
                                                    <ul  class="dropdown-menu">
                                                        <li><a href="{{route('trainer.refreshers.edit', $refresher->id)}}"><i class="fas fa-pencil-alt font-16"></i> Edit</a></li>
                                                        <li><a href="javascript:void(0)" class="viewnotesrefresher" main_id="{{$refresher->id}}"><i class="mdi mdi-note font-16"></i> View Note</a></li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

</div><!-- container -->
@endsection

@push('scripts')
<script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script type="text/javascript">
    $(function () {

        $(document).on('click', '#add_documents', function(e) {
            e.preventDefault();
            let _courserun_id = $(this).attr('courserunid');
            $('#modal-content').empty().html(`@include('trainer.partial.courserun-upload-document-add', ['courserunid' => $data->id])`);
            $('.model-box').modal();
        });

        $(document).on('submit', '#courserun_documnet_upload', function(e) {
            e.preventDefault();
            var btn = $('#submit_document');
            BITBYTE.progress(btn);
            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                url: '{{ route('trainer.ajax.courserun-uploaddocuments') }}',
                type: "POST",
                dataType: "JSON",
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData:false,
                success: function(res) {
                    BITBYTE.unprogress(btn);
                    showToast(res.message, res.success);
                    if( res.success ) {
                        setTimeout(function() {
                            location.reload();
                        },1000);
                    }
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

        // Edit
        $(document).on('click', '.editdoc', function(e) {
            e.preventDefault();
            let _docs_id = $(this).attr('docs_id');
            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                url: '{{ route('trainer.ajax.courserun-uploaddocuments.modal.edit') }}',
                type: "POST",
                dataType: "JSON",
                data: {
                    id: _docs_id
                },
                success: function(res) {
                    $('#modal-content').empty().html(res.html);
                    $('.model-box').modal();
                }
            }); // end ajax
        });

        $(document).on('submit', '#courserun_documnet_upload_edit', function(e) {
            e.preventDefault();
            var btn = $('#submit_document_edit');
            BITBYTE.progress(btn);
            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                url: '{{ route('trainer.ajax.courserun-uploaddocuments.modal.store') }}',
                type: "POST",
                dataType: "JSON",
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData:false,
                success: function(res) {
                    BITBYTE.unprogress(btn);
                    showToast(res.message, res.success);
                    if( res.success ) {
                        setTimeout(function() {
                            location.reload();
                        },1000);
                    }
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

        $(document).on('click', '.viewnotesrefresher', function(e) {
            e.preventDefault();
            let _main_id = $(this).attr('main_id');
            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                url: '{{ route('trainer.ajax.refresher.modal.viewnotes') }}',
                type: "POST",
                dataType: "JSON",
                data: {
                    id: _main_id
                },
                success: function(res) {
                    $('#modal-content').empty().html(res.html);
                    $('.model-box').modal();
                }
            }); // end ajax
        });

        $('.sm-datatable').DataTable();
        $('.sm-datatable-booking').DataTable({
            "order": [[ 5, "desc" ]],
        });

    });
</script>
@endpush
