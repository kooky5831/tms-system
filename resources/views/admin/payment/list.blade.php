@extends('admin.layouts.master')
@section('title', 'Payment')
@push('css')
<!-- DataTables -->
<link href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
<!-- Responsive datatable examples -->
<link href="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
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
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Payment Report</a></li>
                        <li class="breadcrumb-item active">List</li>
                    </ol>
                </div>
                <h4 class="page-title">Payment Report</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" class="tms-report" action="{{route('admin.reports.paymentReport.export.excel')}}">
                        @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group date-ico">
                                    <label for="startDate">Due Date</label>
                                    <input type="text" id="dueDatestartDate" autocomplete="new-password" name="dueDatestartDate" class="form-control" value="{{\Carbon\Carbon::now()->subYear()->format('Y-m-d')}}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group date-ico">
                                    <label for="endDate">Due Date</label>
                                    <input type="text" id="dueDateendDate" autocomplete="new-password" name="dueDateendDate" class="form-control" value="{{\Carbon\Carbon::now()->format('Y-m-d')}}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="sponsored_by_company">Sponsored By Company</label>
                                    <select id="sponsored_by_company" name="sponsored_by_company" class="form-control select2">
                                        <option value="">Select Sponsored</option>
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                        <option value="No (I'm signing up as an individual)">No (I'm signing up as an individual)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="paymentRemark">Payment Remark</label>
                                    <input type="text" id="payment_remark" name="payment_remark" class="form-control" value="">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="company_name">Company Name</label>
                                    <input type="text" id="company_name" name="company_name" class="form-control" value="">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="company_uen">Company UEN</label>
                                    <input type="text" id="company_uen" name="company_uen" class="form-control" value="">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="payment_status">Payment Status</label>
                                    <select name="payment_status" id="payment_status" class="form-control select2" data-placeholder="Select Status">
                                    <option value="">Select Status</option>
                                        <option value="1">Pending</option>
                                        <option value="2">Partial</option>
                                        <option value="3">Full</option>
                                        <option value="4">Refund</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="paymentRemark">Remaining amount</label>
                                    <input type="text" id="remaining_amount" name="remaining_amount" class="form-control" value="">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control select2">
                                        @foreach( enrolledStatus() as $key => $value )
                                            <option value="{{ $key }}" {{ $key == 0 ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            {{-- <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tpg_payment_status">TPG Payment Status</label>
                                    <select id="tpg_payment_status" name="tpg_payment_status" class="form-control select2">
                                        <option value="">Select TPG Payment Status</option>
                                        @foreach( getPaymentStatusForTPG() as $key => $value)
                                            <option value="{{$key}}">{{ $value }}</option>
                                        @endforeach    
                                    </select>
                                </div>
                            </div> --}}
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="course_type">Course Type</label>
                                    <select id="course_type" name="course_type" class="form-control select2">
                                        <option value="">Select Course Mode</option>
                                        @foreach( courseType() as $key => $value )
                                            <option value="{{$key}}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-between">
                            <div cclass="col-md-6">
                                <button class="btn btn-primary mt-4" id="search_date" role="button">Search</button>
                            </div>
                            <div class="col-md-6 export-excel text-right">
                                <button class="btn btn-primary btn-info mt-4" type="submit">Export Excel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- end page title end breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <a class="btn btn-primary px-4 btn-rounded float-right mt-0 mb-3" href="{{ route('admin.payment.add') }}"><i class="add-new"></i> Add New</a>
                    <h4 class="header-title mt-0">Payment List</h4>
                    <div class="table-responsive dash-social">
                        <table id="datatable" class="table">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>NRIC</th>
                                <th>Email</th>
                                <th>Course Name</th>
                                <th>Mode</th>
                                <th>Amount</th>
                                <th>Invoice Number</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr><!--end tr-->
                            </thead>

                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div><!--end card-body-->
            </div><!--end card-->
        </div> <!--end col-->
    </div><!--end row-->

</div><!-- container -->
@endsection

@push('scripts')
<script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/plugins/sweet-alert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/plugins/daterangepicker/daterangepicker.js') }}"></script>
<script type="text/javascript">
    $(function () {
        $(".select2").select2({ width: '100%' });
        $('#dueDatestartDate').daterangepicker({
            locale: {
                format: 'Y-M-DD'
            },
            singleDatePicker: true,
            showDropdowns: true,
            minDate:null,
        });
        $('#from').daterangepicker({
            locale: {
                format: 'Y-M-DD'
            },
            singleDatePicker: true,
            showDropdowns: true,
            minDate:null,
        });
        $('#dueDateendDate').daterangepicker({
            locale: {
                format: 'Y-M-DD',
                cancelLabel: 'Clear'
            },
            singleDatePicker: true,
            showDropdowns: true,
            minDate: new Date(),
        });
        $('#to').daterangepicker({
            locale: {
                format: 'Y-M-DD',
                cancelLabel: 'Clear'
            },
            singleDatePicker: true,
            showDropdowns: true,
            minDate: new Date(),
        });
        $(document).on('click', '.cancelpayment', function () {
            let _payment_id = $(this).attr('payment_id');
            swal.fire({
                title: 'Are you sure?',
                text: "You want to cancel the payment!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, cancel it!',
                cancelButtonText: 'No',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        url: '{{ route('admin.ajax.studentPayment.modal.cancel') }}',
                        type: "POST",
                        dataType: "JSON",
                        data: {
                            id: _payment_id
                        },
                        success: function(res) {
                            if( res.status == true ) {
                                swal.fire(
                                    'Cancelled!',
                                    'Your payment has been cancelled.',
                                    'success'
                                )
                                location.reload();
                            } else {
                                swal.fire(
                                    'Opps',
                                    'Some error occured, Please try again.',
                                    'error'
                                )
                            }
                        }
                    }); // end ajax
                }
            });
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
                url: "{{ route('admin.payment.listdatatable') }}",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: "POST",
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('Authorization');
                },
                data: function(d) {
                    d.search['value'] = $('#datatable_filter input[type="search"]').val();
                    d.search['regex'] = false;
                    d.coursemain = $('#coursemain').val();
                    d.courserun = $('#courserun').val();
                    d.status = $('#status').val();
                    d.student_name = $('#student_name').val();
                    d.enrollment_no = $('#enrollment_no').val();
                    
                    // read start date from the element
                    d.dueDatestartDate = $('#dueDatestartDate').val();
                    d.payment_status = $('#payment_status').val();
                    d.tpg_payment_status = $('#tpg_payment_status').val();
                    d.dueDateendDate = $('#dueDateendDate').val();
                    d.course_type = $('#course_type').val();
                    d.sponsored_by_company = $("#sponsored_by_company").val();
                    d.payment_remark =  $("#payment_remark").val();
                    d.company_name = $("#company_name").val();
                    d.company_uen = $("#company_uen").val();
                    d.remaining_amount = $("#remaining_amount").val();
                    d.status = $("#status").val();
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'name', name: 'students.name'},
                {data: 'nric', name: 'students.nric'},
                {data: 'email', name: 'student_enrolments.email'},
                {data: 'courseName', name: 'courseName'},
                {data: 'payment_mode', name: 'payment_mode'},
                {data: 'fee_amount', name: 'fee_amount'},
                {data: 'xero_invoice_number', name: 'xero_invoice_number'},               
                {data: 'payment_date', name: 'payment_date', width: "6%"},
                {data: 'status', name: 'status'},
                {data: 'action', name: 'action', orderable: false, searchable: false, width: "6%"},
            ],
            order: [[7, 'desc']],
        });

        $(document).on('click', '#search_date', function(e) {
            e.preventDefault();
            table.draw();
        });
    });
</script>
@endpush
