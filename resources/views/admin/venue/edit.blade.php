@extends('admin.layouts.master')
@section('title', 'Edit Venue')
@section('content')
<div class="container-fluid pad0">
    <!-- Page-Title -->
    <div class="row pad0 mar0">
        <div class="col-sm-12 pad0">
            <div class="page-title-box">
                <div class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{route('admin.venue.list')}}">Venue</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
                <h4 class="page-title">Edit Venue</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <div class="row pad0 mar0">
        <div class="col-12 pad0">
            <div class="card">
                <form action="{{ route('admin.venue.edit', $data->id) }}" method="POST" id="editvenue" enctype="multipart/form-data">
                    @csrf
                    <!-- <h5 class="card-header bg-secondary text-white mt-0">Edit Venue</h5> -->
                    <div class="card-body">
                        <h4 class="header-title mt-0">Edit Venue</h4>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="block">Block </label>
                                    <input type="text" class="form-control"  value="{{ $data->block }}" name="block" id="block" placeholder="">
                                    @error('block')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="street">Street </label>
                                    <input type="text" class="form-control" value="{{ $data->street }}" name="street" id="street" placeholder="">
                                    @error('street')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="floor">Floor <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="{{ $data->floor }}" name="floor" id="floor" placeholder="">
                                    @error('floor')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="unit">Unit <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="{{ $data->unit }}" name="unit" id="unit" placeholder="">
                                    @error('unit')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="building">Building </label>
                                    <input type="text" class="form-control" value="{{ $data->building }}" name="building" id="building" placeholder="">
                                    @error('building')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="room">Room <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="{{ $data->room }}" name="room" id="room" placeholder="">
                                    @error('room')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="postal_code">Postal Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="{{ $data->postal_code }}" name="postal_code" id="postal_code" onkeypress="return isNumberKey(event)" placeholder="">
                                    @error('postal_code')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-2">
                                <label class="my-1 control-label" for="wheelchairaccess">Wheel Chair Access </label>
                                <div class="form-group">
                                    <div class="custom-control custom-switch switch-success">
                                        <input type="checkbox" name="wheelchairaccess" class="custom-control-input" id="wheelchairaccess" {{ $data->wheelchairaccess ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="wheelchairaccess"></label>
                                    </div>
                                    @error('wheelchairaccess')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-2">
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
                        <a href="{{ route('admin.venue.list') }}" class="btn btn-danger">Cancel</a>
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
