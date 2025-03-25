@extends('admin.layouts.master')
@section('title', 'Xero Connection')
@section('content')
<div class="container-fluid">

    @if($error)
    <h1>Your connection to Xero failed</h1>
    <p>{{ $error }}</p>
    <a href="{{ route('xero.auth.authorize') }}" class="btn btn-primary btn-large mt-4">
        Reconnect to Xero
    </a>
@elseif($connected)
    <h1>You are connected to Xero</h1>
    <p>{{ $organisationName }} via {{ $username }}</p>
    <a href="{{ route('xero.auth.authorize') }}" class="btn btn-primary btn-large mt-4">
        Reconnect to Xero
    </a>
    <div class="row mt-5">
        <div class="col-md-12">
            <form class="form-horizontal">
                <div class="form-group">
                    <label for="course_fee_account" class="control-label col-sm-6">Course Fee Account <span class="text-danger">*</span></label>
                    <div class="col-sm-4">
                        <select name="course_fee_account" class="form-control select2" id="course_fee_account">
                            <option value="">Select Account</option>
                        </select>
                    </div>                
                    @error('course_fee_account')
                        <label class="form-text text-danger">{{ $message }}</label>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="ssg_grant_account" class="control-label col-sm-6">SSG Grant Account <span class="text-danger">*</span></label>
                    <div class="col-sm-4">
                        <select name="ssg_grant_account" class="form-control ssg_grant_account select2" id="ssg_grant_account">
                            <option value="">Select Account</option>
                        </select>
                    </div>                
                    @error('ssg_grant_account')
                        <label class="form-text text-danger">{{ $message }}</label>
                    @enderror
                </div>

                {{-- <div class="form-group">
                    <label for="ssg_grant_account" class="control-label col-sm-6">GST Absorption<span class="text-danger">*</span></label>
                    <div class="col-sm-4">
                        <select name="gst_absorption" class="form-control gst_absorption select2" id="gst_absorption">
                            <option value="">Select Account</option>
                        </select>
                    </div>                
                    @error('ssg_grant_account')
                        <label class="form-text text-danger">{{ $message }}</label>
                    @enderror
                </div> --}}

                {{-- <div class="form-group">
                    <label for="gst_tax_code" class="control-label col-sm-6">GST Tax Code<span class="text-danger">*</span></label>
                    <div class="col-sm-4">
                        <select name="gst_tax_code" class="form-control select2" id="gst_tax_code">
                            <option value="">Select Account</option>
                        </select>
                    </div>                
                    @error('gst_tax_code')
                        <label class="form-text text-danger">{{ $message }}</label>
                    @enderror
                </div> --}}
                <div class="form-group col-sm-4">
                    <button type="submit" class="btn btn-primary mar-r-10">Update</button>
                </div>
            </form>
        </div>
    </div>
@else
    <h1>You are not connected to Xero</h1>
    <a href="{{ route('xero.auth.authorize') }}" class="btn btn-primary btn-large mt-4">
        Connect to Xero
    </a>
@endif

    <!-- Page-Title -->
    {{-- <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Profile</a></li>
                        <li class="breadcrumb-item active">Update</li>
                    </ol>
                </div>
                <h4 class="page-title">Profile</h4>
            </div>
        </div>
    </div> --}}
    <!-- end page title end breadcrumb -->
    {{-- <div class="row">
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
                                    <label for="avtar">Profile Image</label>
                                    <div class="custom-file">
                                        <input type="file" accept="image/*" name="avtar" class="custom-file-input" id="avtar">
                                        <label class="custom-file-label" for="avtar">Choose file...</label>
                                        @error('avtar')
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

                    </div>
                    <div class="card-footer m-0 clearfix">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-danger">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4 float-right">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div> --}}

</div>
@endsection
@push("scripts")
<script type="text/javascript">
    $(document).ready(function() {
        $(".select2").select2({ width: '100%' });

        $.ajax({
            type: 'GET',
            url: "{{ route('admin.xero.get-xero-accounts') }}",
        }).done(function (data) {
            $.each(data, function (index, value) {
                var newOption = new Option(value, index);
                $('#course_fee_account, #ssg_grant_account, #gst_absorption').append(newOption);
            });
        }).then(function () {
            // After populating the select elements, fetch and set selected values
            return $.ajax({
                type: 'GET',
                url: "{{ route('admin.xero.get-xero-code') }}",
            });
        }).done(function (data) {
            for (var i = 0; i < data.length; i++) {
                if (data[i].name == 'course_fee_account') {
                    $('#course_fee_account').val(data[i].val).trigger('change');
                } else if (data[i].name == 'ssg_grant_account') {
                    $('#ssg_grant_account').val(data[i].val).trigger('change');
                } else if (data[i].name == 'gst_tax_code') {
                    $('#gst_tax_code').val(data[i].val).trigger('change');
                } else if (data[i].name == 'gst_absorption') {
                    $('#gst_absorption').val(data[i].val).trigger('change');
                }
            }
        }).fail(function (error) {
            console.log(error);
        });

        $.ajax({
            type: 'GET',
            url: "{{ route('admin.xero.get-xero-taxrates') }}",
            success: function (data) {
                $.each(data, function(index, value){
                    var newOption = new Option(index, value);
                    $('#gst_tax_code').append(newOption).trigger('change');
                })
            },
            error: function (data) {
                console.log(data);
            } 
        })

        $("form").submit(function (event) {
            var formData = {
                course_fee_account: $("#course_fee_account").val(),
                ssg_grant_account: $("#ssg_grant_account").val(),
                gst_absorption: $("#gst_absorption").val(),
                gst_tax_code: $("#gst_tax_code").val(),
                _token: "{{csrf_token()}}"
            };
            
            $.ajax({
                type: "POST",
                url: "{{ route('admin.xero.set-xero-code') }}",
                data: formData,
                dataType: "json",
                encode: true,
            }).done(function (data) {
                if(data){
                    toastr.success("Xero data updated","Success");
                }
            });
                
        event.preventDefault();
        });
    });
</script>
@endpush
