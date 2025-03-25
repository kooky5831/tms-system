@extends('admin.layouts.master')
@section('title', 'Feedback Settings')
@section('content')
<div class="container-fluid">
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Feedback Setting</a></li>
                        <li class="breadcrumb-item active">Setting</li>
                    </ol>
                </div>
                <h4 class="page-title">Feedback Setting</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form action="{{route('admin.course-feedback.set.settings')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <h4 class="header-title mt-0">Basic Details</h4>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Feedback QR <span class="text-danger">*</span></label>
                                    <div class="custom-file">
                                        <input type="file" accept="image/*" name='feedback_qr' class="custom-file-input" id="feedback_qr" value="{{ asset('storage/feedback-qr-code/' . $feedbackSettingData['feedback_qr']) }}">
                                        <label class="custom-file-label" for="feedback_qr">Choose File</label>
                                        @error('feedback_qr')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                        @enderror
                                    </div>
                                    <img src='{{ asset('storage/feedback-qr-code/' . $feedbackSettingData['feedback_qr']) }}' id="feedback_image_logo" width="200px" style="margin-top: 25px" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Feedback Text<span class="text-danger">*</span></label>
                                    <textarea id="feedback_text" name='feedback_text' class="form-control h-auto" rows="8" required>
                                        {{$feedbackSettingData['feedback_text']}}
                                    </textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer m-0 clearfix">
                        <button type="submit" class="btn btn-primary mar-r-10">Submit</button>
                        <a href="#" class="btn btn-danger">Cancel</a>
                    </div>
                </form>
            </div><!--end card-->
        </div> <!--end col-->
    </div><!--end row-->
</div><!-- container -->
@endsection
@push("scripts")
{{-- <script src="{{ asset('assets/plugins/ckeditor4/ckeditor.js') }}" ></script> --}}
<script src="https://cdn.ckeditor.com/4.11.2/full/ckeditor.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        CKEDITOR.replace( 'feedback_text' );
        CKEDITOR.config.allowedContent = true;
    });
    
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            var customId = $(input).attr('id');
            if(customId == "feedback_qr"){
                reader.onload = function (e) {
                    $('#feedback_image_logo').attr('src', e.target.result);
                }
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    $("#feedback_qr").change(function(){
        readURL(this);
    });
</script>
@endpush