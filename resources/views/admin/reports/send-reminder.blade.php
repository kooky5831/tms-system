@extends('admin.layouts.master')
@section('title', 'Send Reminder - Payment')
@section('content')
<div class="container-fluid">
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Remaining Paymnet</a></li>
                        <li class="breadcrumb-item active">Send Reminder</li>
                    </ol>
                </div>
                <h4 class="page-title">Send Reminder</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form action="{{route('admin.reports.paymentreport.send', $studentDetails->id)}}" method="POST" id="emailSubmit" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <h4 class="header-title mt-0">Send Reminder</h4>
                        <ul class="list-group">
                            <li class="list-group-item">Email Template: {{ $emailTemplate['subject'] }}, {{ $emailTemplate['description'] }}</li>
                            <li class="list-group-item">Course: {{ $studentDetails->courseRun->courseMain->name }}, 
                                {{ $studentDetails->courseRun->course_start_date }} </li>
                        </ul>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="students_list">Students Name <span class="text-danger">*</span></label>
                                    <input type="text" name="student_name" class="form-control" value="{{ $studentDetails->student->name }}, {{ $studentDetails->student->nric }}, {{ $studentDetails->student->email }}"  readonly>
                                    @error('students_list')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="company_list">Company Name <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="Please select the company it will be cc for below list."></i></label>
                                    <input type="text" name="comapany" class="form-control" value="@if(strtolower($studentDetails->sponsored_by_company) == "yes"){{$studentDetails->company_name}} @else{{$studentDetails->sponsored_by_company}}@endif"  
                                    readonly>
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
        $(".select2").select2({ width: '100%' });

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
