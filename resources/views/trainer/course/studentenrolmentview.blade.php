@extends('trainer.layouts.master')
@section('title', 'Student Enrolment')
@section('content')
<div class="container-fluid">
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">View Student Enrolment</a></li>
                        <li class="breadcrumb-item active">View</li>
                    </ol>
                </div>
                <h4 class="page-title">View Student Enrolment</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <div class="row view-all-rec">
        <div class="col-12">
            <div class="card">
                <h5 class="card-header bg-secondary text-white mt-0">View Student Enrolment</h5>
                <div class="card-body">
                    <div class="row">

                        <div class="col-md-3">
                            <label>Course Name / Course Run Id</label>
                            <p><strong>{{$data->courseRun->courseMain->name}} / {{$data->courseRun->tpgateway_id}}</strong></p>
                        </div>
                        <div class="col-md-3">
                            <label>Selected Course Run </label>
                            <p><strong>{{$data->courseRun->course_start_date}} - {{$data->courseRun->course_end_date}}</strong></p>
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
                        @endif
                        <div class="col-md-2">
                            <label>Xero Invoice Number </label>
                            <p><strong>{{ $data->xero_invoice_number }}</strong></p>
                        </div>
                    </div>

                    @if( $singleCourse)
                    <div class="row">

                        <div class="col-md-4">
                            <label>Company SME </label>
                            <p><strong>{{ $data->company_sme }}</strong></p>
                        </div>

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
                            <p><strong>{{ $data->learning_mode }}</strong></p>
                        </div>

                        <div class="col-md-4">
                            <label>Name </label>
                            <p><strong>{{ $data->student->name }}</strong></p>
                        </div>

                        <div class="col-md-4">
                            <label>NRIC</label>
                            <p><strong>{{ convertNricToView($data->student->nric) }}</strong></p>
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
                            <label>Designation </label>
                            <p><strong>{{ $data->designation }}</strong></p>
                        </div>

                        <div class="col-md-4">
                            <label>Education Qualification </label>
                            <p><strong>{{ $data->education_qualification }}</strong></p>
                        </div>

                        <div class="col-md-4">
                            <label>Salary </label>
                            <p><strong>{{ $data->salary }}</strong></p>
                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-4">
                            <label>Company Name </label>
                            <p><strong>{{ $data->company_name }}</strong></p>
                        </div>

                        <div class="col-md-4">
                            <label>Company Contact Person </label>
                            <p><strong>{{ $data->company_contact_person }}</strong></p>
                        </div>

                        <div class="col-md-4">
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
                        <div class="col-md-4">
                            <label>Grant Status</label>
                            <p>
                                <strong>{{$data->grantStatus}}</strong>
                                @if( !is_null($data->grantResponse) )
                                <button class="btn btn-secondary viewenrolmentresponse" type="grant" enrolement_id="{{$data->id}}">Response</button>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-4">
                            <label>Grant Estimated</label>
                            <p>
                                <strong>{{$data->grantEstimated}}</strong>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <label>Grant Ref No</label>
                            <p>
                                <strong>{{$data->grantRefNo}}</strong>
                            </p>
                        </div>
                    </div>
                    @endif

                    <div class="row">

                        @if( $singleCourse)
                        <div class="col-md-3">
                            <label>Course Brochure Determined</label>
                            <p><strong>{{ $data->course_brochure_determined == 1 ? 'Yes' : 'No'}}</strong></p>
                        </div>
                        @endif

                        <div class="col-md-3">
                            <label>Nett Course Fee</label>
                            <p><strong>{{ $data->amount }}</strong></p>
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
                            </p>
                        </div>
                        @endif

                        <div class="col-md-3">
                            <label>Payment Status </label>
                            <p><strong>{{ getPaymentStatus($data->payment_status) }}</strong></p>
                        </div>

                    </div>
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
                            </tr>
                            @endforeach
                            <div>
                                Total Fees ({{ $data->amount }}) - Paid Fees ({{$data->amount_paid}}) = Remaining Fees ({{ $data->amount - $data->amount_paid }})
                            </div>
                        </tbody>
                      </table>
                </div>

            </div><!--end card-->
        </div> <!--end col-->
    </div><!--end row-->

</div><!-- container -->
@endsection
