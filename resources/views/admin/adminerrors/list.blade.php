@extends('admin.layouts.master')
@section('title', 'Errors List')
@push('css')
<!-- DataTables -->
<link href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<!-- Responsive datatable examples -->
<link href="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/daterangepicker/daterangepicker.css') }}" rel="stylesheet" type="text/css" />
<style>
    .select2, .select2-hidden-accessible, .select2-container .select2-selection--multiple {
        height: 50px !important;
    }
    .updateexcpstatus, .updateexcpstatus:hover { background-color: #7fd321; border-color: #7fd321; }
</style>
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
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Errors</a></li>
                        <li class="breadcrumb-item active">List</li>
                    </ol>
                </div>
                <h4 class="page-title">Admin Errors</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group date-ico">
                                    <label for="startDate">Start Date</label>
                                    <input type="text" id="startDate" autocomplete="new-password" name="startDate" class="form-control" value="{{\Carbon\Carbon::now()->subYear()->format('Y-m-d')}}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group date-ico">
                                    <label for="endDate">End Date</label>
                                    <input type="text" id="endDate" autocomplete="new-password" name="endDate" class="form-control" value="{{\Carbon\Carbon::now()->addYear()->format('Y-m-d')}}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select name="status[]" id="status" multiple class="form-control select2">
                                        <option value="1">Pending</option>
                                        <option value="2">Resolved</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-primary mt-4" id="search_data" role="button">Search</button>
                            </div>
                        </div>
                        
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body report-body">
                    <h4 class="header-title mt-0">Errors List</h4>
                    <div class="dropdown mb-3 show-col-wrapper">
                        <button class="btn btn-primary dropdown-toggle btn-show-col" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Column Visibility <i class="ml-2 mdi mdi-chevron-down"></i>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item toggle-vis" data-column="1">Date/Time</a>
                            <a class="dropdown-item toggle-vis" data-column="2">Name</a>
                            <a class="dropdown-item toggle-vis inactive" data-column="3">File Path</a>
                            <a class="dropdown-item toggle-vis" data-column="4">Status Code</a>
                            <a class="dropdown-item toggle-vis" data-column="5">Message</a>
                            <a class="dropdown-item toggle-vis inactive" data-column="6">Trace</a>
                            <a class="dropdown-item toggle-vis" data-column="7">Status</a>
                            <a class="dropdown-item toggle-vis inactive" data-column="8">Notes</a>
                        </div>
                    </div>
                    <div class="table-responsive dash-social">
                        <table id="datatable" class="table">
                            <thead>
                            <tr>
                                
                                <th>S/N</th>
                                <th>Date/Time</th>
                                <th>Name</th>
                                <th>File Path</th>
                                <th>Status Code</th>
                                <th>Message</th>
                                <th>Trace</th>
                                <th>Status</th>
                                <th>Notes</th>
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
<script src="{{ asset('assets/plugins/sweet-alert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/plugins/daterangepicker/daterangepicker.js') }}"></script>
<script type="text/javascript">
    $(function () {

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
            "fnDrawCallback": function( oSettings ) {
                initTooltip();
            },
            "pageLength": 10,
            processing: true,
            serverSide: true,
            aaSorting: [[ 1, "DESC" ]],
            ajax: {
                url: "{{ route('admin.errors.listdatatable') }}",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: "POST",
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('Authorization');
                },
                data: function(d) {
                    d.search['value'] = $('#datatable_filter input[type="search"]').val();
                    d.search['regex'] = false;
                    d.status = $('#status').val();
                    d.from = $('#startDate').val();
                    d.to = $('#endDate').val();
                }
                
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'datetime', name: 'datetime'},
                {data: 'name', name: 'name', searchable: true, orderable: false},
                {data: 'filepath', name: 'filepath', searchable: true, orderable: false, visible: false},
                {data: 'code', name: 'code', searchable: true},
                {data: 'message', name: 'message', searchable: true, orderable: false},
                {data: 'trace', name: 'trace', searchable: true, orderable: false, visible: false},
                {data: 'status', name: 'status'},
                {data: 'notes', name: 'notes', searchable: true, orderable: false, visible: false},
                {data: 'action', name: 'action', searchable: false, orderable: false},
            ]
        });

        $(document).on('click', '#search_data', function(e) {
            e.preventDefault();
            if( $('#startDate').val() != "" && $('#endDate').val() == "" ) {
                showToast("Please select end date");
                return false;
            }
            table.draw();
        });

        $('a.toggle-vis').on('click', function (e) {
            e.stopPropagation();
            e.preventDefault();
            $(this).toggleClass('inactive');
            var column = table.column($(this).attr('data-column'));
            column.visible(!column.visible());
        });

        // Get Exception Modal
        $(document).on('click', '.updateexcpstatus', function(e) {
            e.preventDefault();
            let _exp_id = $(this).attr('exp_id');
            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                url: '{{ route('admin.errors.adminerror.modal.getexception') }}',
                type: "POST",
                dataType: "JSON",
                data: {
                    id: _exp_id
                },
                success: function(res) {
                    $('#modal-content').empty().html(res.html);
                    $('.model-box').modal();
                }
            }); // end ajax
        });

        // Update Exception Status and Note
        $(document).on('submit', '#exception_status_form', function(e) {
            e.preventDefault();
            var btn = $('#submit_exception_status');
            BITBYTE.progress(btn);
            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                url: '{{ route('admin.errors.adminerror.modal.updateexception') }}',
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
