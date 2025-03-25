@extends('admin.layouts.master')
@section('title', 'Edit Course')
@push('css')
<link href="{{ asset('assets/plugins/clockpicker/jquery-clockpicker.min.css') }}" rel="stylesheet" type="text/css" />
<style>
    /*#canvas-container {
        width:  100%;
    }*/
</style>
@endpush
@section('content')
<div class="container-fluid">
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{route('admin.coursemain.list')}}">Course</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
                <h4 class="page-title">Edit Course</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form action="{{ route('admin.coursemain.edit', $data->id) }}" method="POST" id="coursemainedit" enctype="multipart/form-data" novalidate>
                    @csrf
                    <!-- <h5 class="card-header bg-secondary text-white mt-0">Edit Course</h5> -->
                    <div class="card-body">
                        <h4 class="header-title mt-0">Edit Course</h4>
                        <!-- Show Course Main ID as Readonly Start -->
                        <div class="row">
                            <div class="col-md-1 mb-3">
                                <label for="course_main_id">Course ID <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="" data-original-title="This is Main Course ID based on Database table."></i></label>
                                <input type="text" readonly="" class="form-control" value="{{$data->id}}" name="course_main_id" id="course_main_id">
                            </div>
                        </div> 
                        <!-- Show Course Main ID as Readonly End -->
                        <div class="row">

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="{{ $data->name }}" required name="name" id="name" placeholder="" />
                                    @error('name')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-2">
                                <label for="name">Course Abbreviation</label>
                                <input type="text" class="form-control" value="{{ $data->course_abbreviation }}" required name="course_abbreviation" id="course_abbreviation" placeholder="" />
                                @error('course_abbreviation')
                                    <label class="form-text text-danger">{{ $message }}</label>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label for="course_type_id">Select Course Module <span class="text-danger">*</span></label>
                                <select name="course_type_id" id="course_type_id" required class="form-control select2">
                                    <option value="">Select Course Module</option>
                                    @foreach($courseTypelist as $courseTypesRow)
                                    <option value="{{$courseTypesRow->id}}" {{ $data->course_type_id == $courseTypesRow->id ? 'selected' : '' }}>{{$courseTypesRow->name }} </option>
                                    @endforeach
                                </select>
                                @error('course_type_id')
                                    <label class="form-text text-danger">{{ $message }}</label>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="course_type">Course Type<span class="text-danger">*</span></label>
                                    <select name="course_type" id="course_type" required  class="form-control select2">
                                        <option value="">Select Course Type</option>
                                        @foreach( getCourseType() as $key => $courseType )
                                        <option value="{{ $key }}" {{ $data->course_type == $key ? 'selected' : '' }}>{{ $courseType }}</option>
                                        @endforeach
                                    </select>

                                    @error('course_type')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="reference_number">Reference Number <span class="text-danger">*</span> <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="Refer to course reference table in Google Sheets for the correct values."></i></label>
                                    <input type="text" class="form-control" value="{{ $data->reference_number }}" required name="reference_number" id="reference_number" placeholder="" />
                                    @error('reference_number')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4" style="{{ $data->course_type != 2 ? '' : 'display: none;' }}" id="skillcodediv">
                                <div class="form-group">
                                    <label for="skill_code">Competency Code / Skill Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="{{ $data->skill_code }}" name="skill_code" id="skill_code" placeholder="" />
                                    @error('skill_code')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4 nomoduler" style="display: {{$data->course_type_id == 2 ? 'none' : 'block'}}">
                                <div class="form-group">
                                    <label for="course_mode_training">Course Mode of Training <span class="text-danger">*</span></label>
                                    <select class="form-control select2" name="course_mode_training" id="course_mode_training">
                                        <option value="">Select option</option>
                                        <option value="online" {{ $data->course_mode_training == 'online' ? 'selected' : '' }}>Online</option>
                                        <option value="offline" {{ $data->course_mode_training == 'offline' ? 'selected' : '' }}>Offline</option>
                                    </select>
                                    @error('course_mode_training')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4 nomoduler" style="display: {{$data->course_type_id == 2 ? 'none' : 'block'}}">
                                <div class="form-group">
                                    <label for="coursetrainers">Trainers </label>
                                    <select name="coursetrainers[]" id="coursetrainers" multiple class="form-control select2" placeholder="Select Trainers">
                                        @foreach( $trainers as $trainer )
                                        <option value="{{ $trainer->id }}" {{ in_array($trainer->id, $selectedTrainers) ? 'selected' : '' }}>{{ $trainer->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('coursetrainers')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="course_full_fees">Full Course Fee </label>
                                    <input type="text" class="form-control" value="{{ $data->course_full_fees }}" name="course_full_fees" id="course_full_fees" placeholder="" onkeypress="return isNumberKeyWithDecimal(event)" />
                                    @error('course_full_fees')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4 program-type" style="display: {{$data->course_type_id == 1 ? 'block' : 'none'}}">
                                <label for="program_type_id">Select Program Type</label>
                                <?php
                                    $selected = !empty($data->programTypes) ? $data->programTypes : 0; 
                                    $selectedProgramTypeId = [];
                                    foreach ($selected as $ids) {
                                        $selectedProgramTypeId[] = $ids->pivot->program_type_id;
                                    }
                                ?>
                                <select name="program_type_id[]" class="form-control select2" placeholder="Select Program Type" multiple>
                                    <option value="">Select Program Type</option>
                                    @foreach($programTypelist as $programTypeKey =>  $programType)
                                        <option value="{{$programTypeKey}}" {{ is_array($selectedProgramTypeId) ? in_array($programTypeKey, $selectedProgramTypeId) ? 'selected' : '' : '' }}>{{ $programType }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 moduler" style="display: {{$data->course_type_id != 2 ? 'none' : 'block'}}">
                                <div class="form-group">
                                    <label for="coursesmain">Courses </label>
                                    <select name="coursesmain[]" id="coursesmain" multiple class="form-control select2" placeholder="Select Courses">
                                        @if( !empty($selectedcourses) )
                                        @foreach ($selectedcourses as $course)
                                            <option value="{{$course['id']}}" selected>{{$course['text']}}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                    @error('coursesmain')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="coursetag">Select Course Tag</label>
                                    <select name="coursetag[]" id="coursetag" multiple class="form-control select2">
                                        <option value="">Select Course Tag</option>
                                        @foreach($courseTags as $courseTag)
                                            <option {{ in_array($courseTag->id, $data->courseTags->pluck('id')->toArray()) ? 'selected' : ''}} value="{{$courseTag->id}}">{{$courseTag->name}}</option>
                                        @endforeach
                                    </select>
                                    @error('coursetag')
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
                                    <label for="discount_amount">Discount ($)</label>
                                    <input type="text" class="form-control" value="{{ $data->discount_amount }}" name="discount_amount" id="discount_amount" placeholder="" />
                                    @error('discount_amount')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        {{-- <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mt-3">
                                    <div class="checkbox checkbox-primary">
                                        <input id="application_fees" class="application_fees" name="application_fees" type="checkbox" {{ $data->application_fees == 1 ? 'checked' : '' }}>
                                        <label for="application_fees">Application Fees</label>
                                    </div>
                                </div>
                            </div>
                        </div> --}}
                        <!-- Course Grant Settings Start -->
                        <div class="row">
                            <div class="col-lg-12">
                                <h4 class="mt-3 mb-3">Course Grant Settings</h4>
                                <div class="checkbox checkbox-primary">
                                    <input id="is_grant_active" class="is_grant_active" value="{{ $data->is_grant_active == 1 ? 1 : 0 }}" name="is_grant_active" type="checkbox" {{ $data->is_grant_active == 1 ? 'checked' : '' }}>
                                    <label for="is_grant_active">Grants active</label>
                                </div>
                                <div class="checkbox checkbox-primary">
                                    <input id="is_absorb_gst" class="is_absorb_gst" value="{{ $data->is_absorb_gst == 1 ? 1 : 0 }}" name="is_absorb_gst" type="checkbox" {{ $data->is_absorb_gst == 1 ? 'checked' : '' }}>
                                    <label for="is_absorb_gst">Absorb GST</label>
                                </div>

                                <div class="row" id="with_grant">
                                    <div class="col-lg-2 mb-4">
                                        <label class="control-label">No funding (%) <i class="fa fa-info-circle" data-toggle="tooltip" data-html="true" data-placement="top" title="Default 0% no funding value"></i></label>
                                        <input type="number" min="0" max="100" class="form-control" value="{{$data->no_funding}}" required name="no_funding" id="no_funding" placeholder="" />
                                        @error('no_funding')
                                            <label class="form-text text-danger">{{ $message }}</label>
                                        @enderror
                                    </div>
                                    <div class="col-lg-2 mb-4">
                                        <label class="control-label">Enhanced funding (%) <i class="fa fa-info-circle" data-toggle="tooltip" data-html="true" data-placement="top" title="Enhanced funding apply in percentage value"></i></label>
                                        <input type="number" min="0" max="100" class="form-control" value="{{$data->enhanced_funding}}" required name="enhanced_funding" id="enhanced_funding" placeholder="" />
                                        @error('enhanced_funding')
                                            <label class="form-text text-danger">{{ $message }}</label>
                                        @enderror
                                    </div>
                                    <div class="col-lg-2 mb-4">
                                        <label class="control-label">Baseline funding (%) <i class="fa fa-info-circle" data-toggle="tooltip" data-html="true" data-placement="top" title="Baseline funding apply in percentage value"></i></label>
                                        <input type="number" min="0" max="100" class="form-control" value="{{$data->baseline_funding}}" required name="baseline_funding" id="baseline_funding" placeholder="" />
                                        @error('baseline_funding')
                                            <label class="form-text text-danger">{{ $message }}</label>
                                        @enderror
                                    </div>
                                    <div class="col-lg-2 mb-4">
                                        <label class="control-label">GST (%) <i class="fa fa-info-circle" data-toggle="tooltip" data-html="true" data-placement="top" title="GST apply in percentage value"></i></label>
                                        <input type="number" min="0" max="100" class="form-control" value="{{$data->gst}}" required name="gst" id="gst" placeholder="" />
                                        @error('gst')
                                            <label class="form-text text-danger">{{ $message }}</label>
                                        @enderror
                                    </div>
                                    <div class="col-lg-4 mb-4">
                                        <label for="gst_applied_on">GST Applied On <span class="text-danger">*</span></label>
                                        <select class="form-control select2" name="gst_applied_on" id="gst_applied_on">
                                            <option value="">Select option</option>
                                            <option value="1" {{ $data->gst_applied_on == '1' ? 'selected' : '' }}>Course Fee</option>
                                            <option value="2" {{ $data->gst_applied_on == '2' ? 'selected' : '' }}>Baseline funding</option>
                                        </select>
                                        @error('gst_applied_on')
                                            <label class="form-text text-danger">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row" id="without_grant">
                                    <div class="col-lg-2 mb-4">
                                        <label class="control-label">GST (%) <i class="fa fa-info-circle" data-toggle="tooltip" data-html="true" data-placement="top" title="GST apply in percentage value"></i></label>
                                        <input type="number" min="0" max="100" class="form-control" value="{{$data->gst}}" required name="without_grant_gst" id="gst" placeholder="" />
                                        @error('gst')
                                            <label class="form-text text-danger">{{ $message }}</label>
                                        @enderror
                                    </div>
                                    <div class="col-lg-4 mb-4">
                                        <label for="gst_applied_on">GST Applied On <span class="text-danger">*</span></label>
                                        <select class="form-control select2" name="without_grant_gst_applied_on" id="gst_applied_on">
                                            <option value="">Select option</option>
                                            <option value="1" {{ $data->gst_applied_on == '1' ? 'selected' : '' }}>Course Fee</option>
                                        </select>
                                        @error('gst_applied_on')
                                            <label class="form-text text-danger">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Course Grant Settings End -->
                        
                        <!-- Trainer Folder Settings Start -->
                        <div class="row">
                            <div class="col-lg-12">
                                <h4 class="mt-3 mb-3">Trainer Folder Settings</h4>
                                <div class="row">
                                    <div class="col-lg-6 mb-4">
                                        <label class="control-label">Shared Drive Id <i class="fa fa-info-circle" data-toggle="tooltip" data-html="true" data-placement="top" title="You can find Shared Drive Id from Google Drive URL."></i></label>
                                        <div class="input-group">
                                            <input type="text" name="shared_drive_id" class="form-control" value="{{ $data->shared_drive_id }}" />
                                        </div>
                                    </div>

                                    <div class="col-lg-6 mb-4">
                                        <label class="control-label">Trainer Folder Id <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="You can find Folder Id from Google Drive URL."></i></label>
                                        <div class="input-group">
                                            <input type="text" name="trainer_folder_id" class="form-control" value="{{ $data->trainer_folder_id }}" />
                                        </div>
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col-lg-6 mb-4">
                                        <label class="control-label">Trainer Document File ID <i class="fa fa-info-circle" data-toggle="tooltip" data-html="true" data-placement="top" title="Add Trainer Document File ID from Google Drive URL."></i></label>
                                        <div class="input-group">
                                            <input type="text" name="doc_file_id" class="form-control" value="{{ $data->doc_file_id }}" />
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mb-4">
                                        <label class="control-label">ID Verification and Assessment Links Spreadsheet ID <i class="fa fa-info-circle" data-toggle="tooltip" data-html="true" data-placement="top" title="Add ID Verification and Assessment Links Spreadsheet ID from Google Drive URL."></i></label>
                                        <div class="input-group">
                                            <input type="text" name="spreadsheet_file_id" class="form-control" value="{{ $data->spreadsheet_file_id }}" />
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mb-4">
                                        <label class="control-label">Attendance Records File ID <i class="fa fa-info-circle" data-toggle="tooltip" data-html="true" data-placement="top" title="Add Attendance Records File ID from Google Drive URL."></i></label>
                                        <div class="input-group">
                                            <input type="text" name="attendance_file_id" class="form-control" value="{{ $data->attendance_file_id }}" />
                                        </div>
                                    </div>
                                </div>

                                <hr />
                                <h4 class="mt-3 mb-3">Assessment Settings</h4>
                                <div class="row">
                                    <div class="col-lg-6 mb-4">
                                        <label class="control-label">Assessment Records File ID <i class="fa fa-info-circle" data-toggle="tooltip" data-html="true" data-placement="top" title="Add Assessment Records File ID from Google Drive URL."></i></label>
                                        <div class="input-group">
                                            <input type="text" name="assessment_file_id" class="form-control" value="{{ $data->assessment_file_id }}" />
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mb-4">
                                            <label class="control-label">Assessment Records Shortened URL</label>
                                            <div class="input-group">
                                                <input type="text" name="assessment_short_title" class="form-control" value="{{ $data->assessment_short_title }}" />
                                            </div>
                                    </div>
                                </div>
                                <div class="repeater-custom-show-hide">
                                        <div data-repeater-list="assessments">
                                        @if( $data->assessments->isEmpty() )
                                                <div data-repeater-item="">
                                                    <!-- <h5 class="repeaterIndex">Assessment <span class="repeaterItemNumber">1</span></h5> -->
                                                    <div class="form-group row ">
                                                        

                                                        <div class="col-md-3">
                                                            <label class="control-label">Assessment Document File Title</label>
                                                            <div class="input-group">
                                                                <input type="text" name="assessments[0][assessment_file_title]" class="form-control" value="" />
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3">
                                                            <label class="control-label">Assessment Document File ID</label>
                                                            <div class="input-group">
                                                                <input type="text" name="assessments[0][assessment_file_id]" class="form-control" value="" />
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label class="control-label">Start Time</label>
                                                            <div class="input-group">
                                                                <input type="time" name="assessments[0][start_time]" class="form-control" value="" />
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label class="control-label">End Time</label>
                                                            <div class="input-group">
                                                                <input type="time" name="assessments[0][end_time]" class="form-control" value="" />
                                                            </div>
                                                        </div>
                                                        <div class="col-md-1">
                                                            <label class="control-label">Shortened URL</label>
                                                            <div class="input-group">
                                                                <input type="text" name="assessments[0][short_url]" class="form-control" value="" />
                                                            </div>
                                                        </div>

                                                        <div class="col-md-1 verti-cen mt-4">
                                                            <span data-repeater-delete="" class="btn btn-danger btn-sm">
                                                                <span class="far fa-trash-alt"></span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                @foreach ($data->assessments as $k => $assessmentsdata)
                                                    <div data-repeater-item="">
                                                        <!-- <h5 class="repeaterIndex">Assessment <span class="repeaterItemNumber">1</span></h5> -->
                                                        <div class="form-group row ">
                                                            
                                                            <div class="col-md-3">
                                                                <label class="control-label">Assessment Document File Title</label>
                                                                <div class="input-group">
                                                                    <input type="text" name="assessments[{{$k}}][assessment_file_title]" class="form-control" value="{{ $assessmentsdata->assessment_file_title }}" />
                                                                </div>
                                                            </div>

                                                            <div class="col-md-3">
                                                                <label class="control-label">Assessment Document File ID</label>
                                                                <div class="input-group">
                                                                    <input type="text" name="assessments[{{$k}}][assessment_file_id]" class="form-control" value="{{ $assessmentsdata->assessment_file_id }}" />
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <label class="control-label">Start Time</label>
                                                                <div class="input-group">
                                                                    <input type="time" name="assessments[{{$k}}][start_time]" class="form-control" value="{{ $assessmentsdata->start_time }}" />
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <label class="control-label">End Time</label>
                                                                <div class="input-group">
                                                                    <input type="time" name="assessments[{{$k}}][end_time]" class="form-control" value="{{ $assessmentsdata->end_time }}" />
                                                                </div>
                                                            </div>
                                                            <div class="col-md-1">
                                                                <label class="control-label">Shortened URL</label>
                                                                <div class="input-group">
                                                                    <input type="text" name="assessments[{{$k}}][short_url]" class="form-control" value="{{ $assessmentsdata->short_url }}" />
                                                                </div>
                                                            </div>

                                                            <div class="col-md-1 verti-cen mt-4">
                                                                <span data-repeater-delete="" class="btn btn-danger btn-sm">
                                                                    <span class="far fa-trash-alt"></span>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                            
                                        </div><!--end repet-list-->

                                        <div class="form-group row mb-0 text-center">
                                            <div class="col-sm-12">
                                                <span data-repeater-create="" class="btn btn-secondary btn-md reapet-add">
                                                    <span class="white-add-ico"></span> Add Assessment
                                                </span>
                                            </div><!--end col-->
                                        </div><!--end row-->
                                    </div> <!--end repeter-->
                                </div>

                        </div>
                        <hr />
                        <!-- Trainer Folder Settings End -->

                        <div class="row">
                        {{-- @if( $xero['connection']['status'] && is_null($xero['connection']['data']['error']) ) --}}
                        @if(0)
                        <?php $lineItemCodes = $data->lineItems->pluck('code')->toArray(); ?>
                        <div class="col-md-12">
                            <h1>Xero Configuration</h1>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="brandingTheme">Branding Theme </label>
                                <select name="brandingTheme" id="brandingTheme" class="form-control select2" placeholder="Select Branding Theme">
                                    @foreach( $xero['brandingThemes'] as $theme )
                                    <option value="{{ $theme['branding_theme_id'] }}" {{ $data->branding_theme_id == $theme['branding_theme_id'] ? 'selected' : '' }}>{{ $theme['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="items">Items </label>
                                <select name="items[]" id="items" multiple class="form-control select2" placeholder="Select Branding Theme">
                                    @foreach( $xero['items'] as $item )
                                    <option value="{{ $item['code'] }}" {{ $lineItemCodes ? in_array($item['code'], $lineItemCodes) ? 'selected' : '' : '' }}>{{ $item['name'] }}, {{ $item['sales_details']['unit_price'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @else
                        {{-- <h1 class="header-title mt-0 label-xero col-md-12">You are not connected to Xero</h1>
                        <a href="{{ route('xero.auth.authorize') }}" class="btn btn-primary marl15 btn-large mt-2">
                            Connect to Xero
                        </a> --}}
                        @endif
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="coursefileimage">Course Certificate Image</label>
                                    <div class="custom-file">
                                        <input type="file" accept="image/*" name="coursefileimage" class="custom-file-input" id="coursefileimage">
                                        <label class="custom-file-label" for="coursefileimage">Choose File</label>
                                        @error('coursefileimage')
                                            <label class="form-text text-danger">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="fontList">Name Font Size:</label>
                                    <select class="form-control select2" id="name_font_size">
                                        <option value="30">30px</option>
                                        <option value="35">35px</option>
                                        <option value="40">40px</option>
                                        <option value="45">45px</option>
                                        <option value="50">50px</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="fontList">Date Font Size:</label>
                                    <select class="form-control select2" id="date_font_size">
                                        <option value="25">25px</option>
                                        <option value="30">30px</option>
                                        <option value="35">35px</option>
                                    </select>
                                </div>
                            </div>

                            {{-- <div class="col-md-4 align-self-center">
                                <img src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" alt="course-img" id="course-img" style="height: 150px;" class="w-100" />
                            </div> --}}
                            <canvas id="canvas-container" width="1400" height="1000" class="{{ $data->certificate_file ? '' : 'd-none' }}"></canvas>
                            <input type="hidden" name="cords" value="" id="cords" />

                        </div> 
                    </div><!--end card-body-->
                    <div class="card-footer m-0 clearfix">
                        <button type="submit" class="btn btn-primary mar-r-10">Update</button>
                        <a href="{{ route('admin.coursemain.list') }}" class="btn btn-danger">Cancel</a>
                    </div>
                </form>
            </div><!--end card-->
        </div> <!--end col-->
    </div><!--end row-->

</div><!-- container -->
@endsection
@push("scripts")
<script src="{{ asset('assets/plugins/repeater/jquery.repeater.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/4.2.0/fabric.min.js"></script>
<script src="{{ asset('assets/plugins/clockpicker/jquery-clockpicker.min.js') }}"></script>
<script type="text/javascript">

    $('.repeater-custom-show-hide').repeater({
        @if( $data->assessments->isEmpty() )
            initEmpty: true,
        @endif
        isFirstItemUndeletable: false,
        show: function () {
            //var selfRepeaterItem = this;
            $(this).slideDown();
            $('.select2-container').remove();
            $('.select2').select2({ width: '100%' });
            /*var repeaterItems = $("div[data-repeater-item] > div.form-group");
            $(selfRepeaterItem).attr('data-index', repeaterItems.length - 1);
            $(selfRepeaterItem).find('h5.repeaterIndex span.repeaterItemNumber').text(repeaterItems.length);*/
        },
        hide: function (remove) {
            if (confirm('Are you sure you want to remove this item?')) {
            $(this).slideUp(remove);
            }
        },
    });


    var canvas = undefined;
    function getCoordinates(){
        let coords = [];
        canvas.forEachObject(function(obj){
           let prop = {
                top : obj.aCoords.tl.y,
                left : obj.aCoords.tl.x,
                aCoords: obj.aCoords,
                width : obj.width * obj.scaleX,
                height : obj.height,
                label: obj.label,
                font_size: obj._objects[1].fontSize,
                font_color: obj._objects[1].stroke
            };
            coords.push(prop);
        });
        return coords;
    }

    <?php
    // $nameCords = [ 'left' => 345, 'top' => 315, 'width' => 290, 'height' => 25, 'font_size' => 45, 'font_color' => '#1c3a5a'];
    // $dateCords = [ 'left' => 150, 'top' => 535, 'width' => 150, 'height' => 25, 'font_size' => 30, 'font_color' => '#1c3a5a'];
    $nameCords = [ 'left' => 212, 'top' => 477, 'width' => 432, 'height' => 52, 'font_size' => 30, 'font_color' => '#1c3a5a'];
    $dateCords = [ 'left' => 335, 'top' => 756, 'width' => 150, 'height' => 35, 'font_size' => 25, 'font_color' => '#1c3a5a'];

    if( !empty($data->cert_cordinates) ) {
        $cords = json_decode($data->cert_cordinates, TRUE);
        foreach( $cords as $cord ) {
            if( $cord['label'] == "studentname" ) {
                $nameCords = $cord;
            }
            if( $cord['label'] == "certificatedate" ) {
                $dateCords = $cord;
            }
        }
    }
    ?>
        
    function setCourseCertificateFile() {
        
        let fileName = '{{asset("storage/images/course_certificate"). "/" .$data->certificate_file}}';
        // let fileName = '{{url("assets/images/course_certificate")."/".$data->certificate_file}}';
        fabric.Image.fromURL(fileName, function(img) {
            canvas = new fabric.Canvas('canvas-container');

            fabric.Name = fabric.util.createClass(fabric.Group, {
                type: 'ce.tag',

                initialize: function() {

                    options = {};

                    options.top     = {{$nameCords['top']}};
                    options.left    = {{$nameCords['left']}};
                    options.label = "studentname";

                    var defaults    = {
                        width:  {{$nameCords['width']}},
                        height: {{$nameCords['height']}},
                        stroke: 'black',
                        strokeWidth: 1,
                        originX: 'center',
                        originY: 'center'
                    };

                    this.on('moving', function() {
                        options.left = this.left;
                        options.top = this.top;
                    });
                    
                    var items   = [];

                    items[0]    = new fabric.Rect($.extend({}, defaults, {
                        fill: 'transparent',
                    }));

                    items[1]    = new fabric.Textbox('Name', $.extend({}, defaults, {
                        textAlign: 'center',
                        fontFamily: 'Lato, sans-serif',
                        fontSize: <?php echo json_encode($nameCords['font_size'] ?? null) ?>,
                        stroke: <?php echo json_encode($nameCords['font_color'] ?? null); ?>
                    }));

                    var $fontSize = <?php echo json_encode($nameCords['font_size'] ?? null) ?>;

                    if($fontSize != null){
                        $("#name_font_size").val($fontSize);    
                    }

                    $("#name_font_size").on('change', function(){
                        items[1].set('fontSize', parseInt($(this).val()));
                        canvas.renderAll();
                    })

                    this.callSuper('initialize', items, options);

                },
            });

            fabric.Date = fabric.util.createClass(fabric.Group, {
                type: 'ce.tag',

                initialize: function() {

                    options = {};

                    options.top     = {{$dateCords['top']}};
                    options.left    = {{$dateCords['left']}};
                    options.label = "certificatedate";

                    var defaults    = {
                        width:  {{$dateCords['width']}},
                        height: {{$dateCords['height']}},
                        stroke: 'black',
                        strokeWidth: 1,
                        originX: 'center',
                        originY: 'center'
                    };

                    this.on('moving', function() {
                        options.left = this.left;
                        options.top = this.top;
                    });

                    var items   = [];

                    items[0]    = new fabric.Rect($.extend({}, defaults, {
                        fill: 'transparent',
                    }));

                    items[1]    = new fabric.Textbox('Date', $.extend({}, defaults, {
                        textAlign: 'center',
                        fontFamily: 'Lato, sans-serif',
                        fontSize: <?php echo json_encode($dateCords['font_size'] ?? null) ?>,
                        stroke: <?php echo json_encode($dateCords['font_color'] ?? null); ?>
                    }));

                    $("#date_font_size").on('change', function(){
                        items[1].set('fontSize', parseInt($(this).val()));
                        canvas.renderAll();
                    })

                    var $fontSize = <?php echo json_encode($dateCords['font_size'] ?? null) ?>;

                    if($fontSize != null){
                        $("#date_font_size").val($fontSize);    
                    }

                    this.callSuper('initialize', items, options);

                    // this.on('scaling', function(event) {
                    //     var target = event.transform.target;
                    //     options.width = target.scaleX * target.width
                    //     console.log(target.scaleX * target.width);
                    // });

                },
            });

            // "add" rectangle onto canvas
            canvas.add(new fabric.Name());
            canvas.add(new fabric.Date());
            canvas.setBackgroundImage(img, canvas.renderAll.bind(canvas), {
                scaleX: canvas.width / img.width,
                scaleY: canvas.height / img.height
                // scaleX: canvas.width / img.width,
                // scaleY: img.height
            });
        });
    }
    
    @if( !empty($data->certificate_file) && file_exists(Storage::path('public/images/course_certificate/') . $data->certificate_file) )
        setCourseCertificateFile();
    @endif

    document.getElementById('coursefileimage').addEventListener("change", function(e) {
        document.getElementById('canvas-container').classList.remove("d-none");
        var file = e.target.files[0];
        var reader = new FileReader();
        reader.onload = function(f) {
            var data = f.target.result;
            fabric.Image.fromURL(data, function(img) {
                // create a wrapper around native canvas element (with id="c")
                if( canvas === undefined ) {
                    canvas = new fabric.Canvas('canvas-container');
                }
                canvas.clear();
                // canvas.width = window.innerWidth;
                // canvas.height = window.innerHeight;
                // create a rectangle object

                fabric.Name = fabric.util.createClass(fabric.Group, {
                    type: 'ce.tag',

                    initialize: function() {

                        options = {};
                        options.top     = 472;
                        options.left    = 489; 
                        options.label = "studentname";

                        var defaults    = {
                            width:  432,
                            height: 52,
                            stroke: 'black',
                            strokeWidth: 1,
                            originX: 'center',
                            originY: 'center'
                        };

                        this.on('moving', function() {
                            options.left = this.left;
                            options.top = this.top;
                        });
                        
                        var items   = [];

                        items[0]    = new fabric.Rect($.extend({}, defaults, {
                            fill: 'transparent',
                        }));

                        items[1]    = new fabric.Textbox('Name', $.extend({}, defaults, {
                            textAlign: 'center',
                            fontFamily: 'Lato, sans-serif',
                            fontSize: 30,
                            stroke: '#1c3a5a'
                        }));

                        $("#name_font_size").on('change', function(){
                            items[1].set('fontSize', parseInt($(this).val()));
                            canvas.renderAll();
                        })

                        this.callSuper('initialize', items, options);

                    },
                });

                fabric.Date = fabric.util.createClass(fabric.Group, {
                    type: 'ce.tag',

                    initialize: function() {

                        options = {};
                        options.top     = 756;
                        options.left    = 335;
                        options.label = "certificatedate";

                        var defaults    = {
                            width:  150,
                            height: 35,
                            stroke: 'black',
                            strokeWidth: 1,
                            originX: 'center',
                            originY: 'center'
                        };

                        var items   = [];

                        this.on('moving', function() {
                            options.left = this.left;
                            options.top = this.top;
                        });

                        items[0]    = new fabric.Rect($.extend({}, defaults, {
                            fill: 'transparent',
                        }));

                        items[1]    = new fabric.Textbox('Date', $.extend({}, defaults, {
                            textAlign: 'center',
                            fontFamily: 'Lato, sans-serif',
                            fontSize: 25,
                            stroke: '#1c3a5a'
                        }));

                        $("#date_font_size").on('change', function(){
                            items[1].set('fontSize', parseInt($(this).val()));
                            canvas.renderAll();
                        })
                        
                        this.callSuper('initialize', items, options);

                    },
                });

                canvas.add(new fabric.Name());
                canvas.add(new fabric.Date());

                canvas.on('object:selected', function(e) {
                    if (e.target.type === 'i-text') {
                        document.getElementById('textControls').hidden = false;
                    }
                });

                canvas.on('before:selection:cleared', function(e) {
                    if (e.target.type === 'i-text') {
                        document.getElementById('textControls').hidden = true;
                    }
                });

                // Send selected object to front or back
                var selectedObject;
                canvas.on('object:selected', function(event) {
                    selectedObject = event.target;
                });

                var sendSelectedObjectBack = function() {
                    canvas.sendToBack(selectedObject);
                }

                var sendSelectedObjectToFront = function() {
                    canvas.bringToFront(selectedObject);
                }
                // console.log(img.width);
                // console.log(img.height);
                // console.log(canvas.width);
                // console.log(canvas.width / img.width);
                // console.log(canvas.height / img.height);
                // add background image
                canvas.setBackgroundImage(img, canvas.renderAll.bind(canvas), {
                    scaleX: canvas.width / img.width,
                    scaleY: canvas.height / img.height
                    // scaleX: canvas.width / img.width,
                    // scaleY: img.height
                });
            });
        };
        reader.readAsDataURL(file);

    });

    // Delete selected object
    window.deleteObject = function() {
        var activeGroup = canvas.getActiveGroup();
        if (activeGroup) {
            var activeObjects = activeGroup.getObjects();
            for (let i in activeObjects) {
                canvas.remove(activeObjects[i]);
            }
            canvas.discardActiveGroup();
            canvas.renderAll();
        } else canvas.getActiveObject().remove();
    }

    // Refresh page
    function refresh() {
        setTimeout(function() {
            location.reload()
        }, 100);
    }

    // Add text
    function Addtext() {
        canvas.add(new fabric.IText('Tap and Type', {
            left: 50,
            top: 100,
            fontFamily: 'helvetica neue',
            fill: '#000',
            stroke: '#fff',
            strokeWidth: .1,
            fontSize: 45
        }));
    }

   // Download
    // var imageSaver = document.getElementById('lnkDownload');
    // imageSaver.addEventListener('click', saveImage, false);

    function saveImage(e) {
        this.href = canvas.toDataURL({
            format: 'png',
            quality: 0.8
        });
        this.download = 'custom.png'
    }

    // Do some initializing stuff
    fabric.Object.prototype.set({
        transparentCorners: false,
        cornerColor: '#22A7F0',
        borderColor: '#22A7F0',
        cornerSize: 12,
        padding: 5
    });

    function formatResult(opt) {
        if (!opt.id) {
            return opt.text;
        }
        var $opt = $( '<span record="'+opt+'">'+ opt.text + '</span>');
        return $opt;
    };

    function formatSelection(opt) {
        return opt.text;
    }

    $('#coursemainedit').submit(function(e) {
        $('#cords').val(JSON.stringify(getCoordinates()));
        // e.preventDefault();
        // $(this).submit();
    });

    $(document).ready(function() {
        $(".select2").select2({ width: '100%' });

        $(document).on('change', '#course_type', function(e) {
            let _val = $(this).val();
            if( _val == 2 ) {
                $('#skillcodediv').hide();  
            } else {
                $('#skillcodediv').show();
            }
        });

        $(document).on('change', '#course_type_id', function(e) {
            let _val = $(this).val();
            if( _val == 2 ) {
                $('.nomoduler').hide();
                $('.moduler').show();
            } else {
                $('.moduler').hide();
                $('.nomoduler').show();
            }
        });

        $(document).on('change', '#course_type_id', function(e) {
            let _val = $(this).val();
            if( _val == 1 ) {
                $('.program-type').show();
            }else {
                $('.program-type').hide();
            }
        });

        $("#coursesmain").select2({
            placeholder: 'Search Courses',
            multiple: true,
            minimumInputLength: 3,
            width: '100%',
            templateResult: formatResult,
            templateSelection: formatSelection,
            ajax: {
                url: "{{ route('admin.ajax.search.maincourses') }}",
                type: "get",
                dataType: "JSON",
                data: function (params) {
                    return { q: params.term, /*search term*/ };
                },
                processResults: function (response) {
                    return { results: response };
                },
                delay: 250,
                cache: true
            },
        });

        if($("#is_grant_active").prop('checked') == true){
            $('#without_grant').hide();
        } else {
            $('#with_grant').hide();
        }
    });
    $('#is_grant_active').click(function() {
        if($(this).is(':not(:checked)')){
            $(this).val(0);
            $('#without_grant').show();
            $('#with_grant').hide();
        } else {
            $(this).val(1);
            $('#without_grant').hide();
            $('#with_grant').show();
        }
    });
</script>
@endpush
