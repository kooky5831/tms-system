@extends('admin.layouts.master')
@section('title', 'Course Runs')
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
                        <li class="breadcrumb-item"><a href="javascript:void(0);">All Student Exams List</a></li>
                        <li class="breadcrumb-item active">List</li>
                    </ol>
                </div>
                <h4 class="page-title">All Student Exams List</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mt-0">Student Exams List</h4>
                    <div class="table-responsive dash-social min-height-datatable-list">
                        <table id="datatable" class="table">
                            <thead>
                            <tr>
                                <th>Student Enroll id</th>
                                <th>Student</th>
                                <th>Exam Finished</th>
                                <th>Assessment Result</th>
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
$(function() { 

    var table = $('#datatable').DataTable({
        "fnDrawCallback": function( oSettings ) {
            initTooltip();
        },
        "pageLength": 10,
        processing: true,
        serverSide: true, 
        "bAutoWidth": false,
        ajax: {
            url: "{{ route('admin.assessments.exam-settings.review_studetn_exam_list', $allStudentExamList) }}",
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            type: "POST",
            beforeSend: function(xhr){
                xhr.setRequestHeader('Authorization');
            }
        },
        columns: [
            {data:'id', name:'id'},
            {data:'student_id', name:'student_id'},
            {data:'is_finished', name:'is_finished'},
            {data:'is_reviewed', name:'is_reviewed'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });
    
});

</script>
@endpush