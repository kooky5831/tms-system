@extends('admin.layouts.master')
@section('title', 'Add Course Refresher')
@section('content')
<div class="container-fluid">
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Course Refresher</a></li>
                        <li class="breadcrumb-item active">Add</li>
                    </ol>
                </div>
                <h4 class="page-title">Course Refresher</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form action="{{ route('admin.refreshers.add', $data->id) }}" id="courserefresher_add" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <h4 class="header-title mt-0">Course Run - {{ $data->courseMain->name }}</h4>
                        <div class="row">

                            <input type="hidden" name="course_id" value="{{$data->id}}">

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="student_id">Student <span class="text-danger">*</span> </label>
                                    <select name="student_id" id="student_id" class="form-control select2" required placeholder="Select Student">
                                        <option value="">Select Student</option>
                                    </select>
                                    @error('student_id')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <select name="status" id="status" required  class="form-control select2">
                                        <option value="">Select Status</option>
                                        @foreach( getCourseWaitingListStatus() as $key => $statusText )
                                        <option value="{{ $key }}" {{ (old('status') ? old('status') : 1) == ($key ? 'selected' : '') }}>{{ $statusText }}</option>
                                        @endforeach
                                    </select>

                                    @error('status')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label class="my-1 control-label" for="isAttendanceRequired">Is Attendance Required</label>
                                <div class="form-group">
                                    <div class="custom-control custom-switch switch-success">
                                        <input type="checkbox" name="isAttendanceRequired" class="custom-control-input" id="isAttendanceRequired" {{ old('isAttendanceRequired') ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="isAttendanceRequired"></label>
                                    </div>
                                    @error('isAttendanceRequired')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label class="my-1 control-label" for="isAssessmentRequired">Is Assessment Required</label>
                                <div class="form-group">
                                    <div class="custom-control custom-switch switch-success">
                                        <input type="checkbox" name="isAssessmentRequired" class="custom-control-input" id="isAssessmentRequired" {{ old('isAssessmentRequired') ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="isAssessmentRequired"></label>
                                    </div>
                                    @error('isAssessmentRequired')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="notes">Notes</label>
                                    <textarea name="notes" id="notes" rows="3" class="form-control h-auto">{{old('notes')}}</textarea>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- </div> -->
                    <div class="card-footer m-0 clearfix">
                        <button type="submit" class="btn btn-primary mar-r-10">Submit</button>
                        <a href="{{ route('admin.course.courserunview', $data->id) }}" class="btn btn-danger">Cancel</a>
                    </div>
                </form>
            </div><!--end card-->
        </div> <!--end col-->
    </div><!--end row-->

</div><!-- container -->
@endsection
@push("scripts")
<script type="text/javascript">
    function formatResult(opt) {
        if (!opt.id) {
            return opt.text;
        }
        var $opt = $( '<span record="'+opt+'">'+ opt.text + '</span>');
        return $opt;
    };

    function formatSelection(opt) {
        return opt.text;
    }

    $(document).ready(function() {
        $(".select2").select2({ width: '100%' });

        $('#coursefileimage').change(function(ee) {
            readURL(this,'profile-img');
        });

        $("#student_id").select2({
            placeholder: 'Search Student',
            multiple: false,
            minimumInputLength: 3,
            width: '100%',
            templateResult: formatResult,
            templateSelection: formatSelection,
            ajax: {
                url: "{{ route('admin.ajax.search.student') }}",
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
    });

</script>
@endpush
