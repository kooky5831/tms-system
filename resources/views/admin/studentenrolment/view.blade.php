@extends('admin.layouts.master')
@section('title', 'Student Enrolment')
@push('css')
<link href="{{ asset('assets/plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
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
                        <li class="breadcrumb-item"><a href="{{route('admin.studentenrolment.list')}}">Student Enrolment</a></li>
                        <li class="breadcrumb-item active">View</li>
                    </ol>
                </div>
                <h4 class="page-title">View Student Enrolment
                    <a href="{{ route('admin.studentenrolment.edit', $data->id) }}" class="btn btn-info btn-sm float-right mr-3">Edit Enrollment</a>
                    <a href="{{ route('admin.payment.add') }}?studentenrollment={{$data->id}}" class="btn btn-info btn-sm float-right mr-3">Add Payment</a>
                    @if( $data->course_type_id != 2 && $data->status == 0 )
                        <a class="btn btn-info btn-sm float-right mr-3 holdenrolement" href="javascript:void(0)" enrolement_id="{{$data->id}}">Move to Hold List</a>
                    @endif
                </h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <div class="row view-all-rec">
        <div class="col-12">
            <div class="card">
                {{-- <form action="{{ route('admin.studentenrolment.edit', $data->id) }}" method="POST" enctype="multipart/form-data"> --}}
                <form action="{{ route('admin.studentenrolment.edit', $data->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <h5 class="card-header bg-secondary text-white mt-0">View Student Enrolment</h5>
                    <div class="card-body">
                        
                        <div class="row">
                            {{-- <div class="progress">{{$percentProgress}}%</div> --}}
                            <div class="col-md-3">
                                <label>Course Name / Course Run Id</label>
                                <p><strong>{{$data->courseRun->courseMain->name}} / {{$data->courseRun->tpgateway_id}}</strong></p>
                            </div>
                            <div class="col-md-2">
                                <label>Selected Course Run </label>
                                <p><strong>{{$data->courseRun->course_start_date}} - {{$data->courseRun->course_end_date}}</strong></p>
                            </div>

                            <div class="col-md-2">
                                <label>Xero Invoice Number </label>
                                <p><strong>{{ $data->xero_invoice_number }}</strong></p>
                            </div>

                            
                            @if( $singleCourse)
                            <div class="col-md-2">
                                    <label>Sponsored By Company </label>
                                    <p><strong>{{ $data->sponsored_by_company }}</strong></p>
                            </div>

                            <div class="col-md-2">
                                <label>TPGateway Reference Number </label>
                                <p><strong>{{ $data->tpgateway_refno }}</strong></p>
                            </div>
                            @else
                                <div class="col-md-4"></div>
                            @endif
                            
                            <div class="col-md-1">
                                <label>Attended Sessions</label>
                                <div class="circular-progress">
                                    <div class="value-container">{{$percentProgress}}</div>
                                </div>
                            </div>

                        </div>

                        @if( $singleCourse)
                        <div class="row">

                            @if($data->sponsored_by_company == "Yes")
                                <div class="col-md-4">
                                    <label>Company SME </label>
                                    <p><strong>{{ $data->company_sme }}</strong></p>
                                </div>
                            @endif

                            <div class="col-md-4">
                                <label>Nationality </label>
                                <p><strong>{{ $data->nationality }}</strong></p>
                            </div>

                            <div class="col-md-4">
                                <label>Age </label>
                                <p><strong>{{ $data->age }}</strong></p>
                            </div>

                        </div>
                        @endif

                        <div class="row">

                            <div class="col-md-4">
                                <label>Learning Mode </label>
                                <p><strong>{{ $data->courseRun->courseMain->course_mode_training }}</strong></p>
                            </div>

                            <div class="col-md-4">
                                <label>Name </label>
                                <p><strong>{{ $data->student->name }}</strong></p>
                            </div>

                            <div class="col-md-4">
                                <label>NRIC</label>
                                <p><strong>{{ $data->student->nric }}</strong></p>
                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-4">
                                <label>Email </label>
                                <p><strong>{{ $data->email }}</strong></p>
                            </div>

                            <div class="col-md-4">
                                <label>Mobile No </label>
                                <p><strong>{{ $data->mobile_no }}</strong></p>
                            </div>

                            <div class="col-md-4">
                                <label>Date Of Birth </label>
                                <p><strong>{{ $data->dob }}</strong></p>
                            </div>

                        </div>

                        @if( $singleCourse)
                        <div class="row">

                            <div class="col-md-4">
                                <label>Education Qualification </label>
                                <p><strong>{{ $data->education_qualification }}</strong></p>
                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-3">
                                <label>Company UEN </label>
                                <p><strong>{{ $data->company_uen }}</strong></p>
                            </div>

                            <div class="col-md-3">
                                <label>Company Name </label>
                                <p><strong>{{ $data->company_name }}</strong></p>
                            </div>

                            <div class="col-md-3">
                                <label>Company Contact Person </label>
                                <p><strong>{{ $data->company_contact_person }}</strong></p>
                            </div>

                            <div class="col-md-3">
                                <label>Company Contact Person Email </label>
                                <p><strong>{{ $data->company_contact_person_email }}</strong></p>
                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-4">
                                <label>Company Contact Person Number </label>
                                <p><strong>{{ $data->company_contact_person_number }}</strong></p>
                            </div>

                            <div class="col-md-4">
                                <label>Billing Email</label>
                                <p><strong>{{ $data->billing_email }}</strong></p>
                            </div>

                            <div class="col-md-4">
                                <label>Billing Zipcode</label>
                                <p><strong>{{ $data->billing_zip }}</strong></p>
                            </div>

                        </div>
                        @endif

                        <div class="row">
                            @if( $singleCourse)
                            <div class="col-md-4">
                                <label>Billing Address </label>
                                <p><strong>{{ $data->billing_address }}</strong></p>
                            </div>

                            <div class="col-md-4">
                                <label>Billing Country</label>
                                <p><strong>{{ $data->billing_country }}</strong></p>
                            </div>
                            @endif

                            <div class="{{$singleCourse ? 'col-md-4' : 'col-md-12' }}">
                                <label>Remarks</label>
                                <p><strong>{{ $data->remarks }}</strong></p>
                            </div>

                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <label>Payment Remarks </label>
                                <p><strong>{{ $data->payment_remark }}</strong></p>
                            </div>
                            <div class="col-md-4">
                                <label>Payment Due Date </label>
                                <p><strong>{{ $data->due_date }}</strong></p>
                            </div>
                        </div>
                        
                        @if( $singleCourse)
                        <div class="row">

                            <div class="col-md-4">
                                <label>Meal Restrictions</label>
                                <p><strong>{{ $data->meal_restrictions }}</strong></p>
                            </div>

                            <div class="col-md-4">
                                <label>Meal Restrictions Type</label>
                                <p><strong>{{ $data->meal_restrictions_type }}{{$data->meal_restrictions_type == "Other" ? " - ".$data->meal_restrictions_other : '' }}</strong></p>
                            </div>

                            <div class="col-md-4">
                                <label>Computer Navigation Skill</label>
                                <p><strong>{{ $data->computer_navigation_skill == 1 ? 'Yes' : 'No' }}</strong></p>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <label>Enrolment Status</label>
                                <p>
                                    @if( $data->status == 0 )
                                    <span class="badge badge-success text-white">Enrolled</span>
                                    
                                    @elseif( $data->status == 1 )
                                    <span class="badge badge-danger text-white">Enrolment Cancelled</span>

                                    @elseif( $data->status == 2 )
                                    <span class="badge badge-danger text-white">Holding list</span>
                                    @else
                                        <span class="badge badge-danger text-white">Not Enrolled</span>
                                    @endif
                                    @if( !is_null($data->enrollmentResponse) )
                                    <button class="btn btn-secondary viewenrolmentresponse" type="enrolment" enrolement_id="{{$data->id}}">Response</button>
                                    @endif
                                </p>
                            </div>
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
                                    @if( \Carbon\Carbon::parse($data->courseRun->course_end_date." 23:59:59")->isPast() )
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
                                    @if( \Carbon\Carbon::parse($data->courseRun->course_end_date." 23:59:59")->isPast() )
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


                        <div class="row">

                            @if( $singleCourse)
                            <div class="col-md-3">
                                <label>Course Brochure Determined</label>
                                <p><strong>{{ $data->course_brochure_determined == 1 ? 'Yes' : 'No'}}</strong></p>
                            </div>
                            @endif

                            <div class="col-md-2">
                                <label>Nett Course Fee</label>
                                <p><strong>{{ $data->amount }}</strong></p>
                            </div>

                            <div class="col-md-2">
                                <label>Discount Amount</label>
                                <p><strong>{{ $data->discountAmount }}</strong></p>
                            </div>

                            <div class="col-md-3">
                                <label>Payment Mode</label>
                                <p>
                                    <strong>
                                    @if( !is_null($data->payment_mode_company) )
                                        {{ $data->payment_mode_company }}
                                    @elseif( !is_null($data->payment_mode_individual) )
                                        {{ $data->payment_mode_individual }}
                                    @elseif( !is_null($data->other_paying_by) )
                                        {{ $data->other_paying_by }}
                                    @else
                                        -
                                    @endif
                                    </strong>
                                </p>
                            </div>

                            @if( $singleCourse)
                            <div class="col-md-3">
                                <label>Payment TPG Status </label>
                                <p>
                                    @if( is_null($data->isPaymentError) )
                                    <span class="badge badge-info text-white">Not Submitted</span>
                                    @elseif( $data->isPaymentError == 0 )
                                    <span class="badge badge-success text-white">Submitted</span>
                                    @else
                                    <span class="badge badge-danger text-white">Failed</span>
                                    @endif
                                    @if( \Carbon\Carbon::parse($data->courseRun->course_end_date." 23:59:59")->isPast() )
                                    <button class="btn btn-secondary viewpaymentresponse" type="payment" enrolement_id="{{$data->id}}">Response</button>
                                    @endif
                                </p>
                            </div>
                            @endif

                            <div class="col-md-3">
                                <label>Payment Status </label>
                                <p><strong>{{ getPaymentStatus($data->payment_status) }}</strong></p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <label>PESA Referance No. </label>
                                <p><strong>{{ $data->pesa_refrerance_number ?? '-' }}</strong></p>
                            </div>
                            <div class="col-md-4">
                                <label>SkillsFuture Credit </label>
                                <p><strong>{{ $data->skillfuture_credit ?? '-' }}</strong></p>
                            </div>
                            <div class="col-md-4">
                                <label>Vendors@Gov </label>
                                <p><strong>{{ $data->vendor_gov ?? '-' }}</strong></p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                            @if(!$data->grants->isEmpty())
                            <hr>
                                <table class="table">
                                    <thead>
                                        <th>Grant Status</th>
                                        <th>Grant Ref No</th>
                                        <th>Funding Scheme</th>
                                        <th>Funding Component</th>
                                        <th>Amount Estimated</th>
                                        <th>Amount Paid</th>
                                        <th>Amount Recovery</th>
                                        <th>Updated/ Disbursed Date</th>
                                    </thead>
                                    <tbody>
                                        @foreach ($data->grants as $grant)
                                            <tr>
                                                <td>{{$grant->grant_status}}</td>
                                                <td>{{$grant->grant_refno}}</td>
                                                <td>{{$grant->scheme_code}} - {{$grant->scheme_description}}</td>
                                                <td>{{$grant->component_code}} - {{$grant->component_description}}</td>
                                                <td>{{number_format($grant->amount_estimated, 2)}}</td>
                                                <td>{{number_format($grant->amount_paid, 2)}}</td>
                                                <td>{{number_format($grant->amount_recovery, 2)}}</td>
                                                <td>{{$grant->disbursement_date}}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <hr>
                                <table class="table">
                                    <thead>
                                        <th>Grant Status</th>
                                        <th>Grant Ref No</th>
                                        <th>Funding Scheme</th>
                                        <th>Funding Component</th>
                                        <th>Amount Estimated</th>
                                        <th>Amount Paid</th>
                                        <th>Amount Recovery</th>
                                        <th>Updated/ Disbursed Date</th>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="8" class="text-center">No Grants Found!</td>
                                        </tr>
                                    </tbody>
                                </table>
                            @endif

                            @if( !is_null($data->tpgateway_refno) )
                            <label>Grant Response </label>
                            <button class="btn btn-secondary viewenrolmentresponse" type="grant" enrolement_id="{{$data->id}}">Response</button>
                            <hr>
                            @endif
                            </div>
                        </div>
                        @endif
                    </div><!--end card-body-->
                    <h4 class="card-header bg-secondary text-white mt-0">Payment History</h4>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                              <tr>
                                <th>No</th>
                                <th>Fees Amount</th>
                                <th>Transaction ID</th>
                                <th>Mode</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php($count=1)
                                @php($total=0)
                                @php($finaltotal=0)
                                @foreach ($data->payments as $payment)
                                <tr>
                                    <td>{{ $count++ }}</td>
                                    <td>{{ $payment->fee_amount }}</td>
                                    <td>{{ $payment->transaction_id }}</td>
                                    <td>{{ getModeOfPayment($payment->payment_mode) }}</td>
                                    <td>{{ date('d-m-Y', strtotime($payment->payment_date)) }}</td>
                                    <td>
                                        @if( $payment->status == 1 )
                                            <span class="badge badge-soft-danger">Cancelled</span>
                                        @else
                                            <span class="badge badge-soft-success">Paid</span>
                                        @endif
                                    </td>
                                    <td><a href="{{ route('admin.payment.view', $payment->id) }}" data-toggle="tooltip" data-placement="bottom" title="" class="mr-2" data-original-title="View Payment Detail"><i class="fas fa-eye text-info font-16"></i></a></td>
                                </tr>
                                @endforeach
                                <div>
                                    Total Fees ({{ $data->amount }}) - Paid Fees ({{$data->amount_paid}}) = Remaining Fees ({{ $data->amount - $data->amount_paid }})
                                </div>
                            </tbody>
                          </table>
                    </div>

                    <div class="card-footer m-0 clearfix">
                        <a href="{{ route('admin.studentenrolment.list') }}" class="btn btn-danger">Back</a>
                    </div>
                </form>
            </div><!--end card-->
        </div> <!--end col-->
    </div><!--end row-->

</div><!-- container -->
@endsection
@push('scripts')
<script src="{{ asset('assets/plugins/sweet-alert2/sweetalert2.min.js') }}"></script>
<script type="text/javascript">
    $(function () {

        $(document).on('click', '.viewpaymentresponse', function(e) {
            e.preventDefault();
            let _enrolement_id = $(this).attr('enrolement_id');
            let _type = $(this).attr('type');
            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                url: '{{ route('admin.ajax.studentPaymentResponse.modal.view') }}',
                type: "POST",
                dataType: "JSON",
                data: {
                    id: _enrolement_id,
                    type: _type
                },
                success: function(res) {
                    $('#modal-content').empty().html(res.html);
                    $('.model-box').modal();
                }
            }); // end ajax
        });

        @include('admin.partial.actions.holdinglist');

        @include('admin.partial.actions.viewenrolmentresponse');

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

        // grant search
        $(document).on('click', '#searchgrantagain', function(e) {
            e.preventDefault();
            var btn = $('#searchgrantagain');
            BITBYTE.progress(btn);
            let _enrolement_id = $(this).attr('enrolement_id');
            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                url: '{{ route('admin.ajax.studentEnrolmentGrantSearchAgain') }}',
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

        // attendance again
        $(document).on('click', '#addAttendanceAgain', function(e) {
            e.preventDefault();
            var btn = $('#addAttendanceAgain');
            BITBYTE.progress(btn);
            let _enrolement_id = $(this).attr('enrolement_id');
            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                url: '{{ route('admin.ajax.studentEnrolmentAttendanceAgain') }}',
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
                url: '{{ route('admin.ajax.studentEnrolmentAssessmentAgain') }}',
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
    let progressBar = document.querySelector(".circular-progress");
let valueContainer = document.querySelector(".value-container");

let progressValue = 0;
let progressEndValue = {{$percentProgress}};
let speed = 10;

if(progressEndValue > 0){
    let progress = setInterval(() => {
  progressValue++;
  valueContainer.textContent = `${progressValue}%`;
  progressBar.style.background = `conic-gradient(
      #4b80d8 ${progressValue * 3.6}deg,
      #ddeaff ${progressValue * 3.6}deg
  )`;
  if (progressValue == progressEndValue) {
    clearInterval(progress);
  }
}, speed);
}
else {
    valueContainer.textContent = `${progressValue}%`;
    progressBar.style.background = `conic-gradient(
        #4b80d8 ${progressValue * 3.6}deg,
        #ddeaff ${progressValue * 3.6}deg
    )`;
}

</script>
@endpush
<style>
.circular-progress {
  position: relative;
  height: 100px;
  width: 100px;
  border-radius: 50%;
  display: grid;
  place-items: center;

}
.circular-progress:before {
  content: "";
  position: absolute;
  height: 84%;
  width: 84%;
  background-color: #ffffff;
  border-radius: 50%;
}
.value-container {
  position: relative;
  font-family: "Poppins", sans-serif;
  font-size: 26px;
  color: #4b80d8;
  font-weight: 600;
}
    </style>