@extends('trainer.layouts.master')
@section('title', 'View Trainees')
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
                        <li class="breadcrumb-item"><a href="javascript:void(0);">View Trainees</a></li>
                        <li class="breadcrumb-item active">List</li>
                    </ol>
                </div>
                <h4 class="page-title">View Trainees</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <!-- end page title end breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <a href="{{ route('trainer.exam-settings.list') }}" class="btn btn-primary btn-info mb-4 float-right">Back</a>
                    <h4 class="header-title mt-0">Trainees List</h4>
                    <div class="table-responsive dash-social min-height-datatable-list">
                        <table id="datatable" class="table">
                            <thead>
                            <tr>
                                <th>NRIC</th>
                                <th>Name</th>
                                <th>Assessment Method</th>
                                <th>Assessment Status</th>
                                <th>Assessment Outcome</th>
                                <th>Assessment Submitted Time</th>
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
        "pageLength": 100,
        processing: true,
        serverSide: true,
        "bAutoWidth": false,
        order: [[3, 'desc']],
        ajax: {
            url: "{{ route('trainer.exam-settings.list_view_trainees', $courseRunId) }}",
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            type: "POST",
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Authorization');
            },
        },
        columns: [
            {data: 'nric', name: 'nric'},
            {data: 'name', name: 'name'},
            {data: 'assessment_name', name: 'assessment_name'},
            {data: 'is_started', name: 'is_started'},
            {data: 'is_passed', name: 'is_passed'},
            {data: 'finished_time', name: 'finished_time'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });
});
</script>
@endpush