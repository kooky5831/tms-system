@extends('admin.layouts.master')
@section('title', 'Send Email - Admin Task')
@push('css')
    <style>
        .select2-selection__choice {
        display: block !important;
        float: none !important;
        width: 50% !important;
    }
    </style>
@endphp
@section('content')
<div class="container-fluid">
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Admin Task</a></li>
                        <li class="breadcrumb-item active">Send Email</li>
                    </ol>
                </div>
                <h4 class="page-title">Send Email</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form action="{{ route('admin.tasks.sendTaskEmailSubmit', $data->id) }}" method="POST" id="emailSubmit" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <h4 class="header-title mt-0">Send Email</h4>
                        <ul class="list-group">
                            <li class="list-group-item">Email Template: {{ $emailTemplate['subject'] }}, {{ $emailTemplate['description'] }}</li>
                            <li class="list-group-item">Course: {{ $data->course->courseMain->name }}, {{ $data->course->course_start_date }}</li>
                        </ul>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="students_list">Students List <span class="text-danger">*</span></label>
                                    <select name="students_list[]" id="students_list" class="form-control select2" multiple>
                                        @foreach( $studentEnrollments as $key => $enrollment )
                                        <option value="{{ $enrollment->student->id }}" selected>{{ $enrollment->student->name }}, {{ $enrollment->student->nric }}, {{ $enrollment->student->email }}</option>
                                        @endforeach
                                    </select>
                                    @error('students_list')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="refresher_list">Refresher List</label>
                                    <select name="refresher_list[]" id="refresher_list" class="form-control select2" multiple>
                                        @foreach( $refresherStudent as $key => $refresher )
                                            @if($refresher->status != 2)
                                                <option value="{{ $refresher->student->id }}" selected> {{ $refresher->student->name }}, {{ $refresher->student->nric }}, {{ $refresher->student->email }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="company_list">Company List <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="Please select the company it will be cc for below list."></i></label>
                                    <select name="company_list[]" id="company_list" class="form-control select2" multiple>
                                        @foreach( $studentEnrollments as $enrollment )
                                        @if( !empty($enrollment->company_name) )
                                        <option value="{{ $enrollment->id }}" {{ old('company_list') ? in_array($enrollment->id, old('company_list')) ? 'selected' : '' : '' }}>{{ $enrollment->company_name }}, {{ $enrollment->company_contact_person_email }} - {{ $enrollment->student->nric }}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                    @error('company_list')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="content">Email Content <span class="text-danger">*</span> <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="Please refer the dynamic variable from below list."></i></label>
                                    <textarea id="content" name="content" class="form-control h-auto" rows="8" required>{{$content}}</textarea>
                                    @error('content')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12" id="showtext"></div>
                        </div>

                    </div><!--end card-body-->
                    <div class="card-footer m-0 clearfix">
                        <button type="submit" class="btn btn-primary mar-r-10">Submit To Send Email</button>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-danger">Cancel</a>
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
        $("#company_list").select2({ width: '100%' });

        var selectBox = $("#students_list").select2({
            width: '100%',
            /*templateResult: function(data) {
                if (!data.id) {
                    return data.text;
                }
                var index = $('#students_list option').index(data.element) + 1;
                return $('<span>' + index + '. ' + data.text + '</span>');
            },*/
            templateSelection: function(data, container) {
                if (!data.id) {
                    return data.text;
                }
                var selectedStudentIndex = $('#students_list option:selected').index(data.element) + 1;
                return $('<span>' + selectedStudentIndex + '. ' + data.text + '</span>');
            }
        });
        
        selectBox.on('select2:unselect', function (e) {
            var remainingStudentOptions = $('#students_list option:selected');
            remainingStudentOptions.each(function(index) {
                var newIndex = index + 1;
                var optionText = $(this).text().split('.').slice(1).join('.');
                $(this).text(newIndex + '. ' + optionText);
            });   
        });

        var selectBox = $("#refresher_list").select2({
            width: '100%',
            /*templateResult: function(data) {
                if (!data.id) {
                    return data.text;
                }
                var index = $('#refresher_list option').index(data.element) + 1;
                return $('<span>' + index + '. ' + data.text + '</span>');
            },*/
            templateSelection: function(data, container) {
                if (!data.id) {
                    return data.text;
                }
                var selectedRefresherIndex = $('#refresher_list option:selected').index(data.element) + 1;
                return $('<span>' + selectedRefresherIndex + '. ' + data.text + '</span>');
            }
        });
        
        selectBox.on('select2:unselect', function (e) {
            var remainingRefrehserOptions = $('#refresher_list option:selected');
            remainingRefrehserOptions.each(function(index) {
                var newIndex = index + 1;
                var optionText = $(this).text().split('.').slice(1).join('.');
                $(this).text(newIndex + '. ' + optionText);
            });
   
        });


        var editor = CKEDITOR.replace( 'content', {
            on:{
                change:syncPreview,
                contentDom:syncPreview,
            }
        });
        CKEDITOR.config.allowedContent = true;

        var textcontent = document.getElementById('content');
        textcontent.onkeyup = textcontent.onkeypress = function(){
            document.getElementById('showtext').innerHTML = this.value;
        }

        document.getElementById('showtext').innerHTML = textcontent.value;

        function syncPreview(){
            document.getElementById('showtext').innerHTML = editor.getData();
        }

    });
</script>
@endpush
