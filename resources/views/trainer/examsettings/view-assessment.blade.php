@extends('trainer.layouts.master')
@section('title', 'Dashboard')
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
                        <li class="breadcrumb-item"><a href="{{route('trainer.dashboard')}}"><i class="dripicons-home"></i></a></li>
                        <li class="breadcrumb-item active">Assessments</li>
                    </ol>
                </div>
                <h4 class="page-title">Assessments</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div><!--end row-->
    <!-- end page title end breadcrumb -->

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mt-0 mb-3">{{$mainName}}'s Assessments</h4>
                    <div class="table-responsive">
                        <table class="table" id="datatable">
                            <thead class="">
                                <tr>
                                    <th>Id</th>
                                    <th>Assessment Name</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Pax</th>
                                    <th>Actions</th>
                                </tr><!--end tr-->
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        <pre id="example-console-rows"></pre>
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
                url: "{{ route('trainer.ajax.get-all-assessments') }}",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: "POST",
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('Authorization');
                },
                data: function(d) {
                    d.exam_id = {{ $examId }};
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'title', name: 'title'},
                {data: 'exam_start_date', name: 'exam_start_date'},
                {data: 'exam_end_date', name: 'exam_end_date'},
                {data: 'pax', name: 'pax'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],            
        });
    })
</script>
@endpush