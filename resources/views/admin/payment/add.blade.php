@extends('admin.layouts.master')
@section('title', 'Add Payment')
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
                        <li class="breadcrumb-item active">Add</li>
                    </ol>
                </div>
                <h4 class="page-title">Payments</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form action="{{ route('admin.payment.add') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="card-body">
                        <div class="row space-between marlr0">
                            <h4 class="header-title mt-0">Add Payment</h4>
                                {{-- <div class="form-group">
                                    <a href="javascript:void(0);" onclick="viewStudentEnrolment();" id="studentenrollmentlink" class="btn btn-primary" >View Enrolment</a>
                                </div> --}}
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="student_enrolments_id">Student Enrolment <span class="text-danger">*</span></label>
                                    @if( is_null($enrollmentData) )
                                    <select name="student_enrolments_id" required class="form-control select2" id="student_enrolments_id">
                                    </select>
                                    @else
                                    <input type="hidden" name="student_enrolments_id" value="{{$enrollmentData->id}}">
                                    <p>{{$enrollmentData->courseRun->tpgateway_id." ".$enrollmentData->student->name.", ".$enrollmentData->student->nric.", ".$enrollmentData->email}}</p>
                                    @endif
                                    @error('student_enrolments_id')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="payment_mode">Payment Mode <span class="text-danger">*</span></label>
                                    {{-- <input type="text" class="form-control" value="manual" required name="payment_mode" id="payment_mode" placeholder=""/> --}}

                                    <select name="payment_mode" class="form-control select2" id="payment_mode" onchange="checkpaymentmode(this.value)">
                                        <option value="">Select Payment Mode</option>
                                        @foreach( getModeOfPayment() as $key => $modeofpayment )
                                        <option value="{{ $key }}" {{ old('payment_mode') == $key ? 'selected' : '' }}>{{ $modeofpayment }}</option>
                                        @endforeach
                                    </select>
                                    @error('payment_mode')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group date-ico">
                                    <label for="payment_date"> Payment Date </label>
                                    <input type="text" class="form-control singledate" required value="{{ old('payment_date') }}" name="payment_date" id="payment_date" placeholder="" />
                                    @error('payment_date')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">

                            <div class="col-md-12 Cheque" style="display: {{ old('payment_mode') == 1 ? 'block' : 'none' }}">
                                <div class="form-group">
                                    <label for="cheque_no">Cheque Number </label>
                                    <input type="text" class="form-control" value="{{ old('cheque_no') }}" name="cheque_no" id="cheque_no" placeholder="" />
                                    @error('cheque_no')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6 CreditCard" style="display: {{ old('payment_mode') == 6 || old('payment_mode') == 7 ? 'block' : 'none' }}">
                                <div class="form-group">
                                    <label for="creditcard_number">Credit Card / Debit Card Number </label>
                                    <input type="text" class="form-control" value="{{ old('creditcard_number') }}" name="creditcard_number" id="creditcard_number" placeholder="" />
                                    @error('creditcard_number')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6 CreditCard" style="display: {{ old('payment_mode') == 6 || old('payment_mode') == 7 ? 'block' : 'none' }}">
                                <div class="form-group">
                                    <label for="creditcard_type">Credit Card / Debit Card Type </label>
                                    <input type="text" class="form-control" value="{{ old('creditcard_type') }}" name="creditcard_type" id="creditcard_type" placeholder="" />
                                    @error('creditcard_type')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12 iBanking" style="display: {{ old('payment_mode') == 3 ? 'block' : 'none' }}">
                                <div class="form-group">
                                    <label for="creditcard_type">Account Number / Account Detail</label>
                                    <input type="text" class="form-control" value="{{ old('account_number') }}" name="account_number" id="account_number" placeholder="" />
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
                                    <textarea class="form-control" name="payment_remark" rows="4" style="height: auto;" id="payment_remark">{{ old('payment_remark') }}</textarea>
                                    @error('payment_remark')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>


                        <div class="row">

                            <div class="col-md-4">
                                <div class="form-group">
                                    {{-- <label for="fee_amount">Fees Paid Amount <span class="text-danger">*</span></label> --}}
                                    {{-- <label for="fee_amount">Nett Paid Amount <span class="text-danger">*</span></label> --}}
                                    <label for="fee_amount">Nett fees Payable <span class="text-danger">*</span></label>
                                    @if( !is_null($enrollmentData) )
                                        <input type="text" class="form-control" value="{{ old('fee_amount') ?? $enrollmentData->amount }}" required name="fee_amount" id="fee_amount" placeholder="" onkeypress="return isNumberKeyWithDecimal(event)" onkeyup="calculateamountpaid();" readonly/>
                                         {{-- temporary readonly  --}}
                                    @else
                                        <input type="text" class="form-control" value="{{ old('fee_amount') }}" required name="fee_amount" id="fee_amount" placeholder="" onkeypress="return isNumberKeyWithDecimal(event)" onkeyup="calculateamountpaid();" readonly/>
                                    @endif
                                    @error('fee_amount')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    {{-- <label for="fee_amount">Xero Fees Paid Amount <span class="text-danger">*</span></label> --}}
                                    <label for="fee_amount">Xero Invoice Total <span class="text-danger">*</span></label>
                                    @if(!is_null($enrollmentData))
                                        <input type="text" class="form-control" value="{{$enrollmentData->xero_amount}}" name="xero_invoice_amount" id="xero_invoice_amount" placeholder="" readonly/>
                                    @else
                                        <input type="text" class="form-control" value="" name="xero_invoice_amount" id="xero_invoice_amount" placeholder="" readonly/>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="xero_invoice_number">Invoice Number </label>
                                    @if(!is_null($enrollmentData))
                                        <input type="text" class="form-control" value="{{$enrollmentData->xero_invoice_number}}" name="xero_invoice_number" id="xero_invoice_number" placeholder="" readonly/>
                                    @else
                                        <input type="text" class="form-control" value="" name="xero_invoice_number" id="xero_invoice_number" placeholder="" readonly/>
                                    @endif
                                </div>
                            </div>
                            {{-- <div class="col-md-6">
                                <div class="form-group">
                                    <label for="bankaccount_id">Bank Accounts <span class="text-danger">*</span></label>
                                    <select name="bankaccount_id" id="bankaccount_id" class="form-control" required>
                                        <option value="">Select Bank account</option>
                                        @foreach ($xero['bankaccounts'] as $account)
                                            <option value="{{$account['account_id']}}" {{ old('bankaccount_id') == $account['account_id'] ? 'selected' : '' }}>{{$account['name']}} - {{$account['bank_account_type']}}</option>
                                        @endforeach
                                    </select>
                                    @error('bankaccount_id')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div> --}}


                            <input type="hidden" class="form-control" value="{{ old('paid_fees') }}" name="paid_fees" id="paid_fees" placeholder="" />
                            <input type="hidden" class="form-control" value="{{ old('amount_paid') }}" name="amount_paid" id="amount_paid" placeholder="" />
                            <input type="hidden" class="form-control" value="{{ old('amount_xero_paid') }}" name="amount_xero_paid" id="amount_xero_paid" placeholder="" />

                            {{-- <div class="col-md-4">
                                <div class="form-group">
                                    <label for="fee_status">Fee Status: <span class="text-danger">*</span></label>
                                    <select name="fee_status" class="form-control">
                                        <option value=""> Select Fee Status </option>
                                        @foreach( getModeOfFeeStatus() as $key => $modeoffeestatus )
                                        <option value="{{ $key }}">{{ $modeoffeestatus }}</option>
                                        @endforeach
                                    </select>
                                    @error('fee_status')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div> --}}

                            {{-- <div class="col-md-4">
                                <div class="form-group">
                                    <label for="payment_status">Payment Status: <span class="text-danger">*</span></label>
                                    <select name="payment_status" class="form-control">
                                        <option value=""> Select Payment Status </option>
                                        @foreach( getModeOfPaymentStatus() as $key => $modeofpaymentstatus )
                                        <option value="{{ $key }}">{{ $modeofpaymentstatus }}</option>
                                        @endforeach
                                    </select>
                                    @error('payment_status')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div> --}}

                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                {{-- <label for="xero_invoice_number">Xero Amount </label> --}}
                                <label for="xero_invoice_number">Xero Amount Due</label>
                                @if(!is_null($enrollmentData))
                                    <input type="text" class="form-control" value="{{$enrollmentData->xero_due_amount}}" name="xero_due_amount" id="xero_due_amount" placeholder="" readonly/>
                                @else
                                    <input type="text" class="form-control" value="" name="xero_due_amount" id="xero_due_amount" placeholder="" readonly/>
                                @endif
                            </div>
                            <div class="col-md-4">
                                {{-- <label for="xero_invoice_number">Xero Paid Amount </label> --}}
                                <label for="xero_invoice_number">Xero Amount Paid </label>
                                @if(!is_null($enrollmentData))
                                    <input type="text" class="form-control" value="{{$enrollmentData->xero_paid_amount}}" name="xero_paid_amount" id="xero_paid_amount" placeholder="" readonly/>
                                @else
                                    <input type="text" class="form-control" value="" name="xero_paid_amount" id="xero_paid_amount" placeholder="" readonly/>
                                @endif
                            </div>
                            <div class="col-md-4">
                                {{-- <label for="xero_invoice_number"> Xero Fees Paid Amount </label> --}}
                                <label for="xero_invoice_number"> Payment Amount </label>
                                @if(!is_null($enrollmentData))
                                    <input type="text" class="form-control" value="{{$enrollmentData->amount - $enrollmentData->payments->where('status', '!=', 1)->sum('fee_amount') ?? '-'}}" name="xero_fees_amount" id="xero_fees_amount" placeholder="" onkeyup="calculateXeroAmountPaid();"/>
                                @else
                                    <input type="number" class="form-control" value="" step="0.01" name="xero_fees_amount" id="xero_fees_amount" placeholder="" onkeyup="calculateXeroAmountPaid();"/>
                                    <span class="text-danger" id="error_msg" style="display: none"><i class="fa fa-info-circle"></i> Amount is not higher than xero amount</span>
                                @endif
                            </div>
                        </div>
                        {{-- <div class="row">
                            <div class="col-md-12">
                                <div id="feesbreakdown">
                                    @if( !is_null($enrollmentData) )
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Total Fees</th>
                                                    <th>Paid Fees</th>
                                                    <th>Remaining Fees</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>{{$enrollmentData->amount}}</td>
                                                    <td>{{$enrollmentData->payments->where('status', '!=', 1)->sum('fee_amount') ?? '-'}}</td>
                                                    <td>{{$enrollmentData->amount - $enrollmentData->payments->where('status', '!=', 1)->sum('fee_amount') ?? '-'}}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    @endif
                                </div>
                            </div>
                        </div> --}}

                        <div class="row">
                            <div class="col-md-12">
                                <div id="xerofees_breakdown">
                                    @if( !is_null($enrollmentData) )
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Total Fees</th>
                                                    <th>Paid Fees</th>
                                                    <th>Remaining Fees</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>{{$enrollmentData->amount}}</td>
                                                    <td>{{$enrollmentData->payments->where('status', '!=', 1)->sum('fee_amount') ?? '-'}}</td>
                                                    <td>{{$enrollmentData->amount - $enrollmentData->payments->where('status', '!=', 1)->sum('fee_amount') ?? '-'}}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            {{-- <label class="customcheck-payment xero-sync-payment" style="display: {{ old('payment_mode') == 6 || old('payment_mode') == 7 ? 'block' : 'none' }}"> --}}
                                {{-- <span>Sync with Xero</span> --}}
                                {{-- <input type="checkbox" class="xero-sync" name="sync_xero" value="sync_xero_off"> --}}
                                {{-- <input type="checkbox" checked="checked"> --}}
                                {{-- <span class="checkmark-payment"></span> --}}
                            {{-- </label> --}}

                            <label class="customcheck-payment" style="display: {{ old('payment_mode') == 6 || old('payment_mode') == 7 ? 'block' : 'none' }}">
                                Not Sync with Xero
                                <input  class="xero-sync" name="sync_xero" id="sync_xero" type="checkbox">
                                <div class="checkmark-payment"></div>
                            </label>
                        </div>
                    </div>

                  <!--   </div> -->
                    <div class="card-footer m-0">
                        <button type="submit" class="btn btn-primary mar-r-10">Submit</button>
                        <a href="{{ route('admin.payment.list') }}" class="btn btn-danger">Cancel</a>
                    </div>
                </form>
            </div><!--end card-->
        </div> <!--end col-->
    </div><!--end row-->

</div><!-- container -->
@endsection
@push("scripts")
<script src="{{ asset('assets/plugins/select2/select2.full.min.js') }}"></script>
<script src="{{ asset('assets/plugins/daterangepicker/daterangepicker.js') }}"></script>
<script type="text/javascript">
    $(".select2").select2({ width: '100%' });

    @if( is_null($enrollmentData) )
        var student_enrolments_id = 0;
    @else
        var student_enrolments_id = {{$enrollmentData->id}};
    @endif

    function initDateAndTimePicker() {
        $('.singledate').daterangepicker({
            locale: {
                format: 'YYYY-MM-DD'
            },
            singleDatePicker: true,
            showDropdowns: true,
            // minDate: new Date(),
            // minYear: 2018,
        });
    }

    function formatResult(opt) {
        if (!opt.id) {
            return opt.text;
        }
        var $opt = $( '<span record="'+opt+'">'+ opt.text + '</span>');
        return $opt;
    };

    // opt.amount  === net fees payable
    // opt.amount - opt.paid_amt || '-' === payment amount
    function formatSelection(opt) {
        if( opt.id ) {
            // add data to table
            let selectedappendtr = `
            <table class='table'>
                <thead>
                    <tr>
                        <th>Total Fees</th>
                        <th>Paid Fees</th>
                        <th>Remaining Fees</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>${opt.amount}</td>
                        <td>${opt.paid_amt || '-'}</td>
                        <td>${opt.amount - opt.paid_amt || '-'}</td>
                    </tr>
                </tbody>
            </table>`;
            student_enrolments_id = opt.id;
            $('#feesbreakdown').html(selectedappendtr);
            $('#student_enrolments_id').val('').trigger('change');
            $('#student_enrolments_id').val(student_enrolments_id);
            var finalamount = opt.amount - opt.paid_amt;

            //xero-amount will be shows on nett-coursefees)
            var finalRemainigXeroAmount = opt.xero_nett_course_fees - opt.xero_paid_amount;	
            $("#fee_amount").val(opt.xero_nett_course_fees);
            $('#xero_fees_amount').val((finalamount).toFixed(2));
            // $("#fee_amount").val(opt.amount);
            // $('#xero_fees_amount').val((finalRemainigXeroAmount).toFixed(2));
            
            //commented code
            // $("#fee_amount").val(finalamount);
            $("#paid_fees").val(opt.paid_amt);
            $("#xero_invoice_number").val(opt.xero_invoice_number);
            $('#xero_due_amount').val(opt.xero_due_amount);
            $('#xero_paid_amount').val(opt.xero_paid_amount);
            // $('#xero_fees_amount').val(opt.xero_due_amount);

            $("#xero_invoice_amount").val(opt.xero_amount);            
            let xeroselectedAppendtr = `<table class='table'>
                <thead>
                    <tr>
                        <th>Nett Fees Payable</th>
                        <th>Paid Fees</th>
                        <th>Remaining Fees</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>${opt.xero_nett_course_fees}</td>
                        <td>${opt.paid_amt || '-'}</td>
                        <td>${(finalamount).toFixed(2)}</td>
                    </tr>
                </tbody>
            </table>`; 
            $('#xerofees_breakdown').html(xeroselectedAppendtr);
            
            var remaining = opt.amount - opt.paid_amt;
            var amountpaid = opt.paid_amt + remaining ;

            $('#amount_paid').val(amountpaid);
        }
        return opt.text;
    }

    initDateAndTimePicker();

    $(function () {
        $("#student_enrolments_id").select2({
            placeholder: 'Search for Student',
            multiple: false,
            minimumInputLength: 3,
            templateResult: formatResult,
            templateSelection: formatSelection,
            ajax: {
                url: "{{ route('admin.ajax.search.studentenrolment') }}",
                type: "get",
                dataType: "JSON",
                data: function (params) {
                    return { q: params.term, /*search term*/ };
                },
                processResults: function (response) {
                    return { results: response };
                },
                delay: 250,
                cache: true
            },
        });

    });

    function viewStudentEnrolment()
    {
        var protocol = $(location).attr('protocol');
        var hostname = $(location).attr('hostname');

        protocol += "//";

        var studentURL = protocol+hostname+"/admin/studentenrolment/view/"+student_enrolments_id;
        if( student_enrolments_id > 0 ) {
            window.open(studentURL);
        }
    }

    function checkpaymentmode(payment_mode)
    {
        $("#sync_xero").prop("checked", false);
        if(payment_mode == 1)
        {
            $(".Cheque").show();
            $(".CreditCard").hide();
            $(".iBanking").hide();
            $(".customcheck-payment").hide();
        }
        else if(payment_mode == 6 || payment_mode == 7)
        {
            $(".customcheck-payment").show();
            $(".CreditCard").show();
            $(".Cheque").hide();
            $(".iBanking").hide();
            $("#sync_xero").prop("checked", true);
        }
        else if(payment_mode == 3)
        {
            $(".iBanking").show();
            $(".Cheque").hide();
            $(".CreditCard").hide();
            $(".customcheck-payment").hide();
        }
        else if(payment_mode == 5){
            $(".customcheck-payment").show();
            $("#sync_xero").prop("checked", true);
        }
        else
        {
            $(".Cheque").hide();
            $(".iBanking").hide();
            $(".CreditCard").hide();
            $(".customcheck-payment").hide();
        }
    }

    function calculateamountpaid()
    {
        var fee_amount = $("#fee_amount").val();
        var paid_fees = $("#paid_fees").val();

        var amount_paid = parseInt(paid_fees) + parseInt(fee_amount);

        $('#amount_paid').val(amount_paid);
    }

    function calculateXeroAmountPaid(){
        var xero_fee_amount = parseFloat($('#fee_amount').val());
        var xero_paid_amount = parseFloat($('#xero_paid_amount').val());

        if(xero_paid_amount == 0) {
            var amount_xero_paid = 0;
            $('#amount_xero_paid').val(amount_xero_paid);
        } else {
            var amount_xero_paid = xero_fee_amount - xero_paid_amount;
            $('#amount_xero_paid').val(amount_xero_paid);
        }

        var cururruntValue = $('#xero_fees_amount').val();

        if(cururruntValue > xero_fee_amount) {
            $('#error_msg').show();
            $('#xero_fees_amount').val(xero_fee_amount);
        } else {
            $('#error_msg').hide();
        }
    }

</script>
@endpush