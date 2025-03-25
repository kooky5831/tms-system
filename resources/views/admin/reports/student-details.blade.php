@extends('admin.layouts.master')
@section('title', 'Reports - Student Details')
@push('css')
<!-- DataTables -->
<link href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<!-- Responsive datatable examples -->
<link href="{{ asset('assets/plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/daterangepicker/daterangepicker.css') }}" rel="stylesheet" type="text/css" />
{{-- <link href="{{ asset('assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" /> --}}

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
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Reports</a></li>
                        <li class="breadcrumb-item active">Student Details</li>
                    </ol>
                </div>
                <h4 class="page-title">Student Details</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" class="tms-report" action="{{route('admin.reports.studentDetails.export.excel')}}">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="coursemain">Course</label>
                                    <select name="coursemain[]" id="coursemain" multiple class="form-control select2">
                                        @foreach ($courseMainList as $coursemain)
                                            <option value="{{$coursemain->id}}">{{$coursemain->name}} ( {{$coursemain->reference_number}})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            {{-- <div class="col-md-3">
                                <div class="form-group">
                                    <label for="courserun">Course Run</label>
                                    <select name="courserun" id="courserun" class="form-control select2">
                                        <option value="">Select Course Run</option>
                                    </select>
                                </div>
                            </div> --}}
                            <div class="col-md-3">
                                <div class="form-group date-ico">
                                    <label for="startDate">Start Date</label>
                                    <input type="text" id="startDate" autocomplete="new-password" name="from" class="form-control" value="{{\Carbon\Carbon::now()->subYear()->format('Y-m-d')}}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group date-ico">
                                    <label for="endDate">End Date</label>
                                    <input type="text" id="endDate" autocomplete="new-password" name="to" class="form-control" value="{{\Carbon\Carbon::now()->addYear()->format('Y-m-d')}}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status">Enrollment Status</label>
                                    <select id="status" name="status[]" multiple class="form-control select2">
                                        @foreach( enrolledStatusWithRefreshers() as $key => $value )
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="student_name">Student Name</label>
                                    <input id="student_name" name="student_name" class="form-control" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="payment_status">Payment Status</label>
                                    <select name="payment_status" id="payment_status" class="form-control select2" data-placeholder="Select Status">
                                    <option value="">Select Status</option>
                                        <option value="1">Pending</option>
                                        <option value="2">Partial</option>
                                        <option value="3">Full</option>
                                        <option value="4">Refund</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="mincoursefee">Course Fee</label>
                                    <div class="two-col">
                                        <input type="number" id="mincoursefee" placeholder="Min" name="mincoursefee" class="form-control" value="">
                                        <input type="number" id="maxcoursefee" placeholder="Max" name="maxcoursefee" class="form-control" value="">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="minamountdue">Amount Due</label>
                                    <div class="two-col">
                                        <input type="number" id="minamountdue" placeholder="Min" name="minamountdue" class="form-control" value="">
                                        <input type="number" id="maxamountdue" placeholder="Max" name="maxamountdue" class="form-control" value="">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="course_mode_training">Learning Mode</label>
                                    <select id="course_mode_training" name="course_mode_training" class="form-control select2">
                                        <option value="">Select Mode</option>
                                        <option value="online">Online</option>
                                        <option value="offline">Offline</option>
                                    </select>
                                </div>
                            </div>
                            
                            

                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="nationality">Nationality</label>
                                    <!-- <input type="text" id="nationality" name="nationality" class="form-control" value=""> -->
                                    <select class="form-control select2" name="nationality" id="nationality">
                                        <option value="">Select Nationality</option>
                                        @foreach( getNationalityList() as $nationalitie )
                                            <option value="{{$nationalitie}}">{{$nationalitie}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="company_name">Company Name</label>
                                    <input type="text" id="company_name" name="company_name" class="form-control" value="">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="company_uen">Company UEN</label>
                                    <input type="text" id="company_uen" name="company_uen" class="form-control" value="">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="xero_invoice_number">Xero Invoice Number</label>
                                    <input type="text" id="xero_invoice_number" name="xero_invoice_number" class="form-control" value="">
                                </div>
                            </div>

                            
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="sponsored_by_company">Sponsored By Company</label>
                                    <select id="sponsored_by_company" name="sponsored_by_company" class="form-control select2">
                                        <option value="">Select Sponsored</option>
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                        <option value="No (I'm signing up as an individual)">No (I'm signing up as an individual)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row justify-content-between">    
                            <div class="col-md-6">
                                <button class="btn btn-primary mt-4" id="search_date" role="button">Search</button>
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

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body report-body">
                    <h4 class="header-title mt-0">Student Details - Report</h4>
                    <div class="dropdown mb-3 show-col-wrapper">
                        <button class="btn btn-primary dropdown-toggle btn-show-col" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Column Visibility <i class="ml-2 mdi mdi-chevron-down"></i>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item toggle-vis" data-column="0">Course Name</a>
                            <a class="dropdown-item toggle-vis" data-column="1">Student Name</a>
                            <a class="dropdown-item toggle-vis inactive" data-column="2">NRIC</a>
                            <a class="dropdown-item toggle-vis" data-column="3">Email</a>
                            <a class="dropdown-item toggle-vis" data-column="4">Phone No</a>
                            <a class="dropdown-item toggle-vis inactive" data-column="5">Sponsord By Company</a>
                            <a class="dropdown-item toggle-vis inactive" data-column="6">Xero Invoice Number</a>
                            <a class="dropdown-item toggle-vis inactive" data-column="7">Nationality</a>
                            <a class="dropdown-item toggle-vis inactive" data-column="8">Learning Mode</a>
                            <a class="dropdown-item toggle-vis" data-column="9">Company Name</a>
                            <a class="dropdown-item toggle-vis inactive" data-column="10">Company UEN</a>
                            <a class="dropdown-item toggle-vis" data-column="11">Payment Status</a>
                            <a class="dropdown-item toggle-vis" data-column="12">Payment Amount</a>
                            <a class="dropdown-item toggle-vis inactive" data-column="13">Attendance</a>
                            <a class="dropdown-item toggle-vis inactive" data-column="14">Assessmnet</a>
                            <a class="dropdown-item toggle-vis" data-column="15">Status</a>
                            <a class="dropdown-item toggle-vis" data-column="16">Remarks</a>
                        </div>
                    </div>
                    <div class="table-responsive dash-social min-height-datatable-list">
                        <table id="datatable" class="table responsive student-report display nowrap">
                            <thead>
                            <tr>
                                <th>Course Name</th>
                                <th>Student Name</th>
                                <th>NRIC</th>
                                <th>Email</th>
                                <th>Phone No</th>
                                <th>Sponsord By Company</th>
                                <th>Xero Invoice Number</th>
                                <th>Nationality</th>
                                <th>Learning Mode</th>
                                <th>Company Name</th>
                                <th>Company UEN</th>
                                <th>Payment Status</th>
                                <th>Payment Amount</th>
                                <th>Attendance</th>
                                <th>Assessmnet</th>
                                <th>Status</th>
                                <th>Remarks</th>
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
{{-- <script src="{{ asset('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script> --}}
<script src="{{ asset('assets/plugins/daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('assets/plugins/sweet-alert2/sweetalert2.min.js') }}"></script>

<script type="text/javascript">
    $(function () {
        $(".select2").select2({ placeholder: "Select Course" });
        var date = new Date();
        var today = new Date(date.getFullYear(), date.getMonth()-1,1);
       
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
       
        function startDateChanged(ev) {
            // $('#endDate').datepicker('destroy');
            // do something, like clearing an input
            let minDate = new Date($('#startDate').val());
            $('#endDate').val('');
            $('#endDate').datepicker('setStartDate', minDate);
        }

        @include('admin.partial.actions.cancelenrolement');

        @include('admin.partial.actions.holdinglist');

        @include('admin.partial.actions.viewenrolmentresponse');

        @include('admin.partial.actions.viewpayment');

        /* $(document).on('change', '#coursemain', function(e) {
            e.preventDefault();
            let _coursemain = $(this).val();
            if( _coursemain != "" ) {
                // get the course run list for this courses
                $.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    url: '{{ route('admin.ajax.reports.courserun.list') }}',
                    type: "POST",
                    dataType: "JSON",
                    data: {
                        id: _coursemain
                    },
                    success: function(res) {
                        if( res.status ) {
                            $('#courserun').empty();
                            if( res.list.length > 0 ) {
                                $('#courserun').append(new Option('Select Course Run', ''));
                                res.list.map((course) => {
                                    $('#courserun').append(new Option(`${course.tpgateway_id} (${course.course_start_date})`, course.id));
                                });
                            }
                        }
                    },
                    error: function(err) {
                        if( err.status == 422 ) {
                            // display error
                            showToast(err.responseJSON.message, 0);
                            return false;
                        }
                    }
                }); // end ajax
            }
        }); */

        // Datatable
        var table = $('#datatable').DataTable({
            "fnDrawCallback": function( oSettings ) {
                initTooltip();
            },
            "pageLength": 10,
            // scrollX: true,
            "bAutoWidth": false,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.reports.studentDetails.listdatatable') }}",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: "POST",
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('Authorization');
                },
                data: function(d) {
                    d.search['value'] = $('#datatable_filter input[type="search"]').val();
                    d.search['regex'] = false;
                    d.coursemain = $('#coursemain').val();
                    // d.courserun = $('#courserun').val();
                    d.status = $('#status').val();
                    d.student_name = $('#student_name').val();
                    // read start date from the element
                    d.from = $('#startDate').val();
                    // read end date from the element
                    d.to = $('#endDate').val();
                    d.payment_status = $('#payment_status').val();
                    d.mincoursefee = $('#mincoursefee').val();
                    d.maxcoursefee = $('#maxcoursefee').val();
                    d.minamountdue = $('#minamountdue').val();
                    d.maxamountdue = $('#maxamountdue').val();
                    d.course_mode_training = $('#course_mode_training').val();
                    d.nationality = $('#nationality').val();
                    d.company_name = $('#company_name').val();
                    d.company_uen = $('#company_uen').val();
                    d.xero_invoice_number = $('#xero_invoice_number').val();
                    d.sponsored_by_company = $('#sponsored_by_company').val();
                   
                }
            },
            columns: [
                {data: 'courseName', name: 'courseName'},
                {data: 'student_name', name: 'name', orderable: false, searchable: false},
                {data: 'nric', name: 'nric', visible: false},
                {data: 'email', name: 'email'},
                {data: 'mobile_no', name: 'mobile_no'},
                {data: 'sponsored_by_company', name: 'sponsored_by_company', visible: false},
                {data: 'xero_invoice_number', name: 'xero_invoice_number', visible: false},
                {data: 'nationality', name: 'nationality', visible: false},
                {data: 'learning_mode', name: 'learning_mode', visible: false},
                {data: 'company_name', name: 'company_name'},
                {data: 'company_uen', name: 'company_uen', visible: false},
                {data: 'payment_status', name: 'payment_status'},
                {data: 'amount', name: 'amount'},
                {data: 'attendedSessions', name: 'attendedSessions', visible: false},
                {data: 'assessment', name: 'assessment', visible: false},
                {data: 'status', name: 'status'},
                {data: 'remarks', name: 'remarks'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        });

        $(document).on('click', '#search_date', function(e) {
            e.preventDefault();
            /*if( $('#coursemain').val() == "" ) {
                showToast("Please select course");
                return false;
            }*/
            table.draw();
        });

        $('a.toggle-vis').on('click', function (e) {
            e.stopPropagation();
            e.preventDefault();
            $(this).toggleClass('inactive');
            var column = table.column($(this).attr('data-column'));
            column.visible(!column.visible());
        });

    });
</script>
@endpush
