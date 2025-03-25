@extends('admin.layouts.master')
@section('title', 'Edit Trainer')
@section('content')
<div class="container-fluid">
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{route('admin.user.trainer')}}">Trainer</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
                <h4 class="page-title">Trainer</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form action="{{ route('admin.trainer.edit', $data->id) }}" id="edittrainer" method="POST" enctype="multipart/form-data">
                    @csrf
                   <!--  <h5 class="card-header bg-secondary text-white mt-0">Edit Trainer</h5> -->
                    <div class="card-body">
                        <h4 class="header-title mt-0">Edit Trainer</h4>
                        <div class="row">
                            <div class="col-lg-12 text-right">
                                <button class="btn btn-secondary viewtrainerresponse" trainer_id="{{$data->id}}">Trainer Response</button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="salutationId">Salutation <span class="text-danger">*</span></label>
                                    <select name="salutationId" id="salutationId"  class="form-control select2">
                                        {{-- <option value="">Select Salutation</option> --}}
                                        @foreach( getSalutations() as $key => $salutation )
                                        <option value="{{ $key }}" {{ $data->trainer->salutationId == $key ? 'selected' : '' }}>{{ $salutation }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="{{ $data->name }}" name="name" id="name" placeholder="">
                                    @error('name')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="email">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" readonly value="{{ $data->email }}" name="email" id="email" autocomplete="new-password" placeholder="">
                                    @error('email')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tpgateway_id">TPGateway ID</label>
                                    <input type="text" class="form-control" readonly value="{{ $data->trainer->tpgateway_id }}" name="tpgateway_id" id="tpgateway_id" placeholder="">
                                </div>
                            </div>
                        </div>

                        <!-- New Fields Start -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="id_number">NRIC or ID No.<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="{{ $data->trainer->id_number }}" name="id_number" id="id_number" placeholder="">
                                    @error('id_number')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="id_type">ID Type <span class="text-danger">*</span></label>
                                    <select name="id_type"  class="form-control select2">
                                        <option value="">Please select Type</option>
                                        @foreach( getTrainerIdType() as $key => $idtype )
                                        <option value="{{ $key }}" {{$data->trainer->id_type == $key ? 'selected' : ''}} > {{$idtype}}</option>
                                        @endforeach
                                    </select>
                                    @error('id_type')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="role_type">Role Type <span class="text-danger">*</span></label>
                                        <select name="role_type[]"  class="form-control select2" multiple>
                                            <?php $rids = json_decode($data->trainer->role_type); 
                                            if(!empty($rids)) : ?>
                                            @foreach( getTrainerRoles() as $key => $role )
                                            <option value="{{ $key }}" @foreach($rids as $r){{$r == $key ? 'selected': ''}}   @endforeach >{{$role}}</option>
                                            @endforeach
                                            <?php 
                                            else: ?>
                                                @foreach( getTrainerRoles() as $key => $role )
                                                <option value="{{ $key }}" {{ old('role_type') == $key ? 'selected' : '' }}>{{ $role }}</option>
                                                @endforeach
                                            <?php endif; ?>
                                        </select>
                                    @error('role_type')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <!-- New Fields End -->

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
                                    <label for="experience">Experience <span class="text-danger">*</span></label>
                                    <textarea id="experience" class="form-control" rows="3" name="experience">{{$data->trainer->experience}}</textarea>
                                    @error('experience')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="linkedInURL">linkedIn URL <span class="text-danger">*</span></label>
                                    <input type="text" id="linkedInURL" class="form-control" name="linkedInURL" value="{{$data->trainer->linkedInURL}}" />
                                    @error('linkedInURL')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="domainArea">Domain Area of Practice <span class="text-danger">*</span></label>
                                    <textarea rows="3" class="form-control" name="domainArea" id="domainArea" cols="55" maxlength="1000"> {{ $data->trainer->domainAreaOfPractice }} </textarea>
                                    @error('domainArea')
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
                            <div class="col-lg-12">
                                <label class="header-title mt-0"> Qualifications</label>
                                <div class="repeater-custom-show-hide">
                                    <div data-repeater-list="qualifications">
                                        <?php $qualifications = json_decode($data->trainer->qualifications); ?>
                                        @if( !empty($qualifications) )
                                        @foreach ($qualifications as $k => $qualificationData)
                                            <div data-repeater-item="">
                                                <div class="form-group row  d-flex align-items-end">
                                                    <div class="col-sm-6">
                                                        <label class="control-label">Level <span class="text-danger">*</span></label>
                                                        <select name="qualifications[{{$k}}][level]"  class="form-control select2">
                                                            @foreach ($qualificationsList as $qualification)
                                                                <option value="{{$qualification->code}}" {{ $qualificationData->level == $qualification->code ? 'selected' : '' }}>{{$qualification->title}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-sm-5">
                                                        <label class="control-label">Description <span class="text-danger">*</span></label>
                                                        <textarea rows="3" class="form-control"  name="qualifications[{{$k}}][description]" cols="55" maxlength="1000"> {{$qualificationData->description}} </textarea>
                                                    </div>
                                                    <div class="col-sm-1 verti-cen">
                                                        <span data-repeater-delete="" class="btn btn-danger btn-sm">
                                                            <span class="far fa-trash-alt"></span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                        @else
                                            <div data-repeater-item="">
                                                <div class="form-group row  d-flex align-items-end">
                                                    <div class="col-sm-6">
                                                        <label class="control-label">Level <span class="text-danger">*</span></label>
                                                        <select name="qualifications[0][level]"  class="form-control select2">
                                                            @foreach ($qualificationsList as $qualification)
                                                                <option value="{{$qualification->code}}" {{old('qualifications')[$qualification]['level'] == $qualification->code ? 'selected' : ''}}>{{$qualification->title}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-sm-5">
                                                        <label class="control-label">Description <span class="text-danger">*</span></label>
                                                        <textarea rows="3" class="form-control" name="qualifications[0][description]" cols="55" maxlength="1000"> {{old('qualifications')[0]['description']}} </textarea>
                                                    </div>
                                                    <div class="col-sm-1 verti-cen">
                                                        <span data-repeater-delete="" class="btn btn-danger btn-sm">
                                                            <span class="far fa-trash-alt"></span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                    </div><!--end repet-list-->

                                    <div class="form-group row mb-0">
                                        <div class="col-sm-12">
                                            <span data-repeater-create="" class="btn btn-secondary btn-md">
                                                <span class="fa fa-plus"></span> Add
                                            </span>
                                        </div><!--end col-->
                                    </div><!--end row-->
                                </div> <!--end repeter-->
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-2">
                                <div class="row">
                                    <div class="col-md-12 align-self-center met-profile">
                                        <div class="met-profile-main">
                                            <div class="met-profile-main-pic">
                                                <img src="{{ $data->profileImage }}" alt="profile-img" id="profile-img" class=" w-100">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">

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


                            <!-- Trainer Signature Start -->
                            <div class="col-md-2">
                                <div class="row">
                                    <div class="col-md-12 align-self-center met-profile">
                                        <div class="met-profile-main">
                                            <div class="met-profile-main-pic">
                                                <img src="{{ $data->trainerSign }}" alt="Trainer Signature" id="signature-img" class=" w-100">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">

                                        <div class="form-group">
                                            <label for="trainer_signature" style="margin-top: 20px;">Trainer Signature</label>
                                            <div class="custom-file">
                                                <input type="file" accept="image/*" name="trainer_signature" class="custom-file-input" id="trainer_signature">
                                                <label class="custom-file-label" for="trainer_signature">Choose File</label>
                                                @error('trainer_signature')
                                                    <label class="form-text text-danger">{{ $message }}</label>
                                                @enderror
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <!-- Trainer Signature End -->

                            {{-- <div class="col-md-2">
                                <label class="my-1 control-label">Type <span class="text-danger">*</span></label>
                                <div class="form-group">
                                    <div class="form-check-inline my-1">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="type_existing" value="1" {{ $data->trainer->type == 2 ? '' : 'checked' }} name="type" class="custom-control-input">
                                            <label class="custom-control-label" for="type_existing">Existing</label>
                                        </div>
                                    </div>
                                    <div class="form-check-inline my-1">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="type_new" value="2" {{ $data->trainer->type == 2 ? 'checked' : '' }} name="type" class="custom-control-input">
                                            <label class="custom-control-label" for="type_new">New</label>
                                        </div>
                                    </div>
                                    @error('type')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div> --}}
                            
                            <div class="col-md-2">
                                <label class="my-1 control-label" for="status">Status <span class="text-danger">*</span></label>
                                <div class="form-group">
                                    <div class="custom-control custom-switch switch-success">
                                        <input type="checkbox" name="status" class="custom-control-input" id="status" {{ $data->status ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="status">Status</label>
                                    </div>
                                </div>
                            </div>

                            
                        </div>

                    </div><!--end card-body-->
                    <div class="card-footer m-0 clearfix">
                        <button type="submit" class="btn btn-primary mar-r-10">Update</button>
                        <a href="{{ route('admin.user.trainer') }}" class="btn btn-danger">Cancel</a>
                    </div>
                </form>
            </div><!--end card-->
        </div> <!--end col-->
    </div><!--end row-->

</div><!-- container -->
@endsection
@push("scripts")
<script src="{{ asset('assets/plugins/repeater/jquery.repeater.min.js') }}"></script>

<script type="text/javascript">

    $(document).on('click', '.viewtrainerresponse', function(e) {
        e.preventDefault();
        let _trainer_id = $(this).attr('trainer_id');
        $.ajax({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            url: '{{ route('admin.ajax.trainerResponse.modal.view') }}',
            type: "POST",
            dataType: "JSON",
            data: {
                id: _trainer_id,
            },
            success: function(res) {
                $('#modal-content').empty().html(res.html);
                $('.model-box').modal();
            }
        }); // end ajax
    });

    $(".select2").select2({ width: '100%' });
    $('.repeater-custom-show-hide').repeater({
        isFirstItemUndeletable: true,
        show: function () {
            $(this).slideDown();
            $('.select2-container').remove();
            $('.select2').select2({
                width: '100%',
                // placeholder: "Placeholder text",
                // allowClear: true
            });
        },
        hide: function (remove) {
          if (confirm('Are you sure you want to remove this item?')) {
            $(this).slideUp(remove);
          }
        }
    });
    $('#profile_avatar').change(function(ee) {
        readURL(this,'profile-img');
    });

    // Trainer Signature
    $('#trainer_signature').change(function(ee) {
        readURL(this,'signature-img');
    });
</script>
@endpush
