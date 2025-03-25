@extends('admin.layouts.master')
@section('title', 'Reports - Course Registration')
@push('css')
<!-- DataTables -->
<link href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<!-- Responsive datatable examples -->
<link href="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/daterangepicker/daterangepicker.css') }}" rel="stylesheet" type="text/css" />
{{-- <link href="{{ asset('assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" /> --}}

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
                        <li class="breadcrumb-item active">Course Registrations</li>
                    </ol>
                </div>
                <h4 class="page-title">Course Registrations</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" class="tms-report" action="{{route('admin.reports.courseregistration.export.excel')}}">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="coursemain">Course</label>
                                    <select name="coursemain[]" id="coursemain" multiple class="js-example-basic-multiple form-control select2" data-placeholder="Select Course">
                                        @foreach ($courseMainList as $coursemain)
                                            <option value="{{$coursemain->id}}">{{$coursemain->name}} ( {{$coursemain->reference_number}})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2 col-lg-3">
                                <div class="form-group date-ico">
                                    <label for="startDate">Start Date</label>
                                    <input type="text" id="startDate" autocomplete="new-password" name="from" class="form-control" value="{{\Carbon\Carbon::now()->subYear()->format('Y-m-d')}}">
                                </div>
                            </div>
                            <div class="col-md-2 col-lg-3">
                                <div class="form-group date-ico">
                                    <label for="endDate">End Date</label>
                                    <input type="text" id="endDate" autocomplete="new-password" name="to" class="form-control" value="{{\Carbon\Carbon::now()->addYear()->format('Y-m-d')}}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="modeoftraining">Type</label>
                                    <select name="modeoftraining[]" id="modeoftraining" multiple class="form-control select2" data-placeholder="Select Type">
                                        @foreach( getModeOfTraining() as $key => $modeoftraining )
                                        <option value="{{ $key }}">{{ $modeoftraining }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-5">
                                <div class="form-group">
                                    <label for="registeredusercount">Slot/Registered Students</label>
                                    <div class="two-col">
                                        <input type="number" id="minregisteredusercount" placeholder="Min" name="registeredusercount" class="form-control" value="">
                                        <input type="number" id="maxregisteredusercount" placeholder="Max" name="registeredusercount" class="form-control" value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                       <div class="row">
                            <div class="col-md-4 col-lg-3">
                                <div class="form-group">
                                    <label for="trainers">Cancelled</label>
                                    <div class="two-col">
                                        <input type="number" id="mincancelusercount" placeholder="Min" name="cancelusercount" class="form-control" value="">
                                        <input type="number" id="maxcancelusercount" placeholder="Max" name="cancelusercount" class="form-control" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <div class="form-group">
                                    <label for="trainers">Trainer</label>
                                    <select name="trainers[]" id="trainers" multiple class="form-control select2" data-placeholder="Select Trainer">
                                        @foreach ($trainers as $trainer)
                                            <option value="{{$trainer->id}}">{{$trainer->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <div class="form-group">
                                    <label for="is_published">Status</label>
                                    <select name="is_published[]" id="is_published" multiple class="form-control select2" data-placeholder="Select Status">
                                        <option value="1" selected>Published</option>
                                        <option value="0">Un Published</option>
                                        <option value="2">Cancelled</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <button class="btn btn-primary mt-4" id="search_date" role="button">Search</button>
                            </div>
                            <div class="col-md-6">
                                <button class="btn btn-info btn-primary mt-4 float-left float-md-right float-lg-right" type="submit">Export Excel</button>
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
                    <h4 class="header-title mt-0">Course Registrations - Report</h4>
                    <div class="dropdown mb-3 show-col-wrapper">
                        <button class="btn btn-primary dropdown-toggle btn-show-col" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Column Visibility <i class="ml-2 mdi mdi-chevron-down"></i>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item toggle-vis" data-column="0">Course Run</a>
                            <a class="dropdown-item toggle-vis" data-column="1">Course Title</a>
                            <a class="dropdown-item toggle-vis" data-column="2">Reference</a>
                            <a class="dropdown-item toggle-vis" data-column="3">Type</a>
                            <a class="dropdown-item toggle-vis" data-column="4">Start date</a>
                            <a class="dropdown-item toggle-vis" data-column="5">End date</a>
                            <a class="dropdown-item toggle-vis" data-column="6">Slot</a>
                            <a class="dropdown-item toggle-vis" data-column="7">Cancelled</a>
                            <a class="dropdown-item toggle-vis" data-column="8">Status</a>
                            <a class="dropdown-item toggle-vis" data-column="9">Trainer</a>
                        </div>
                    </div>
                    <div class="table-responsive dash-social min-height-datatable-list">
                        <table id="datatable" class="table datatable-resize w-100">
                            <thead>
                            <tr>
                                <th>Course Run</th>
                                <th>Course Title</th>
                                <th>Reference</th>
                                <th>Type</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Slot</th>
                                <th>Cancelled</th>
                                <th>Status</th>
                                <th>Trainer</th>
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

</div><!-- container -->
@endsection

@push('scripts')
<script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/plugins/daterangepicker/daterangepicker.js') }}"></script>
{{-- <script src="{{ asset('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script> --}}


<script type="text/javascript">
    $(function () {
        $(".select2").select2({width: '100%'});

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

        function startDateChanged(ev) {
            // $('#endDate').datepicker('destroy');
            // do something, like clearing an input
            let minDate = new Date($('#startDate').val());
            $('#endDate').val('');
            $('#endDate').datepicker('setStartDate', minDate);
        }

        /*$(document).on('change', '#coursemain', function(e) {
            e.preventDefault();
            let _coursemain = $(this).val();
            if( _coursemain != "" ) {
                // get the course run list for this courses
                $.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    url: '{{ route('admin.ajax.reports.courserun.list') }}',
                    type: "POST",
                    dataType: "JSON",
                    data: {
                        id: _coursemain
                    },
                    success: function(res) {
                        if( res.status ) {
                            $('#courserun').empty();
                            if( res.list.length > 0 ) {
                                $('#courserun').append(new Option('Select Course Run', ''));
                                res.list.map((course) => {
                                    $('#courserun').append(new Option(`${course.tpgateway_id} (${course.course_start_date})`, course.id));
                                });
                            }
                        }
                    },
                    error: function(err) {
                        if( err.status == 422 ) {
                            // display error
                            showToast(err.responseJSON.message, 0);
                            return false;
                        }
                    }
                }); // end ajax
            }
        });*/

        // Datatable
        var table = $('#datatable').DataTable({
            // scrollX: true,
            // "fnDrawCallback": function( oSettings ) {
            //     initTooltip();
            // },
            "pageLength": 100,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.reports.courseregistration.listdatatable') }}",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: "POST",
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('Authorization');
                },
                data: function(d) {
                    d.search['value'] = $('#datatable_filter input[type="search"]').val();
                    d.search['regex'] = false;
                    d.coursemain = $('#coursemain').val();
                    d.modeoftraining = $('#modeoftraining').val();
                    d.trainers = $('#trainers').val();
                    d.is_published = $('#is_published').val();
                    d.mincancelusercount = $('#mincancelusercount').val();
                    d.maxcancelusercount = $('#maxcancelusercount').val();
                    d.minregisteredusercount = $('#minregisteredusercount').val();
                    d.maxregisteredusercount = $('#maxregisteredusercount').val();
                    // d.courserun = $('#courserun').val();
                    // read start date from the element
                    d.from = $('#startDate').val();
                    // read end date from the element
                    d.to = $('#endDate').val();
                }
            },
            columns: [
                {data: 'tpgateway_id', name: 'tpgateway_id'},
                {data: 'name', name: 'course_mains.name'},
                {data: 'reference_number', name: 'course_mains.reference_number'},
                {data: 'modeoftraining', name: 'modeoftraining', orderable: false},
                {data: 'course_start_date', name: 'course_start_date'},
                {data: 'course_end_date', name: 'course_end_date'},
                {data: 'slot', name: 'slot', orderable: false, searchable: false},
                {data: 'cancelusercount', name: 'cancelusercount'},
                {data: 'is_published', name: 'is_published'},
                {data: 'trainername', name: 'users.name'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        });

        $(document).on('click', '#search_date', function(e) {
            e.preventDefault();
            /*if( $('#coursemain').val() == "" ) {
                showToast("Please select course");
                return false;
            }*/
            table.draw();
        });

        $('.dropdown-menu a.toggle-vis').on('click', function (e) {
            e.stopPropagation();
            e.preventDefault();
            $(this).toggleClass('inactive');
            var column = table.column($(this).attr('data-column'));
            column.visible(!column.visible());
        });

    });
</script>
@endpush
