@extends('admin.layouts.master')
@section('title', 'Student Enrolment List')
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
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Students</a></li>
                        <li class="breadcrumb-item active">List</li>
                    </ol>
                </div>
                <h4 class="page-title">Students List</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    {{-- <button class="btn btn-primary px-4 btn-rounded float-right mt-0 mb-3" onclick="document.location.href='{{ route('admin.studentenrolment.add') }}';"><i class="add-new"></i>  Add New</button> --}}
                    <h4 class="header-title mt-0">
                        Students List
                    </h4>
                    <div class="table-responsive">
                        <table id="datatable" class="table">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>NRIC</th>
                                <th>Email</th>
                                <th>Mobile Number</th>
                                <th>Nationality</th>
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
<script type="text/javascript">
    $(function () {
        $(".select2").select2({ width: '100%' });

        $(document).on('click', '.viewcourserun', function(e) {
            e.preventDefault();
            let _student_id = $(this).attr('student_id');
            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                url: '{{ route('admin.ajax.studentCourseRun.modal.list') }}',
                type: "POST",
                dataType: "JSON",
                data: {
                    id: _student_id
                },
                success: function(res) {
                    $('#modal-content').empty().html(res.html);
                    $('.model-box').modal();
                }
            }); // end ajax
        });

        $(document).on('click', '.editstudent', function(e) {
            e.preventDefault();
            let _student_id = $(this).attr('student_id');
            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                url: '{{ route('admin.ajax.studentEdit.modal') }}',
                type: "POST",
                dataType: "JSON",
                data: {
                    id: _student_id
                },
                success: function(res) {
                    $('#modal-content').empty().html(res.html);
                    $('.model-box').modal();
                }
            }); // end ajax
        });

        $(document).on('submit', '#student_edit_form', function(e) {
            e.preventDefault();
            var btn = $('#submit_student_edit');
            BITBYTE.progress(btn);
            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                url: '{{ route('admin.ajax.studentEdit.modal.store') }}',
                type: "POST",
                dataType: "JSON",
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData:false,
                success: function(res) {
                    BITBYTE.unprogress(btn);
                    showToast(res.message, res.success);
                    if( res.success ) {
                        setTimeout(function() {
                            location.reload();
                        },1000);
                    }
                },
                error: function(err) {
                    BITBYTE.unprogress(btn);
                    if( err.status == 422 ) {
                        // display error
                        showToast(err.responseJSON.message, 0);
                        return false;
                    }
                }
            }); // end ajax
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
                url: "{{ route('admin.students.listdatatable') }}",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: "POST",
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('Authorization');
                },
                data: function(d) {
                    d.search['value'] = $('input[type="search"]').val();
                    d.search['regex'] = false;
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'name', name: 'name'},
                {data: 'nric', name: 'nric'},
                {data: 'email', name: 'email'},
                {data: 'mobile_no', name: 'mobile_no'},
                {data: 'nationality', name: 'nationality'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        });
    });
</script>
@endpush
