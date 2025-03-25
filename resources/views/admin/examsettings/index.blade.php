@extends('admin.layouts.master')
@section('title', 'Exam Settings')
@push('css')
<!-- DataTables -->
<link href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<!-- Responsive datatable examples -->
<link href="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/x-editable/css/bootstrap-editable.css')}}" rel="stylesheet" type="text/css" >

@endpush
@section('content')

<div class="container-fluid">
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Exam Settings</a></li>
                        <li class="breadcrumb-item active">List</li>
                    </ol>
                </div>
                <h4 class="page-title">Exam Settings</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <!-- end page title end breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <a class="btn btn-primary px-4 btn-rounded float-right mt-0 mb-3" href="{{route('admin.assessments.exam-settings.add')}}"><i class="add-new"></i> Add New</a>
                    <h4 class="header-title mt-0">Exam List</h4>
                    <div class="table-responsive dash-social min-height-datatable-list">
                        <table id="datatable" class="table">
                            <thead>
                            <tr>
                                <th>Exam ID</th>
                                <th>Exam Course</th>
                                {{-- <th>Exam Duration</th>
                                <th>Exam Time</th> --}}
                                <th>Total Assessment</th>
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
<script src="{{ asset('assets/plugins/x-editable/js/bootstrap-editable.min.js')}}"></script>

<script type="text/javascript">
$(function () {
    // Datatable
    var pageNumber = 0;
    var table = $('#datatable').DataTable({
        "fnDrawCallback": function( oSettings ) {
            initTooltip();
            inlineEdit();
        },
        "pageLength": 10,
        processing: true,
        serverSide: true,
        "bAutoWidth": false,
        ajax: {
            url: "{{ route('admin.assessments.exam-settings.listdatatable') }}",
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            type: "POST",
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Authorization');
            },
        },

        columns: [
            {data: 'id', name: 'id'},
                {data: 'course_main_name', name: 'course_main_name'},
                // {data: 'exam_duration', name: 'exam_duration'},
                // {data: 'exam_time', name: 'exam_time'},
                {data: 'assessment_count', name: 'assessment_count'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
        createdRow: function(row, data, dataIndex) {
                $(row).data('record_id', data.record_id); // Store the 'id' value in the row data
            }
    });

    table.on('page.dt', function(){
            var info = table.page.info();
            pageNumber = info.page+1;
        })

        function inlineEdit(){
            if ($('.inline-editable').length) {
                $.fn.editableform.buttons = '<button type="submit" class="btn btn-success editable-submit btn-sm waves-effect waves-light"><i class="mdi mdi-check"></i></button>' + '<button type="button" class="btn btn-danger editable-cancel btn-sm waves-effect waves-light"><i class="mdi mdi-close"></i></button>';
                
                $('.inline_assess_name').editable({
                    showbuttons: 'bottom',
                    success: function(response, newValue) {
                        var recordId = $(this).closest('tr').data('record_id');
                        var field = $(this).data('field');
                        var value = newValue;
                        updateFunction(recordId, field, value, pageNumber);
                    }
                });
            }  
        }

        function updateFunction(record_id, field, value, pageNumber){
            var currentPageNumber = table.page();
            $.ajax({
                url: "{{ route('admin.assessments.exam-settings.assess_name') }}",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: "POST",
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('Authorization');
                },
                data:{"record_id":record_id, "field":field, "value":value},
                success:function(response){
                    table.page(currentPageNumber).draw('page');
                }
            })
        }

});
</script>
@endpush