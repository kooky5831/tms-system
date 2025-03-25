@extends('admin.layouts.master')
@section('title', 'Edit Course Run')
@push('css')
<link href="{{ asset('assets/plugins/daterangepicker/daterangepicker.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/clockpicker/jquery-clockpicker.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
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
                        <li class="breadcrumb-item"><a href="{{route('admin.course.listall')}}">Course Run</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
                <h4 class="page-title">Edit Course Run</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form id="frm_course_run_edit" action="{{ route('admin.course.edit', $data->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                   <!--  <h5 class="card-header bg-secondary text-white mt-0">Edit Course - {{ $courseMain->name }}</h5> -->
                    <div class="card-body">
                        <h4 class="header-title mt-0">Edit Course Run - {{ $courseMain->name }}</h4>
                        <div class="row">

                            <input type="hidden" name="course_main_id" value="{{$data->course_main_id}}" />
                            @if( isset($_GET['editpage']) )
                            <input type="hidden" name="editpage" value="{{$_GET['editpage']}}" />
                            @endif

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="coursetrainers">Main Trainers<span class="text-danger">*</span> </label>
                                    <select name="coursetrainers" id="coursetrainers" class="form-control select2" required placeholder="Select Trainers">
                                        @foreach( $courseMain->trainers as $trainer )
                                        <option value="{{ $trainer->id }}" {{ $trainer->id == $data->maintrainer ? 'selected' : '' }}>{{ $trainer->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('coursetrainers')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="courseassistanttrainers">Assistant Trainers </label>
                                    <select name="courseassistanttrainers[]" id="courseassistanttrainers" multiple class="form-control select2" placeholder="Select Trainers">
                                        @foreach( $courseMain->trainers as $trainer )
                                        <option value="{{ $trainer->id }}" {{ is_array($selectedTrainers) ? in_array($trainer->id, $selectedTrainers) ? 'selected' : '' : '' }}>{{ $trainer->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('courseassistanttrainers')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="modeoftraining">Mode Of Training<span class="text-danger">*</span></label>
                                    <select name="modeoftraining" required  class="form-control select2" onchange="return displaySessionByModeOftraining(this.value)">
                                        <option value="">Select Mode</option>
                                        @foreach( getModeOfTraining() as $key => $modeoftraining )
                                        <option value="{{ $key }}" {{ $data->modeoftraining == $key ? 'selected' : '' }}>{{ $modeoftraining }}</option>
                                        @endforeach
                                    </select>

                                    @error('modeoftraining')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="venue_id">Course Primary Venue <span class="text-danger">*</span></label>
                                    <select name="venue_id" id="venue_id" required class="form-control select2">
                                        <option value="">Select Venue</option>
                                        @foreach($venueslist as $venue)
                                        <option value="{{$venue->id}}" {{ $data->venue_id == $venue->id ? 'selected' : '' }}>{{$venue->street}} {{$venue->building}} - {{$venue->postal_code}}</option>
                                        @endforeach
                                    </select>
                                    @error('venue_id')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-4">
                                <div class="form-group date-ico">
                                    <label for="registration_opening_date">Registration Opening Date <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control singledate" value="{{ $data->registration_opening_date }}" name="registration_opening_date" id="registration_opening_date" placeholder="" />
                                    @error('registration_opening_date')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group date-ico">
                                    <label for="registration_closing_date">Registration Closing Date <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control singledate" value="{{ $data->registration_closing_date }}" name="registration_closing_date" id="registration_closing_date" placeholder="" />
                                    @error('registration_closing_date')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="registration_closing_time">Registration Closing Time <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" value="{{ $data->registration_closing_time }}" name="registration_closing_time" id="registration_closing_time" placeholder="" />
                                    @error('registration_closing_time')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group date-ico">
                                    <label for="course_start_date">Course Start Date <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control singledate" value="{{ $data->course_start_date }}" name="course_start_date" id="course_start_date" placeholder="" />
                                    @error('course_start_date')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="course_end_date">Course End Date <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control singledate" value="{{ $data->course_end_date }}" name="course_end_date" id="course_end_date" placeholder="" />
                                    @error('course_end_date')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="course_link">Course Link </label>
                                    <input type="text" class="form-control" value="{{ $data->course_link }}" name="course_link" id="course_link" placeholder="" />
                                    @error('course_link')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="meeting_id">Course Meeting Id </label>
                                    <input type="text" class="form-control" value="{{ $data->meeting_id }}" name="meeting_id" id="meeting_id" placeholder="" />
                                    @error('meeting_id')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="meeting_pwd">Course Meeting Password </label>
                                    <input type="text" class="form-control" value="{{ $data->meeting_pwd }}" name="meeting_pwd" id="meeting_pwd" placeholder="" />
                                    @error('meeting_pwd')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- <div class="row">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="schinfotype_code">Schedule Info Type Code<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="schinfotype_code" id="schinfotype_code" value="{{ $data->schinfotype_code }}" placeholder="">
                                    @error('schinfotype_code')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="schinfotype_desc">Schedule Info Type Description <span class="text-danger">*</span></label>
                                    <textarea id="schinfotype_desc" class="form-control" rows="3" name="schinfotype_desc">{{ $data->schinfotype_desc }}</textarea>
                                    @error('schinfotype_desc')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                        </div> --}}

                        <div class="row">

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="coursevacancy_code">Course Vacancy Code<span class="text-danger">*</span></label>
                                    <select class="form-control select2" value="{{ $data->coursevacancy_code }}" required name="coursevacancy_code" id="coursevacancy_code">
                                        <option value="">Select Vacancy Code</option>
                                        @foreach( getCourseVacancy() as $key => $vacancy )
                                        <option value="{{ $key }}" {{ $data->coursevacancy_code == $key ? 'selected' : '' }}>{{ $vacancy }}</option>
                                        @endforeach
                                    </select>
                                    @error('coursevacancy_code')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>


                            <!-- Course Run Remarks Start -->

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="course_remarks">Remarks</label>
                                    <textarea id="course_remarks" class="form-control" rows="4" cols="50" name="course_remarks">{{ $data->course_remarks }}</textarea>
                                </div>
                            </div>

                            <!-- Course Run Remarks End -->

                            {{-- <div class="col-md-6">
                                <div class="form-group">
                                    <label for="coursevacancy_desc">Course Vacancy Description <span class="text-danger">*</span></label>
                                    <textarea id="coursevacancy_desc" class="form-control" rows="3" name="coursevacancy_desc">{{ $data->coursevacancy_desc }}</textarea>
                                    @error('coursevacancy_desc')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div> --}}

                        </div>

                        {{-- <div class="row">

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="sch_info">Schedule Info <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="{{ $data->sch_info }}" required name="sch_info" id="sch_info" placeholder="" />
                                    @error('sch_info')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                        </div> --}}

                        <div class="row" id="sessiondiv">
                            <div class="col-lg-12">
                                <h4> Sessions </h4>
                                <hr />
                                <div class="repeater-custom-show-hide">
                                    <div data-repeater-list="sessions">
                                        @if( $data->session->isEmpty() )
                                        <div data-repeater-item="">
                                            <div class="form-group row ">

                                                <div class="col-md-8">
                                                    <label class="control-label">Start Date / Start Time - End Date / End Time  <span class="text-danger">*</span></label>
                                                    <div class="input-group date">
                                                    <input type="text" name="sessions[0][session_schedule]" class="daterange form-control" value="{{ old('sessions')[0]['session_schedule'] ?? "" }}" />
                                                    <span class="verti-cen input-group-addon">
                                                        <img src="{{ asset('assets/images/calendar.png') }}" alt="calender">
                                                    </span>
                                                    </div>

                                                    @error('session_schedule')
                                                        <label class="form-text text-danger">{{ $message }}</label>
                                                    @enderror
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="control-label">Session Mode <span class="text-danger">*</span></label>
                                                    <select name="sessions[0][session_mode]" required  class="form-control select2">
                                                        <option value="">Select Mode</option>
                                                        @foreach( getModeOfTraining() as $key => $modeoftraining )
                                                        <option value="{{ $key }}" {{ (old('sessions')[0]['session_mode']) ?? 'selected' ?? '' }}>{{ $modeoftraining }}</option>
                                                    @endforeach
                                                </select>
                                                </div>

                                                <div class="col-md-1 verti-cen mt-4">
                                                    <span data-repeater-delete="" class="btn btn-danger btn-sm">
                                                        <span class="far fa-trash-alt"></span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        @else
                                        @foreach ($data->session as $k => $sessionsdata)
                                        <div data-repeater-item="">
                                            <div class="form-group row">
                                                <div class="col-md-8">
                                                    <label class="control-label">Start Date / Start Time - End Date / End Time  <span class="text-danger">*</span></label>
                                                    <div class="input-group date">
                                                    <input type="text" name="sessions[{{$k}}][session_schedule]" class="daterange form-control" value="{{ $sessionsdata->session_schedule }}" />

                                                     <span class="verti-cen input-group-addon">
                                                   <img src="{{ asset('assets/images/calendar.png') }}" alt="calender">
                                                   </span>
                                                    </div>

                                                    @error('session_schedule')
                                                        <label class="form-text text-danger">{{ $message }}</label>
                                                    @enderror
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="control-label">Session Mode <span class="text-danger">*</span></label>
                                                    <select name="sessions[{{$k}}][session_mode]" required class="form-control select2">
                                                        <option value="">Select Mode</option>
                                                        @foreach( getModeOfTraining() as $key => $modeoftraining )
                                                        <option value="{{ $key }}" {{ $sessionsdata->session_mode == $key ? 'selected' : '' }}>{{$modeoftraining}} </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-1 verti-cen mt-4">
                                                    <span data-repeater-delete="" class="btn btn-danger btn-sm">
                                                        <span class="far fa-trash-alt"></span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                        @endif

                                    </div><!--end repet-list-->

                                    <div class="form-group row mb-0 text-center">
                                        <div class="col-sm-12">
                                            <span data-repeater-create="" class="btn btn-secondary btn-md reapet-add">
                                                <span class="white-add-ico"></span> Add Session
                                            </span>
                                        </div><!--end col-->
                                    </div><!--end row-->
                                </div> <!--end repeter-->
                            </div>
                        </div>
                        <hr />

                        <div class="row mt-4">

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="minintakesize">Min Intake Size <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="{{ $data->minintakesize }}" name="minintakesize" id="minintakesize" onkeypress="return isNumberKey(event)" placeholder="">
                                    @error('minintakesize')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="intakesize">Max Intake Size </label>
                                    <input type="text" class="form-control" required value="{{ $data->intakesize }}" name="intakesize" id="intakesize" onkeypress="return isNumberKey(event)" placeholder="" />
                                    @error('intakesize')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="threshold">Threshold </label>
                                    <input type="text" class="form-control" value="{{ $data->threshold }}" name="threshold" id="threshold" onkeypress="return isNumberKey(event)" placeholder="" />
                                    @error('threshold')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="publish">Status <span class="text-danger">*</span></label>
                                    <select name="publish" id="publish" class="form-control select2" data-placeholder="Select Status" {{ $data->is_published == 2 ? 'disabled' : ''}}>
                                        <option value="1" {{ $data->is_published == 1 ? 'selected' : '' }}>Published</option>
                                        <option value="0" {{ $data->is_published == 0 ? 'selected' : '' }}>Un Published</option>
                                        <option value="2" {{ $data->is_published == 2 ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                    @error('publish')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="coursefileimage">Course File Image</label>
                                    <div class="custom-file">
                                        <input type="file" accept="image/*" name="coursefileimage" class="custom-file-input" id="coursefileimage">
                                        <label class="custom-file-label" for="coursefileimage">Choose file...</label>
                                        @error('coursefileimage')
                                            <label class="form-text text-danger">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 align-self-center met-profile">
                                <div class="met-profile-main">
                                    <div class="met-profile-main-pic">
                                        <img src="{{ asset('assets/images/course/'.$data->coursefileimage) }}" onerror="data:image/png;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" alt="course-img" id="course-img" class="rounded-circle w-100">
                                    </div>
                                </div>
                            </div>
                        </div> --}}

                    </div><!--end card-body-->
                    <div class="card-footer m-0 clearfix">
                        <button type="submit" class="btn btn-primary mar-r-10">Update</button>
                        <a href="{{ route('admin.course.list', $courseMain->id) }}" class="btn btn-danger">Cancel</a>

                        <?php if($data->is_published != 2) : ?>
                        <a class="btn  btn-danger cancelcourserun float-right" courserun_id="{{$data->id}}" href="javascript:void(0)" ><i class="fas fa-times-circle"></i> Cancel Course Run</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div><!--end card-->
        </div> <!--end col-->
    </div><!--end row-->
    <!-- Ajax loader for cancel course run -->
    <div class="ajax-loader"><div class="loader-center"><div class="tms_loader"></div></div></div>
    <!-- Ajax loader for cancel course run -->
</div><!-- container -->
@endsection
@push("scripts")
<script src="{{ asset('assets/plugins/repeater/jquery.repeater.min.js') }}"></script>
<script src="{{ asset('assets/plugins/daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('assets/plugins/clockpicker/jquery-clockpicker.min.js') }}"></script>
<script src="{{ asset('assets/plugins/sweet-alert2/sweetalert2.min.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        
        /* Delete Course Run Start */
        @include('admin.partial.actions.cancelcourserun');    
        /* Delete Course Run End */

        $(".select2").select2({ width: '100%' });
        $('.repeater-custom-show-hide').repeater({
            @if( $data->session->isEmpty() )
            initEmpty: true,
            @endif
            isFirstItemUndeletable: true,
            show: function () {
                $(this).slideDown();
                $('.select2-container').remove();
                $('.select2').select2({
                    width: '100%',
                    // placeholder: "Placeholder text",
                    // allowClear: true
                });
                initDateAndTimePicker();
            },
            hide: function (remove) {
              if (confirm('Are you sure you want to remove this item?')) {
                $(this).slideUp(remove);
              }
            }
        });

        $('#coursefileimage').change(function(ee) {
            readURL(this,'course-img');
        });

    });

    function initDateAndTimePicker() {
        $('.singledate').daterangepicker({
            locale: {
                format: 'Y-M-DD'
            },
            singleDatePicker: true,
            showDropdowns: true,
            // minDate: new Date(),
            minYear: 2019,
        });

        /*$('.daterange').each(function() {
            let _val = $(this).val();
            $(this).daterangepicker({
                timePicker: true,
                locale: {
                    format: 'Y/M/DD hh:mm A'
                }
            });
        });*/

        $('.daterange').daterangepicker({
            timePicker: true,
            locale: {
              format: 'Y/M/DD hh:mm A'
            }
        });

        /* $('.clockpickerinput').clockpicker({
            autoclose: true
        }); */
    }

    function displaySessionByModeOftraining(modeOfTrainingID)
    {
        if(modeOfTrainingID == "2" || modeOfTrainingID == "4") {  $("#sessiondiv").hide();  }
        else {  $("#sessiondiv").show();  }
    }

    initDateAndTimePicker();
</script>
@endpush
