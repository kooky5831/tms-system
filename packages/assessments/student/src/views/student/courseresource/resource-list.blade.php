@extends('assessments::student.layouts.master')
@section('title', 'Course Resource List')
@push('css')
<!-- DataTables -->
<link href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<!-- Responsive datatable examples -->
<link href="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/daterangepicker/daterangepicker.css') }}" rel="stylesheet" type="text/css" />
<style>
    .fontsize-15{ font-size:15px; }  
    .primary-color { color: #658bf7; font-weight: 500; }
    .assessment .card { height: 100%; border-radius: 10px; overflow: hidden; border: 1px solid rgba(154, 170, 207, 0.1); transition: border 0.1s, transform 0.3s; } 
    .assessment .card .card-body { padding-bottom:75px; }
    .assessment .btn-primary { min-width: 150px; border: 1px solid #6673fd; padding: 10px; }
    .assessment .card:hover { border-color: #6673fd; -webkit-transform: translateY(-10px); transform: translateY(-10px); }
    .assessment .card .card-footer { position: absolute; left: 20px; bottom: 30px; background-color: transparent; padding: 15px 0 0; }
    .assessment .card .card-footer .badge { min-width: 150px; line-height: 26px; padding: 10px; }
    p.fontsize-14 {font-size: 14px;}
    .course-resource-paginate {float: right;margin-top: 30px;}
    /* .course-resource-paginate .hidden {display: none;} */
</style>
@endpush
@section('content')

<div class="container-fluid">
        <!-- Page-Title -->
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <h4 class="page-title">Course Resources</h4>
                </div><!--end page-title-box-->
            </div><!--end col-->
        </div><!--end row-->
    <div class="row assessment">
        @foreach($coureResource as $course)
        @php
        // $courseStartDate =  Carbon\Carbon::createFromFormat('Y-m-d', $course->course_start_date);
        @endphp
            <div class="col-3 mb-3">
                <div class="card shadow">
                    <div class="card-body card-p">
                        <h3 class="card-title">{{ $course->name }}</h3>
                        <p class="fontsize-14">You have access to this resource</p>
                        <div class="card-footer">
                            <a href="{{ route('student.course-resources.assessment.get-resources', $course->course_main_id) }}" class="btn btn-primary" id="exam_rules">Access Now</a>
                        </div>
                    </div>
                </div>
            </div>
                {{-- @if(!$courseStartDate->isPast()) --}}
                {{-- @else  --}}
                {{-- <div class="col-3 mb-3">
                    <div class="card shadow">
                        <div class="card-body card-p">
                            <h3 class="card-title">{{ $course->name }}</h3>
                            <p class="fontsize-14">You had access to this resource till <strong>{{ Carbon\Carbon::parse($course->course_start_date)->format('Y-m-d') }}</strong></p>
                        </div>
                    </div>
                </div> --}}
                {{-- @endif --}}
        @endforeach
    </div>

    <div class="course-resource-paginate">
        {{ $coureResource->links() }}
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/plugins/daterangepicker/daterangepicker.js') }}"></script>

@endpush
