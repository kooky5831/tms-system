@extends('admin.layouts.master')
@section('title', 'Activities')
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
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Activities</a></li>
                    </ol>
                </div>
                <h4 class="page-title">Activities</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->

    <!--- Filter Start -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" class="tms-report" action="" id="frm_activity_filter">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="coursemain">Type</label>
                                    <select name="auditable_type[]" id="auditable_type" multiple class="js-example-basic-multiple form-control select2" data-placeholder="Select Type">
                                        <option value="App\Models\StudentEnrolment">Student Enrolmet</option>
                                        <option value="App\Models\Course">Course Run</option>
                                        <option value="App\Models\CourseMain">Course</option>
                                        {{-- <option value="App\Models\CourseDocuments">Course Documents</option> --}}
                                        {{-- <option value="App\Models\CourseSoftBooking">Course Soft Booking</option> --}}
                                        {{-- <option value="App\Models\WaitingList">WaitingList</option> --}}
                                        <option value="App\Models\Student">Student</option>
                                        {{-- <option value="App\Models\Venue">Venue</option> --}}
                                        <option value="App\Models\Payment">Payment</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="action">Action</label>
                                    <select name="action_type" id="action_type" class="form-control select2" data-placeholder="Select Type">
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
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
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="studentid">Student Name</label>
                                    <select class="form-control select2" id="studentid" name="studentid"></select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="courseid">Course Name</label>
                                    <select class="form-control select2" id="courseid" name="courseid"></select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <button class="btn btn-primary mt-4 mr-2" id="activity_filter" role="button">Search</button>
                                <button class="btn btn-primary btn-info mt-4" id="clear_filter" role="button">Clear Filter</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--- Filter End --->

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable" class="table">
                            <thead>
                            <tr>
                                <th>Action</th>
                                <th>User</th>
                                <th>Time</th>
                                <th>Description</th>
                            </tr>
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
<script src="{{ asset('assets/plugins/daterangepicker/daterangepicker.js') }}"></script>
{{-- <script src="{{ asset('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script> --}}
<script type="text/javascript">
    $(function () {
        $.ajax({
            type: 'GET',
            url: "{{ route('admin.activities.get-actions') }}",
            success: function (data) {
                $("#action_type").html(data);
            },
            error: function (data) {
                console.log(data);
            }
        });
        
        $(".select2").select2({width: '100%'});

        $("#studentid").select2({
            allowClear: true,
            placeholder: 'Select Student',
            ajax: {
                url: "{{ route('admin.activities.studentsearch') }}",
                dataType: 'json',
                delay: 250,
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.name,
                                id: item.id
                            }
                        })
                    };
                },
                cache: true
            }
        });

        $("#courseid").select2({
            allowClear: true,
            placeholder: 'Select Course',
            ajax: {
                url: "{{ route('admin.activities.coursesearch') }}",
                dataType: 'json',
                delay: 250,
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.name,
                                id: item.id
                            }
                        })
                    };
                },
                cache: true
            }
        });

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
            // bFilter: false,
            "fnDrawCallback": function( oSettings ) {
                initTooltip();
            },
            "pageLength": 10,
            processing: true,
            serverSide: true,
            searching: false,
            ajax: {
                url: "{{ route('admin.activities.listdatatable') }}",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: "POST",
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('Authorization');
                },
                data: function(d) {
                    d.from = $('#startDate').val();
                    d.to = $('#endDate').val();
                    d.auditable_type = $('#auditable_type').val();
                    d.studentid = $('#studentid').val();
                    d.courseid = $('#courseid').val();
                    d.action_type = $('#action_type').val();
                }
            },
            columns: [
                {data: 'action', name: 'action', orderable: false},
                {data: 'user_id', name: 'user_id', orderable: false},
                {data: 'time', name: 'time', orderable: false},
                {data: 'description', name: 'description', orderable: false},
            ]
        });

        $(document).on('click', '#activity_filter', function(e) {
            e.preventDefault();
            /*if( $('#coursemain').val() == "" ) {
                showToast("Please select course");
                return false;
            }*/
            table.draw();
        });

        $(document).on('click', '#clear_filter', function(e) {
            e.preventDefault();
            $("#frm_activity_filter #studentid").empty();
            $("#frm_activity_filter #courseid").empty();
            $("#frm_activity_filter .select2").val('').trigger("change");
            $("#frm_activity_filter").trigger("reset");
        });

    });
</script>
@endpush
