@extends('admin.layouts.master')
@section('title', 'Email Logs - Admin Task')
@section('content')
<div class="container-fluid">
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Email Logs</a></li>
                        <li class="breadcrumb-item active">Email Logs</li>
                    </ol>
                </div>
                <h4 class="page-title">Email Logs</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form>
                    @csrf
                    <div class="card-body">
                        <h4 class="header-title mt-0">Email Logs</h4>
                        <ul class="list-group">

                        </ul>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="students_list">Subject</label>
                                            <input type="text" class="form-control" value="{{ $data->mail_logs_subject}}" disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="students_list">To</label>
                                            <input type="text" class="form-control" value="{{ $data->mail_logs_to}}" disabled>
                                        </div>                                       
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="students_list">From</label>
                                            <input type="text" class="form-control" value="{{ $data->mail_logs_from}}" disabled>
                                        </div>                                        
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="students_list">CC</label>
                                            <input type="text" class="form-control" value="{{ $data->mail_logs_cc}}" disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="students_list">BCC</label>
                                            <input type="text" class="form-control" value="{{ $data->mail_logs_bcc}}" disabled>
                                        </div>                                       
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="content">Email Content <span class="text-danger">*</span> <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="Please refer the dynamic variable from below list."></i></label>
                                    <textarea id="content" name="content" class="form-control h-auto" rows="8" disabled>{{$data->mail_logs_content}}</textarea>
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
                        <a href="{{ route('admin.maillogs.list') }}" class="btn btn-danger">Cancel</a>
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
