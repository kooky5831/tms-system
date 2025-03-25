@extends('admin.layouts.master')
@section('title', 'All Course Runs')
@push('css')
<!-- DataTables -->
<link href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<!-- Responsive datatable examples -->
<link href="{{ asset('assets/plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
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
                        <li class="breadcrumb-item"><a href="javascript:void(0);">All Course Runs</a></li>
                        <li class="breadcrumb-item active">List</li>
                    </ol>
                </div>
                <h4 class="page-title">All Course Runs</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="coursemain">Course</label>
                                    <select name="coursemain[]" id="coursemain" multiple class="form-control select2">
                                        @foreach ($courseList as $course)
                                            <option value="{{$course->id}}">{{$course->name}} ( {{$course->reference_number}})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group date-ico">
                                    <label for="startDate">Start Date</label>
                                    <input type="text" id="startDate" autocomplete="new-password" name="startDate" class="form-control" value="{{\Carbon\Carbon::now()->subYear()->format('Y-m-d')}}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group date-ico">
                                    <label for="endDate">End Date</label>
                                    <input type="text" id="endDate" autocomplete="new-password" name="endDate" class="form-control" value="{{\Carbon\Carbon::now()->addYear()->format('Y-m-d')}}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="courserunid">Course Run Id</label>
                                    <input id="courserunid" name="courserunid" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="modeoftraining">Course Mode</label>
                                    <select name="modeoftraining[]" id="modeoftraining" multiple class="form-control select2">
                                        @foreach( getModeOfTraining() as $key => $modeoftraining )
                                        <option value="{{ $key }}">{{ $modeoftraining }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="trainers">Trainer</label>
                                    <select name="trainers[]" id="trainers" multiple class="form-control select2">
                                        @foreach ($trainers as $trainer)
                                            <option value="{{$trainer->id}}">{{$trainer->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="is_published">Status</label>
                                    <select name="is_published[]" id="is_published" multiple class="form-control select2" data-placeholder="Select Status">
                                        <option value="1">Published</option>
                                        <option value="0">Un Published</option>
                                        <option value="2">Cancelled</option>
                                    </select>
                                </div>
                            </div>
                            
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="course_type">Course Type</label>
                                    <select name="course_type[]" id="course_type" multiple class="form-control select2" data-placeholder="Select Course Type">
                                        <option value="1">WSQ</option>
                                        <option value="2">non-WSQ</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-1">
                                <button class="btn btn-primary mt-4" id="search_data" role="button">Search</button>
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
                    @can('course-add')
                    <a class="btn btn-primary px-4 btn-rounded float-right mt-0 mb-3" href="javascript:void(0)" id="add_course_run_btn"><i class="add-new"></i> Add New</a>
                    @endcan
                    <h4 class="header-title mt-0">All Course Runs</h4>
                    <div class="table-responsive dash-social min-height-datatable-list">
                        <table id="datatable" class="table">
                            <thead>
                            <tr>
                                <th>Course Run</th>
                                <th>Course Title</th>
                                <th>Type</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Slot</th>
                                <th>Trainer</th>
                                <th>Status</th>
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
<!-- Ajax loader for cancel course run -->
<div class="ajax-loader"><div class="loader-center"><div class="tms_loader"></div></div></div>
<!-- Ajax loader for cancel course run -->
@endsection

@push('scripts')
<script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/plugins/daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('assets/plugins/sweet-alert2/sweetalert2.min.js') }}"></script>
<script type="text/javascript">
    $(function () {

    /* Delete Course Run Start */
    @include('admin.partial.actions.cancelcourserun');    
    /* Delete Course Run End */

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
            ajax: {
                url: "{{ route('admin.course.listalldatatable') }}",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: "POST",
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('Authorization');
                },
                data: function(d) {
                    d.search['value'] = $('#datatable_filter input[type="search"]').val();
                    d.search['regex'] = false;
                    d.coursemain = $('#coursemain').val();
                    d.trainers = $('#trainers').val();
                    d.modeoftraining = $('#modeoftraining').val();
                    // read start date from the element
                    d.from = $('#startDate').val();
                    // read end date from the element
                    d.to = $('#endDate').val();
                    d.courserunid = $('#courserunid').val();
                    d.is_published = $('#is_published').val();
                    d.course_type = $('#course_type').val();
                }
            },
            columns: [
                {data: 'tpgateway_id', name: 'tpgateway_id'},
                {data: 'name', name: 'course_mains.name'},
                // {data: 'reference_number', name: 'course_mains.reference_number'},
                {data: 'modeoftraining', name: 'modeoftraining', orderable: false},
                {data: 'course_start_date', name: 'course_start_date'},
                {data: 'course_end_date', name: 'course_end_date'},
                {data: 'slot', name: 'slot', orderable: false, searchable: false},
                {data: 'trainername', name: 'users.name'},
                {data: 'is_published', name: 'is_published'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        });

        $(document).on('click', '#search_data', function(e) {
            e.preventDefault();
            if( $('#startDate').val() != "" && $('#endDate').val() == "" ) {
                showToast("Please select end date");
                return false;
            }
            table.draw();
        });

    });
</script>
@endpush
