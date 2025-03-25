@extends('admin.layouts.master')
@section('title', 'Edit Manager')
@section('content')
<div class="container-fluid">
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{route('admin.user.superadmin')}}">Managers</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
                <h4 class="page-title">Edit Manager User</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form action="{{ route('admin.user.superadmin.edit', $data->id) }}" id="editsuperadmin" method="POST" enctype="multipart/form-data">
                    @csrf
                   <!--  <h5 class="card-header bg-secondary text-white mt-0">Edit Staff User</h5> -->
                    <div class="card-body">
                        <h4 class="header-title mt-0">Edit Manager User</h4>
                        <div class="row">
                        <div class="col-md-10">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="{{ $data->name }}" name="name" id="name" placeholder="">
                                    @error('name')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" readonly value="{{ $data->email }}" name="email" id="email" autocomplete="new-password" placeholder="">
                                    @error('email')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone_number">Phone Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="{{ $data->phone_number }}" name="phone_number" id="phone_number" onkeypress="return isNumberKey(event)" placeholder="">
                                    @error('phone_number')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="timezone">Timezone <span class="text-danger">*</span></label>
                                    <select name="timezone" id="timezone" class="form-control select2">
                                        <option value="">Select Timezone</option>
                                        @foreach( $timezones as $timezoneval => $timezone )
                                        <option value="{{ $timezoneval }}" {{ $data->timezone == $timezoneval ? 'selected' : '' }}>{{ $timezone }}</option>
                                        @endforeach
                                    </select>
                                    @error('timezone')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="username">NRIC or ID No. <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="{{ $data->username }}" name="username" id="username" placeholder="">
                                    @error('username')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 mt-4">
                                <div class="form-group">
                                    <div class="custom-control custom-switch switch-success">
                                        <input type="checkbox" name="status" class="custom-control-input" id="status" {{ $data->status ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="status">Status</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                        <div class="col-md-2">
                        <div class="row">
                            <div class="col-md-12 padl30 align-self-center met-profile">
                                <div class="met-profile-main">
                                    <div class="met-profile-main-pic">
                                        <img src="{{ $data->profileImage }}" alt="profile-img" id="profile-img" class="w-100">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 padl30">
                                <div class="form-group">
                                    <!-- <label for="profile_avatar">Profile Image</label> -->
                                    <div class="custom-file">
                                        <input type="file" accept="image/*" name="profile_avatar" class="custom-file-input" id="profile_avatar">
                                        <label class="custom-file-label" for="profile_avatar">Choose File</label>
                                        @error('profile_avatar')
                                            <label class="form-text text-danger">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
                    </div><!--end card-body-->
                    <div class="card-footer m-0 clearfix">
                        <button type="submit" class="btn btn-primary mar-r-10">Update</button>
                        <a href="{{ route('admin.user.superadmin') }}" class="btn btn-danger">Cancel</a>
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
