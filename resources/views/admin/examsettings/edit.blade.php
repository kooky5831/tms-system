@extends('admin.layouts.master')
@section('title', 'Edit Exam')
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
                        <li class="breadcrumb-item"><a href="">Exam</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
                <h4 class="page-title">Edit Exam</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <form action="{{ route('admin.assessments.exam-settings.edit', $examMainData->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="is_assigned" name="is_assigned" value="{{ $examMainData->is_assigned }}">
                    <div class="card-body">
                        <h4 class="header-title mt-0">Exam Details</h4>
                        <div class="row">
                            <div class="col-md-12">
                                <label for="course_main_id">Select Main Course<span class="text-danger">*</span></label>
                                <select name="course_main_id[]" id="course_main_id" class="form-control select2" multiple>
                                    @foreach ($allTrainerCourseRuns as $trainerCourse)
                                        <option value="{{ $trainerCourse->course_main_id }}" {{ is_array($getCourseMain) ? in_array($trainerCourse->course_main_id, $getCourseMain) ? 'selected' : '' : '' }}>{{ $trainerCourse->courseMain->name }}</option>
                                    @endforeach
                                </select>
                                @error('course_id')
                                    <label class="form-text text-danger">{{ $message }}</label>
                                @enderror
                            </div>
                        </div>
                        {{-- <div class="row">
                            <div class="col-md-6 mt-2">
                                <label for="appt">Exam Start time</label>
                                <input type="time" id="assessment_time" name="assessment_time" class="form-control" value="{{ $examMainData->assessment_time }}">
                                @error('assessment_time')
                                    <label class="form-text text-danger">{{ $message }}</label>
                                @enderror
                            </div>
                            <div class="col-md-6 mt-2">
                                <label for="appt">Exam Duration (HH:MM)</label>
                                <input type="text" id="durationInput"  name="assessment_duration" class="form-control" value="{{ $examMainData->assessment_duration }}">
                                <span id="durationDisplay"></span>
                                <span id="durationErrorDisplay" class="text-danger"></span>
                                @error('assessment_duration')
                                    <label class="form-text text-danger">{{ $message }}</label>
                                @enderror                                
                            </div>
                        </div> --}}

                        <div class="card-body" style="margin-bottom: -60px; margin-left: -10px;">
                            <div class="row">
                                <h4 class="header-title mt-0">Assessment Details</h4>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mt-4 repeater-custom-show-hide">
                                    <div data-repeater-list="assessments">
                                        @if( !$getAssessmentData->isEmpty() )
                                            @foreach ($getAssessmentData as $k => $assessment)
                                                <div data-repeater-item="">
                                                    <div class="form-group row ">
                                                        <div class="col-md-3">
                                                            <label class="control-label">Assessment Name</label>
                                                            <div class="input-group">
                                                                <input type="text" name="assessments[{{$k}}][title]" class="form-control" value="{{ $assessment->title }}" />
                                                                <input type="hidden" name="assessments[{{$k}}][id]" class="form-control" value="{{ $assessment->id }}" />
                                                            </div>
                                                        </div>

                                                        <div class="col-md-2">
                                                            <label class="control-label">Assessment Generate On</label>
                                                            <div class="input-group">
                                                                <select name="assessments[{{$k}}][date_option]" class="form-control date_option select2" id="">
                                                                    <option value="">Select Option</option>
                                                                    <option value="1" {{$assessment->date_option == '1' ? 'selected' : ''}}>Start Date</option>
                                                                    <option value="2" {{$assessment->date_option == '2' ? 'selected' : ''}}>End Date</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3">
                                                            <label for="appt">Assessment Start time</label>
                                                            <input type="time" id="assessment_time" name="assessments[{{$k}}][assessment_time]" class="form-control assessment_time" value="{{ $assessment->assessment_time }}">
                                                        </div>

                                                        <div class="col-md-2">
                                                            <label for="appt">Assessment Duration (HH:MM)</label>
                                                            <input type="text" id="durationInput"  name="assessments[{{$k}}][assessment_duration]" class="form-control durationInput" value="{{ $assessment->assessment_duration }}">
                                                            {{-- <span id="durationDisplay" class="durationDisplay"></span> --}}
                                                            {{-- <span id="durationErrorDisplay" class="text-danger durationErrorDisplay"></span> --}}
                                                        </div>
                                                        
                                                        <div class="col-md-1">
                                                            <label>Trainee Access</label>
                                                            <label class="customcheck">
                                                                <input type="checkbox" class="" name="assessments[{{$k}}][trainee_view_access]" 
                                                                @if($assessment->trainee_view_access == 1) checked @endif>
                                                            <span class="checkmark"></span>
                                                            </label>
                                                        </div>
                                                        {{-- <div class="col-md-1 verti-cen mt-4">
                                                            <span data-repeater-delete="" class="btn btn-danger btn-sm">
                                                                <span class="far fa-trash-alt"></span>
                                                            </span>
                                                        </div> --}}
                                                    </div>
                                                    
                                                </div>
                                            @endforeach
                                        @endif
                                        
                                    </div><!--end repet-list-->
                                </div> <!--end repeter-->
                            </div>
                        </div>
                    </div>
                    <div class="card-footer m-0 clearfix">
                        <button type="submit" class="btn btn-primary mar-r-10">Submit</button>
                        <a href="{{ route('admin.assessments.exam-settings.list') }}" class="btn btn-danger">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div><!-- container -->
@endsection
@push('scripts')
<script src="{{ asset('assets/plugins/repeater/jquery.repeater.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script type="text/javascript">


function formatResult(opt) {
    if (!opt.id) {
        return opt.text;
    }
    var $opt = $( '<span record="'+opt+'">'+ opt.text + '</span>');
    if( opt.coursetype != 1 ) {
        // hide all
        $('.onlysingle').addClass('d-none');
        $('.toggleRequired').prop('required', false);
    } else {
        // unhide all
        $('.onlysingle').removeClass('d-none');
        $('.toggleRequired').prop('required', true);
    }
    return $opt;
};

function formatSelection(opt) {
    return opt.text;
}

function initCourseSearch() {
    $(".select2").select2({ width: '100%' });

    $(".f2f_course_id").select2({
        placeholder: 'Search Courses',
        // multiple: true,
        // minimumInputLength: 3,
        width: '100%',
        templateResult: formatResult,
        templateSelection: formatSelection,
        ajax: {
            url: "{{ route('admin.ajax.search.courseruns') }}",
            type: "get",
            dataType: "JSON",
            data: function (params) {
                return { q: params.term, /*search term*/ };
            },
            processResults: function (response) {
                return { results: response };
            },
            delay: 250,
            cache: true
        },
    });
}

$(document).ready(function() {
    initCourseSearch();

    const durationInput = $("#durationInput");
    const durationErrorDisplay = $("#durationErrorDisplay");
    const durationDisplay = $("#durationDisplay");

    durationInput.on("input", updateDuration);

    function updateDuration() {
        const input = durationInput.val();
        const regex = /^(\d{1,2}):(\d{2})$/;
        const match = regex.exec(input);

        if (match) {
            const hours = parseInt(match[1]) || 0;
            const minutes = parseInt(match[2]) || 0;
            const totalMinutes = hours * 60 + minutes;

            if (totalMinutes >= 0) {
                durationDisplay.text(`Selected Duration: ${hours} hours and ${minutes} minutes`);
                durationErrorDisplay.hide();
                durationDisplay.show();
            } else {
                durationDisplay.text("Please enter a valid duration (HH:MM).");
                durationErrorDisplay.hide();
                durationDisplay.show();
            }
        } else {
            durationErrorDisplay.text("Please enter a valid duration (HH:MM).");
            durationDisplay.hide();
            durationErrorDisplay.show();
        }
    }
});

</script>
@endpush