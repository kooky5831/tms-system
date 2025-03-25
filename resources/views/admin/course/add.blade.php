@extends('admin.layouts.master')
@section('title', 'Add Course Run')
@push('css')
<link href="{{ asset('assets/plugins/daterangepicker/daterangepicker.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/clockpicker/jquery-clockpicker.min.css') }}" rel="stylesheet" type="text/css" />

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
                        <li class="breadcrumb-item active">Add</li>
                    </ol>
                </div>
                <h4 class="page-title">Course Run</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form action="{{ route('admin.course.add', $courseMain->id) }}" id="courserun_add" method="POST" enctype="multipart/form-data">
                    @csrf
                    <!-- <h5 class="card-header bg-secondary text-white mt-0">Add Course Run - {{ $courseMain->name }}</h5> -->
                    <div class="card-body">
                        <h4 class="header-title mt-0">Add Course Run - {{ $courseMain->name }}</h4>
                        <div class="row">

                            <input type="hidden" name="course_main_id" value="{{$courseMain->id}}">

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="coursetrainers">Main Trainer<span class="text-danger">*</span> </label>
                                    <select name="coursetrainers" id="coursetrainers" class="form-control select2" placeholder="Select Trainers">
                                        @foreach( $courseMain->trainers as $trainer )
                                        <option value="{{ $trainer->id }}" {{ old('coursetrainers') ? $trainer->id == old('coursetrainers') ? 'selected' : '' : '' }}>{{ $trainer->name }}</option>
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
                                        <option value="{{ $trainer->id }}" {{ is_array(old('courseassistanttrainers')) ? in_array($trainer->id, old('courseassistanttrainers')) ? 'selected' : '' : '' }}>{{ $trainer->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('courseassistanttrainers')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                            <?php $defaultMode = $courseMain->course_mode_training == 'online' ? 2 : 1; ?>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="modeoftraining">Mode Of Training<span class="text-danger">*</span></label>
                                    <select name="modeoftraining" id="modeoftraining" required  class="form-control select2" onchange="return displaySessionByModeOftraining(this.value)">
                                        <option value="">Select Mode</option>
                                        @foreach( getModeOfTraining() as $key => $modeoftraining )
                                        <option value="{{ $key }}" {{ old('modeoftraining') || $defaultMode == $key ? 'selected' : '' }}>{{ $modeoftraining }}</option>
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
                                    <select name="venue_id" id="venue_id" class="form-control select2">
                                        <option value="">Select Venue</option>
                                        @foreach($venueslist as $venue)
                                        <option value="{{$venue->id}}" {{ old('venue_id') == $venue->id ? 'selected' : ''  }}>{{$venue->street}} {{$venue->building}} - {{$venue->postal_code}}</option>
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
                                    <input type="text" class="form-control" value="{{ old('registration_opening_date') }}" name="registration_opening_date" id="registration_opening_date" placeholder="" />
                                    @error('registration_opening_date')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group date-ico">
                                    <label for="registration_closing_date">Registration Closing Date <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="{{ old('registration_closing_date') }}" name="registration_closing_date" id="registration_closing_date" placeholder="" />
                                    @error('registration_closing_date')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="registration_closing_time">Registration Closing Time <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" value="{{ old('registration_closing_time') ?? "18:00:00" }}" name="registration_closing_time" id="registration_closing_time" placeholder="" />
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
                                    <input type="text" class="form-control" value="{{ old('course_start_date') }}" name="course_start_date" id="course_start_date" placeholder="" />
                                    @error('course_start_date')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group date-ico">
                                    <label for="course_end_date">Course End Date <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="{{ old('course_end_date') }}" name="course_end_date" id="course_end_date" placeholder="" />
                                    @error('course_end_date')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="course_link">Course Link </label>
                                    <input type="text" class="form-control" value="{{ old('course_link') }}" name="course_link" id="course_link" placeholder="" />
                                    @error('course_link')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="meeting_id">Course Meeting Id </label>
                                    <input type="text" class="form-control" value="{{ old('meeting_id') }}" name="meeting_id" id="meeting_id" placeholder="" />
                                    @error('meeting_id')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="meeting_pwd">Course Meeting Password </label>
                                    <input type="text" class="form-control" value="{{ old('meeting_pwd') }}" name="meeting_pwd" id="meeting_pwd" placeholder="" />
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
                                    <input type="text" class="form-control" value="{{ old('schinfotype_code') ? old('schinfotype_code') : '01' }}" name="schinfotype_code" id="schinfotype_code" placeholder="">
                                    @error('schinfotype_code')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="schinfotype_desc">Schedule Info Type Description <span class="text-danger">*</span></label>
                                    <textarea id="schinfotype_desc" class="form-control" rows="3" name="schinfotype_desc">{{ old('schinfotype_desc') }}</textarea>
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
                                    <select class="form-control select2" value="{{ old('coursevacancy_code') }}" required name="coursevacancy_code" id="coursevacancy_code">
                                        <option value="">Select Vacancy Code</option>
                                        @foreach( getCourseVacancy() as $key => $vacancy )
                                        <option value="{{ $key }}" {{ (old('coursevacancy_code') == $key ? 'selected' : $key == 'A') ? 'selected' : '' }}>{{ $vacancy }}</option>
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
                                    <textarea id="course_remarks" class="form-control" rows="4" cols="50" name="course_remarks">{{ old('course_remarks') }}</textarea>
                                </div>
                            </div>

                            <!-- Course Run Remarks End -->

                            <!-- <div class="row"> -->
                            {{-- <div class="col-md-6">
                                <div class="form-group">
                                    <label for="coursevacancy_desc">Course Vacancy Description <span class="text-danger">*</span></label>
                                    <textarea id="coursevacancy_desc" class="form-control" rows="3" name="coursevacancy_desc">{{ old('coursevacancy_desc') }}</textarea>
                                    @error('coursevacancy_desc')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                           <!--  </div> --> --}}

                        </div>

                        {{-- <div class="row">

                           <div class="col-md-12">
                                <div class="form-group">
                                    <label for="sch_info">Schedule Info <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="{{ old('sch_info') }}" required name="sch_info" id="sch_info" placeholder="" />
                                    @error('sch_info')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                        </div> --}}

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Add Session - Auto Generate</label>
                                    <span class="btn btn-danger btn-sm" id="sessionaddbtn">
                                        <span class="add-new"></span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="row" id="sessiondiv">
                            <div class="col-lg-12">
                                <h4> Sessions </h4>
                                <hr />
                                <div class="repeater-custom-show-hide">
                                    <div data-repeater-list="sessions">
                                        <div data-repeater-item="">
                                            <div class="form-group row ">

                                                <div class="col-md-8">
                                                    <label class="control-label">Start Date / Start Time - End Date / End Time  <span class="text-danger">*</span></label>
                                                    <div class="input-group date">
                                                        <input type="text" name="sessions[0][session_schedule]" class="daterange form-control" value="{{ (old('sessions')[0]['session_schedule']) ?? old('sessions')[0]['session_schedule'] ?? '' }}" />
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
                                                    <?php $defaultMode = $courseMain->course_mode_training == 'online' ? 2 : 1; ?>
                                                    <select name="sessions[0][session_mode]" class="form-control select2 session_mode">
                                                        <option value="">Select Mode</option>
                                                        @foreach( getModeOfTraining() as $key => $modeoftraining )
                                                        <option value="{{ $key }}" 
                                                        {{ $defaultMode == $key  ? 'selected' : '' }}
                                                        
                                                        >{{$modeoftraining}} </option>
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

                                    </div><!--end repet-list-->

                                    <div class="form-group row mb-0 text-center">
                                        <div class="col-sm-12">
                                            <span data-repeater-create="" class="btn btn-secondary btn-md reapet-add">
                                                <span class="white-add-ico"></span> Add Another Session
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
                                    <input type="text" class="form-control" required value="{{ old('minintakesize') }}" name="minintakesize" id="minintakesize" onkeypress="return isNumberKey(event)" placeholder="">
                                    @error('minintakesize')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="intakesize">Max Intake Size <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" required value="{{ old('intakesize') }}" name="intakesize" id="intakesize" onkeypress="return isNumberKey(event)" placeholder="">
                                    @error('intakesize')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="threshold">Threshold </label>
                                    <input type="text" class="form-control" value="{{ old('threshold') }}" name="threshold" id="threshold" onkeypress="return isNumberKey(event)" placeholder="">
                                    @error('threshold')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="publish">Status <span class="text-danger">*</span></label>
                                    <select name="publish" id="publish" class="form-control select2" data-placeholder="Select Status">
                                        <option value="1" {{ old('publish') == 1 ? 'selected' : '' }}>Published</option>
                                        <option value="0" {{ old('publish') == 0 ? 'selected' : '' }}>Un Published</option>
                                        <option value="2" {{ old('publish') == 2 ? 'selected' : '' }}>Cancelled</option>
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
                                        <img src="data:image/png;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" alt="profile-img" id="profile-img" class="rounded-circle w-100 d-none">
                                    </div>
                                </div>
                            </div>
                        </div> --}}
                    </div>

                    <!-- </div> -->
                    <div class="card-footer m-0 clearfix">
                        <button type="submit" class="btn btn-primary mar-r-10">Submit</button>
                        <a href="{{ route('admin.course.list', $courseMain->id) }}" class="btn btn-danger">Cancel</a>
                    </div>
                </form>
            </div><!--end card-->
        </div> <!--end col-->
    </div><!--end row-->

</div><!-- container -->
@endsection
@push("scripts")
<script src="{{ asset('assets/plugins/repeater/jquery.repeater.min.js') }}"></script>
<script src="{{ asset('assets/plugins/daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('assets/plugins/clockpicker/jquery-clockpicker.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.validate.min.js') }}"></script>

<script type="text/javascript">

    $('#courserun_add').validate({
        ignore: 'input[type=hidden], .select2-search__field', // ignore hidden fields
        // Different components require proper error label placement
        errorPlacement: function (error, element) {
        // Unstyled checkboxes, radios
        if (element.parents().hasClass('form-check')) {
            error.appendTo(element.parents('.form-check').parent());
        } else if (element.attr("name") == "modeoftraining") {
            error.insertAfter($(element).next('span'));
        } else if (element.attr("name") == "venue_id") {
            error.insertAfter($(element).next('span'));
        } else if (element.attr("name") == "coursetrainers") {
            error.insertAfter($(element).next('span'));
        } else if (element.attr("name") == "coursevacancy_code") {
            error.insertAfter($(element).next('span'));
        } 
        // Other elements
        else {
            error.insertAfter(element);
        }
        },
        rules: {
            name: {
                required: true,
            },
            modeoftraining: {
                required: true,
            },
            venue_id: {
                required: true,
            },
            coursetrainers: {
                required: true,
            },
            registration_opening_date: {
                required: true,
            },
            registration_closing_date: {
                required: true,
            },
            course_start_date: {
                required: true,
            },
            course_end_date: {
                required: true,
            },
            registration_closing_time: {
                required: true,
            },
            coursevacancy_code: {
                required: true,
            }
        },
        messages: {
            name: {
                required: "Name is required.",
            },
            modeoftraining: {
                required: "Please select mode of training.",
            },
            venue_id: {
                required: "Please select venue.",
            },
            coursetrainers: {
                required: "Main trainer is required.",
            },
            registration_opening_date: {
                required: "Registration opening date is required",
            },
            registration_closing_date: {
                required: "Registration closing date required.",
            },
            course_start_date: {
                required: "Course start date is required.",
            },
            course_end_date: {
                required: "Course end date is required.",
            },
            registration_closing_time: {
                required: "Registration closing time is required.",
            }
        }
    });
 
    var $repeater = $('.repeater-custom-show-hide').repeater({
        initEmpty: true,
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

    function setRepeter() {

        let courseStartDate = moment($('#course_start_date').val(), "YYYY-MM-DD");
        let courseEndDate = moment($('#course_end_date').val(), "YYYY-MM-DD");

        let courseDays = courseEndDate.diff(courseStartDate, 'days');

        let sessionData = {
            sessions: []
        };
        let startDateFirstHalf = "", endDateFirstHalf = "";
        let startDateSecondHalf = "", endDateSecondHalf = "";
        for (let i = 0; i <= courseDays; i++) {
            
            // add first half session
            startDateFirstHalf = moment(courseStartDate).hours(9).minutes(0).seconds(0).milliseconds(0).format('Y/M/DD hh:mm A');
            endDateFirstHalf = moment(courseStartDate).hours(13).minutes(0).seconds(0).milliseconds(0).format('Y/M/DD hh:mm A');
            sessionData.sessions.push({
                session_schedule: startDateFirstHalf + " - " + endDateFirstHalf,
            })
            // add another half day session
            startDateSecondHalf = moment(courseStartDate).hours(14).minutes(0).seconds(0).milliseconds(0).format('Y/M/DD hh:mm A');
            endDateSecondHalf = moment(courseStartDate).hours(18).minutes(0).seconds(0).milliseconds(0).format('Y/M/DD hh:mm A');
            sessionData.sessions.push({
                session_schedule: startDateSecondHalf + " - " + endDateSecondHalf,
            })
            courseStartDate.add('days', 1);
            
        }

        if( sessionData.sessions.length < 1 ) {
            return false;
        }

        // var myJson = '{"group-a":[{"input-grocery-list-item":"Apple","input-grocery-list-item-quantity":"3"}]}';
        // var myObj = JSON.parse(myJson);

        $repeater.setList(sessionData["sessions"]);
        $('#courserun_add .session_mode').val(1).trigger('change');
        
        
    }

    $(document).ready(function() {
        $(".select2").select2({ width: '100%' });
        $('#coursefileimage').change(function(ee) {
            readURL(this,'profile-img');
        });

        // $(document).on('click', '#courserun_add', function(e) {

        // })

        // check if this course code is available or not
        $(document).on('blur', '#course_code', function(e) {
            e.preventDefault();
        });

        $(document).on('click', '#sessionaddbtn', function(e) {
            e.preventDefault();
            addSessionFields();
        });

        @if( old('modeoftraining') != null && old('modeoftraining') != 2 && old('modeoftraining') != 4 )
            addSessionFields();
        @endif

    });

    function addSessionFields() {
        // create session according to course start date and end date
        setRepeter();
    }

    function initDateAndTimePicker() {
        $('.singledate').daterangepicker({
            locale: {
                format: 'Y-M-DD'
            },
            singleDatePicker: true,
            showDropdowns: true,
            minDate: new Date(),
            minYear: 2019,
        });
        $('#course_start_date, #course_end_date').daterangepicker({
            locale: {
                format: 'Y-M-DD'
            },
            singleDatePicker: true,
            showDropdowns: true,
            minDate: new Date(),
            minYear: 2019,
        });

        $('#registration_opening_date').daterangepicker({
            locale: {
                format: 'Y-M-DD'
            },
            singleDatePicker: true,
            showDropdowns: true,
            minDate: new Date(),
            minYear: 2019,
        });

        $('#registration_opening_date').on('change', function(ev, picker) {
            // do something, like clearing an input
            $('#registration_closing_date').daterangepicker({
                locale: {
                    format: 'Y-M-DD'
                },
                singleDatePicker: true,
                showDropdowns: true,
                minDate: moment($('#registration_opening_date').val(), "YYYY-MM-DD").add(1, 'd'),
                minYear: 2019,
            });

            $('#course_start_date').daterangepicker({
                locale: {
                    format: 'Y-M-DD'
                },
                singleDatePicker: true,
                showDropdowns: true,
                minDate: moment($('#registration_closing_date').val(), "YYYY-MM-DD"),
                minYear: 2019,
            });
            $('#course_end_date').daterangepicker({
                locale: {
                    format: 'Y-M-DD'
                },
                singleDatePicker: true,
                showDropdowns: true,
                minDate: moment($('#course_start_date').val(), "YYYY-MM-DD"),
                minYear: 2019,
            });
        });

        $('#course_start_date').on('change', function(ev, picker) {
            $('#course_end_date').daterangepicker({
                locale: {
                    format: 'Y-M-DD'
                },
                singleDatePicker: true,
                showDropdowns: true,
                minDate: moment($('#course_start_date').val(), "YYYY-MM-DD"),
                minYear: 2019,
            });
        });

        $('#registration_closing_date').daterangepicker({
            locale: {
                format: 'Y-M-DD'
            },
            singleDatePicker: true,
            showDropdowns: true,
            minDate: new Date(),
            minYear: 2019,
        });

        $('#registration_closing_date').on('change', function(ev, picker) {
            $('#course_start_date').daterangepicker({
                locale: {
                    format: 'Y-M-DD'
                },
                singleDatePicker: true,
                showDropdowns: true,
                minDate: moment($('#registration_closing_date').val(), "YYYY-MM-DD"),
                minYear: 2019,
            });
            $('#course_end_date').daterangepicker({
                locale: {
                    format: 'Y-M-DD'
                },
                singleDatePicker: true,
                showDropdowns: true,
                minDate: moment($('#course_start_date').val(), "YYYY-MM-DD"),
                minYear: 2019,
            });
        });

        $('.datetimepickerstart').daterangepicker({
            timePicker: true,
            singleDatePicker: true,
            showDropdowns: true,
            startDate: moment().hours(9).minutes(0).seconds(0).milliseconds(0),
            locale: {
              format: 'Y/M/DD hh:mm A'
            },
            minDate: new Date(),
            minYear: 2020,
        });

        $('.datetimepickerend').daterangepicker({
            timePicker: true,
            singleDatePicker: true,
            showDropdowns: true,
            startDate: moment().hours(18).minutes(0).seconds(0).milliseconds(0),
            locale: {
              format: 'Y/M/DD hh:mm A'
            },
            minDate: new Date(),
            minYear: 2020,
        });

        $('.daterange').daterangepicker({
            timePicker: true,
            // startDate: moment($('#course_start_date').val(), "YYYY-MM-DD").hours(9).minutes(0).seconds(0).milliseconds(0).format('Y/M/DD hh:mm A'),
            // endDate: moment($('#course_start_date').val(), "YYYY-MM-DD").hours(13).minutes(0).seconds(0).milliseconds(0).format('Y/M/DD hh:mm A'),
            minDate: new Date(),
            locale: {
              format: 'Y/M/DD hh:mm A'
            }
        });

        /*$('.clockpickerinput').clockpicker({
            autoclose: true
        });*/
    }

    function displaySessionByModeOftraining(modeOfTrainingID)
    {
        if(modeOfTrainingID == "2" || modeOfTrainingID == "4") {  $("#sessiondiv").hide();  }
        else {  $("#sessiondiv").show();  }
    }

    $('#modeoftraining').trigger('change');

    initDateAndTimePicker();

</script>
@endpush
