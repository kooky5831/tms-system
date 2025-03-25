@extends('admin.layouts.master')
@section('title', 'Reports - Refresher Details')
@push('css')
<!-- DataTables -->
<link href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<!-- Responsive datatable examples -->
<link href="{{ asset('assets/plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
{{-- <link href="{{ asset('assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" /> --}}
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
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Reports</a></li>
                        <li class="breadcrumb-item active">Refresher Details</li>
                    </ol>
                </div>
                <h4 class="page-title">Refresher Details</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" class="tms-report" action="{{route('admin.reports.refresher.export.excel')}}">
                        @csrf
                        <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="coursemain">Course</label>
                                        <select name="coursemain" id="coursemain" class="form-control select2">
                                            <option value="">Select Course</option>
                                            @foreach ($courseMainList as $coursemain)
                                                <option value="{{$coursemain->id}}">{{$coursemain->name}} ( {{$coursemain->reference_number}})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group date-ico">
                                        <label for="startDate">Start Date</label>
                                        <input type="text" id="startDate" autocomplete="new-password" name="from" class="form-control" value="{{\Carbon\Carbon::now()->subYear()->format('Y-m-d')}}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group date-ico">
                                        <label for="endDate">End Date</label>
                                        <input type="text" id="endDate" autocomplete="new-password" name="to" class="form-control" value="{{\Carbon\Carbon::now()->addYear()->format('Y-m-d')}}">
                                    </div>
                                </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select id="status" name="status" class="form-control select2">
                                        <option value="1">Accepted</option>
                                        @foreach( StatusWithRefreshers() as $key => $value )
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="course_run_id">Course Run ID</label>
                                    <input id="course_run_id" name="course_run_id" class="form-control" />
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="trainer">Trainer</label>
                                    <input id="trainer" name="trainer" class="form-control" />
                                </div>
                            </div>
                        </div>

                        <div class="row justify-content-between">    
                            <div class="col-md-6">
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

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body report-body">
                    <h4 class="header-title mt-0">Refresher Details - Report</h4>
                    <div class="table-responsive dash-social min-height-datatable-list">
                        <table id="datatable" class="table responsive">
                            <thead>
                            <tr>
                                <th>Course Name</th>
                                <th>Student Name</th>
                                <th>Trainer</th>
                                <th>NRIC</th>
                                <th>Email</th>
                                <th>Notes</th>
                                <th>Status</th>
                                {{-- <th>Action</th> --}}
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
{{-- <script src="{{ asset('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script> --}}
<script src="{{ asset('assets/plugins/daterangepicker/daterangepicker.js') }}"></script>

<script type="text/javascript">
    $(function () {
        // Datatable
        $(".select2").select2();
        var date = new Date();
        var today = new Date(date.getFullYear(), date.getMonth()-1,1);
        // $( '#startDate' ).datepicker( 'setDate', today );
       
        $('#startDate').daterangepicker({
            locale: {
                format: 'Y-M-DD'
            },
            singleDatePicker: true,
            showDropdowns: true,
            minDate:null,
        });
        // .change(startDateChanged);
        // .on('changeDate', startDateChanged);

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

        // function startDateChanged(ev) {
        //     // $('#endDate').datepicker('destroy');
        //     // do something, like clearing an input
        //     let minDate = new Date($('#startDate').val());
        //     $('#endDate').val('');
        //     $('#endDate').daterangepicker('setStartDate', minDate);
        // }

        var table = $('#datatable').DataTable({
            "fnDrawCallback": function( oSettings ) {
                initTooltip();
            },
            "pageLength": 10,
            "bAutoWidth": false,
            "aoColumnDefs": [
            { "sWidth": "340px", "aTargets": [ 0, 5 ] }
            ],
            // scrollX: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.reports.refresherDetails.listdatatable') }}",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: "POST",
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('Authorization');
                },
                data: function(d) {
                    d.search['value'] = $('#datatable_filter input[type="search"]').val();
                    d.search['regex'] = false;
                    d.coursemain = $('#coursemain').val();
                    // d.courserun = $('#courserun').val();
                    d.status = $('#status').val();
                    d.course_run_id = $('#course_run_id').val();
                    d.trainer = $('#trainer').val();
                    // read start date from the element
                    d.from = $('#startDate').val();
                    // read end date from the element
                    d.to = $('#endDate').val();
                   
                },
            },
            columns: [
                {data: 'courseName', name: 'courseName'},
                {data: 'name', name: 'name'},
                {data: 'maintrainer', name: 'maintrainer', orderable: false, searchable: false},
                {data: 'nric', name: 'nric'},
                {data: 'email', name: 'email'},
                {data: 'notes', name: 'notes'},
                {data: 'status', name: 'status', orderable: false, searchable: false},
                // {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        });

        $(document).on('click', '#search_date', function(e) {
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

    });
</script>
@endpush