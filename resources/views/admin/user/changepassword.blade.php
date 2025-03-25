@extends('admin.layouts.master')
@section('title', 'Change Password')
@section('content')
<div class="container-fluid">
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Change Password</a></li>
                        <li class="breadcrumb-item active">Update</li>
                    </ol>
                </div>
                <h4 class="page-title">Change Password</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <div class="row">
        <div class="col-6 offset-3">
            <div class="card">
                <form action="{{ route('admin.changepassword') }}" method="POST">
                    @csrf
                    <h5 class="card-header bg-secondary text-white mt-0">Change Password</h5>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="old_password">Old Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" name="old_password" id="old_password" autocomplete="new-password" placeholder="Old Password">
                                    @error('old_password')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="new_password">New Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" name="new_password" id="new_password" autocomplete="new-password" placeholder="New Password">
                                    @error('new_password')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="confirm_new_password">Confirm Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" name="confirm_new_password" id="confirm_new_password" autocomplete="new-password" placeholder="Confirm Password">
                                    @error('confirm_new_password')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>

                    </div><!--end card-body-->
                    <div class="card-footer m-0 clearfix">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-danger">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4 float-right">Change</button>
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

    });
</script>
@endpush
