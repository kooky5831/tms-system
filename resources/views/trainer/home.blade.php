@extends('trainer.layouts.master')
@section('title', 'Exam Settings')
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
                        <li class="breadcrumb-item active">Exam Settings</li>
                    </ol>
                </div>
                <h4 class="page-title">EXAM SETTINGS</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div><!--end row-->
    <!-- end page title end breadcrumb -->

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <a class="btn btn-primary px-4 btn-rounded float-right mt-0 mb-3" href="{{route('trainer.dashboard.add')}}"><i class="add-new"></i> Add New</a>
                    <h4 class="header-title mt-0 mb-3">All Exams List</h4>
                    <div class="table-responsive">
                        <table class="table" id="datatable">
                            <thead class="">
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
                        <pre id="example-console-rows"></pre>
                    </div>
                </div><!--end card-body-->
            </div><!--end card-->
        </div><!--end col-->
    </div><!--end row-->

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
            // serverSide: true,
            autoWidth: false,
            ajax: {
                url: "{{ route('trainer.ajax.search.trainerdata') }}",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: "POST",
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('Authorization');
                },
                data: function(d) {
                    d.from = $('#startDate').val();
                    // read end date from the element
                    d.to = $('#endDate').val();
                }
            },
            columns: [
                {data: 'id', name: 'id'},
                {data: 'course_main_name', name: 'course_main_name'},
                // {data: 'exam_duration', name: 'exam_duration'},
                // {data: 'exam_time', name: 'exam_time'},
                {data: 'assessment_count', name: 'assessment_count'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
        });
    })
</script>
@endpush