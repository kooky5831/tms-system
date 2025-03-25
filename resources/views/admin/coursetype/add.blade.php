@extends('admin.layouts.master')
@section('title', 'Add Course Type')
@section('content')
<div class="container-fluid">
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Course Type</a></li>
                        <li class="breadcrumb-item active">Add</li>
                    </ol>
                </div>
                <h4 class="page-title">Course Type</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form action="{{ route('admin.coursetype.add') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <h5 class="card-header bg-secondary text-white mt-0">Add Course Type</h5>
                    <div class="card-body">

                        <div class="row">

                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="{{ old('name') }}" required name="name" id="name" placeholder="" />
                                    @error('name')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="my-1 control-label" for="status">Status : <span class="text-danger">*</span></label>
                                <div class="form-group">
                                    <div class="custom-control custom-switch switch-success">
                                        <input type="checkbox" name="status" class="custom-control-input" id="status" {{ old('status') ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="status"></label>
                                    </div>
                                    @error('status')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>



                        </div>

                    </div>

                    </div><!--end card-body-->
                    <div class="card-footer m-0 clearfix">
                        <a href="{{ route('admin.coursetype.list') }}" class="btn btn-danger">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4 float-right">Submit</button>
                    </div>
                </form>
            </div><!--end card-->
        </div> <!--end col-->
    </div><!--end row-->

</div><!-- container -->
@endsection
