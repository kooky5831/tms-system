@extends('admin.layouts.master')
@section('title', 'All Completed Course Runs')
@push('css')
<!-- DataTables -->
<link href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<!-- Responsive datatable examples -->
<link href="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
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
                        <li class="breadcrumb-item"><a href="javascript:void(0);">All Completed Course Runs</a></li>
                        <li class="breadcrumb-item active">List</li>
                    </ol>
                </div>
                <h4 class="page-title">All Completed Course Runs</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @can('course-add')
                    <a class="btn btn-primary px-4 btn-rounded float-right mt-0 mb-3" href="javascript:void(0)" id="add_course_run_btn"><i class="add-new"></i> Add New</a>
                    @endcan
                    <h4 class="header-title mt-0">All Completed Course Runs</h4>
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
<script type="text/javascript">
    $(function () {
        // Datatable
        var table = $('#datatable').DataTable({
            "fnDrawCallback": function( oSettings ) {
                initTooltip();
            },
            "pageLength": 10,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.course.listallcompleteddatatable') }}",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: "POST",
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('Authorization');
                },
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
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        });
    });
</script>
@endpush
