@extends('admin.layouts.master')
@section('title', 'Staff Permissions')
@section('content')

<div class="container-fluid">
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Permissions</a></li>
                        <li class="breadcrumb-item active">View</li>
                    </ol>
                </div>
                <h4 class="page-title">Update Permissions</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="card">

                <form action="{{ route("admin.user.admin.permissionpost", $role->id) }}" method="POST">
                    <div class="card-body">
                        @csrf

                        <label class="mt-0 header-title">{{ $role->name }}</label>
                        <div class="form-group {{ $errors->has('permission') ? 'has-error' : '' }}">
                            <label for="permission" class="mt-2 header-title mb-4">Permissions *
                                <span class="btn btn-info btn-xs select-all">Select All</span>
                                <span class="btn btn-info btn-xs deselect-all">Deselect All</span>
                            </label>
                            <div class="row">
                                @foreach($permissions as $id => $permission)
                                <div class="col-md-3">
                                    <div class="checkbox checkbox-primary">
                                        <input id="permission-{{ $id }}" class="permission_check" value="{{ $id }}" name="permission[]" type="checkbox" {{ in_array($id, $userpermissions) ? 'checked' : '' }}>
                                        <label for="permission-{{ $id }}">{{ $permission }}</label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @if($errors->has('permission'))
                                <em class="invalid-feedback">
                                    {{ $errors->first('permission') }}
                                </em>
                            @endif
                            <h5 class="mb-0 font-weight-bold mt-4 text-danger">
                                Select permission which you want to assign
                            </h5>
                        </div>

                    </div><!--end card-body-->
                    <div class="card-footer m-0 clearfix">
                        <input class="btn btn-primary mar-r-10" type="submit" value="Update">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-danger">Cancel</a>
                    </div>
                </form>

            </div><!--end card-->
        </div> <!--end col-->
    </div><!--end row-->

</div><!-- container -->
@endsection
@push('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        $(".select2").select2({ width: '100%' });
        $('.select-all').click(function () {
            $('.permission_check').prop('checked', true);
        });
        $('.deselect-all').click(function () {
            $('.permission_check').prop('checked', false);
        });
    });
</script>
@endpush
