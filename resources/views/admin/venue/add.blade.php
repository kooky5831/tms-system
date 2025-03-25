@extends('admin.layouts.master')
@section('title', 'Add Venue')
@section('content')
<div class="container-fluid">
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{route('admin.venue.list')}}">Venue</a></li>
                        <li class="breadcrumb-item active">Add</li>
                    </ol>
                </div>
                <h4 class="page-title">Venue</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form action="{{ route('admin.venue.add') }}" method="POST" id="addvenue" enctype="multipart/form-data">
                    @csrf
                    <!-- <h5 class="card-header bg-secondary text-white mt-0">Add Venue</h5> -->
                    <div class="card-body">
                        <h4 class="header-title mt-0">Add Venue</h4>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <!-- <label for="block">Block </label> -->
                                    <input type="text" class="form-control" value="{{ old('block') }}" name="block" id="block" placeholder="Block">
                                    @error('block')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <!-- <label for="street">Street </label> -->
                                    <input type="text" class="form-control" value="{{ old('street') }}" name="street" id="street" placeholder="Street">
                                    @error('street')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <!-- <label for="floor">Floor <span class="text-danger">*</span></label> -->
                                    <input type="text" class="form-control" value="{{ old('floor') }}" name="floor" id="floor" placeholder="Floor">
                                    @error('floor')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                   <!--  <label for="unit">Unit <span class="text-danger">*</span></label> -->
                                    <input type="text" class="form-control" value="{{ old('unit') }}" name="unit" id="unit" placeholder="Unit">
                                    @error('unit')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <!-- <label for="building">Building </label> -->
                                    <input type="text" class="form-control" value="{{ old('building') }}" name="building" id="building" placeholder="Building">
                                    @error('building')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <!-- <label for="room">Room <span class="text-danger">*</span></label> -->
                                    <input type="text" class="form-control" value="{{ old('room') }}" name="room" id="room" placeholder="Room">
                                    @error('room')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                   <!--  <label for="postal_code">Postal Code <span class="text-danger">*</span></label> -->
                                    <input type="text" class="form-control" value="{{ old('postal_code') }}" name="postal_code" id="postal_code" onkeypress="return isNumberKey(event)" placeholder="Postal Code">
                                    @error('postal_code')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="my-1 control-label" for="wheelchairaccess">Wheel Chair Access </label>
                                <div class="form-group">
                                    <div class="custom-control custom-switch switch-success">
                                        <input type="checkbox" name="wheelchairaccess" class="custom-control-input" id="wheelchairaccess" {{ old('wheelchairaccess') ? 'checked' : '' }}>
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
                                        <input type="checkbox" name="status" class="custom-control-input" id="status" {{ old('status') ? 'checked' : '' }}>
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
                        <button type="submit" class="btn btn-primary mar-r-10">Submit</button>
                        <a href="{{ route('admin.venue.list') }}" class="btn btn-danger">Cancel</a>
                    </div>
                </form>
            </div><!--end card-->
        </div> <!--end col-->
    </div><!--end row-->

</div><!-- container -->
@endsection
@push('scripts')

<script type="text/javascript">

</script>
@endpush
