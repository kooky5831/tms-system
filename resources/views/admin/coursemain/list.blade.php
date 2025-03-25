@extends('admin.layouts.master')
@section('title', 'Courses List')
@push('css')
<!-- DataTables -->
<link href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<!-- Responsive datatable examples -->
<link href="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
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
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Courses</a></li>
                        <li class="breadcrumb-item active">List</li>
                    </ol>
                </div>
                <h4 class="page-title">Courses</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <form>
                        @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="course_module">Course Module</label>
                                    <select id="course_module" name="course_module" class="form-control select2">
                                        <option value="">Select Course Module</option>
                                        @foreach( courseModule() as $key => $value )
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
                            <div class="col-md-3">
                                <label for="course_mode">Course Mode</label>
                                <select id="course_mode" name="course_mode" class="form-control select2">
                                    <option value="">Select Course Mode</option>
                                    @foreach( courseMode() as $key => $value )
                                        <option value="{{$key}}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="course_tags">Course Tags</label>
                                <select id="course_tags" name="course_tags" class="form-control select2">
                                    <option value="">Select Course Tags</option>
                                    @foreach( courseTags() as $key => $value )
                                        <option value="{{$key}}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <button class="btn btn-primary mt-4" id="search_date" role="button">Search</button>
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
                    <a class="btn btn-primary px-4 btn-rounded float-right mt-0 mb-3" href="{{ route('admin.coursemain.add') }}"><i class="add-new"></i> Add New</a>
                    <h4 class="header-title mt-0">Courses List</h4>
                    <div class="table-responsive dash-social">
                        <table id="datatable" class="table">
                            <thead>
                            <tr>
                                <th></th>
                                <th>No</th>
                                <th>Course Module</th>
                                <th>Name</th>
                                <th>Reference Number</th>
                                <th>Skill Code</th>
                                <th>Course Type</th>
                                <th>Course Mode</th>
                                <th>Course Tags</th>
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
<script type="text/javascript">
    $(function () {
        $(".select2").select2();
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
                url: "{{ route('admin.coursemain.listdatatable') }}",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: "POST",
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('Authorization');
                },
                data: function(d){
                    d.search['value'] = $('#datatable_filter input[type="search"]').val();
                    d.search['regex'] = false;
                    d.course_module = $('#course_module').val();
                    d.course_type = $('#course_type').val();
                    d.course_mode = $('#course_mode').val();
                    d.course_tags = $('#course_tags').val();
                }
            },
            columns: [
                {data: 'created_at', name: 'created_at', visible: false },
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'coursetype', name: 'coursetype', orderable: false},
                {data: 'name', name: 'name'},
                {data: 'reference_number', name: 'reference_number'},
                {data: 'skill_code', name: 'skill_code'},
                {data: 'course_type', name: 'course_type'},
                {data: 'course_mode_training', name: 'course_mode_training'},
                {data: 'course_tags', name: 'course_tags', orderable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        });

        
        $(document).on('click', '#search_date', function(e) {
            e.preventDefault();
            table.draw();
        });
    });
</script>
@endpush
