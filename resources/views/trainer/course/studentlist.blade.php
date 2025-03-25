@extends('trainer.layouts.master')
@section('title', 'Courses Run Student List')
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
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Courses Runs Student List</a></li>
                        <li class="breadcrumb-item active">List</li>
                    </ol>
                </div>
                <h4 class="page-title">Courses Run Student List</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row float-right">
                        <form action="{{ route('trainer.course.generate-attendance', $course->id) }}" target="_blank" id="courserun_generateAttendance" method="POST" enctype="multipart/form-data">
                            @csrf
                            <button type="submit" class="btn btn-success px-4 btn-rounded mt-0 mb-3 mr-3">Generate Attendence</button>
                        </form>
                        <button class="btn btn-primary px-4 btn-rounded mt-0 mb-3" onclick="history.back()">Go Back</button>
                    </div>
                    <h4 class="header-title mt-0">Students List - {{$course->courseMain->name}}</h4>
                    <h4 class="header-title mt-0">Course Date - {{$course->course_start_date}} to {{$course->course_end_date}}</h4>
                    <div class="table-responsive dash-social">
                        <table id="datatable" class="table">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>NRIC</th>
                                <th>Email</th>
                                <th>Payment Status</th>
                                <th>Action</th>
                            </tr><!--end tr-->
                            </thead>

                            <tbody>
                                @foreach ($students as $s => $student)
                                    <tr>
                                        <td>{{++$s}}</td>
                                        <td>{{$student->student->name}}</td>
                                        <td>{{convertNricToView($student->student->nric)}}</td>
                                        <td>{{$student->email}}</td>
                                        <td>{{getPaymentStatus($student->payment_status)}}</td>
                                        <td>
                                            <a href="{{route('trainer.studentenrolment.view', $student->id)}}" target="_blank" data-toggle="tooltip" data-placement="bottom" title="View" class="mr-2"><i class="fas fa-eye text-info font-16"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
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
        // Datatable
        var table = $('#datatable').DataTable({
            "fnDrawCallback": function( oSettings ) {
                initTooltip();
            },
            "pageLength": 10,
            processing: true,
        });
    });
</script>
@endpush
