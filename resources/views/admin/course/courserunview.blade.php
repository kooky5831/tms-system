@extends('admin.layouts.master')
@section('title', 'Courses Run View')
@push('css')
<!-- DataTables -->
<link href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<!-- Responsive datatable examples -->
<link href="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/dataTables.checkboxes.css') }}" rel="stylesheet" type="text/css" />

@endpush
@section('content')
<div class="container-fluid">
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{route('admin.course.listall')}}">Courses Runs</a></li>
                        <li class="breadcrumb-item active">List</li>
                    </ol>
                </div>
                <h4 class="page-title">Courses Runs View
                    {{-- <a class="btn btn-info btn-sm float-right mr-3" href="{{ URL::previous() }}"> Back </a> --}}
                </h4>
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
                    
                    <form method="POST" class="tms-report" action="{{route('admin.reports.courserun.export.excel', $data->id)}}">
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
                                <a href="{{ route('admin.course.generate-attendance', $data->id) }}" target="_blank" id="courserun_generateAttendance"><i class="fas fa-download font-16"></i>Attendence Sheet</a>
                            </li>
                            <li>
                                <a href="{{ route('admin.course.generate-assessment', $data->id) }}" target="_blank" id="courserun_generateAssessment"><i class="fas fa-download font-16"></i>Assessment Sheet</a>
                            </li>
                            <li><a href="{{ route('admin.course.edit', $data->id) }}"><i class="fas fa-pencil-alt font-16"></i>Edit</a></li>
                            {{-- <li><a href=""><i class="far fa-trash-alt font-16"></i>Delete</a></li> --}}
                        </ul>
                    </div>
                    <!-- Trainer folder Start -->
                    @if($data->is_published != 2 )
                        <div class="generate-folders dis-in">
                            <a href="javascript:void(0);" class="dis-in btn btn-primary btn-trainer mar-r-10" data-courseid="{{$data->id}}">Generate Trainer Folder</a>
                            <label class="dis-in trainer-job-status">{{$data->trainer_job_status}}</label>
                        </div>
                    @endif
                    <!-- Trainer folder End -->
                        <div class="generate-folders dis-in">
                            <button class="btn btn-info btn-primary mar-r-10" type="submit">Export Excel</button>
                        </div>
                    <form>
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
                    <button class="nav-link" id="nav-soft-booking-tab" data-toggle="tab" data-target="#nav-soft-booking" type="button" role="tab" aria-controls="nav-soft-booking" aria-selected="false">Soft Booking &nbsp;<span class="badge badge-primary text-white">{{count($data->courseSoftBooking->where('status', 0))}}</span></button>
                    <button class="nav-link" id="nav-waiting-list-tab" data-toggle="tab" data-target="#nav-waiting-list" type="button" role="tab" aria-controls="nav-waiting-list" aria-selected="false">Waiting List &nbsp;<span class="badge badge-primary text-white">{{count($data->courseWaitingList->where('status', 0))}}</span></button>
                    <button class="nav-link" id="nav-payments-list-tab" data-toggle="tab" data-target="#nav-payments-list" type="button" role="tab" aria-controls="nav-payments-list" aria-selected="false">Payments List</button>
                    <button class="nav-link" id="nav-refresher-tab" data-toggle="tab" data-target="#nav-refresher-list" type="button" role="tab" aria-controls="nav-refresher-list" aria-selected="false">Refreshers &nbsp;<span class="badge badge-primary text-white">{{count($data->courseRefreshers->where('status', '!=', 2))}}</span></button>
                    @if( $data->courseMain->course_type_id == 1)
                    <button class="nav-link" id="nav-response-list-tab" data-toggle="tab" data-target="#nav-response-list" type="button" role="tab" aria-controls="nav-response-list" aria-selected="false">TP Gateway Response </button>
                    @endif
                    {{-- <button class="nav-link" id="nav-activity-list-tab" data-toggle="tab" data-target="#nav-activity-list" type="button" role="tab" aria-controls="nav-activity-list" aria-selected="false">Activity List &nbsp;<span class="badge badge-primary text-white"></span></button> --}}
                    <button class="nav-link" id="nav-admintasks-list-tab" data-toggle="tab" data-target="#nav-admintasks-list" type="button" role="tab" aria-controls="nav-admintasks-list" aria-selected="false">Tasks List &nbsp;<span class="badge badge-primary text-white"></span></button>
                </div>
            </nav>

        </div>

        <div class="col-md-12">

            <div class="tab-content" id="nav-tabContent">
                <div class="row marlr0 tab-pane fade show active" id="nav-details" role="tabpanel" aria-labelledby="nav-details-tab">
                    <div class="page-title-box">
                        <h4 class="header-title mt-0">{{$data->courseMain->name}}</h4>
                        <div class="row">
                            @if($data->course_link)
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="course_link">Course Link</label>
                                    <input type="text" readonly="" class="form-control" value="{{ $data->course_link }}" />
                                </div>
                            </div>
                            @endif
                            @if($data->meeting_id)
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="meeting_id">Course Meeting Id</label>
                                    <input type="text" readonly="" class="form-control" value="{{ $data->meeting_id }}" />
                                </div>
                            </div>
                            @endif
                            @if($data->meeting_pwd)
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="meeting_pwd">Course Meeting Password</label>
                                    <input type="text" readonly="" class="form-control" value="{{ $data->meeting_pwd }}" />
                                </div>
                            </div>
                            @endif
                        </div>
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
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Registration Closing Time</label>
                                    <input type="time" readonly="" class="form-control" value="{{ $data->registration_closing_time }}" />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Course Start Date</label>
                                    <input type="text" readonly="" class="form-control" value="{{ $data->course_start_date }}" />
                                </div>
                            </div>
                            <div class="col-md-2">
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
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Course Vacancy Code</label>
                                    <input type="text" readonly="" class="form-control" value="{{ $data->coursevacancy_code }}" />
                                </div>
                            </div>
                            <!-- Show Course Remarks -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Course Remarks</label>
                                    <textarea type="text" readonly="" class="form-control">{{ $data->course_remarks }}</textarea>
                                </div>
                            </div>
                            <!-- Show Course Remarks -->
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
                        @if( $data->courseMain->course_type_id != 3)
                        <a class="btn btn-info px-4 btn-rounded float-right mt-3 mb-3 ml-2" href="{{ route('admin.course.generateCertificate', $data->id) }}"><i class="fa fa-print"></i> Certificates</a>
                        @endif
                        <form method="POST" action={{route('admin.reports.courseRunTrainee.export.excel', $data->id)}}>
                            @csrf
                            <button type="submit" class="btn btn-warning float-right mt-3 mb-3 mr-2">Download Trainee List</button>
                            <button type="button" class="btn btn-danger float-right mt-3 mb-3 mr-2 cancel-enroll-all-couresrun" id="cancel-enroll-all-couresrun">
                                <i class="far fa-trash-alt font-16"></i> Cancel Enrolement</button>
                            <a class="btn btn-primary px-4 btn-rounded float-right mt-3 mb-3 mr-2" href="{{ route('admin.studentenrolment.add_enroll_via', $data->id) }}"><i class="add-new"></i>  Add New</a>
                            {{-- <button type="button" class="btn btn-primary px-4 btn-rounded float-right mt-3 mb-3 mr-2 add-enroll-couresrun" id="add-enroll-couresrun" data-courserunid="{{$data->id}}"><i class="add-new"></i> Add New</button> --}}
                        </form>
                        <h4 class="header-title mt-0">Trainee List</h4>
                        <div class="table-responsive dash-social min-height-datatable-list">
                            <table class="table" id="datatable-cancel">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th>TP Gateway Ref</th>
                                    <th>NRIC</th>
                                    <th>Trainee Name</th>
                                    <th>Email Address</th>
                                    <th>Contact No.</th>
                                    <th>Assessment</th>
                                    <th>Company Name</th>
                                    <th>Payment Mode</th>
                                    <th>Status</th>
                                    <th>Attended Sessions</th>
                                    <th>Remarks</th>
                                    <th>Invoice #</th>
                                    <th>Payment Status</th>
                                    <th>Action</th>
                                </tr><!--end tr-->
                                </thead>
                                <tbody>
                                    @foreach( $data->courseEnrolledNotEnrolledEnrolments as $enrolment )
                                    @php
                                    $totalSession = count($enrolment->attendances);
                                    $presentSessionCount = $enrolment->attendances->where('is_present',1)->where('attendance_sync',1)->count();
                                    $percentProgress = 0;
                                    if($presentSessionCount && $totalSession != 0){
                                        $percentProgress = round($presentSessionCount / $totalSession * 100);
                                    }
                                    @endphp
                                        <tr>
                                            <td>{{$enrolment->id}}</td>
                                            <td>{{$enrolment->tpgateway_refno }}</td>
                                            <td>{{ convertNricToView($enrolment->student->nric) }}</td>
                                            <td>{{ $enrolment->student->name }}</td>
                                            <td>{{ $enrolment->email }}</td>
                                            <td>{{ $enrolment->mobile_no }}</td>
                                            <td>
                                                @if(!is_null($enrolment->assessment))
                                                <span class="badge badge-soft-{{$enrolment->assessment == 'c' ? 'success' : 'danger' }}">{{ syncAssessmentWithTrainer($enrolment->assessment) }}</span>
                                                @else
                                                --
                                                @endif
                                            </td>
                                            <td>{{ $enrolment->company_name }}</td>
                                            <td>
                                                @if( !is_null($enrolment->payment_mode_company) )
                                                    {{ $enrolment->payment_mode_company }}
                                                @elseif( !is_null($enrolment->payment_mode_individual) )
                                                    {{ $enrolment->payment_mode_individual }}
                                                @elseif( !is_null($enrolment->other_paying_by) )
                                                    {{ $enrolment->other_paying_by }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if( $enrolment->status == 1 )
                                                <span class="badge badge-soft-danger">Enrolment Cancelled</span>
                                                @elseif( $enrolment->status == 2 )
                                                <span class="badge badge-soft-danger">Holding List</span>
                                                @endif
                                                @if( $enrolment->status == 0 )
                                                <span class="badge badge-soft-success">Enrolled</span>
                                                @elseif( $enrolment->status == 1 )
                                                <span class="badge badge-soft-danger">Enrolment Cancelled</span>
                                                @else
                                                <span class="badge badge-soft-danger">Not Enrolled</span>
                                                @endif
                                            </td>
                                            <td>{{$percentProgress}}%</td>
                                            <td>{{ $enrolment->remarks }}</td>
                                            <td>{{ $enrolment->xero_invoice_number }}</td>
                                            <td>${{ $enrolment->xero_paid_amount }}/${{ $enrolment->amount }}</td>
                                            {{-- <td>{{ getPaymentStatus($enrolment->payment_status) }}</td> --}}
                                            <td>
                                                <div class="dropdown dot-list">
                                                    <a href="#" class="dropdown-toggle rounded-bdr" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="list-dots"></span></a>
                                                    <ul  class="dropdown-menu">
                                                        @can('studentenrolment-view')
                                                        <li><a href="{{route('admin.studentenrolment.view',$enrolment->id)}}"><i class="fas fa-eye font-16"></i> View</a></li>
                                                        <li><a href="{{route('admin.course.preview-invoice', $enrolment->id)}}"><i class="fa fa-sharp fa-solid fa-file"></i> Preview Invoices</a></li>
                                                        @endcan
                                                        <li><a href="{{route('admin.studentenrolment.edit',$enrolment->id)}}"><i class="fas fa-pencil-alt font-16"></i>Edit</a></li>
                                                        <li><a href="{{ route('admin.payment.add') }}?studentenrollment={{$enrolment->id}}"><i class="fas fa-dollar-sign font-16"></i> Add Payment</a></li>
                                                        <li><a class="viewpayment" href="javascript:void(0)" enrolement_id="{{$enrolment->id}}"><i class="fas fa-dollar-sign font-16"></i>View Payments</a></li>
                                                        @if( $enrolment->status != 1 )
                                                        <li><a class="viewenrolmentresponse" href="javascript:void(0)" type="enrolment" enrolement_id="{{$enrolment->id}}"><i class="fas fa-eye font-16"></i>Enrollment Res</a></li>
                                                        @endif
                                                        @if( $enrolment->courseRun->course_type_id != 2 )
                                                            <li><a class="cancelenrolement" href="javascript:void(0)" enrolement_id="{{$enrolment->id}}" ><i class="far fa-trash-alt font-16"></i>Cancel Enrolement</a></li>
                                                            <li><a class="holdenrolement" href="javascript:void(0)" enrolement_id="{{$enrolment->id}}" ><i class="far fa-stop-circle font-16"></i>Move to Hold List</a></li>
                                                        @endif
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
                                    <th>Company Name</th>
                                    <th>Payment Mode</th>
                                    <th>Remarks</th>
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
                                            <td>{{ $enrolment->company_name }}</td>
                                            <td>
                                                @if( !is_null($enrolment->payment_mode_company) )
                                                    {{ $enrolment->payment_mode_company }}
                                                @elseif( !is_null($enrolment->payment_mode_individual) )
                                                    {{ $enrolment->payment_mode_individual }}
                                                @elseif( !is_null($enrolment->other_paying_by) )
                                                    {{ $enrolment->other_paying_by }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ $enrolment->remarks }}</td>
                                            <td>{{ $enrolment->xero_invoice_number }}</td>
                                            <td>${{ $enrolment->amount_paid }}/${{ $enrolment->amount }}</td>
                                            {{-- <td>{{ getPaymentStatus($enrolment->payment_status) }}</td> --}}
                                            <td>
                                                <div class="dropdown dot-list">
                                                    <a href="#" class="dropdown-toggle rounded-bdr" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="list-dots"></span></a>
                                                    <ul  class="dropdown-menu">
                                                        @can('studentenrolment-view')
                                                        <li><a href="{{route('admin.studentenrolment.view',$enrolment->id)}}"><i class="fas fa-eye font-16"></i> View</a></li>
                                                        <li><a href="{{route('admin.course.preview-invoice', $enrolment->id)}}"><i class="fa fa-sharp fa-solid fa-file"></i> Preview Invoices</a></li>
                                                        @endcan
                                                        <li><a href="{{route('admin.studentenrolment.edit',$enrolment->id)}}"><i class="fas fa-pencil-alt font-16"></i>Edit</a></li>
                                                        <li><a href="{{ route('admin.payment.add') }}?studentenrollment={{$enrolment->id}}"><i class="fas fa-dollar-sign font-16"></i> Add Payment</a></li>
                                                        <li><a class="viewpayment" href="javascript:void(0)" enrolement_id="{{$enrolment->id}}"><i class="fas fa-dollar-sign font-16"></i>View Payments</a></li>
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
                                    <th>Company Name</th>
                                    <th>Payment Mode</th>
                                    <th>Remarks</th>
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
                                            <td>{{ $enrolment->company_name }}</td>
                                            <td>
                                                @if( !is_null($enrolment->payment_mode_company) )
                                                    {{ $enrolment->payment_mode_company }}
                                                @elseif( !is_null($enrolment->payment_mode_individual) )
                                                    {{ $enrolment->payment_mode_individual }}
                                                @elseif( !is_null($enrolment->other_paying_by) )
                                                    {{ $enrolment->other_paying_by }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ $enrolment->remarks }}</td>
                                            <td>{{ $enrolment->xero_invoice_number }}</td>
                                            <td>${{ $enrolment->amount_paid }}/${{ $enrolment->amount }}</td>
                                            {{-- <td>{{ getPaymentStatus($enrolment->payment_status) }}</td> --}}
                                            <td>
                                                <div class="dropdown dot-list">
                                                    <a href="#" class="dropdown-toggle rounded-bdr" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="list-dots"></span></a>
                                                    <ul  class="dropdown-menu">
                                                        @can('studentenrolment-view')
                                                        <li><a href="{{route('admin.studentenrolment.view',$enrolment->id)}}"><i class="fas fa-eye font-16"></i> View</a></li>
                                                        <li><a href="{{route('admin.course.preview-invoice', $enrolment->id)}}"><i class="fa fa-sharp fa-solid fa-file"></i> Preview Invoices</a></li>
                                                        @endcan
                                                        <li><a href="{{route('admin.studentenrolment.edit',$enrolment->id)}}"><i class="fas fa-pencil-alt font-16"></i>Edit</a></li>
                                                        <li><a href="{{ route('admin.payment.add') }}?studentenrollment={{$enrolment->id}}"><i class="fas fa-dollar-sign font-16"></i> Add Payment</a></li>
                                                        <li><a class="viewpayment" href="javascript:void(0)" enrolement_id="{{$enrolment->id}}"><i class="fas fa-dollar-sign font-16"></i>View Payments</a></li>
                                                        @if( $enrolment->courseRun->course_type_id != 2 )
                                                            <li><a class="cancelenrolement" href="javascript:void(0)" enrolement_id="{{$enrolment->id}}" ><i class="far fa-trash-alt font-16"></i>Cancel Enrolement</a></li>
                                                        @endif
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
                <div class="row marlr0 tab-pane fade" id="nav-soft-booking" role="tabpanel" aria-labelledby="nav-soft-booking-tab">
                    <div class="page-title-box">
                        <a class="btn btn-primary px-4 btn-rounded float-right mt-0 mb-3" href="{{ route('admin.softbooking.add') }}"><i class="add-new"></i> Add Soft Booking</a>
                        <h4 class="header-title mt-0">Soft Booking</h4>
                        <div class="table-responsive dash-social">
                            <table class="table sm-datatable-booking">
                                <thead>
                                <tr>
                                    <th>Nric</th>
                                    <th>Name</th>
                                    <th>Email Address</th>
                                    <th>Contact No.</th>
                                    <th>Deadline Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr><!--end tr-->
                                </thead>

                                <tbody>
                                    @foreach( $data->courseSoftBooking as $booking )
                                        <tr>
                                            <td>{{ convertNricToView($booking->nric) }}</td>
                                            <td>{{ $booking->name }}</td>
                                            <td>{{ $booking->email }}</td>
                                            <td>{{ $booking->mobile }}</td>
                                            <td>{{ $booking->deadline_date->format('d M Y') }}</td>
                                            <td>
                                                <span class="badge badge-soft-{{$booking->status == 1 ? 'success' : 'danger' }}">{{getCourseSoftBookingStatus($booking->status)}}</span>
                                            <td>
                                                <div class="dropdown dot-list">
                                                    <a href="#" class="dropdown-toggle rounded-bdr" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="list-dots"></span></a>
                                                    <ul  class="dropdown-menu">
                                                        @if( $booking->status == 0)
                                                        <li><a href="{{route('admin.studentenrolment.add')}}?softbooking={{$booking->id}}"><i class="fas fa-user font-16"></i> Enroll Student</a></li>
                                                        @endif
                                                        <li><a href="{{route('admin.softbooking.edit',$booking->id)}}"><i class="fas fa-pencil-alt font-16"></i> Edit</a></li>
                                                        <li><a href="javascript:void(0)" class="viewnotessoftbooking" main_id="{{$booking->id}}"><i class="mdi mdi-note font-16"></i> View Note</a></li>
                                                        {{-- <li><a href="{{route('admin.softbooking.edit',$booking->id)}}"><i class="far fa-trash-alt font-16"></i> Delete</a></li> --}}
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
                <div class="row marlr0 tab-pane fade" id="nav-waiting-list" role="tabpanel" aria-labelledby="nav-waiting-list-tab">
                    <div class="page-title-box">
                        <a class="btn btn-primary px-4 btn-rounded float-right mt-0 mb-3" href="{{ route('admin.waitinglist.add') }}"><i class="add-new"></i> Add Waiting List</a>
                        <h4 class="header-title mt-0">Waiting List</h4>
                        <div class="table-responsive dash-social">
                            <table class="table sm-datatable-booking">
                                <thead>
                                <tr>
                                    <th>Nric</th>
                                    <th>Name</th>
                                    <th>Email Address</th>
                                    <th>Contact No.</th>
                                    <th>Register On</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr><!--end tr-->
                                </thead>

                                <tbody>
                                    @foreach( $data->courseWaitingList as $waiting )
                                        <tr>
                                            <td>{{ convertNricToView($waiting->nric) }}</td>
                                            <td>{{ $waiting->name }}</td>
                                            <td>{{ $waiting->email }}</td>
                                            <td>{{ $waiting->mobile }}</td>
                                            <td>{{ $waiting->created_at->format('d M Y') }}</td>
                                            <td>
                                                <span class="badge badge-soft-{{$waiting->status == 1 ? 'success' : 'danger' }}">{{getCourseWaitingListStatus($waiting->status)}}</span>
                                            <td>
                                                <div class="dropdown dot-list">
                                                    <a href="#" class="dropdown-toggle rounded-bdr" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="list-dots"></span></a>
                                                    <ul  class="dropdown-menu">
                                                        @if( $waiting->status == 0)
                                                        <li><a href="{{route('admin.studentenrolment.add')}}?waitinglist={{$waiting->id}}"><i class="fas fa-user font-16"></i> Enroll Student</a></li>
                                                        @endif
                                                        <li><a href="{{route('admin.waitinglist.edit',$waiting->id)}}"><i class="fas fa-pencil-alt font-16"></i> Edit</a></li>
                                                        <li><a href="javascript:void(0)" class="viewnoteswaitinglist" main_id="{{$waiting->id}}"><i class="mdi mdi-note font-16"></i> View Note</a></li>
                                                        {{-- <li><a href="{{route('admin.waitinglist.edit',$waiting->id)}}"><i class="far fa-trash-alt font-16"></i> Delete</a></li> --}}
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
                <div class="row marlr0 tab-pane fade" id="nav-payments-list" role="tabpanel" aria-labelledby="nav-payments-list-tab">
                    <div class="page-title-box">
                        <h4 class="header-title mt-0">Payments List</h4>
                        <div class="table-responsive dash-social">
                            <table class="table sm-datatable">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th>Name</th>
                                    <th>NRIC</th>
                                    <th>Email</th>
                                    <th>Mode</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr><!--end tr-->
                                </thead>
                                <tbody>
                                    @foreach( $payments as $payment )
                                        <tr>
                                            <td></td>
                                            <td>{{ convertNricToView($payment->studentEnrolment->student->nric) }}</td>
                                            <td>{{ $payment->studentEnrolment->student->name }}</td>
                                            <td>{{ $payment->studentEnrolment->email }}</td>
                                            <td>{{ getModeOfPayment($payment->payment_mode) }}</td>
                                            <td>${{ $payment->fee_amount }}</td>
                                            <td>{{ $payment->payment_date }}</td>
                                            <td>
                                                @if( $payment->status == 1 )
                                                <span class="badge badge-soft-danger">Cancelled</span>
                                                @else
                                                <span class="badge badge-soft-success">Paid</span>
                                                @endif
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
                        @if( \Carbon\Carbon::parse($data->course_end_date)->addWeeks(1)->isFuture() )
                        <a class="btn btn-primary px-4 btn-rounded float-right mt-0 mb-3" href="{{ route('admin.refreshers.add', $data->id) }}"><i class="add-new"></i> Add New</a>
                        @endif
                        @if( count($data->courseRefreshers->where('status', '!=', 2)) > 0 )
                        <form method="POST" action={{route('admin.reports.courseRunRefreshers.export.excel', $data->id)}}>
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
                                                        <li><a href="{{route('admin.refreshers.view',$refresher->id)}}"><i class="far fa-eye font-16"></i> View</a></li>
                                                        <li><a href="{{route('admin.refreshers.edit', $refresher->id)}}"><i class="fas fa-pencil-alt font-16"></i> Edit</a></li>
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
                @if( $data->courseMain->course_type_id == 1)
                <div class="row marlr0 tab-pane fade" id="nav-response-list" role="tabpanel" aria-labelledby="nav-response-list-tab">
                    <div class="page-title-box">
                        @if( empty($data->tpgateway_id) )
                            <a class="btn btn-primary px-4 btn-rounded float-right mt-0 mb-3" href="{{ route('admin.course.add-courserun-tpgateway', $data->id) }}"><i class="add-new"></i> Add to TPGateway</a>
                        @endif
                        <h4 class="header-title mt-0">Response</h4>
                        @if( !is_null($data->courseRunResponse) )
                            <?php $courseRunRes = json_decode($data->courseRunResponse); ?>
                            <?php dump($courseRunRes); ?>
                        @endif
                    </div>
                </div>
                @endif

                {{--<div class="row marlr0 tab-pane fade" id="nav-activity-list" role="tabpanel" aria-labelledby="nav-activity-list-tab">
                    <div class="page-title-box">
                    <div class="table-responsive dash-social">
                            <table class="table sm-datatable">
                                <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Category</th>
                                    <th>Activity</th>
                                </tr><!--end tr-->
                                </thead>
                                <tbody>
                                     @if( count($revisions) )
                                        @foreach ($revisions as $k => $revision)
                                            <tr>
                                                <td>{{date('d M Y', strtotime($revision->created_at))}}</td>
                                                <td>{{getModuleNameByType($revision->revisionable_type)}}</td>
                                                <td>
                                                @if($revision->key =='created_at' && !$revision->old_value)
                                                    @php echo str_replace("_"," ",ucfirst($revision->key)).' of '.$data->courseMain->name .'('.$revision->new_value.') was added by '.getAdminNameById($revision->user_id); @endphp
                                                    @else
                                                        @if(!empty($revision->old_value))
                                                            @php echo str_replace("_"," ",ucfirst($revision->key)).' of '.$data->courseMain->name .' was changed by '.getAdminNameById($revision->user_id).' from '.$revision->old_value. ' to ' .$revision->new_value; @endphp
                                                        @else
                                                            @php echo str_replace("_"," ",ucfirst($revision->key)).' of '.$data->courseMain->name .' was set to '.$revision->new_value.' by '.getAdminNameById($revision->user_id); @endphp
                                                        @endif
                                                @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        @else
                                            <tr><td colspan="5" align="center">No Activities found</td></tr>
                                    @endif 
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>--}}

                <div class="row marlr0 tab-pane fade" id="nav-admintasks-list" role="tabpanel" aria-labelledby="nav-admintasks-list-tab">
                    <div class="page-title-box">
                    <div class="table-responsive dash-social">
                            <table class="table sm-datatable">
                                <thead>
                                <tr>
                                    <th>Task Type</th>
                                    <th>Template Name</th>
                                    <th>Notes</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr><!--end tr-->
                                </thead>
                                <tbody>
                                    @if( count($data->courseTasks) )
                                        @foreach ($data->courseTasks as $k => $task)
                                            <tr>
                                                <td>
                                                    <strong>{{ triggerEventTypes($task->task_type) }}</strong>
                                                </td>
                                                <td>
                                                    @if( $task->task_type == 2 && !is_null($task->sms_template_id) )
                                                        {{ $task->smsTemplate->name }}
                                                    @elseif( $task->task_type == 1 )
                                                        {{ $task->template_name }}
                                                    @elseif( $task->task_type == 3 )
                                                        {{ $task->task_text }}
                                                    @endif
                                                </td>
                                                <td>{!! nl2br(e($task->notes)) !!}</td>
                                                <td>
                                                    @if( $task->status == 1 )
                                                        <span class="badge badge-soft-primary">Created</span>
                                                    @elseif( $task->status == 2 )
                                                        <span class="badge badge-soft-warning">Pending</span>
                                                    @else
                                                        <span class="badge badge-soft-success">Completed</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="dropdown dot-list">
                                                        <a href="#" class="dropdown-toggle rounded-bdr" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="list-dots"></span></a>
                                                        <ul  class="dropdown-menu">
                                                            <li><a href="javascript:void(0)" task_id="{{$task->id}}" class="updatetasknote"><i class="mdi mdi-note font-16"></i> Update Note</a></li>
                                                            <li><a href="javascript:void(0)" task_id="{{$task->id}}" class="viewtaskdetails"><i class="mdi mdi-note font-16"></i>View Task Details</a></li>
                                                            @if( $task->task_type == 1 && $task->status != 3)
                                                            <li><a href="{{route('admin.tasks.sendTaskEmail', $task->id)}}"><i class="mdi mdi-email-check-outline font-16"></i> Send Email</a></li>
                                                            @elseif( $task->task_type == 2 && $task->status != 3 )
                                                            <li><a href="{{route('admin.tasks.sendTasksms', $task->id)}}"><i class="fas fa-sms font-16"></i> Send SMS</a></li>
                                                            @endif
                                                            @if( $task->status != 3 )
                                                            <li><a href="javascript:void(0)" task_id="{{$task->id}}" class="marktaskComplete"><i class="fas fa-tasks font-16"></i> Mark Task as Completed</a></li>
                                                            @endif
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                        @else
                                            <tr><td colspan="5" align="center">No Task found</td></tr>
                                    @endif
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
<script src="{{ asset('assets/plugins/sweet-alert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/js/dataTables.checkboxes.min.js') }}"></script>

<script type="text/javascript">
    $(function () {

        // Generate Documents
        $(document).on('click', '.btn-trainer', function(e) {
            e.preventDefault();
            let course_id = $(this).data('courseid');
            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                url: '{{ route('admin.ajax.generate.documents') }}',
                type: "POST",
                dataType: "JSON",
                data: {
                    id: course_id
                },
                success: function(res) {
                    showToast(res.msg, 1);
                }
            }); // end ajax
        });

        $(document).on('click', '.viewpayment', function(e) {
            e.preventDefault();
            let _enrolement_id = $(this).attr('enrolement_id');
            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                url: '{{ route('admin.ajax.studentEnrolmentPayment.modal.list') }}',
                type: "POST",
                dataType: "JSON",
                data: {
                    id: _enrolement_id
                },
                success: function(res) {
                    $('#modal-content').empty().html(res.html);
                    $('.model-box').modal();
                }
            }); // end ajax
        });

        $(document).on('click', '#add_documents', function(e) {
            e.preventDefault();
            let _courserun_id = $(this).attr('courserunid');
            $('#modal-content').empty().html(`@include('admin.partial.courserun-upload-document-add', ['courserunid' => $data->id])`);
            $('.model-box').modal();
        });

        $(document).on('submit', '#courserun_documnet_upload', function(e) {
            e.preventDefault();
            var btn = $('#submit_document');
            BITBYTE.progress(btn);
            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                url: '{{ route('admin.ajax.courserun-uploaddocuments') }}',
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
                url: '{{ route('admin.ajax.courserun-uploaddocuments.modal.edit') }}',
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
                url: '{{ route('admin.ajax.courserun-uploaddocuments.modal.store') }}',
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

        $(document).on('click', '.viewnotessoftbooking', function(e) {
            e.preventDefault();
            let _main_id = $(this).attr('main_id');
            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                url: '{{ route('admin.ajax.softbooking.modal.viewnotes') }}',
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

        $(document).on('click', '.viewnotesrefresher', function(e) {
            e.preventDefault();
            let _main_id = $(this).attr('main_id');
            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                url: '{{ route('admin.ajax.refresher.modal.viewnotes') }}',
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

        $(document).on('click', '.viewnoteswaitinglist', function(e) {
            e.preventDefault();
            let _main_id = $(this).attr('main_id');
            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                url: '{{ route('admin.ajax.waitinglist.modal.viewnotes') }}',
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

        @include('admin.partial.actions.holdinglist');

        @include('admin.partial.actions.viewenrolmentresponse');

        @include('admin.partial.actions.cancelenrolement');

        $(document).on('click', '#enrolagain', function(e) {
            e.preventDefault();
            var btn = $('#enrolagain');
            BITBYTE.progress(btn);
            let _enrolement_id = $(this).attr('enrolement_id');
            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                url: '{{ route('admin.ajax.studentEnrolmentAgain') }}',
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

        var cNtable = $('#datatable-cancel').DataTable({
            'columnDefs': [
                {
                'targets': 0,
                "render": function (data, type, row, meta) {
                        return (data) ?  '<input type="checkbox" class="dt-checkboxes checkbox" value=' +data+ '>' : '<input type="checkbox" disabled >';
                },
                'checkboxes': {
                    'selectRow': true
                    },
                }
            ],
            'select': {
                'style': 'multi'
            }
        });
        $(document).on('click', '.cancel-enroll-all-couresrun', function(e) {
            swal.fire({
                title: 'Are you sure?',
                text: "You want to cancel the enrolement!",
                input: "text",
                inputLabel: "Type DELETE to confirm",
                inputPlaceholder: "Type DELETE to confirm",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, cancel it!',
                cancelButtonText: 'No',
                reverseButtons: true,
                inputValidator: (inputValue) => {
                if (inputValue === null) return false;
                if (inputValue === "") {
                    return "You need to Type DELETE to confirm!";
                }
                if (inputValue.toUpperCase() != "DELETE") {
                    return "You need to Type DELETE to confirm!";
                }
                }
            }).then((result) => {
                if (result.value) { 
                    e.preventDefault();
                    var form = this;
                    var idsArr = [];

                    // Iterate over all selected checkboxes
                    var selectedData = $(".checkbox").each(function(index, rowId){
                            // Create a hidden element                 
                        if(this.checked){
                            $(form).append(
                                    $('<input>')
                                    .attr('type', 'hidden')
                                    .attr('name', 'id[]')
                                    .val(rowId)
                                );
                        }  
                        ids = idsArr.push($(rowId));
                    });
                    var rows_selected = cNtable.column(0).checkboxes.selected();
                        console.log("====>>>", rows_selected);
                        // Filter out true and false values
                        rows_selected = rows_selected.filter(function(value) {
                            return value !== true && value !== false;
                        });

                        // Filter out empty and null values
                        rows_selected = rows_selected.filter(function(value) {
                            return value !== null && value !== undefined && value !== "";
                        });

                        // Remove occurrences of the string "null"
                        rows_selected = rows_selected.filter(function(value) {
                            return value !== "null";
                        });
                                            
                    if(idsArr.length <=0){
                        swal.fire(
                            'Opps',
                            'Please select one enrollment to cancel.',
                            'error'
                        )
                    }else {                       
                            var _enrolement_Ids = rows_selected.toArray();                               
                            $.ajax({
                                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                                url: '{{ route('admin.ajax.studentEnrolment.modal.cancel') }}',
                                type: "POST",
                                dataType: "JSON",
                                data: {
                                    ids: _enrolement_Ids
                                },
                                success: function(res) {
                                    console.log(res);
                                    if( res.status == true ) {
                                        swal.fire({
                                            title: 'Cancelled',
                                            text: "Your enrolements has been cancelled.",
                                            type: 'success',
                                            confirmButtonText: 'Ok',
                                        }).then((result) => {
                                            if (result.value) {
                                                location.reload();
                                            }
                                        })
                                    } else {
                                        swal.fire(
                                            'Opps',
                                            'Some error occured, Please try again.',
                                            'error'
                                        )
                                    }
                                }
                            });
                    }
                }
            });
        });

        $(document).on('click', '#add-enroll-couresrun', function(e){
            e.preventDefault();
            var _courseRunId = $(this).data('courserunid');
            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                url: '',
                type: "GET",
                dataType: "JSON",
                data: {
                    courseRunId: _courseRunId
                },
                success: function(res) {
                    // console.log(res);
                    $('.ea-admin-portal').html(res.view)
                }
            });
        });

        var table = $('.sm-datatable').DataTable();
        
        $('.sm-datatable-booking').DataTable({
            "order": [[ 5, "desc" ]],
        });


        // Datatable
        var table = $('#datatable').DataTable({
            "fnDrawCallback": function( oSettings ) {
                initTooltip();
            },
            "pageLength": 10,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.course.listdatatable', $data->courseMain->id) }}",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: "POST",
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('Authorization');
                },
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'minintakesize', name: 'minintakesize'},
                {data: 'intakesize', name: 'intakesize'},
                {data: 'threshold', name: 'threshold'},
                {data: 'registeredusercount', name: 'registeredusercount'},
                {data: 'modeoftraining', name: 'modeoftraining', orderable: false, searchable: false},
                {data: 'coursevacancy_code', name: 'coursevacancy_code'},
                {data: 'course_type', name: 'course_type'},
                {data: 'is_published', name: 'is_published'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        });
    });
</script>
@endpush
