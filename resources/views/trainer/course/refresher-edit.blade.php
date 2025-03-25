@extends('trainer.layouts.master')
@section('title', 'Edit Course Refresher')
@section('content')
<div class="container-fluid">
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Course Refresher</a></li>
                        <li class="breadcrumb-item active">Edit</li>
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
                <form action="{{ route('trainer.refreshers.edit', $data->id) }}" id="courserefresher_edit" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <h4 class="header-title mt-0">Course Run - {{ $data->course->courseMain->name }}</h4>
                        <div class="row">

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="student_id">Student </label>
                                    <input name="student_id" id="student_id" class="form-control" readonly value="{{$data->student->name}} - {{ $data->student->nric }}" />
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
                                        <option value="{{ $key }}" {{ $data->status == $key ? 'selected' : '' }}>{{ $statusText }}</option>
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
                                        <input type="checkbox" name="isAttendanceRequired" class="custom-control-input" id="isAttendanceRequired" {{ $data->isAttendanceRequired ? 'checked' : '' }}>
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
                                        <input type="checkbox" name="isAssessmentRequired" class="custom-control-input" id="isAssessmentRequired" {{ $data->isAssessmentRequired ? 'checked' : '' }}>
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
                                    <textarea name="notes" id="notes" rows="3" class="form-control h-auto">{{$data->notes}}</textarea>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- </div> -->
                    <div class="card-footer m-0 clearfix">
                        <button type="submit" class="btn btn-primary mar-r-10">Update</button>
                        <a href="{{ route('trainer.course.courserunview', $data->course_id) }}" class="btn btn-danger">Cancel</a>
                    </div>
                </form>
            </div><!--end card-->
        </div> <!--end col-->
    </div><!--end row-->

</div><!-- container -->
@endsection
@push("scripts")
<script type="text/javascript">
    $(document).ready(function() {
        $(".select2").select2({ width: '100%' });
        $('#coursefileimage').change(function(ee) {
            readURL(this,'profile-img');
        });
    });

</script>
@endpush
