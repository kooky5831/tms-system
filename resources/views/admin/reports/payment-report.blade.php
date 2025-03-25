@extends('admin.layouts.master')
@section('title', 'Payment Tracker List')
@push('css')
<!-- DataTables -->
<link href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<!-- Responsive datatable examples -->
<link href="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/daterangepicker/daterangepicker.css') }}" rel="stylesheet" type="text/css" />
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
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Reports</a></li>
                        <li class="breadcrumb-item active">Payments</li>
                    </ol>
                </div>
                <h4 class="page-title">Payments Tracker</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" class="tms-report" action="{{route('admin.reports.paymentReport.export.excel')}}">
                        @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group date-ico">
                                    <label for="startDate">Due Date</label>
                                    <input type="text" id="dueDatestartDate" autocomplete="new-password" name="dueDatestartDate" class="form-control" value="{{\Carbon\Carbon::now()->subYear()->format('Y-m-d')}}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group date-ico">
                                    <label for="endDate">Due Date</label>
                                    <input type="text" id="dueDateendDate" autocomplete="new-password" name="dueDateendDate" class="form-control" value="{{\Carbon\Carbon::now()->format('Y-m-d')}}">
                                </div>
                            </div>
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
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="paymentRemark">Payment Remark</label>
                                    <input type="text" id="payment_remark" name="payment_remark" class="form-control" value="">
                                </div>
                            </div>
                        </div>
                        <div class="row">
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
                                    <label for="payment_status">Payment Status</label>
                                    <select name="payment_status" id="payment_status" class="form-control select2" >
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
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control select2">
                                        @foreach( enrolledStatus() as $key => $value )
                                            <option value="{{ $key }}" {{ $key == 0 ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="paymentRemark">Remaining Amount From</label>
                                    <input type="text" id="remaining_amount_from" name="remaining_amount_from" class="form-control" value="">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="paymentRemark">Remaining Amount To</label>
                                    <input type="text" id="remaining_amount_to" name="remaining_amount_to" class="form-control" value="">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tpg_payment_status">TPG Payment Status</label>
                                    <select id="tpg_payment_status" name="tpg_payment_status" class="form-control select2">
                                        <option value="">Select TPG Payment Status</option>
                                        @foreach( getPaymentStatusForTPG() as $key => $value)
                                            <option value="{{$key}}">{{ $value }}</option>
                                        @endforeach    
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="course_type">Course Type</label>
                                    <select id="course_type" name="course_type" class="form-control select2">
                                        <option value="">Select Course Mode</option>
                                        @foreach( courseType() as $key => $value )
                                            <option value="{{$key}}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group date-ico">
                                    <label for="from">Start Date</label>
                                    <input type="text" id="from" autocomplete="new-password" name="from" class="form-control" value="{{\Carbon\Carbon::now()->subYear()->format('Y-m-d')}}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group date-ico">
                                    <label for="to">End Date</label>
                                    <input type="text" id="to" autocomplete="new-password" name="to" class="form-control" value="">
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-between">
                            <div cclass="col-md-6">
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
    <!-- end page title end breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body report-body">
                        <h4 class="header-title mt-0">
                            Payment Tracker List
                        </h4>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="float-right">
                                    <button type="submit" class="btn btn-info px-4 btn-rounded mt-0 mb-3" id="get-payment-tpg">Get Payment Status From TPG</button>
                                    <button type="submit" class="btn btn-success px-4 btn-rounded mt-0 mb-3" id="submit-payment-tpg">Submit Payment Status To TPG</button>                                 
                                </div>
                            </div>
                        </div>
                        <div class="dropdown mb-3 show-col-wrapper">
                            <button class="btn btn-primary dropdown-toggle btn-show-col" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Column Visibility <i class="ml-2 mdi mdi-chevron-down"></i>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item toggle-vis" data-column="1">Student Name</a>
                                <a class="dropdown-item toggle-vis" data-column="2">Email</a>
                                <a class="dropdown-item toggle-vis inactive" data-column="3">Billing Email</a>
                                <a class="dropdown-item toggle-vis" data-column="4">Phone No</a>
                                <a class="dropdown-item toggle-vis" data-column="5">Course Name</a>
                                <a class="dropdown-item toggle-vis inactive" data-column="6">Company Contact Person</a>
                                <a class="dropdown-item toggle-vis inactive" data-column="7">Company Contact Email</a>
                                <a class="dropdown-item toggle-vis inactive" data-column="8">Company Contact Number</a>
                                <a class="dropdown-item toggle-vis inactive" data-column="9">Sponsored by Company</a>
                                <a class="dropdown-item toggle-vis inactive" data-column="10">Payment Mode</a>
                                <a class="dropdown-item toggle-vis inactive" data-column="11">Remark</a>
                                <a class="dropdown-item toggle-vis" data-column="12">Payment Remark</a> 
                                <a class="dropdown-item toggle-vis" data-column="13">Due date</a>  
                                <a class="dropdown-item toggle-vis" data-column="14">Invoice ID</a>
                                <a class="dropdown-item toggle-vis" data-column="15">Remaining Amount</a>
                                <a class="dropdown-item toggle-vis" data-column="16">Payment Status</a>
                                <a class="dropdown-item toggle-vis" data-column="17">Status</a>
                            </div>
                        </div>
                        <div class="table-responsive dash-social min-height-datatable-list">
                            
                            <table id="datatable" class="table">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th>No</th>
                                    <th>Student Name</th>
                                    <th>Email</th>
                                    <th>Billing Email</th>
                                    <th>Phone No</th>
                                    <th>Course Name</th>
                                    <th>Company Name</th>
                                    <th>Company Contact Person</th>
                                    <th>Company Contact Email</th>
                                    <th>Company Contact Number</th>
                                    <th>Sponsored By Company</th>
                                    <th>Payment Mode</th>
                                    <th>Remark</th>
                                    <th>Payment Remark</th>
                                    <th>Due date</th>
                                    <th>Invoice ID</th>
                                    <th>Remaining Amount</th>
                                    <th>TPG Payment Status</th>
                                    <th>Payment Status</th>
                                    <th>Status</th>
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
<script src="{{ asset('assets/js/dataTables.checkboxes.min.js') }}"></script>
<!-- XEditable Plugin -->
<script src="{{ asset('assets/plugins/x-editable/js/bootstrap-editable.min.js')}}"></script>
<script type="text/javascript">
    $(function () {
        var pageNumber = 0;
        $(".select2").select2({ width: '100%' });
        $('#dueDatestartDate').daterangepicker({
            locale: {
                format: 'Y-M-DD'
            },
            singleDatePicker: true,
            showDropdowns: true,
            minDate:null,
        });
        $('#from').daterangepicker({
            locale: {
                format: 'Y-M-DD'
            },
            singleDatePicker: true,
            showDropdowns: true,
            minDate:null,
        });
        $('#dueDateendDate').daterangepicker({
            locale: {
                format: 'Y-M-DD',
                cancelLabel: 'Clear'
            },
            singleDatePicker: true,
            showDropdowns: true,
            minDate: new Date(),
        });
        $('#to').daterangepicker({
            locale: {
                format: 'Y-M-DD',
                cancelLabel: 'Clear'
            },
            singleDatePicker: true,
            showDropdowns: true,
            minDate: new Date(),
        });
        $('#dueDatestartDate').on('change', function(ev, picker) {
            // do something, like clearing an input
            $('#dueDateendDate').daterangepicker({
                locale: {
                    format: 'Y-M-DD',
                    cancelLabel: 'Clear'
                },
                singleDatePicker: true,
                showDropdowns: true,
                minDate: moment($('#dueDatestartDate').val(), "YYYY-MM-DD"),
                minYear: 2019,
            });
        });
        $('#from').on('change', function(ev, picker) {
            // do something, like clearing an input
            $('#to').daterangepicker({
                locale: {
                    format: 'Y-M-DD',
                    cancelLabel: 'Clear'
                },
                singleDatePicker: true,
                showDropdowns: true,
                minDate: moment($('#dueDatestartDate').val(), "YYYY-MM-DD"),
                minYear: 2019,
            });
        });
        var table = $('#datatable').DataTable({
            "fnDrawCallback": function( oSettings ) {
                initTooltip();
                inlineEdit();
            },
            "pageLength": 10,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: "{{ route('admin.reports.paymentreport.listdatatable') }}",
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
                    d.dueDatestartDate = $('#dueDatestartDate').val();
                    d.from = $('#from').val();
                    // read end date from the element
                    d.payment_status = $('#payment_status').val();
                    d.tpg_payment_status = $('#tpg_payment_status').val();
                    d.dueDateendDate = $('#dueDateendDate').val();
                    d.to = $('#to').val();
                    d.course_type = $('#course_type').val();
                    d.sponsored_by_company = $("#sponsored_by_company").val();
                    d.payment_remark =  $("#payment_remark").val();
                    d.company_name = $("#company_name").val();
                    d.company_uen = $("#company_uen").val();
                    d.remaining_amount = $("#remaining_amount").val();
                    d.status = $("#status").val();
                    d.remaining_amount_from = $("#remaining_amount_from").val();
                    d.remaining_amount_to = $("#remaining_amount_to").val();
                }
            },
            columns: [
                {data: 'paymenttpg', name: 'paymenttpg', orderable: false, searchable: false},
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'name', name: 'students.name', orderable: false, searchable: true},
                {data: 'email', name: 'email'},
                {data: 'billing_email', name: 'student_enrolments.billing_email', visible: false},
                {data: 'mobile_no', name: 'student_enrolments.mobile_no'},
                {data: 'courseName', name: 'courseName'},
                {data: 'company_name', name: 'student_enrolments.company_name'},
                {data: 'company_contact_person', name:'student_enrolments.company_contact_person', visible: false},
                {data: 'company_contact_person_email',name:'student_enrolments.company_contact_person_email', visible: false},
                {data: 'company_contact_person_number',name:'student_enrolments.company_contact_person_number', visible: false},
                {data: 'sponsored_by_company', name: 'student_enrolments.sponsored_by_company', visible: false},
                {data: 'payment_mode', name: 'payment_mode', visible:  false},
                {data: 'remarks', name: 'student_enrolments.remarks', visible: false},
                {data: 'payment_remark', name: 'payment_remark', 
                    render: function(data, type, row) {
                        data = (data == null) ? "" : data;
                        return '<a href="#" class="inline_payment_remark inline-editable" data-field="inline-payment-remark" data-type="textarea"  data-value="'+row['full_payment_remark']+'" data-pk="1" data-title="Enter Payment Remarks">'+data+'</a>';
                    }, 
                    class: 'editable payment_remark'},
                {data: 'due_date', name: 'student_enrolments.due_date',
                    render: function(data, type, row) {
                        data = (data == null) ? "" : data;
                        return '<a href="#" class="inline-dob inline-editable" data-placement="right" data-field="inline-dob" data-type="combodate" data-value="'+data+'" data-format="YYYY-MM-DD" data-viewformat="DD/MM/YYYY"></a>';
                    },
                 class: 'editable due_date'},
                {data: 'xero_invoice_number', name: 'student_enrolments.xero_invoice_number'},
                {data: 'remaining_amount',name: 'remaining_amount'},
                {data: 'payment_tpg_status', name: 'student_enrolments.payment_tpg_status'},
                {data: 'payment_status', name: 'student_enrolments.payment_status'},
                {data: 'status', name: 'student_enrolments.status'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            'columnDefs': [
                {
                'targets': 0,
                "render": function (data, type, row, meta) {
                        return (row.tpgateway_refno) ?  '<input type="checkbox" class="dt-checkboxes checkbox" value=' +data+ '>' : '<input type="checkbox" disabled >';
                },
                'checkboxes': {
                    'selectRow': true
                }
                }
            ],
            'select': 'multi',
            createdRow: function(row, data, dataIndex) {
                $(row).data('record_id', data.record_id); // Store the 'id' value in the row data
            }
        });

        table.on('page.dt', function(){
            var info = table.page.info();
            pageNumber = info.page+1;
        })
        
        $(document).on('click', '#search_date', function(e) {
            e.preventDefault();
            table.draw();
        });
        $('a.toggle-vis').on('click', function (e) {
            e.stopPropagation();
            e.preventDefault();
            $(this).toggleClass('inactive');
            var column = table.column($(this).attr('data-column'));
            column.visible(!column.visible());
        });
        function inlineEdit(){
            if ($('.inline-editable').length) {
                $.fn.editableform.buttons = '<button type="submit" class="btn btn-success editable-submit btn-sm waves-effect waves-light"><i class="mdi mdi-check"></i></button>' + '<button type="button" class="btn btn-danger editable-cancel btn-sm waves-effect waves-light"><i class="mdi mdi-close"></i></button>';
                
                $('.inline_payment_remark').editable({
                    showbuttons: 'bottom',
                    success: function(response, newValue) {
                        var recordId = $(this).closest('tr').data('record_id');
                        var field = $(this).data('field');
                        var value = newValue;
                        updateFunction(recordId, field, value, pageNumber);
                    }
                });
                $('.inline-dob').editable({
                    showbuttons: 'top',
                    success: function(response, newValue) {
                        var recordId = $(this).closest('tr').data('record_id');
                        var field = $(this).data('field');
                        var value = moment(newValue).format('Y-M-DD');
                        updateFunction(recordId, field, value, pageNumber);
                    }
                });
            }  
        }
        $.fn.combodate.defaults = {
            minYear: new Date().getFullYear(),
            maxYear: 2050,
            yearDescending: false,
            firstItem: "empty",
            customClass: 'form-control',
        }
        function updateFunction(record_id, field, value, pageNumber){
            var currentPageNumber = table.page();
            $.ajax({
                url: "{{ route('admin.reports.paymentreport.updatePayment') }}",
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

        $('#get-payment-tpg').on('click', function(e) {
            e.preventDefault();
            var idsArr = [];
            var form = this;
            // Iterate over all selected checkboxes
            var selectedData = $(".checkbox").each(function(index, rowId){
                if(this.checked){
                    $(form).append(
                        $('<input>')
                        .attr('type', 'hidden')
                        .attr('name', 'id[]')
                        .val(rowId)
                    );
                    ids = idsArr.push($(rowId));
                } 
            });

            var rows_selected = table.column(0).checkboxes.selected();

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
            console.log("length==",idsArr);
            if(idsArr.length <=0){
                showToast('Please select atleast one payment to sync', 0);
            } else {
                var _enrolement_Ids = rows_selected.join(",");                               
                $.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    url: '{{ route('admin.reports.paymentreport.gettpg') }}',
                    type: "POST",
                    dataType: "JSON",
                    data: {
                        ids: _enrolement_Ids
                    },
                    success: function(res) {
                        if( res.status == true ) {
                            showToast(res.msg, 1);
                        } else {
                            showToast(res.msg, 0);
                        }
                        setTimeout(function(){
                            location.reload();
                        }, 2000);
                    }
                });
            }

        });

        $('#submit-payment-tpg').on('click', function(e) {
            e.preventDefault();
            var idsArr = [];
            var form = this;

            // Iterate over all selected checkboxes
            var selectedData = $(".checkbox").each(function(index, rowId){
                if(this.checked){
                    $(form).append(
                        $('<input>')
                        .attr('type', 'hidden')
                        .attr('name', 'id[]')
                        .val(rowId)
                    );
                    ids = idsArr.push($(rowId));
                } 
            });

            var rows_selected = table.column(0).checkboxes.selected();

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
                showToast('Please select atleast one payment to submit', 0);
            } else {
                var _enrolement_Ids = rows_selected.join(",");                               
                $.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    url: '{{ route('admin.reports.paymentreport.submittpg') }}',
                    type: "POST",
                    dataType: "JSON",
                    data: {
                        ids: _enrolement_Ids
                    },
                    success: function(res) {
                        if( res.status == true ) {
                            showToast(res.msg, 1);
                        } else {
                            showToast(res.msg, 0);
                        }
                        setTimeout(function(){
                            location.reload();
                        }, 2000);
                    }
                });
            }

        });
    });
    
</script>
@endpush
