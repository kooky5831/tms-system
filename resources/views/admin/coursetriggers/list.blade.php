@extends('admin.layouts.master')
@section('title', 'Course Triggers List')
@push('css')
<!-- DataTables -->
<link href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<!-- Responsive datatable examples -->
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
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Course Triggers List</a></li>
                        <li class="breadcrumb-item active">List</li>
                    </ol>
                </div>
                <h4 class="page-title">Course Triggers</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="coursemain">Course</label>
                                    <select name="coursemain[]" id="coursemain" multiple class="form-control select2" placeholder="Select course">
                                        @foreach ($courseMainList as $coursemain)
                                            <option value="{{$coursemain->id}}">{{$coursemain->name}} ( {{$coursemain->reference_number}})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="event_when">When</label>
                                    <select name="event_when[]" id="event_when" multiple class="form-control select2">
                                        <option value="">{{ ucwords("select when") }}</option>
                                        @foreach(triggerEventWhen() as $eventWhenKey => $eventWhen)
                                        <option value="{{$eventWhenKey}}">{{ ucwords($eventWhen) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="event_type">Type</label>
                                    <select name="event_type" id="event_type" class="form-control select2">
                                            <option value="">{{ ucwords("select type") }}</option>
                                        @foreach(triggerEventTypes() as $eventTypeKey => $eventType)
                                            <option value="{{$eventTypeKey}}">{{ ucwords($eventType) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control select2" data-placeholder="Select Status">
                                        <option value="">Select Status</option>
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="coursetag">Tags</label>
                                    <select name="coursetag[]" id="coursetag" multiple class="form-control select2">
                                        <option value="">Select Tag</option>
                                        @foreach($courseTags as $courseTag)
                                            <option value="{{$courseTag->id}}">{{$courseTag->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="from">From</label>
                                    <input type="number" min="0" max="9" id="from" name="from" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="to">To</label>
                                    <input type="number" min="0" max="31" id="to" name="to" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">
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
                <div class="card-body">
                    @can('coursetriggers-add')
                    <a class="btn btn-primary px-4 btn-rounded float-right mt-0 mb-3" href="{{ route('admin.coursetrigger.add') }}"><i class="add-new"></i> Add New</a>
                    @endcan
                    <h4 class="header-title mt-0">Course Triggers List</h4>
                    <div class="table-responsive dash-social">
                        <table id="datatable" class="table">
                            <thead>
                            <tr>
                                <th></th>
                                <th>No</th>
                                <th>Trigger Title</th>
                                <th>Courses</th>
                                <th>Tags</th>
                                <th>Priority</th>
                                <th>Event When</th>
                                <th>Event Type</th>
                                <th>Template Name</th>
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
<script src="{{ asset('assets/plugins/daterangepicker/daterangepicker.js') }}"></script>
<script type="text/javascript">
    $(function () {
        $(".select2").select2({ width: '100%' });

        // Datatable
        var table = $('#datatable').DataTable({
            "fnDrawCallback": function( oSettings ) {
                initTooltip();
            },
            "pageLength": 10,
            processing: true,
            serverSide: true,
            aaSorting: [[ 0, "DESC" ]],
            ajax: {
                url: "{{ route('admin.coursetrigger.listdatatable') }}",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: "POST",
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('Authorization');
                },
                data: function(d) {
                    d.search['value'] = $('#datatable_filter input[type="search"]').val();
                    d.search['regex'] = false;
                    d.coursemain = $('#coursemain').val();
                    d.event_type = $('#event_type').val();
                    d.event_when = $('#event_when').val();
                    d.status     = $('#status').val();
                    d.coursetag  = $('#coursetag').val();
                    d.from = $('#from').val();
                    d.to = $('#to').val();
                }
            },
            columns: [
                {data: 'created_at', name: 'created_at', visible: false },
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'triggerTitle', name: 'triggerTitle'},
                {data: 'coursename', name: 'coursename'},
                {data: 'tags', name: 'tags', orderable: false},
                {data: 'priority', name:'priority', searchable: false},
                {data: 'event_when', name: 'event_when'},
                {data: 'event_type', name: 'event_type'},
                {data: 'template_name', name: 'template_name'},
                {data: 'status', name: 'status'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        });

        $(document).on('click', '#search_data', function(e) {
            e.preventDefault();
            table.draw();
        });

    });
</script>
@endpush
