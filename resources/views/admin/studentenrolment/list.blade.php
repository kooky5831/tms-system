@extends('admin.layouts.master')
@section('title', 'Student Enrolment List')
@push('css')
<!-- DataTables -->
<link href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<!-- Responsive datatable examples -->
<link href="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/daterangepicker/daterangepicker.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/dataTables.checkboxes.css') }}" rel="stylesheet" type="text/css" />

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
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Student Enrolment</a></li>
                        <li class="breadcrumb-item active">List</li>
                    </ol>
                </div>
                <h4 class="page-title">Student Enrolment</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="coursemain">Course</label>
                                    <select name="coursemain[]" id="coursemain" multiple class="form-control select2">
                                        {{-- <option value="">Select Course</option> --}}
                                        @foreach ($courseMainList as $coursemain)
                                            <option value="{{$coursemain->id}}">{{$coursemain->name}} ( {{$coursemain->reference_number}})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
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
                                    <input type="text" id="startDate" autocomplete="new-password" name="startDate" class="form-control" value="{{\Carbon\Carbon::now()->subYear()->format('Y-m-d')}}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group date-ico">
                                    <label for="endDate">End Date</label>
                                    {{-- <input type="text" id="endDate" autocomplete="new-password" name="endDate" class="form-control" value="{{\Carbon\Carbon::now()->addDays(7)->format('Y-m-d')}}"> --}}
                                    <input type="text" id="endDate" autocomplete="new-password" name="endDate" class="form-control" value="{{\Carbon\Carbon::now()->addYear()->format('Y-m-d')}}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status">Enrollment Status</label>
                                    <select id="status" name="status" class="form-control select2">
                                        <option value="">Select Enrollment Status</option>
                                        @foreach( enrolledStatus() as $key => $value )
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
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="enrollment_no">Enrollment No</label>
                                    <input id="enrollment_no" name="enrollment_no" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="course_type">Course Type</label>
                                    <select name="course_type[]" id="course_type" multiple class="form-control select2" data-placeholder="Select Course Type">
                                        <option value="1">WSQ</option>
                                        <option value="2">non-WSQ</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="sponsored_by_company">Sponsored By Company</label>
                                    <select name="sponsored_by_company[]" id="sponsored_by_company" multiple class="form-control select2" data-placeholder="Select Sponsored By">
                                        <option value="Yes">Yes</option>
                                        <option value="No (I'm signing up as an individual)">No (I'm signing up as an individual)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="company_name">Company Name</label>
                                    <input id="company_name" type="text" name="company_name" class="form-control" />
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="company_uen">Company UEN</label>
                                    <input id="company_uen" type="text" name="company_uen" class="form-control" />
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="company_contact_person">Company Contact Name</label>
                                    <input id="company_contact_person" type="text" name="company_contact_person" class="form-control" />
                                </div>
                            </div>
        
                        </div>
 
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="pesa_refrerance_number">PSEA Referance No.</label>
                                    <input type="text" class="form-control" name="pesa_refrerance_number" id="pesa_refrerance_number" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="skillfuture_credit">SkillsFuture Credit</label>
                                    <input type="text" class="form-control" name="skillfuture_credit" id="skillfuture_credit" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="vendor_gov">Vendors@Gov</label>
                                    <input type="text" class="form-control" name="vendor_gov" id="vendor_gov"/>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="company_contact_person_number">Company Contact Number</label>
                                    <input id="company_contact_person_number" type="text" name="company_contact_person_number" class="form-control" />
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="company_contact_person_email">Company Contact Email</label>
                                    <input id="company_contact_person_email" type="text" name="company_contact_person_email" class="form-control" />
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="billing_email">Billing Email</label>
                                    <input id="billing_email" type="text" name="billing_email" class="form-control" />
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-1">
                                <button class="btn btn-primary mt-4" id="search_date" role="button">Search</button>
                            </div>
                        </div>
                        <input  type="hidden" name="enllorment_ids" value="" id="enllorment_ids">
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- end page title end breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body report-body">
                    <a class="btn btn-primary px-4 btn-rounded float-right mt-0 mb-3" href="{{ route('admin.studentenrolment.add') }}"><i class="add-new"></i>  Add New</a>
                    <a class="btn btn-danger btn-rounded float-right mt-0 mb-3 mr-3 " href="javascript:void(0)" id="cancel-enroll-all"><i class="far fa-trash-alt font-16"></i> Cancel Enrolement</a>
                    <a class="btn btn-info btn-rounded float-right mt-0 mb-3 mr-3 " href="javascript:void(0)" id="do-enroll-all"><i class="far fa-trash-alt font-16"></i> Enroll Into TPG</a>
                    {{-- {{dd($courseRunList)}} --}}
                    <h4 class="header-title mt-0">
                        Student Enrolment List
                    </h4>
                    <div class="dropdown mb-3 show-col-wrapper">
                        <button class="btn btn-primary dropdown-toggle btn-show-col" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Column Visibility <i class="ml-2 mdi mdi-chevron-down"></i>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item toggle-vis" data-column="1">Course Name</a>
                            <a class="dropdown-item toggle-vis" data-column="2">TPGateway Ref No.</a>
                            <a class="dropdown-item toggle-vis" data-column="3">Student Name</a>
                            <a class="dropdown-item toggle-vis" data-column="4">NRIC</a>
                            <a class="dropdown-item toggle-vis" data-column="5">Email</a>
                            <a class="dropdown-item toggle-vis" data-column="6">Phone No</a>
                            <a class="dropdown-item toggle-vis inactive" data-column="7">Sponsored By Company</a>
                            <a class="dropdown-item toggle-vis inactive" data-column="8">Company Name</a>
                            <a class="dropdown-item toggle-vis inactive" data-column="9">Company UEN</a>
                            <a class="dropdown-item toggle-vis inactive" data-column="10">Company Contact Name</a>
                            <a class="dropdown-item toggle-vis inactive" data-column="11">Company Contact Number</a>
                            <a class="dropdown-item toggle-vis inactive" data-column="12">Company Contact Email</a>
                            <a class="dropdown-item toggle-vis inactive" data-column="13">Billing Email</a>
                            <a class="dropdown-item toggle-vis" data-column="14">Status</a>
                            <a class="dropdown-item toggle-vis" data-column="15">Attended Sessions</a>
                            <a class="dropdown-item toggle-vis" data-column="16">Invoice</a>
                            <a class="dropdown-item toggle-vis" data-column="17">Payment Status</a>
                        </div>
                    </div>
                    <div class="table-responsive dash-social min-height-datatable-list">
                        <table id="datatable" class="table">
                            <thead>
                            <tr>
                                <th></th>
                                <th>No</th>
                                <th>Course Name</th>
                                <th>TP Gateway Ref</th>
                                <th>Student Name</th>
                                <th>NRIC</th>
                                <th>Email</th>
                                <th>Phone No</th>
                                <th>Sponsored By Company</th>
                                <th>Company Name</th>
                                <th>Company UEN</th>
                                <th>Company Contact Name</th>
                                <th>Company Contact Number</th>
                                <th>Company Contact Email</th>
                                <th>Billing Email</th>
                                <th>Status</th>
                                <th>Attended Sessions</th>
                                <th>Invoice #</th>
                                <th>Payment Status</th>
                                <th>Action</th>
                            </tr><!--end tr-->
                            </thead>

                            <tbody>

                            </tbody>
                        </table>
                        <pre id="example-console-rows"></pre>
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
<script src="{{ asset('assets/js/dataTables.checkboxes.min.js') }}"></script>

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

        @include('admin.partial.actions.cancelenrolement');

        @include('admin.partial.actions.holdinglist');

        @include('admin.partial.actions.viewenrolmentresponse');

        @include('admin.partial.actions.viewpayment');

        $(document).on('click', '#enrolagain', function(e) {
            e.preventDefault();
            var btn = $('#enrolagain');
            BITBYTE.progress(btn);
            let _enrolement_id = $(this).attr('enrolement_id');
            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                url: '{{ route('admin.ajax.studentEnrolmentAgain') }}',
                type: "POST",
                dataType: "JSON",
                data: {
                    id: _enrolement_id
                    // ids: _enrolement_ids
                },
                success: function(res) {
                    BITBYTE.unprogress(btn);
                    if( res.status == true ) {
                        showToast(res.msg, 1);
                    } else {
                        showToast(res.msg, 0);
                    }
                    setTimeout(function(){
                        location.reload();
                    }, 2000);
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

        /*$(document).on('change', '#coursemain', function(e) {
            e.preventDefault();
            let _coursemain = $(this).val();
            $('#courserun').empty();
            $('#courserun').append(new Option('Select Course Run', ''));
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
                            if( res.list.length > 0 ) {
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
        });*/

        // Datatable
        var table = $('#datatable').DataTable({
            "fnDrawCallback": function( oSettings ) {
                initTooltip();
            },
            "pageLength": 10,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: "{{ route('admin.studentenrolment.listdatatable') }}",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: "POST",
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('Authorization');
                },
                data: function(d) {
                    d.search['value'] = $('#datatable_filter input[type="search"]').val();
                    d.search['regex'] = false;
                    d.coursemain = $('#coursemain').val();
                    d.courserun = $('#courserun').val();
                    d.status = $('#status').val();
                    d.student_name = $('#student_name').val();
                    d.enrollment_no = $('#enrollment_no').val();
                    // read start date from the element
                    d.from = $('#startDate').val();
                    // read end date from the element
                    d.to = $('#endDate').val();
                    d.course_type = $('#course_type').val();
                    d.sponsored_by_company = $('#sponsored_by_company').val();
                    d.company_name = $('#company_name').val();
                    d.company_uen = $('#company_uen').val();
                    d.company_contact_person = $('#company_contact_person').val();
                    d.company_contact_person_number = $('#company_contact_person_number').val();
                    d.company_contact_person_email = $('#company_contact_person_email').val();
                    d.billing_email = $('#billing_email').val();
                    d.pesa_refrerance_number = $('#pesa_refrerance_number').val();
                    d.skillfuture_credit = $('#skillfuture_credit').val();
                    d.vendor_gov = $('#vendor_gov').val();
                    
                }
            },
            columns: [
                {data: 'cancelcheckbox', name: 'cancelcheckbox', orderable: false, searchable: false},
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'courseName', name: 'courseName'},
                {data: 'tpgateway_refno', name: 'tpgateway_refno'},
                {data: 'student_name', name: 'students.name', orderable: false, searchable: true},
                {data: 'nric', name: 'nric'},
                {data: 'email', name: 'email'},
                {data: 'mobile_no', name: 'mobile_no'},
                {data: 'sponsored_by_company', name: 'sponsored_by_company', visible:false},
                {data: 'company_name', name: 'company_name', visible:false},
                {data: 'company_uen', name: 'company_uen', visible:false},
                {data: 'company_contact_person', name: 'company_contact_person', visible:false},
                {data: 'company_contact_person_number', name: 'company_contact_person_number', visible:false},
                {data: 'company_contact_person_email', name: 'company_contact_person_email', visible:false},
                {data: 'billing_email', name: 'billing_email', visible:false},
                {data: 'status', name: 'status'},
                {data: 'attendedSessions', name: 'attendedSessions', orderable: false, searchable: false},
                {data: 'xero_invoice_number', name: 'xero_invoice_number'},
                {data: 'payment_status', name: 'payment_status'},
                {data: 'action', name: 'actio', orderable: false, searchable: false},
            ],
            'columnDefs': [
                {
                'targets': 0,
                "render": function (data, type, row, meta) {
                        return (data) ?  '<input type="checkbox" class="dt-checkboxes checkbox" value=' +data+ '>' : '<input type="checkbox" disabled >';
                },
                'checkboxes': {
                    'selectRow': true
                }
                }
            ],
            'select': 'multi',            
        });

        $('#datatable').on('select.dt', function () {
            var count = table.rows( { selected: true } ).count();
        });

        $(document).on('click', '#search_date', function(e) {
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

        // Handle click on "Select all" control
        $('#cancel-enroll').on('click', function(){
        // Get all rows with search applied
        var rows = table.rows({ 'search': 'applied' }).nodes();
        // Check/uncheck checkboxes for all rows in the table
        $('input[type="checkbox"]', rows).prop('checked', this.checked);
        });

        // Handle form submission event
        $('#cancel-enroll-all').on('click', function(e){
            swal.fire({
                title: 'Are you sure?',
                text: "You want to cancel the enrolement!",
                input: "text",
                inputLabel: "Type DELETE to confirm",
                inputPlaceholder: "Type DELETE to confirm",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, cancel it!',
                cancelButtonText: 'No',
                reverseButtons: true,
                inputValidator: (inputValue) => {
                if (inputValue === null) return false;
                if (inputValue === "") {
                    return "You need to Type DELETE to confirm!";
                }
                if (inputValue.toUpperCase() != "DELETE") {
                    return "You need to Type DELETE to confirm!";
                }
                }
            }).then((result) => {
                if (result.value) { 
                        e.preventDefault();
                        var form = this;
                        var idsArr = [];

                        // Iterate over all selected checkboxes
                        var selectedData = $(".checkbox").each(function(index, rowId){
                             // Create a hidden element                 
                            if(this.checked){
                                $(form).append(
                                        $('<input>')
                                        .attr('type', 'hidden')
                                        .attr('name', 'id[]')
                                        .val(rowId)
                                    );
                            }  
                            ids = idsArr.push($(rowId));
                        });
                        var rows_selected = table.column(0).checkboxes.selected();
                            // Filter out true and false values
                            rows_selected = rows_selected.filter(function(value) {
                                return value !== true && value !== false;
                            });

                            // Filter out empty and null values
                            rows_selected = rows_selected.filter(function(value) {
                                return value !== null && value !== undefined && value !== "";
                            });

                            // Remove occurrences of the string "null"
                            rows_selected = rows_selected.filter(function(value) {
                                return value !== "null";
                            });
                                             
                        if(idsArr.length <=0){
                            swal.fire(
                                'Opps',
                                'Please select one enrollment to cancel.',
                                'error'
                            )
                        }else {                       
                                var _enrolement_Ids = rows_selected.toArray();                               
                                $.ajax({
                                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                                    url: '{{ route('admin.ajax.studentEnrolment.modal.cancel') }}',
                                    type: "POST",
                                    dataType: "JSON",
                                    data: {
                                        ids: _enrolement_Ids
                                    },
                                    success: function(res) {
                                        console.log(res);
                                        if( res.status == true ) {
                                            swal.fire({
                                                title: 'Cancelled',
                                                text: "Your enrolements has been cancelled.",
                                                type: 'success',
                                                confirmButtonText: 'Ok',
                                            }).then((result) => {
                                                if (result.value) {
                                                    location.reload();
                                                }
                                            })
                                        } else {
                                            swal.fire(
                                                'Opps',
                                                'Some error occured, Please try again.',
                                                'error'
                                            )
                                        }
                                    }
                                });
                            }
                }
            });
        });

        $('#do-enroll-all').on('click', function(e){
            swal.fire({
                title: 'Are you sure?',
                text: "You want to bulk enrole!",
                input: "text",
                inputLabel: "Type CONFIRM to confirm",
                inputPlaceholder: "Type CONFIRM to confirm",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, confirm it!',
                cancelButtonText: 'No',
                reverseButtons: true,
                inputValidator: (inputValue) => {
                if (inputValue === null) return false;
                if (inputValue === "") {
                    return "You need to Type CONFIRM to confirm!";
                }
                if (inputValue.toUpperCase() != "CONFIRM") {
                    return "You need to Type CONFIRM to confirm!";
                }
                }
            }).then((result) => {
                if (result.value) { 
                        e.preventDefault();
                        var form = this;
                        var idsArr = [];

                        // Iterate over all selected checkboxes
                        var selectedData = $(".checkbox").each(function(index, rowId){
                             // Create a hidden element                 
                            if(this.checked){
                                $(form).append(
                                        $('<input>')
                                        .attr('type', 'hidden')
                                        .attr('name', 'id[]')
                                        .val(rowId)
                                    );
                            }  
                            ids = idsArr.push($(rowId));
                        });
                        var rows_selected = table.column(0).checkboxes.selected();
                            // Filter out true and false values
                            rows_selected = rows_selected.filter(function(value) {
                                return value !== true && value !== false;
                            });

                            // Filter out empty and null values
                            rows_selected = rows_selected.filter(function(value) {
                                return value !== null && value !== undefined && value !== "";
                            });

                            // Remove occurrences of the string "null"
                            rows_selected = rows_selected.filter(function(value) {
                                return value !== "null";
                            });
                                             
                        if(idsArr.length <=0){
                            swal.fire(
                                'Opps',
                                'Please select one enrollment.',
                                'error'
                            )
                        }else {                       
                                var _enrolement_Ids = rows_selected.toArray();                               
                                $.ajax({
                                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                                    url: '{{ route('admin.ajax.studentEnrolmentAgain') }}',
                                    type: "POST",
                                    dataType: "JSON",
                                    data: {
                                        ids: _enrolement_Ids
                                    },
                                    success: function(res) {
                                        console.log(res);
                                        if( res.status == true ) {
                                            swal.fire({
                                                title: 'Cancelled',
                                                text: "Your enrolements has been enrol.",
                                                type: 'success',
                                                confirmButtonText: 'Ok',
                                            }).then((result) => {
                                                if (result.value) {
                                                    location.reload();
                                                }
                                            })
                                        } else {
                                            swal.fire(
                                                'Opps',
                                                'Some error occured, Please try again.',
                                                'error'
                                            )
                                        }
                                    }
                                });
                            }
                }
            });
        });
    });
</script>
@endpush
