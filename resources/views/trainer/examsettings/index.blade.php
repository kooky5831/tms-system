@extends('trainer.layouts.master')
@section('title', 'Assessment Settings')
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
                        <li class="breadcrumb-item"><a href="javascript:void(0);">All Assessments</a></li>
                        <li class="breadcrumb-item active">List</li>
                    </ol>
                </div>
                <h4 class="page-title">All Assessments</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <!-- end page title end breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <a class="btn btn-primary px-4 btn-rounded float-right mt-0 mb-3" href="{{route('trainer.dashboard.add')}}"><i class="add-new"></i> Add New</a>
                    <h4 class="header-title mt-0">Assessment List</h4>
                    <div class="table-responsive dash-social min-height-datatable-list">
                        <table id="datatable" class="table">
                            <thead>
                            <tr>
                                <th>Course</th>
                                <th>Assessment Method</th>
                                <th>Assessment Duration</th>
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
            url: "{{ route('trainer.exam-settings.listdatatable') }}",
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            type: "POST",
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Authorization');
            },
        },
        columns: [
            {data: 'course_id', name: 'course_id'},
            {data: 'assessment_name', name: 'assessment_name', 
                    render: function(data, type, row) {
                        data = (data == null) ? "" : data;
                        return '<a href="#" class="inline_assess_name inline-editable" data-field="inline-assess-name" data-type="textarea"  data-value="'+row['assessment_name']+'" data-pk="1" data-title="Enter Assessment Name">'+data+'</a>';
                    }, 
                    class: 'editable assess_name'},
            {data: 'exam_duration', name: 'exam_duration', orderable: false},
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
                url: "{{ route('trainer.exam-settings.assess_name') }}",
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