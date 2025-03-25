@extends('admin.layouts.master')
@section('title', 'Edit SMS Template')
@section('content')
<div class="container-fluid">
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{route('admin.smstemplates.list')}}">SMS Templates</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
                <h4 class="page-title">Edit SMS Template</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form action="{{ route('admin.smstemplates.edit', $data->id) }}" method="POST" id="smstemplatesedit" enctype="multipart/form-data" novalidate>
                    @csrf
                    <div class="card-body">
                        <h4 class="header-title mt-0">Edit SMS Template</h4>
                        <div class="row">

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="{{ $data->name }}" required name="name" id="name" placeholder="" />
                                    @error('name')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <input type="text" class="form-control" value="{{ $data->description }}" name="description" id="description" placeholder="" />
                                    @error('description')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="content">Content <span class="text-danger">*</span> <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="Please refer the dynamic variable from below list."></i></label>
                                    <textarea id="content" name="content" class="form-control h-auto" rows="8" required>{{$data->content}}</textarea>
                                    @error('content')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <p>Variables:
                                    @foreach( dynamicTriggerVarNames() as $names )
                                        <code>{{$names}} </code>
                                    @endforeach
                                </p>
                            </div>
                        </div>

                    </div><!--end card-body-->
                    <div class="card-footer m-0 clearfix">
                        <button type="submit" class="btn btn-primary mar-r-10">Update</button>
                        <a href="{{ route('admin.smstemplates.list') }}" class="btn btn-danger">Cancel</a>
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
        // $(".select2").select2({ width: '100%' });
    });
</script>
@endpush
