@extends('admin.layouts.master')
@section('title', 'Profile')
@section('content')
<div class="container-fluid">
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Profile</a></li>
                        <li class="breadcrumb-item active">Update</li>
                    </ol>
                </div>
                <h4 class="page-title">Profile</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form action="{{ route('admin.profile') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <h5 class="card-header bg-secondary text-white mt-0">Update Profile</h5>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="{{ Auth::user()->name }}" name="name" id="name" placeholder="Name">
                                    @error('name')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone_number">Phone <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="{{ Auth::user()->phone_number }}" name="phone_number" id="phone_number" onkeypress="return isNumberKey(event)" placeholder="Phone Number">
                                    @error('phone_number')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="timezone">Timezone <span class="text-danger">*</span></label>
                                    <select name="timezone" id="timezone" class="form-control select2">
                                        <option value="">Select Timezone</option>
                                        @foreach( $timezones as $timezoneval => $timezone )
                                        <option value="{{ $timezoneval }}" {{ Auth::user()->timezone == $timezoneval ? 'selected' : '' }}>{{ $timezone }}</option>
                                        @endforeach
                                    </select>
                                    @error('timezone')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="profile_avatar">Profile Image</label>
                                    <div class="custom-file">
                                        <input type="file" accept="image/*" name="profile_avatar" class="custom-file-input" id="profile_avatar">
                                        <label class="custom-file-label" for="avtar">Choose file...</label>
                                        @error('profile_avatar')
                                            <label class="form-text text-danger">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 align-self-center met-profile">
                                <div class="met-profile-main">
                                    <div class="met-profile-main-pic">
                                        <img src="{{ Auth::user()->profileImage }}" alt="profile-img" id="profile-img" class="rounded-circle w-100">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div><!--end card-body-->
                    <div class="card-footer m-0 clearfix">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-danger">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4 float-right">Update</button>
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
        $(".select2").select2({ width: '100%' });
        $('#profile_avatar').change(function(ee) {
            readURL(this,'profile-img');
        });
    });
</script>
@endpush
