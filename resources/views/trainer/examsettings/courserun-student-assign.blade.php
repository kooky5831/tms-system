@extends('trainer.layouts.master')
@section('title', 'Course Runs')
@push('css')
<!-- DataTables -->
<link href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<!-- Responsive datatable examples -->
<link href="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/daterangepicker/daterangepicker.css') }}" rel="stylesheet" type="text/css" />
@endpush
@section('content')

    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">All Exam Course Run</a></li>
                        <li class="breadcrumb-item active">List</li>
                    </ol>
                </div>
                <h4 class="page-title">All Exam Course Run</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <!-- end page title end breadcrumb -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form class="tms-report">
                        @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group date-ico">
                                    <label for="startDate">Start Date</label>
                                    <input type="text" id="startDate" name="startDate" class="form-control" value="{{\Carbon\Carbon::now()->subDays(1)->format('Y-m-d')}}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group date-ico">
                                    <label for="endDate">End Date</label>
                                    <input type="text" id="endDate" name="endDate" class="form-control" value="{{\Carbon\Carbon::now()->format('Y-m-d')}}">
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-between">    
                            <div class="col-md-6">
                                <button class="btn btn-primary" id="trainer_search_runs_data" role="button">Search</button>
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
                <div class="card-body">
                    <h4 class="header-title mt-0">Course Run List</h4>
                    <div class="table-responsive dash-social min-height-datatable-list">
                        <table id="datatable" class="table">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Course Run ID</th>
                                <th>Course Main name</th>
                                <th>Start Date </th>
                                <th>End Date</th>
                                <th>Number of Pax</th>
                                <th>Actions</th>
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

@endsection
@push('scripts')
<script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/plugins/daterangepicker/daterangepicker.js') }}"></script>
<script type="text/javascript">
$(function () {

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

    // Datatable
    var table = $('#datatable').DataTable({
        "fnDrawCallback": function( oSettings ) {
            initTooltip();
        },
        "pageLength": 10,
        processing: true,
        serverSide: true,
        "bAutoWidth": false,
        ajax: {
            url: "{{ route('trainer.exam-settings.listdatatable') }}",
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            type: "POST",
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Authorization');
            },
            data: function(d) {
                d.search['value'] = $('#datatable_filter input[type="search"]').val();
                d.search['regex'] = false;
                d.from = $('#startDate').val();
                d.to = $('#endDate').val();
            }
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'tpgateway_id', name: 'tpgateway_id', searchable: true},
            {data: 'course_name', name: 'course_name', orderable: true, searchable: true},
            {data: 'course_start_date', name:'course_start_date', searchable: false},
            {data: 'course_end_date', name: 'course_end_date', orderable: false, searchable: false},
            {data: 'pax', name: 'pax', searchable: false},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ]
    });

    $(document).on('click', '#trainer_search_runs_data', function(e) {
            e.preventDefault();
            table.draw();
    });
});
</script>
@endpush