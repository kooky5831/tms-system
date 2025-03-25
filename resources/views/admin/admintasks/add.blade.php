@extends('admin.layouts.master')
@section('title', 'Add Course Trigger')
@push('css')
<link href="{{ asset('assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
@endpush
@section('content')
<div class="container-fluid">
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Course Trigger</a></li>
                        <li class="breadcrumb-item active">Add</li>
                    </ol>
                </div>
                <h4 class="page-title">Add Course Trigger</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form action="{{ route('admin.coursetrigger.add') }}" method="POST" id="coursecoursetriggeradd" enctype="multipart/form-data">
                    @csrf
                    <!-- <h5 class="card-header bg-secondary text-white mt-0">Add Course</h5> -->
                    <div class="card-body">
                        <h4 class="header-title mt-0">Add Course Trigger</h4>
                        <div class="row">

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="event_when">Event When <span class="text-danger">*</span></label>
                                    <select name="event_when" id="event_when" class="form-control">
                                        <option value="">Select Event When</option>
                                        @foreach(triggerEventWhen() as $eventWhenKey => $eventWhen)
                                        <option value="{{$eventWhenKey}}" {{ old('event_when') == $eventWhenKey ? 'selected' : '' }}>{{ ucwords($eventWhen) }}</option>
                                        @endforeach
                                    </select>
                                    @error('event_when')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3" style="{{old('event_when') != 1 ? 'display:none;' : '' }}" id="noOfDaysDiv">
                                <label for="no_of_days">No Of Days <span class="text-danger">*</span></label>
                                <input type="text" name="no_of_days" id="no_of_days" class="form-control" value="{{ old('no_of_days') }}" onkeypress="return isNumberKey(event)" />
                                @error('no_of_days')
                                    <label class="form-text text-danger">{{ $message }}</label>
                                @enderror
                            </div>

                            <div class="col-md-3" style="{{old('event_when') != 2 ? 'display:none;' : '' }}" id="dateInMonthDiv">
                                <label for="date_in_month">Date in Month <span class="text-danger">*</span></label>
                                <select name="date_in_month" id="date_in_month" class="form-control select2">
                                    <option value="">Select Date in Month</option>
                                    @for($i = 1; $i < 29; $i++)
                                    <option value="{{$i}}" {{ old('date_in_month') == $i ? 'selected' : '' }}>{{ $i }} </option>
                                    @endfor
                                </select>
                                @error('date_in_month')
                                    <label class="form-text text-danger">{{ $message }}</label>
                                @enderror
                            </div>

                            <div class="col-md-3" style="{{old('event_when') != 3 ? 'display:none;' : '' }}" id="dayOfWeekDiv">
                                <label for="day_of_week">Day of Week <span class="text-danger">*</span></label>
                                <select name="day_of_week" id="day_of_week" class="form-control select2">
                                    <option value="">Select Day of Week</option>
                                    @foreach(getDaysOfWeek() as $dayKey => $dayName)
                                    <option value="{{$dayKey}}" {{ old('day_of_week') == $dayKey ? 'selected' : '' }}>{{ $dayName }}</option>
                                    @endforeach
                                </select>
                                @error('day_of_week')
                                    <label class="form-text text-danger">{{ $message }}</label>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="event_type">Event Type <span class="text-danger">*</span></label>
                                    <select name="event_type" id="event_type" class="form-control">
                                        <option value="">Select Event Type</option>
                                        @foreach(triggerEventTypes() as $eventTypeKey => $eventType)
                                        <option value="{{$eventTypeKey}}" {{ old('event_type') == $eventTypeKey ? 'selected' : '' }}>{{ ucwords($eventType) }}</option>
                                        @endforeach
                                    </select>
                                    @error('event_type')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="coursemain">Select Course <span class="text-danger">*</span></label>
                                    <select name="coursemain[]" id="coursemain" multiple class="form-control select2">
                                        <option value="">Select Course</option>
                                        @foreach($courseMains as $courseMain)
                                        <option value="{{$courseMain->id}}" {{ is_array(old('coursemain')) ? in_array($courseMain->id, old('coursemain')) ? 'selected' : '' : '' }}>{{ $courseMain->name }} - {{ $courseMain->reference_number }}</option>
                                        @endforeach
                                    </select>
                                    @error('coursemain')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12" style="{{old('event_type') != 3 && empty(old('task_text')) ? 'display:none;' : '' }} {{old('event_type') == 3 ? '' : 'display:none;' }}" id="taskTextDiv">
                                <label for="task_text">Task Text <span class="text-danger">*</span></label>
                                <input name="task_text" id="task_text" class="form-control" value="{{ old('task_text') }}" />
                                @error('task_text')
                                    <label class="form-text text-danger">{{ $message }}</label>
                                @enderror
                            </div>


                            <div class="col-md-12" style="{{old('event_type') != 1 && empty(old('template_name')) ? 'display:none;' : '' }} {{old('event_type') == 1 ? '' : 'display:none;' }}" id="emailTemplateDiv">
                                <label for="template_name">Select Email Template <span class="text-danger">*</span></label>
                                <select name="template_name" id="template_name" class="form-control select2">
                                    <option value="">Select Email Template</option>
                                    @foreach($templates->all() as $template)
                                    <option value="{{$template->template_name}}__!!__{{$template->template_slug}}" {{ old('template_name') == $template->template_name."__!!__".$template->template_slug ? 'selected' : '' }}>{{ ucwords($template->template_name) }} - {{ $template->template_description }} </option>
                                    @endforeach
                                </select>
                                @error('template_name')
                                    <label class="form-text text-danger">{{ $message }}</label>
                                @enderror
                            </div>

                            <div class="col-md-12" style="{{old('event_type') != 2 && empty(old('sms_template')) ? 'display:none;' : '' }} {{old('event_type') == 2 ? '' : 'display:none;' }}" id="smsTemplateDiv">
                                <label for="sms_template">Select SMS Template <span class="text-danger">*</span></label>
                                <select name="sms_template" id="sms_template" class="form-control select2">
                                    <option value="">Select SMS Template</option>
                                    @foreach($smsTemplates as $smstemplate)
                                    <option value="{{$smstemplate->id}}" {{ old('sms_template') == $smstemplate->id ? 'selected' : '' }}>{{ ucwords($smstemplate->name) }} - {{ $smstemplate->description }} </option>
                                    @endforeach
                                </select>
                                @error('sms_template')
                                    <label class="form-text text-danger">{{ $message }}</label>
                                @enderror
                            </div>

                            {{-- <div class="col-md-3">
                                <div class="form-group">
                                    <label for="course_type">Course Type<span class="text-danger">*</span></label>
                                    <select name="course_type" id="course_type" required  class="form-control">
                                        <option value="">Select Course Type</option>
                                        @foreach( getCourseType() as $key => $courseType )
                                        <option value="{{ $key }}" {{ old('course_type') == $key ? 'selected' : '' }}>{{ $courseType }}</option>
                                        @endforeach
                                    </select>

                                    @error('course_type')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div> --}}
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <div class="custom-control custom-switch switch-success">
                                        <input type="checkbox" name="status" class="custom-control-input" id="status" {{ old('status') ? 'checked' : 'checked' }}>
                                        <label class="custom-control-label" for="status">Status</label>
                                    </div>
                                    @error('status')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>

                    </div>

                   <!--  </div> -->
                    <div class="card-footer m-0 clearfix">
                        <button type="submit" class="btn btn-primary mar-r-10">Submit</button>
                        <a href="{{ route('admin.coursetrigger.list') }}" class="btn btn-danger">Cancel</a>
                    </div>
                </form>
            </div><!--end card-->
        </div> <!--end col-->
    </div><!--end row-->

</div><!-- container -->
@endsection
@push("scripts")
<script src="{{ asset('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {

        $(".select2").select2({ width: '100%' });

        $(document).on('change', '#event_when', function(e) {
            let _val = $(this).val();
            $('#no_of_days').val('');
            $('#date_in_month').val('');
            $('#day_of_week').val('');
            if( _val == 1 ) {
                $('#dateInMonthDiv').hide();
                $('#dayOfWeekDiv').hide();
                $('#noOfDaysDiv').show();
            } else if( _val == 2 ) {
                $('#dayOfWeekDiv').hide();
                $('#noOfDaysDiv').hide();
                $('#dateInMonthDiv').show();
            } else if ( _val == 3 ) {
                $('#dateInMonthDiv').hide();
                $('#noOfDaysDiv').hide();
                $('#dayOfWeekDiv').show();
            }
            $(".select2").select2({ width: '100%' });
        });

        $(document).on('change', '#event_type', function(e) {
            let _val = $(this).val();
            $('#smsTemplateDiv').hide();
            $('#sms_template').val('');
            $('#taskTextDiv').hide();
            $('#task_text').val('');
            $('#emailTemplateDiv').hide();
            $('#template_name').val('');

            if( _val == 1 ) {
                $('#emailTemplateDiv').show();
            } else if( _val == 2 ) {
                $('#smsTemplateDiv').show();
            } else if( _val == 3 ) {
                $('#taskTextDiv').show();
            }
            $(".select2").select2({ width: '100%' });
        });
    });
</script>
@endpush
