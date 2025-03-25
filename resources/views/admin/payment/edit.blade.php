@extends('admin.layouts.master')
@section('title', 'Edit Payment')
@push('css')
<link href="{{ asset('assets/plugins/daterangepicker/daterangepicker.css') }}" rel="stylesheet" type="text/css" />
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
                        <li class="breadcrumb-item"><a href="{{route('admin.payment.list')}}">Payment</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
                <h4 class="page-title">Edit Payment</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form action="{{ route('admin.payment.edit', $data->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="row space-between marlr0">
                            <h4 class="header-title mt-0">Edit Payment</h4>
                            <div class="form-group" style="float: right;">
                                <a href="{{route('admin.studentenrolment.view', $data->studentEnrolment->id)}}" id="studentenrollmentlink" class="btn btn-primary">View Enrolment</a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="student_enrolments_id">Student Enrolment <span class="text-danger">*</span></label>
                                    <input type="text" readonly name="student_enrolments_id" value="{{$data->studentEnrolment->student->name }} - {{$data->studentEnrolment->student->nric }}" class="form-control" />
                                    {{-- <select name="student_enrolments_id" class="form-control select2" id="student_enrolments_id">
                                        <option value="">Select Student Enrolment</option>
                                        @foreach($studentEnrolmentList as $studentEnrolment)
                                        <option value="{{$studentEnrolment->id}}" {{ $data->student_enrolments_id == $studentEnrolment->id ? 'selected' : ''  }}> Name ( {{$studentEnrolment->student->name}} ) - NRIC( {{$studentEnrolment->student->nric}} ) - Email( {{ $studentEnrolment->email }} )</option>
                                        @endforeach
                                    </select> --}}
                                    @error('student_enrolments_id')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            {{-- <div class="col-md-4">
                                <div class="form-group">
                                    <label for="student_enrolments_id">Student Enrolments <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="{{ $data->student_enrolments_id }}" required name="student_enrolments_id" id="student_enrolments_id" placeholder="" />
                                    @error('student_enrolments_id')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div> --}}

                            
                        <!-- </div>

                        <div class="row"> -->

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="payment_mode">Payment Mode <span class="text-danger">*</span></label>
                                    {{-- <input type="text" class="form-control" value="{{ $data->payment_mode }}" required name="payment_mode" id="payment_mode" placeholder="" /> --}}
                                    <select name="payment_mode" class="form-control select2" id="payment_mode" onchange="checkpaymentmode(this.value)">
                                        <option value="">Select Payment Mode</option>
                                        @foreach( getModeOfPayment() as $key => $modeofpayment )
                                        <option value="{{ $key }}" {{ $data->payment_mode == $key ? 'selected' : '' }}>{{ $modeofpayment }}</option>
                                        @endforeach
                                    </select>

                                    @error('payment_mode')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12 Cheque" style="display: {{ $data->payment_mode == 1 ? 'block' : 'none' }}">
                                <div class="form-group">
                                    <label for="cheque_no">Cheque Number </label>
                                    <input type="text" class="form-control" value="{{ $data->cheque_no }}" name="cheque_no" id="cheque_no" placeholder="" />
                                    @error('cheque_no')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6 CreditCard" style="display: {{ $data->payment_mode == 6 || $data->payment_mode == 7 ? 'block' : 'none' }}">
                                <div class="form-group">
                                    <label for="creditcard_number"> Credit / Debit Card Number </label>
                                    <input type="text" class="form-control" value="{{ $data->creditcard_number }}" name="creditcard_number" id="creditcard_number" placeholder="" />
                                    @error('creditcard_number')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6 CreditCard" style="display: {{ $data->payment_mode == 6 || $data->payment_mode == 7 ? 'block' : 'none' }}">
                                <div class="form-group">
                                    <label for="creditcard_type"> Credit / Debit Card Type </label>
                                    <input type="text" class="form-control" value="{{ $data->creditcard_type }}" name="creditcard_type" id="creditcard_type" placeholder="" />
                                    @error('creditcard_type')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                             <div class="col-md-12 iBanking" style="display: {{ $data->payment_mode == 3 ? 'block' : 'none' }}">
                                <div class="form-group">
                                    <label for="creditcard_type"> Account Number / Account Detail</label>
                                    <input type="text" class="form-control" value="{{ $data->account_number }}" name="account_number" id="account_number" placeholder="" />
                                    @error('account_number')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="payment_remark"> Remark</label>
                                    <textarea class="form-control" name="payment_remark" rows="4" style="height: auto;" id="payment_remark">{{ $data->payment_remark }}</textarea>
                                    @error('payment_remark')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">

                            <div class="col-md-3">
                                <div class="form-group date-ico">
                                    <label for="payment_date"> Payment Date </label>
                                    @if( !empty($data->payment_date) )
                                        <input type="text" class="form-control singledate" value="{{ \Carbon\Carbon::parse($data->payment_date)->format('Y-m-d') }}" name="payment_date" id="payment_date" placeholder="" />
                                    @else
                                        <input type="text" class="form-control singledate" value="" name="payment_date" id="payment_date" placeholder="" />
                                    @endif
                                    @error('payment_date')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                      <!--   </div>

                        <div class="row"> -->

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="fee_amount"> Fees Paid Amount <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="{{ $data->fee_amount }}" required name="fee_amount" id="fee_amount" placeholder="" onkeypress="return isNumberKeyWithDecimal(event)" />
                                    @error('fee_amount')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            {{-- <div class="col-md-4">
                                <div class="form-group">
                                    <label for="payment_method"> Payment Method <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="{{ $data->payment_method }}" name="payment_method" id="payment_method" placeholder="" />
                                    @error('payment_method')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div> --}}

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="transaction_id"> Transaction ID <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="{{ $data->transaction_id }}" name="transaction_id" id="transaction_id" placeholder="" disabled="" />
                                    @error('transaction_id')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label for="xero_invoice_number">Invoice Number </label>
                                @if(!is_null($data->studentEnrolment->xero_invoice_number))
                                    <input type="text" class="form-control" value="{{$data->studentEnrolment->xero_invoice_number}}" required name="xero_invoice_number" id="xero_invoice_number" placeholder="" disabled/>
                                @else
                                    <input type="text" class="form-control" value="" required name="xero_invoice_number" id="xero_invoice_number" placeholder="" disabled/>
                                @endif
                            </div>

                            <label class="customcheck-payment" style="display: {{ old('payment_mode') == 6 || old('payment_mode') == 7 ? 'block' : 'none' }}">
                                Not Sync with Xero
                                <input  class="xero-sync" name="sync_xero" value="sync_xero_off" type="checkbox" checked >
                                <div class="checkmark-payment"></div>
                            </label>
                        </div>

                    </div><!--end card-body-->
                    <div class="card-footer m-0 clearfix">
                        <button type="submit" class="btn btn-primary mar-r-10">Update</button>
                        <a href="{{ route('admin.payment.list') }}" class="btn btn-danger">Cancel</a>
                    </div>
                </form>
            </div><!--end card-->
        </div> <!--end col-->
    </div><!--end row-->

</div><!-- container -->
@endsection
@push("scripts")
<script src="{{ asset('assets/plugins/daterangepicker/daterangepicker.js') }}"></script>

<script type="text/javascript">

    $(document).ready(function() {
        $(".select2").select2({ width: '100%' });

        $('.singledate').daterangepicker({
            locale: {
                format: 'YYYY-MM-DD'
                /*format: 'DD-MM-YYYY'*/
            },
            singleDatePicker: true,
            showDropdowns: true,
            /*minDate: new Date(),
            minYear: 2019,*/
        });
    });

    function checkpaymentmode(payment_mode)
    {
        if(payment_mode == 1)
        {
            $(".Cheque").show();
            $(".CreditCard").hide();
            $(".iBanking").hide();
            // $(".customcheck-payment").hide();
        }
        else if(payment_mode == 6 || payment_mode == 7)
        {
            $(".CreditCard").show();
            $(".Cheque").hide();    
            $(".iBanking").hide();
            // $(".customcheck-payment").show();
        }
        else if(payment_mode == 3)
        {
            $(".iBanking").show();
            $(".Cheque").hide();
            $(".CreditCard").hide();
            // $(".customcheck-payment").hide();
        }
        else if(payment_mode == 5){
            $(".customcheck-payment").show();
        }
        else
        {
            $(".Cheque").hide();
            $(".iBanking").hide();
            $(".CreditCard").hide();
            // $(".customcheck-payment").hide();
        }
    }

    checkpaymentmode("{{ $data->payment_mode }}");

</script>
@endpush
