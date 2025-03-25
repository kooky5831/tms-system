@extends('admin.layouts.master')
@section('title', 'Payment')
@section('content')
<div class="container-fluid">
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{route('admin.payment.list')}}">Payment</a></li>
                        <li class="breadcrumb-item active">View</li>
                    </ol>
                </div>
                <h4 class="page-title">View Payment
                    @if( $data->status == 1 )
                    <span class="btn btn-danger btn-sm float-right mr-3">Cancelled</span>
                    @else
                    <span class="btn btn-success btn-sm float-right mr-3">Paid</span>
                    @endif
                </h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                {{-- <form action="{{ route('admin.studentenrolment.edit', $data->id) }}" method="POST" enctype="multipart/form-data"> --}}
                <form action="{{ route('admin.payment.edit', $data->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <!-- <h5 class="card-header bg-secondary text-white mt-0">View Payment</h5> -->
                    <div class="card-body">
                        <h4 class="header-title mt-0">View Payment</h4>
                        <div class="row">

                            <div class="col-md-4">
                                <label>Course Name / Course Run Id</label>
                                <p><strong>{{$data->studentEnrolment->courseRun->courseMain->name}} / {{$data->studentEnrolment->courseRun->tpgateway_id}}</strong></p>
                            </div>
                            <div class="col-md-3">
                                <label>Selected Course Run </label>
                                <p><strong>{{$data->studentEnrolment->courseRun->course_start_date}} - {{$data->studentEnrolment->courseRun->course_end_date}}</strong></p>
                            </div>

                            <div class="col-md-3">
                                <label>Enrolment Id </label>
                                <p><strong>{{ $data->studentEnrolment->tpgateway_refno }}</strong></p>
                            </div>
                        </div>
                        <div class="row">
                            {{-- {{ dd($data) }} --}}
                            <div class="col-md-3">
                                <label>Name </label>
                                <p><strong>{{$data->studentEnrolment->student->name}} </strong></p>
                            </div>

                            <div class="col-md-3">
                                <label>NRIC </label>
                                <p><strong>{{ convertNricToView($data->studentEnrolment->student->nric) }}</strong></p>
                            </div>

                            <div class="col-md-3">
                                <label>email </label>
                                <p><strong>{{ $data->studentEnrolment->email }}</strong></p>
                            </div>

                            <div class="col-md-2">
                                <label>Age</label>
                                <p><strong>{{ $data->studentEnrolment->age }}</strong></p>
                            </div>

                          </div>

                        <div class="row">

                            <div class="col-md-3">
                                <label>Mobile No </label>
                                <p><strong>{{ $data->studentEnrolment->mobile_no }}</strong></p>
                            </div>

                            <div class="col-md-3">
                                <label>Date Of Birth </label>
                                <p><strong>{{ $data->studentEnrolment->dob }}</strong></p>
                            </div>

                            <div class="col-md-3">
                                <label>Qualification </label>
                                <p><strong>{{ $data->studentEnrolment->education_qualification }}</strong></p>
                            </div>

                            <div class="col-md-3">
                                <label>Designation </label>
                                <p><strong>{{ $data->studentEnrolment->designation }}</strong></p>
                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-3">
                                <label>Salary </label>
                                <p><strong>{{ $data->studentEnrolment->salary }}</strong></p>
                            </div>

                            <div class="col-md-3">
                                <label>Nationality </label>
                                <p><strong>{{ $data->studentEnrolment->nationality }}</strong></p>
                            </div>

                            <div class="col-md-3">
                                <label>Learning Mode </label>
                                <p><strong>{{ $data->studentEnrolment->learning_mode }}</strong></p>
                            </div>

                        </div>
                        <hr>
                        <div class="row">

                            <div class="col-md-3">
                                <label>Company Name </label>
                                <p><strong>{{ $data->studentEnrolment->company_name }}</strong></p>
                            </div>

                            <div class="col-md-3">
                                <label>Company Contact Person</label>
                                <p><strong>{{ $data->studentEnrolment->company_contact_person }}</strong></p>
                            </div>

                            <div class="col-md-3">
                                <label>Company Contact Person Email </label>
                                <p><strong>{{ $data->studentEnrolment->company_contact_person_email }}</strong></p>
                            </div>

                            <div class="col-md-3">
                                <label>Company Contact Person Number </label>
                                <p><strong>{{ $data->studentEnrolment->company_contact_person_number }}</strong></p>
                            </div>

                        </div>
                        <hr>
                        <div class="row">

                            <div class="col-md-3">
                                <label>Billing Email </label>
                                <p><strong>{{ $data->studentEnrolment->billing_email }}</strong></p>
                            </div>

                            <div class="col-md-3">
                                <label>Billing Address</label>
                                <p><strong>{{ $data->studentEnrolment->billing_address }}</strong></p>
                            </div>

                            <div class="col-md-3">
                                <label>Billing Zip </label>
                                <p><strong>{{ $data->studentEnrolment->billing_zip }}</strong></p>
                            </div>

                            <div class="col-md-3">
                                <label>Billing Country </label>
                                <p><strong>{{ $data->studentEnrolment->billing_country }}</strong></p>
                            </div>
                        </div>
                        <hr>
                        <div class="row">

                            <div class="col-md-3">
                                <label>Payment Mode </label>
                                <p><strong>{{ getModeOfPayment($data->payment_mode) }}</strong></p>
                            </div>

                            @if($data->payment_mode == 1)
                            <div class="col-md-3">
                                <label>Cheque Number </label>
                                <p><strong>{{ $data->cheque_no }}</strong></p>
                            </div>
                            @else
                            <div class="col-md-3">
                                <label>Credit Card Number </label>
                                <p><strong>{{ $data->creditcard_number }}</strong></p>
                            </div>

                            <div class="col-md-3">
                                <label>Credit Card Type </label>
                                <p><strong>{{ $data->creditcard_type }}</strong></p>
                            </div>
                            @endif

                            {{-- <div class="col-md-3">
                                <label>Payment Amount </label>
                                <p><strong>{{ $data->payment_amount }}</strong></p>
                            </div> --}}

                        </div>

                        <div class="row">

                            <div class="col-md-3">
                                <label>Fee Amount </label>
                                <p><strong>{{ $data->fee_amount }}</strong></p>
                            </div>

                            {{-- <div class="col-md-3">
                                <label>Fee Status </label>
                                <p><strong>{{ getModeOfFeeStatus($data->fee_status) }}</strong></p>
                            </div> --}}

                            <div class="col-md-3">
                                <label>Payment Date </label>
                                <p><strong>{{ $data->payment_date }}</strong></p>
                            </div>

                            <div class="col-md-3">
                                <label>Transaction ID </label>
                                <p><strong>{{ $data->transaction_id }}</strong></p>
                            </div>

                            {{-- <div class="col-md-3">
                                <label>Payment Status </label>
                                <p><strong>{{ getModeOfPaymentStatus($data->payment_status) }}</strong></p>
                            </div> --}}
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label>Payment Remark</label>
                                <p>{{$data->payment_remark}}</p>
                            </div>
                        </div>

                    </div><!--end card-body-->
                    <div class="card-footer m-0 clearfix">
                        <a href="{{ route('admin.payment.list') }}" class="btn btn-danger">Back</a>
                    </div>
                </form>
            </div><!--end card-->
        </div> <!--end col-->
    </div><!--end row-->

</div><!-- container -->
@endsection
