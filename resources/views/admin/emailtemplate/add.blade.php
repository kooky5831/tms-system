@extends('admin.layouts.master')
@section('title', 'Add Email Template')
@section('content')
<div class="container-fluid">
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{route('admin.emailtemplates.list')}}">Email Templates</a></li>
                        <li class="breadcrumb-item active">Add</li>
                    </ol>
                </div>
                <h4 class="page-title">Add Email Template</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form action="{{ route('admin.emailtemplates.add') }}" method="POST" id="emailtemplatesadd" enctype="multipart/form-data" novalidate>
                    @csrf
                    <!-- <h5 class="card-header bg-secondary text-white mt-0">Add Course</h5> -->
                    <div class="card-body">
                        <h4 class="header-title mt-0">Add Email Template</h4>
                        <div class="row">

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="subject">Subject <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="{{ old('subject') }}" required name="subject" id="subject" placeholder="" />
                                    @error('subject')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <input type="text" class="form-control" value="{{ old('description') }}" name="description" id="description" placeholder="" />
                                    @error('description')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="description">Trigger Type <span class="text-danger">*</span></label>
                                    <select name="template_for" id="template_for" required class="form-control select2">
                                        <option value="">Select Trigger Type</option>
                                        @foreach( $emailTemplateTriggerTypes as $key => $emailTemplateTriggerType)
                                            <option value="{{$key}}" {{ old('template_for') == $key ? 'selected' : '' }}>{{ $emailTemplateTriggerType }} </option>
                                        @endforeach
                                    </select>
                                    @error('template_for')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <input type="checkbox" name="is_send_certificate" {{ old('is_send_certificate') ? 'checked' : '' }} >
                                    <label>Send certificate </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="template_text">Body <span class="text-danger">*</span> <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="Please refer the dynamic variable from below list."></i></label>
                                    <textarea id="template_text" name="template_text" class="form-control h-auto" rows="8" required>{{old('template_text')}}</textarea>
                                    @error('template_text')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <p><label for="subject">Variables: </label>
                                    @foreach( dynamicTriggerVarNames() as $names )
                                        <code>{{$names}} </code>
                                    @endforeach
                            
                                </p>
                            </div>
                        </div>
                    </div>

                   <!--  </div> -->
                    <div class="card-footer m-0 clearfix">
                        <button type="submit" class="btn btn-primary mar-r-10">Submit</button>
                        <a href="{{ route('admin.emailtemplates.list') }}" class="btn btn-danger">Cancel</a>
                    </div>
                </form>
            </div><!--end card-->
        </div> <!--end col-->
    </div><!--end row-->

</div><!-- container -->
@endsection
@push("scripts")
<script src="https://cdn.ckeditor.com/4.11.2/full/ckeditor.js"></script>
{{-- <script src="{{ asset('assets/plugins/ckeditor4/ckeditor.js') }}" ></script> --}}
<script type="text/javascript">
    $(document).ready(function() {
        $(".select2").select2({ width: '100%' });

        CKEDITOR.replace( 'template_text' );
        CKEDITOR.config.allowedContent = true;

        var counter = 0;
        $('#add_more').click(function () {
            counter = parseInt(counter) + 1;
            $('.add-more').append('<div class = "row"><div class = "col-md-3">\n\
            \n\<label></label><div class="row">\n\
            \n\<div class="col-md-12"><input type=text class="form-control" name="keywords[' + counter + '][key]" placeholder="Replacement Tags"></div></div></div>\n\
            \n\<div class = "col-md-3">\n\
            \n\<label></label><div class="row">\n\
            \n\<div class="col-md-12"><input type=text class="form-control" name="keywords[' + counter + '][value]" placeholder="Replacement Tags description"></div></div></div>\n\
            \n\<div class = "col-md-3">\n\
            \n\<label></label><div class="row">\n\
            \n\<div class="col-md-12"><button class="btn btn-danger ml-3" id="remove-keyword-' + counter + '" title="Remove" type="button">Remove <i class="icon-cancel-circle2 ml-2"></i></button></div></div></div>\n\
            \n\</div>');
            $(document).on('click', '#remove-keyword-' + counter, function () {
                $(this).parent().parent().parent().parent().remove();
            });
            $("input").on("keypress", function (e) {
                if (e.which === 32 && !this.value.length)
                    e.preventDefault();
            });
        });
    });
</script>
@endpush
