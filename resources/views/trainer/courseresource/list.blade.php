@extends('trainer.layouts.master')
@section('title', 'Course Resource List')
@push('css')
<!-- DataTables -->
<link href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
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
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Course Resource List</a></li>
                        <li class="breadcrumb-item active">List</li>
                    </ol>
                </div>
                <h4 class="page-title">Course Resources</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->


    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <a class="btn btn-primary px-4 btn-rounded float-right mt-0 mb-3" href="{{ route('trainer.course-resources.add') }}"><i class="add-new"></i> Add New</a>
                    <h4 class="header-title mt-0">Course Resource List</h4>
                    <div class="table-responsive dash-social">
                        <table id="datatable" class="table">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Main course</th>
                                <th>Resource count</th>
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
<script src="{{ asset('assets/plugins/daterangepicker/daterangepicker.js') }}"></script>
<script type="text/javascript">
    $(function () {
        $(".select2").select2({ width: '100%' });
        // Datatable
        var table = $('#datatable').DataTable({
            "fnDrawCallback": function( oSettings ) {
                initTooltip();
            },
            "pageLength": 10,
            processing: true,
            serverSide: true,
            aaSorting: [[ 0, "DESC" ]],
            ajax: {
                url: "{{ route('trainer.course-resources.listdatatable') }}",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: "POST",
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('Authorization');
                },
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'course_main_name', name: 'course_main_name'},
                {data: 'resource_count', name: 'resource_count'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        });
    });
</script>
@endpush
