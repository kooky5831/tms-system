@extends('admin.layouts.master')
@section('title', 'Edit Course Tag')
@section('content')
<div class="container-fluid pad0">
    <!-- Page-Title -->
    <div class="row pad0 mar0">
        <div class="col-sm-12 pad0">
            <div class="page-title-box">
                <div class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{route('admin.coursetags.list')}}">Course Tag</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
                <h4 class="page-title">Edit Course Tag</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <div class="row pad0 mar0">
        <div class="col-12 pad0">
            <div class="card">
                <form action="{{ route('admin.coursetags.edit', $data->id) }}" method="POST">
                    @csrf
                    <!-- <h5 class="card-header bg-secondary text-white mt-0">Edit Venue</h5> -->
                    <div class="card-body">
                        <h4 class="header-title mt-0">Edit Course Tag</h4>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="{{ $data->name }}" required name="name" id="name" placeholder="" />
                                    @error('name')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="my-1 control-label" for="status">Status : <span class="text-danger">*</span></label>
                                <div class="form-group">
                                    <div class="custom-control custom-switch switch-success">
                                        <input type="checkbox" name="status" class="custom-control-input" id="status" {{ $data->status ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="status"></label>
                                    </div>
                                    @error('status')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div><!--end card-body-->
                    <div class="card-footer m-0 clearfix">
                        <button type="submit" class="btn btn-primary mar-r-10">Update</button>
                        <a href="{{ route('admin.coursetags.list') }}" class="btn btn-danger">Cancel</a>
                    </div>
                </form>
            </div><!--end card-->
        </div> <!--end col-->
    </div><!--end row-->

</div><!-- container -->
@endsection
@push("scripts")
<script type="text/javascript">
    $(".select2").select2({ width: '100%' });
    $('#profile_avatar').change(function(ee) {
        readURL(this,'profile-img');
    });
</script>
@endpush
