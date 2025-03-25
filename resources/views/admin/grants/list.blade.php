@extends('admin.layouts.master')
@section('title', 'Grants List')
@push('css')
<!-- DataTables -->
<link href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<!-- Responsive datatable examples -->
<link href="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/daterangepicker/daterangepicker.css') }}" rel="stylesheet" type="text/css" />
{{-- <link href="{{ asset('assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" /> --}}
<style>
     .select2, .select2-hidden-accessible, .select2-container .select2-selection--multiple {
        height: 50px !important;
    }
    .fetchgrantstatus.btn { font-size:14px; }
    .fetchgrantstatus.btn-primary { padding:7px; min-width:122px; border: 1px solid #6673fd; }
    .fetchgrantstatus.btn-primary:hover { border: 1px solid #6673fd; }
</style>
@endpush
@section('content')
<div class="container-fluid">
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Grants List</a></li>
                        <li class="breadcrumb-item active">List</li>
                    </ol>
                </div>
                <h4 class="page-title">Grant Lists</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" class="tms-report" action="{{route('admin.grants.grantdetails.export.excel')}}">
                        @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group date-ico">
                                    <label for="startDate">Course Start Date</label>
                                    <input type="text" id="startDate" name="startDate" class="form-control" value="{{\Carbon\Carbon::now()->subYear()->format('Y-m-d')}}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group date-ico">
                                    <label for="endDate">Course End Date</label>
                                    <input type="text" id="endDate" name="endDate" class="form-control" value="{{\Carbon\Carbon::now()->addYear()->format('Y-m-d')}}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status">Enrollment Status</label>
                                    <select id="status" name="status" class="form-control select2">
                                        <option value="">Select Status</option>
                                        @foreach( enrolledStatus() as $key => $value )
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="grant_status">Grant Status</label>
                                    <select name="grant_status[]" id="grant_status" multiple class="form-control select2">
                                        <option value="Grant Processing">Grant Processing</option>
                                        <option value="Completed">Completed</option>
                                        <option value="Cancelled">Cancelled</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <button class="btn btn-primary mt-4" id="search_data" role="button">Search</button>
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

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body report-body">
                    <h4 class="header-title mt-0">Grants List</h4>
                    <div class="dropdown mb-3 show-col-wrapper">
                        <button class="btn btn-primary dropdown-toggle btn-show-col" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Column Visibility <i class="ml-2 mdi mdi-chevron-down"></i>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item toggle-vis" data-column="0">Enrolment Ref. No.</a>
                            <a class="dropdown-item toggle-vis inactive" data-column="1">Student Name</a>
                            <a class="dropdown-item toggle-vis inactive" data-column="2">NRIC</a>
                            <a class="dropdown-item toggle-vis inactive" data-column="3">Email</a>
                            <a class="dropdown-item toggle-vis inactive" data-column="4">Enrolment Status</a>
                            <a class="dropdown-item toggle-vis inactive" data-column="5">Course Name</a>
                            <a class="dropdown-item toggle-vis inactive" data-column="6">Course Start/End Date</a>
                            <a class="dropdown-item toggle-vis" data-column="7">Grant Ref No.</a>
                            <a class="dropdown-item toggle-vis" data-column="8">Grant Status</a>
                            <a class="dropdown-item toggle-vis" data-column="9">Funding Scheme</a>
                            <a class="dropdown-item toggle-vis inactive" data-column="10">Funding Component</a>
                            <a class="dropdown-item toggle-vis" data-column="11">Amount Estimated</a>
                            <a class="dropdown-item toggle-vis" data-column="12">Amount Paid</a>
                            <a class="dropdown-item toggle-vis" data-column="13">Amount Recovery</a>
                            <a class="dropdown-item toggle-vis" data-column="14">Updated/ Disbursed Date</a>
                        </div>
                    </div>
                    <div class="table-responsive dash-social">
                        <table id="datatable" class="table">
                            <thead>
                            <tr>
                                <th>Enrolment Ref. No.</th>
                                <th>Student Name</th>
                                <th>Student NRIC</th>
                                <th>Student Email</th>
                                <th>Enrolment Status</th>
                                <th>Course Name</th>
                                <th>Course Start/End Date</th>
                                <th>Grant Ref No.</th>
                                <th>Grant Status</th>
                                <th>Funding Scheme</th>
                                <th>Funding Component</th>
                                <th>Amount Estimated</th>
                                <th>Amount Paid</th>
                                <th>Amount Recovery</th>
                                <th>Updated/ Disbursed Date</th>
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
<!-- Ajax loader for fetch grant data -->
<div class="ajax-loader"><div class="loader-center"><div class="tms_loader"></div></div></div>
<!-- Ajax loader for fetch grant data -->
</div><!-- container -->
@endsection

@push('scripts')
<script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/plugins/sweet-alert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/plugins/daterangepicker/daterangepicker.js') }}"></script>
{{-- <script src="{{ asset('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script> --}}

<script type="text/javascript">
    $(function () {

        $(".select2").select2({ width: '100%' });

        $('#startDate').daterangepicker({
            locale: {
                format: 'Y-M-DD'
            },
            singleDatePicker: true,
            showDropdowns: true,
        });

        $('#endDate').daterangepicker({
            locale: {
                format: 'Y-M-DD',
                cancelLabel: 'Clear'
            },
            singleDatePicker: true,
            showDropdowns: true,
            minDate: new Date(),
        });

        $('#startDate').on('change', function(ev, picker) {
            // do something, like clearing an input
            $('#endDate').daterangepicker({
                locale: {
                    format: 'Y-M-DD',
                    cancelLabel: 'Clear'
                },
                singleDatePicker: true,
                showDropdowns: true,
                minDate: moment($('#startDate').val(), "YYYY-MM-DD"),
                minYear: 2019,
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
            autoWidth: false,
            aaSorting: [[ 0, "DESC" ]],
            ajax: {
                url: "{{ route('admin.grants.listdatatable') }}",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: "POST",
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('Authorization');
                },
                data: function(d) {
                    d.search['value'] = $('#datatable_filter input[type="search"]').val();
                    d.search['regex'] = false;
                    d.grant_status = $('#grant_status').val();
                    d.from = $('#startDate').val();
                    d.to = $('#endDate').val();
                    d.status = $("#status").val();           
                }
            },
            columns: [
                
                {data: 'enrolment_ref_no', name: 'enrolment_ref_no', orderable: false},
                {data: 'student_name', name: 'student_name', visible: false, orderable: false},
                {data: 'nric', name: 'nric', visible: false, orderable: false},
                {data: 'email', name: 'email', visible: false, orderable: false},
                {data: 'status', name: 'status', visible: false, orderable: false},
                {data: 'course_name', name: 'course_name', visible: false, orderable: false},
                {data: 'dates', name: 'dates', visible: false, orderable: false},
                {data: 'grant_refno', name: 'grant_refno', orderable: false},
                {data: 'grant_status', name: 'grant_status'},
                {data: 'funding_scheme', name: 'funding_scheme', orderable: false},
                {data: 'funding_component', name: 'funding_component', visible: false, orderable: false},
                {data: 'amount_estimated', name: 'amount_estimated', orderable: false},
                {data: 'amount_paid', name: 'amount_paid', orderable: false},
                {data: 'amount_recovery', name: 'amount_recovery', orderable: false},
                {data: 'disbursement_date', name: 'disbursement_date', orderable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        });

        $(document).on('click', '#search_data', function(e) {
            e.preventDefault();
            table.draw();
        });

        $('a.toggle-vis').on('click', function (e) {
            e.stopPropagation();
            e.preventDefault();
            $(this).toggleClass('inactive');
            var column = table.column($(this).attr('data-column'));
            column.visible(!column.visible());
        });

        // Ajax to fetch grant data by grant ref no.
        $(document).on('click', '.fetchgrantstatus', function (e) {
            e.preventDefault();
            let _grant_id = $(this).attr('grant_id');
            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                url: '{{ route('admin.ajax.grant.status') }}',
                type: "POST",
                dataType: "JSON",
                data: {
                    id: _grant_id
                },
                beforeSend: function() {
                    $(".ajax-loader").show();
                },
                complete: function(){
                    $(".ajax-loader").hide();
                },
                success: function(res) {
                    $(".ajax-loader").hide();
                    if( res.status ) {
                        showToast(res.msg, 1);
                        setTimeout(function() {
                            location.reload();
                        },2000);
                    }
                    else{
                        showToast(res.msg, 0);
                        setTimeout(function() {
                            location.reload();
                        },2000);
                    }
                }
            }); 
        }); // end ajax

    });
</script>
@endpush
