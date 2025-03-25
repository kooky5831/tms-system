@extends('admin.layouts.master')
@section('title', 'Add Waiting List')
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
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Waiting List</a></li>
                        <li class="breadcrumb-item active">Add</li>
                    </ol>
                </div>
                <h4 class="page-title">Waiting List</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form action="{{ route('admin.waitinglist.add') }}" method="POST" id="addwaitinglist" enctype="multipart/form-data">
                    @csrf
                   <!--  <h5 class="card-header bg-secondary text-white mt-0">Add Waiting List</h5> -->
                    <div class="card-body">
                        <h4 class="header-title mt-0">Add Waiting List</h4>
                        <div class="row">

                            <div class="col-md-11">
                                <div class="form-group">
                                    <label for="course_id">Course Run<span class="text-danger">*</span></label>
                                    <select name="course_id" id="course_id" class="form-control select2">
                                        <option value="">Select Course Run</option>
                                        @foreach($courseList as $course)
                                        <option value="{{$course->id}}" data-registeredusercount="{{$course->registeredusercount}}" data-intakesize={{$course->intakesize}} {{ old('course_id') == $course->id ? 'selected' : ''  }}>{{$course->tpgateway_id }} ({{$course->course_start_date}}) - {{ $course->courseMain->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('course_id')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="contact_number"> Seats Available</label>
                                    <br />
                                    <label id="seats_available">0</label>
                                </div>
                            </div>
                        </div>

                        <h4 class="header-title mt-0">TRAINEE DETAILS</h4>

                        <div class="repeater-custom-show-hide">
                            <div data-repeater-list="students">
                                <div data-repeater-item="">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name">Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" value="{{ old('students[0][name]') }}" name="students[0][name]" placeholder="" />
                                                @error('name')
                                                    <label class="form-text text-danger">{{ $message }}</label>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label for="contact_number"> Contact Number <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" value="{{ old('students[0][contact_number]') }}" name="students[0][contact_number]" onkeypress="return isNumberKey(event)" placeholder="" />
                                                @error('contact_number')
                                                    <label class="form-text text-danger">{{ $message }}</label>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-1 verti-cen">
                                            <span data-repeater-delete="" class="btn btn-danger btn-sm">
                                                <span class="far fa-trash-alt"></span>
                                            </span>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="email">Email <span class="text-danger">*</span></label>
                                                <input type="email" class="form-control" value="{{ old('students[0][email]') }}" name="students[0][email]" placeholder="" />
                                                @error('email')
                                                    <label class="form-text text-danger">{{ $message }}</label>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="nric">Nric <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" value="{{ old('students[0][nric]') }}" name="students[0][nric]" placeholder="" />
                                                @error('nric')
                                                    <label class="form-text text-danger">{{ $message }}</label>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="status">Status <span class="text-danger">*</span></label>
                                                <select name="students[0][status]" class="form-control select2">
                                                    @foreach (getCourseWaitingListStatus() as $stat => $statLabel)
                                                        <option value="{{ $stat }} {{ old('students[0][status]')  == $stat ? 'selected': '' }}">{{$statLabel}}</option>
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
                                                <textarea name="students[0][notes]" rows="3" class="form-control h-auto"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <hr />

                                </div>
                            </div>
                            <div class="form-group row mb-0 text-center">
                                <div class="col-sm-12">
                                    <span data-repeater-create="" class="btn btn-secondary btn-md reapet-add">
                                        <span class="white-add-ico"></span>  Add Another Trainee
                                    </span>
                                </div><!--end col-->
                            </div><!--end row-->
                        </div>

                    </div>

                    <!-- </div> -->
                    <div class="card-footer m-0 clearfix">
                        <button type="submit" class="btn btn-primary mar-r-10">Submit</button>
                        <a href="{{ route('admin.waitinglist.list') }}" class="btn btn-danger">Cancel</a>
                    </div>
                </form>
            </div><!--end card-->
        </div> <!--end col-->
    </div><!--end row-->

</div><!-- container -->
@endsection
@push("scripts")
<script src="{{ asset('assets/plugins/daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('assets/plugins/repeater/jquery.repeater.min.js') }}"></script>

<script type="text/javascript">

    function setRepeter() {

        let $repeater = $('.repeater-custom-show-hide').repeater({
            // initEmpty: true,
            isFirstItemUndeletable: true,
            show: function () {
                $(this).slideDown();
            },
            hide: function (remove) {
              if (confirm('Are you sure you want to remove this item?')) {
                $(this).slideUp(remove);
              }
            }
        });
    }


    $(document).ready(function() {
        setRepeter();

        $(".select2").select2({ width: '100%' });

        $(document).on("change", "#course_id",function() {
            // let _val = $(this).val();
            let _registeredusercount = $("#course_id").select2().find(":selected").data("registeredusercount");
            let _intakesize = $("#course_id").select2().find(":selected").data("intakesize");
            $('#seats_available').text(_intakesize - _registeredusercount);
        });

        @if( old('course_id') )
            let _registeredusercount = $("#course_id").select2().find(":selected").data("registeredusercount");
            let _intakesize = $("#course_id").select2().find(":selected").data("intakesize");
            $('#seats_available').text(_intakesize - _registeredusercount);
        @endif

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
