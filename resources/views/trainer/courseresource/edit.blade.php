@extends('trainer.layouts.master')
@section('title', 'Add Course Resource')
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
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{route('admin.course-resources.index')}}">Course Resources</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
                <h4 class="page-title">Edit Course Resource</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form action="{{ route('trainer.course-resources.edit', $resource->id) }}" method="POST" id="coursecoursetriggeradd" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <h4 class="header-title mt-0">Edit Course Resource</h4>

                        <div class="row">
                            <div class="col-md-12">
                                <label for="course_main_id">Main Course<span class="text-danger">*</span></label>
                                <select name="course_main_id[]" id="course_main_id" class="form-control select2" multiple>
                                    @foreach( $allCources as $maincource )
                                    <option value="{{ $maincource->id }}" {{ is_array($getCourseMainResource) ? in_array($maincource->id, $getCourseMainResource) ? 'selected' : '' : '' }}>{{ $maincource->name }}</option>
                                    @endforeach
                                </select>
                                @error('course_main_id')
                                    <label class="form-text text-danger">{{ $message }}</label>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mt-2">
                                <div class="form-group">
                                    <label for="resource_title">Resource Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="{{ $resource->resource_title }}" name="resource_title" id="resource_title" placeholder="Course Resource Title">
                                    @error('resource_title')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 mt-2">
                                <div class="form-group">
                                    <label for="resource_file">Upload Resource</label>
                                    <div class="">
                                        <input type="file" accept="image/*|.pdf|.doc|.docx|.xlsx" name="resource_file" class="" id="resource_file">
                                        <span>Old File: {{$resource->resource_file}}</span>
                                        @error('resource_file')
                                            <label class="form-text text-danger mt-3">{{ $message }}</label>
                                        @enderror
                                        <p style="color: red;">Allowed file formats: .pdf, .doc, .docx, .xlsx</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                   <!--  </div> -->
                    <div class="card-footer m-0 clearfix">
                        <button type="submit" class="btn btn-primary mar-r-10">Submit</button>
                        <a href="{{ route('trainer.course-resources.index') }}" class="btn btn-danger">Cancel</a>
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
    });
</script>
@endpush
