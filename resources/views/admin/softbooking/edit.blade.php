@extends('admin.layouts.master')
@section('title', 'Edit Soft Booking')
@push('css')
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
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{route('admin.softbooking.list')}}">Soft Booking</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
                <h4 class="page-title">Edit Soft Booking</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form action="{{ route('admin.softbooking.edit', $data->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <!-- <h5 class="card-header bg-secondary text-white mt-0">Edit Soft Booking</h5> -->
                    <div class="card-body">
                        <h4 class="header-title mt-0">Edit Soft Booking</h4>
                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="course_id">Course Run <span class="text-danger">*</span></label>
                                    <select name="course_id" required class="form-control select2">
                                        <option value="">Select Course Run</option>
                                        @foreach($courseList as $course)
                                        <option value="{{$course->id}}" {{ $data->course_id == $course->id ? 'selected' : ''  }}>{{$course->tpgateway_id }} ({{$course->course_start_date}}) - {{ $course->courseMain->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('course_id')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group date-ico">
                                    <label for="deadline_date"> Deadline Date <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control singledate" value="{{ $data->deadline_date }}" name="deadline_date" id="deadline_date" placeholder="" />
                                    @error('deadline_date')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                        </div>

                        <h4 class="header-title mt-0">TRAINEE DETAILS</h4>

                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="{{ $data->name }}" required name="name" id="name" placeholder="" />
                                    @error('name')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contact_number"> Contact Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="{{ $data->mobile }}" required name="contact_number" onkeypress="return isNumberKey(event)" placeholder="" />
                                    @error('contact_number')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" value="{{ $data->email }}" required name="email" placeholder="" />
                                    @error('email')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="nric">Nric <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="{{ $data->nric }}" required name="nric" placeholder="" />
                                    @error('nric')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <select name="status" class="form-control select2">
                                        @foreach (getCourseSoftBookingStatus() as $stat => $statLabel)
                                            <option value="{{$stat}}" {{ $data->status == $stat ? 'selected' : '' }}>{{$statLabel}}</option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="notes">Notes</label>
                                    <textarea name="notes" id="notes" rows="3" class="form-control h-auto">{{$data->notes}}</textarea>
                                </div>
                            </div>
                        </div>

                    </div><!--end card-body-->
                    <div class="card-footer m-0 clearfix">
                        <button type="submit" class="btn btn-primary mar-r-10">Update</button>
                        <a href="{{ route('admin.softbooking.list') }}" class="btn btn-danger">Cancel</a>
                    </div>
                </form>
            </div><!--end card-->
        </div> <!--end col-->
    </div><!--end row-->

</div><!-- container -->
@endsection
@push("scripts")
<script src="{{ asset('assets/plugins/daterangepicker/daterangepicker.js') }}"></script>

<script type="text/javascript">

    $(document).ready(function() {
        $(".select2").select2({ width: '100%' });

        $('.singledate').daterangepicker({
            locale: {
                format: 'Y-M-DD'
            },
            singleDatePicker: true,
            showDropdowns: true,
            minDate: new Date(),
            minYear: 2019,
        });
    });

</script>
@endpush
