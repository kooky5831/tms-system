@extends('admin.layouts.master')
@section('title', 'Edit Program Type')
@section('content')
<div class="container-fluid pad0">
    <!-- Page-Title -->
    <div class="row pad0 mar0">
        <div class="col-sm-12 pad0">
            <div class="page-title-box">
                <div class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{route('admin.programtype.list')}}">Program Type</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
                <h4 class="page-title">Edit Program Type</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <div class="row pad0 mar0">
        <div class="col-12 pad0">
            <div class="card">
                <form action="{{ route('admin.programtype.edit', $data->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <!-- <h5 class="card-header bg-secondary text-white mt-0">Edit Venue</h5> -->
                    <div class="card-body">
                        <h4 class="header-title mt-0">Edit Program Type</h4>
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
                        <div class="row">
                                <div class="col-md-3">
                                    <div class="checkbox checkbox-primary ">
                                        <input id="is_discount" class="is_discount" value="" name="is_discount" type="checkbox" {{ $data->is_discount == 1 ? 'checked' : '' }}>
                                        <label for="is_discount">Apply Discount</label>
                                    </div>
                                    <div class="" id="discount_div">
                                        <label for="discount_percentage">Discount Percentage</label>
                                        <input type="text" class="form-control" value="{{ $data->discount_percentage }}" name="discount_percentage" id="discount_percentage" placeholder="" />
                                        @error('discount_percentage')
                                            <label class="form-text text-danger">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="checkbox checkbox-primary ">
                                        <input id="is_application_fee" class="is_application_fee" value="" name="is_application_fee" type="checkbox" {{ $data->is_application_fee == 1 ? 'checked' : '' }}>
                                        <label for="is_application_fee">Apply Application Fee</label>
                                    </div>
                                    <div class="" id="application_div">
                                        <label for="application_fee">Application Fee Amount</label>
                                        <input type="text" class="form-control" value="{{ $data->application_fee }}" name="application_fee" id="application_fee" placeholder="" />
                                        @error('application_fee')
                                            <label class="form-text text-danger">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="checkbox checkbox-primary ">
                                        <input id="is_absorb_gst" class="is_absorb_gst" value="" name="is_absorb_gst" type="checkbox" {{ $data->is_absorb_gst == 1 ? 'checked' : '' }}>
                                        <label for="is_absorb_gst">Absorb GST</label>
                                    </div>
                                </div>
                        </div>
                        <div class="row">
                        </div>
                    </div><!--end card-body-->
                    <div class="card-footer m-0 clearfix">
                        <button type="submit" class="btn btn-primary mar-r-10">Update</button>
                        <a href="{{ route('admin.programtype.list') }}" class="btn btn-danger">Cancel</a>
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

    // $("#is_discount:checked").click(function() {
    //     $("#discount_div").removeClass('d-none');
    // });
    /*$(document).ready(function() {
        $("#is_discount:checkbox").click(function(event) {
            console.log($(this).is(":checked"))
            if ($(this).is(":checked"))
                $("#discount_div").removeClass('d-none');
            else
                $("#discount_div").addClass('d-none');
        });
        
        $("#is_application_fee:checkbox").click(function(event) {
            console.log($(this).is(":checked"))
            if ($(this).is(":checked"))
                $("#application_div").removeClass('d-none');
            else
                $("#application_div").addClass('d-none');
        });
    });*/
</script>
@endpush
