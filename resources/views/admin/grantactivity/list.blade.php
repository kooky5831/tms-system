@extends('admin.layouts.master')
@section('title', 'Activities')
@push('css')
<!-- DataTables -->
<link href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<!-- Responsive datatable examples -->
<link href="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/daterangepicker/daterangepicker.css') }}" rel="stylesheet" type="text/css" />
{{-- <link href="{{ asset('assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" /> --}}
<link href="{{ asset('assets/plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
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
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Grant Activities</a></li>
                    </ol>
                </div>
                <h4 class="page-title">Grant Activities</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->

    <!--- Filter Start -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" class="tms-report grant-log-form" action="{{route('admin.grant.grantlog.export.excel')}}" id="frm_activity_filter">
                        @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group date-ico">
                                    <label for="startDate">Start Date</label>
                                    <input type="text" id="startDate" name="from" class="form-control" value="{{\Carbon\Carbon::now()->subYear()->format('Y-m-d')}}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group date-ico">
                                    <label for="endDate">End Date</label>
                                    <input type="text" id="endDate"  name="to" class="form-control" value="{{\Carbon\Carbon::now()->addYear()->format('Y-m-d')}}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="grant_id">Grant ID</label>
                                    <input type="text" id="grant_id" name="grant_id" class="form-control" value="">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="grant_status">Grant Staus</label>
                                    <select name="grant_status[]" id="grant_status" multiple class="form-control select2">
                                        <option value="Grant Processing">Grant Processing</option>
                                        <option value="Completed">Completed</option>
                                        <option value="Cancelled">Cancelled</option>
                                    </select>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="enrolment_id">Enrolment ID</label>
                                    <input type="text" id="enrolment_id" name="enrolment_id" class="form-control" value="">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="student_name">Student Name</label>
                                    <input type="text" id="student_name" name="student_name" class="form-control" value="">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <button class="btn btn-primary mt-4 mr-2" id="activity_filter" role="button">Search</button>
                                <button class="btn btn-primary btn-info mt-4" id="clear_filter" role="button">Clear Filter</button>
                            </div>
                            <div class="col-md-6 export-excel text-right">
                                <button class="btn btn-primary btn-info mt-4" type="submit">Export Excel</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--- Filter End --->

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable" class="table w-100">
                            <thead>
                            <tr>
                                <th>Event</th>
                                <th>User</th>
                                <th>Time</th>
                                <th>Description</th>
                                <th>Remarks</th>
                                <th>Action</th>
                            </tr>
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
{{-- <script src="{{ asset('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script> --}}
<script src="{{ asset('assets/plugins/sweet-alert2/sweetalert2.min.js') }}"></script>
<script type="text/javascript">
    $(function () {
        
        $(".select2").select2({width: '100%'});

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

        $('#startDate').on('change', function(ev, picker) {
            // do something, like clearing an input
            $('#endDate').daterangepicker({
                locale: {
                    format: 'Y-M-DD',
                    cancelLabel: 'Clear'
                },
                singleDatePicker: true,
                showDropdowns: true,
                minDate: moment($('#startDate').val(), "YYYY-MM-DD"),
                minYear: 2019,
            });
        });
        
        // Datatable
        var table = $('#datatable').DataTable({
            // bFilter: false,
            "fnDrawCallback": function( oSettings ) {
                initTooltip();
            },
            "pageLength": 10,
            processing: true,
            serverSide: true,
            searching: false,
            ajax: {
                url: "{{ route('admin.grant.listdatatable') }}",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: "POST",
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('Authorization');
                },
                data: function(d) {
                     d.from = $('#startDate').val();
                     d.to = $('#endDate').val();
                     d.grant_id = $('#grant_id').val();
                     d.grant_status = $('#grant_status').val();
                     d.enrolment_id = $('#enrolment_id').val();
                     d.student_name = $('#student_name').val();
                }
            },
            "columnDefs": [
                { "width": "20%", "targets": 4 },
            ],
            columns: [
                {data: 'event', name: 'event', orderable: false},
                {data: 'user_id', name: 'user_id', orderable: false},
                {data: 'grant_logs.created_at', name: 'grant_logs.created_at', orderable: false},
                {data: 'description', name: 'description', orderable: false},
                {data: 'notes', name: 'notes', orderable: false, "render": function(data, type, row){
            	return data.split("\n").join("<br/>");}},
                {data: 'action', name: 'action', orderable: false},   
            ]
        });

        $(document).on('click', '#activity_filter', function(e) {
            e.preventDefault();
            table.draw();
        });

        $(document).on('click', '#clear_filter', function(e) {
            e.preventDefault();
            $("#frm_activity_filter").trigger("reset");
            $('#grant_status').val('').trigger('change');
        });

        // Get Action Modal
        $(document).on('click', '.grant-remark', function(e) {
            e.preventDefault();
            let _grantid = $(this).attr('grantid');
            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                url: '{{ route('admin.grant.grantlog.modal.getgrantaction') }}',
                type: "POST",
                dataType: "JSON",
                data: {
                    id: _grantid
                },
                success: function(res) {
                    $('#modal-content').empty().html(res.html);
                    $('.model-box').modal();
                }
            }); // end ajax
        });

        // Mark grant log as Resolved
        $(document).on('click', '.grant-resolved', function(e) {
            let _grantid = $(this).attr('grantid');
            e.preventDefault();
            swal.fire({
                title: 'Are you sure?',
                text: "You want to Makr this Grant Log as Resolved?",
                input: "text",
                inputLabel: "Type YES to confirm",
                inputPlaceholder: "Type YES to confirm",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Resolved it!',
                cancelButtonText: 'No',
                reverseButtons: true,
                inputValidator: (inputValue) => {
                    if (inputValue === null) return false;
                    if (inputValue === "") {
                        return "You need to Type YES to confirm!";
                    }
                    if (inputValue.toUpperCase() != "YES") {
                        return "You need to Type YES to confirm!";
                    }
                }
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        url: '{{ route('admin.grant.grantlog.resolvedgrantlog') }}',
                        type: "POST",
                        dataType: "JSON",
                        data: {
                            id: _grantid
                        },
                        success: function(res) {
                            showToast(res.message, res.success);
                            if( res.success ) {
                                setTimeout(function() {
                                    location.reload();
                                },1000);
                            }
                        }
                    }); // end ajax
                }
            });
        });

        
        // Update Grant Action Status and Note
        $(document).on('submit', '#grant_action_form', function(e) {
            e.preventDefault();
            var btn = $('#submit_grant_action');
            BITBYTE.progress(btn);
            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                url: '{{ route('admin.grant.grantlog.modal.updategrantlog') }}',
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

    });
</script>
@endpush
