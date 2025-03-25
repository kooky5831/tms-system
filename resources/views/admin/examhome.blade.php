@extends('admin.layouts.master')
@section('title', 'Exam Course Runs')
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
                        <li class="breadcrumb-item"><a href="javascript:void(0);">{{config('app.name')}}</a></li>
                        <li class="breadcrumb-item active">Exam Course Runs</li>
                    </ol>
                </div>
                <h4 class="page-title">Exam Course Runs</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div><!--end row-->
    <!-- end page title end breadcrumb -->

    {{-- <div class="row dash-box">
        <div class="col-lg-3">
            <div class="card card-eco purple-back">
                <div class="card-body">
                    <div class="d-flex ">
                        <i class="dripicons-user-group card-eco-icon text-warning  align-self-center"></i>
                        <div>
                            <h4 class="title-text mt-0">Total Courses</h4>
                            <h3 class="font-weight-bold">{{$data['totalCourses']}}</h3>
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" aria-valuenow="90" aria-valuemin="0" aria-valuemax="100" style="width:90%">
                                  <span class="sr-only">90% Complete</span>
                                </div>
                            </div>
                            <p class="sub-text"><span>40%</span> Increase in 10 Days</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" class="tms-report" action="{{route('admin.reports.assessment.export.excel')}}">
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
                                <button class="btn btn-primary" id="search_exam_data" role="button">Search</button>
                            </div>
                            <div class="col-md-6">
                                <button class="btn btn-info btn-primary mb-4 float-left float-md-right float-lg-right" type="submit">Export Excel</button>
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
                    <h4 class="header-title mt-0 mb-3">Course Runs List</h4>
                    
                    <div class="table-responsive">
                        <table class="table" id="datatable">
                            <thead class="">
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
                    <div class="paginate float-right mt-3">
                    </div>
                </div><!--end card-body-->
                
            </div><!--end card-->
        </div><!--end col-->
    </div><!--end row-->

</div><!-- container -->

@endsection
@push('scripts')
<script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/plugins/daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('assets/js/dataTables.checkboxes.min.js') }}"></script>
<script>
    $(function(){
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
        
        //Datatable
        var table = $('#datatable').DataTable({
            "fnDrawCallback": function( oSettings ) {
                initTooltip();
            },
            "pageLength": 10,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: "{{ route('admin.assessments.examdashboard.ajax.search') }}",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: "POST",
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('Authorization');
                },
                data: function(d) {
                    d.search['value'] = $('#datatable_filter input[type="search"]').val();
                    d.search['regex'] = false;
                    // read start date from the element
                    d.from = $('#startDate').val();
                    // read end date from the element
                    d.to = $('#endDate').val();
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'tpgateway_id', name: 'tpgateway_id'},
                {data: 'course_name', name: 'course_name'},
                {data: 'course_start_date', name:'course_start_date'},
                {data: 'course_end_date', name: 'course_end_date', orderable: false},
                {data: 'pax', name: 'pax'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],            
        });

        $(document).on('click', '#search_exam_data', function(e) {
            e.preventDefault();
            table.draw();
        });
    })
</script>
@endpush