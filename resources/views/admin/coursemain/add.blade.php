@extends('admin.layouts.master')
@section('title', 'Add Course')
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
                        <li class="breadcrumb-item active">Add</li>
                    </ol>
                </div>
                <h4 class="page-title">Add Course</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form action="{{ route('admin.coursemain.add') }}" method="POST" id="coursemainadd" enctype="multipart/form-data" novalidate>
                    @csrf
                    <!-- <h5 class="card-header bg-secondary text-white mt-0">Add Course</h5> -->
                    <div class="card-body">
                        <h4 class="header-title mt-0">Add Course</h4>
                        <div class="row">

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="{{ old('name') }}" required name="name" id="name" placeholder="" />
                                    @error('name')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-2">
                                <label for="name">Course Abbreviation</label>
                                <input type="text" class="form-control" value="{{ old('course_abbreviation') }}" required name="course_abbreviation" id="course_abbreviation" placeholder="" />
                                @error('course_abbreviation')
                                    <label class="form-text text-danger">{{ $message }}</label>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="course_type_id">Select Course Module <span class="text-danger">*</span></label>
                                    <select name="course_type_id" id="course_type_id" required class="form-control select2">
                                        <option value="">Select Course Module</option>
                                        @foreach($courseTypelist as $courseTypesRow)
                                        <option value="{{$courseTypesRow->id}}" {{ old('course_type_id') == $courseTypesRow->id ? 'selected' : '' }}>{{ $courseTypesRow->name }} </option>
                                        @endforeach
                                    </select>
                                    @error('course_type_id')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="course_type">Course Type<span class="text-danger">*</span></label>
                                    <select name="course_type" id="course_type" required  class="form-control select2">
                                        <option value="">Select Course Type</option>
                                        @foreach( getCourseType() as $key => $courseType )
                                        <option value="{{ $key }}" {{ old('course_type') == $key ? 'selected' : '' }}>{{ $courseType }}</option>
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
                                    <input type="text" class="form-control" value="{{ old('reference_number') }}" required name="reference_number" id="reference_number" placeholder="" />
                                    @error('reference_number')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4" id="skillcodediv">
                                <div class="form-group">
                                    <label for="skill_code">Competency Code / Skill Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="{{ old('skill_code') }}" name="skill_code" id="skill_code" placeholder="" />
                                    @error('skill_code')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4 nomoduler">
                                <div class="form-group">
                                    <label for="course_mode_training">Course Mode of Training <span class="text-danger">*</span></label>
                                    <select class="form-control select2" name="course_mode_training" id="course_mode_training">
                                        <option value="">Select option</option>
                                        <option value="online" {{ old('course_mode_training') == 'online' ? 'selected' : '' }}>Online</option>
                                        <option value="offline" {{ old('course_mode_training') == 'offline' ? 'selected' : '' }}>Offline</option>
                                    </select>
                                    @error('course_mode_training')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4 nomoduler">
                                <div class="form-group">
                                    <label for="coursetrainers">Trainers </label>
                                    <select name="coursetrainers[]" id="coursetrainers" multiple class="form-control select2" placeholder="Select Trainers">
                                        @foreach( $trainers as $trainer )
                                        <option value="{{ $trainer->id }}" {{ old('coursetrainers') ? in_array($trainer->id, old('coursetrainers')) ? 'selected' : '' : '' }}>{{ $trainer->name }}</option>
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
                                    <input type="text" class="form-control" value="{{ old('course_full_fees') ?? '888.00' }}" name="course_full_fees" id="course_full_fees" placeholder="" onkeypress="return isNumberKeyWithDecimal(event)" />
                                    @error('course_full_fees')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4 program-type" style="display:none;">
                                <label for="program_type_id">Select Program Type</label>
                                <select name="program_type_id[]" class="form-control select2" placeholder="Select Program Type" multiple>
                                    <option value="">Select Program Type</option>
                                    @foreach($programTypelist as $programTypeKey =>  $programType)
                                        <option value="{{$programTypeKey}}" {{ is_array(old('program_type_id')) ? in_array($programTypeKey, old('program_type_id')) ? 'selected' : '' : '' }}>{{ $programType }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 moduler" style="display: none;">
                                <div class="form-group">
                                    <label for="coursesmain">Courses </label>
                                    <select name="coursesmain[]" id="coursesmain" class="form-control select2" placeholder="Select Courses">
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
                                           <option value="{{$courseTag->id}}" {{ is_array(old('coursetag')) ? in_array($courseTag->id, old('coursetag')) ? 'selected' : '' : '' }}>{{ $courseTag->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('coursetag')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        {{-- <div class="row">
                            <div class="col-md-4 mt-3">
                                <div class="form-group">
                                    <div class="checkbox checkbox-primary">
                                        <input id="application_fees" class="application_fees" name="application_fees" type="checkbox" >
                                        <label for="application_fees">Application Fees</label>
                                    </div>
                                </div>
                            </div>
                        </div> --}}
                        {{-- {{dd($xero['items'])}} --}}

                        <!-- Trainer Folder Settings Start -->
                        <div class="row">
                            <div class="col-lg-12">
                                <h4 class="mt-3 mb-3">Trainer Folder Settings</h4> 
                                <div class="row">
                                    <div class="col-lg-6 mb-4">
                                        <label class="control-label">Shared Drive Id <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="You can find Shared Drive Id from Google Drive URL."></i></label>
                                        <div class="input-group">
                                            <input type="text" name="shared_drive_id" class="form-control" value="" />
                                        </div>
                                    </div>

                                    <div class="col-lg-6 mb-4">
                                        <label class="control-label">Trainer Folder Id <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="You can find Folder Id from Google Drive URL."></i></label>
                                        <div class="input-group">
                                            <input type="text" name="trainer_folder_id" class="form-control" value="" />
                                        </div>
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col-lg-6 mb-4">
                                        <label class="control-label">Trainer Document File ID <i class="fa fa-info-circle" data-toggle="tooltip" data-html="true" data-placement="top" title="Add Trainer Document File ID from Google Drive URL."></i></label>
                                        <div class="input-group">
                                            <input type="text" name="doc_file_id" class="form-control" value="" />
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mb-4">
                                        <label class="control-label">ID Verification and Assessment Links Spreadsheet ID <i class="fa fa-info-circle" data-toggle="tooltip" data-html="true" data-placement="top" title="Add ID Verification and Assessment Links Spreadsheet ID from Google Drive URL."></i></label>
                                        <div class="input-group">
                                            <input type="text" name="spreadsheet_file_id" class="form-control" value="" />
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mb-4">
                                        <label class="control-label">Attendance Records File ID <i class="fa fa-info-circle" data-toggle="tooltip" data-html="true" data-placement="top" title="Add Attendance Records File ID from Google Drive URL."></i></label>
                                        <div class="input-group">
                                            <input type="text" name="attendance_file_id" class="form-control" value="" />
                                        </div>
                                    </div>
                                </div>

                                <hr />
                                
                                <h4 class="mt-3 mb-3">Assessment Settings</h4>
                                <div class="row">
                                    <div class="col-lg-6 mb-4">
                                        <label class="control-label">Assessment Records File ID <i class="fa fa-info-circle" data-toggle="tooltip" data-html="true" data-placement="top" title="Add Assessment Records File ID from Google Drive URL."></i></label>
                                        <div class="input-group">
                                            <input type="text" name="assessment_file_id" class="form-control" value="" />
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mb-4">
                                            <label class="control-label">Assessment Records Shortened URL</label>
                                            <div class="input-group">
                                                <input type="text" name="assessment_short_title" class="form-control" value="" />
                                            </div>
                                    </div>
                                </div>
                                <div class="mt-4 repeater-custom-show-hide">
                                        <div data-repeater-list="assessments">
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
                        <div class="col-md-12">
                            <h1>Xero Configuration</h1>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="brandingTheme">Branding Theme </label>
                                <select name="brandingTheme" id="brandingTheme" class="form-control select2" placeholder="Select Branding Theme">
                                    @foreach( $xero['brandingThemes'] as $theme )
                                    <option value="{{ $theme['branding_theme_id'] }}" {{ old('brandingTheme') ? $theme['branding_theme_id'] == old('brandingTheme') ? 'selected' : '' : '' }}>{{ $theme['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="items">Items </label>
                                <select name="items[]" id="items" multiple class="form-control select2" placeholder="Select Branding Theme">
                                    @foreach( $xero['items'] as $item )
                                    <option value="{{ $item['code'] }}" {{ old('items') ? in_array($item['code'], old('items')) ? 'selected' : '' : '' }}>{{ $item['name'] }}, {{ $item['sales_details']['unit_price'] }}</option>
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

                        <div class="row  mt-4">
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
                            <canvas id="canvas-container" width="1400" height="1000" class="d-none"></canvas>
                            <input type="hidden" name="cords" value="" id="cords" />
                        </div>
                        

                    </div>

                   <!--  </div> -->
                    <div class="card-footer m-0 clearfix">
                        <button type="submit" class="btn btn-primary mar-r-10">Submit</button>
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
        initEmpty: true,
        isFirstItemUndeletable: false,
       show: function () {
           
           $(this).slideDown();
           $('.select2-container').remove();
           $('.select2').select2({
               width: '100%',
               // placeholder: "Placeholder text",
               // allowClear: true
           });
           /*var selfRepeaterItem = this;
           var repeaterItems = $("div[data-repeater-item] > div.form-group");
           $(selfRepeaterItem).attr('data-index', repeaterItems.length - 1);
           $(selfRepeaterItem).find('h5.repeaterIndex span.repeaterItemNumber').text(repeaterItems.length);*/
       },
       hide: function (remove) {
           if (confirm('Are you sure you want to remove this item?')) {
           $(this).slideUp(remove);
           }
       },
   });

    // $('#canvas-container').attr('width', window.innerWidth);
    // $('#canvas-container').attr('height', window.innerHeight);

    $('#coursemainadd').submit(function(e) {
        $('#cords').val(JSON.stringify(getCoordinates()));
        // e.preventDefault();
        // $(this).submit();
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

    document.getElementById('coursefileimage').addEventListener("change", function(e) {
        document.getElementById('canvas-container').classList.remove("d-none");

        var file = e.target.files[0];
        var reader = new FileReader();
        reader.onload = function(f) {
            var data = f.target.result;
            fabric.Image.fromURL(data, function(img) {
                // create a wrapper around native canvas element (with id="c")
                canvas = new fabric.Canvas('canvas-container');
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
                            height: 25,
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
                            fontSize: 30,
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

/*
    var lineOffset = 4;
    var anchrSize = 2;

    var mousedown = false;
    var clickedArea = {box: -1, pos:'o'};
    var x1 = -1;
    var y1 = -1;
    var x2 = -1;
    var y2 = -1;

    var boxes = [];
    var tmpBox = null;

    document.getElementById("canvas").onmousedown = function(e) {
        mousedown = true;
        clickedArea = findCurrentArea(e.offsetX, e.offsetY);
        x1 = e.offsetX;
        y1 = e.offsetY;
        x2 = e.offsetX;
        y2 = e.offsetY;
    };

    document.getElementById("canvas").onmouseup = function(e) {
        if (clickedArea.box == -1 && tmpBox != null) {
            boxes.push(tmpBox);
        } else if (clickedArea.box != -1) {
            var selectedBox = boxes[clickedArea.box];
            if (selectedBox.x1 > selectedBox.x2) {
                var previousX1 = selectedBox.x1;
                selectedBox.x1 = selectedBox.x2;
                selectedBox.x2 = previousX1;
            }
            if (selectedBox.y1 > selectedBox.y2) {
                var previousY1 = selectedBox.y1;
                selectedBox.y1 = selectedBox.y2;
                selectedBox.y2 = previousY1;
            }
        }

        clickedArea = {box: -1, pos:'o'};
        tmpBox = null;
        mousedown = false;
        console.log(boxes);
    };

    document.getElementById("canvas").onmouseout = function(e) {
        if (clickedArea.box != -1) {
            var selectedBox = boxes[clickedArea.box];
            if (selectedBox.x1 > selectedBox.x2) {
                var previousX1 = selectedBox.x1;
                selectedBox.x1 = selectedBox.x2;
                selectedBox.x2 > previousX1;
            }
            if (selectedBox.y1 > selectedBox.y2) {
                var previousY1 = selectedBox.y1;
                selectedBox.y1 = selectedBox.y2;
                selectedBox.y2 > previousY1;
            }
        }
        mousedown = false;
        clickedArea = {box: -1, pos:'o'};
        tmpBox = null;
    };

    document.getElementById("canvas").onmousemove = function(e) {
        if (mousedown && clickedArea.box == -1) {
            x2 = e.offsetX;
            y2 = e.offsetY;
            redraw();
        } else if (mousedown && clickedArea.box != -1) {
    x2 = e.offsetX;
    y2 = e.offsetY;
    xOffset = x2 - x1;
    yOffset = y2 - y1;
    x1 = x2;
    y1 = y2;

    if (clickedArea.pos == 'i'  ||
        clickedArea.pos == 'tl' ||
        clickedArea.pos == 'l'  ||
        clickedArea.pos == 'bl') {
      boxes[clickedArea.box].x1 += xOffset;
    }
    if (clickedArea.pos == 'i'  ||
        clickedArea.pos == 'tl' ||
        clickedArea.pos == 't'  ||
        clickedArea.pos == 'tr') {
      boxes[clickedArea.box].y1 += yOffset;
    }
    if (clickedArea.pos == 'i'  ||
        clickedArea.pos == 'tr' ||
        clickedArea.pos == 'r'  ||
        clickedArea.pos == 'br') {
      boxes[clickedArea.box].x2 += xOffset;
    }
    if (clickedArea.pos == 'i'  ||
        clickedArea.pos == 'bl' ||
        clickedArea.pos == 'b'  ||
        clickedArea.pos == 'br') {
      boxes[clickedArea.box].y2 += yOffset;
    }
    redraw();
  }
}

function redraw() {
  // canvas.width = canvas.width;
  var context = document.getElementById("canvas").getContext('2d');
  context.clearRect(0, 0, 800, 600);
  context.beginPath();
  for (var i = 0; i < boxes.length; i++) {
    drawBoxOn(boxes[i], context);
  }
  if (clickedArea.box == -1) {
    tmpBox = newBox(x1, y1, x2, y2);
    if (tmpBox != null) {
      drawBoxOn(tmpBox, context);
    }
  }
}

function findCurrentArea(x, y) {
  for (var i = 0; i < boxes.length; i++) {
    var box = boxes[i];
    xCenter = box.x1 + (box.x2 - box.x1) / 2;
    yCenter = box.y1 + (box.y2 - box.y1) / 2;
    if (box.x1 - lineOffset <  x && x < box.x1 + lineOffset) {
      if (box.y1 - lineOffset <  y && y < box.y1 + lineOffset) {
        return {box: i, pos:'tl'};
      } else if (box.y2 - lineOffset <  y && y < box.y2 + lineOffset) {
        return {box: i, pos:'bl'};
      } else if (yCenter - lineOffset <  y && y < yCenter + lineOffset) {
        return {box: i, pos:'l'};
      }
    } else if (box.x2 - lineOffset < x && x < box.x2 + lineOffset) {
      if (box.y1 - lineOffset <  y && y < box.y1 + lineOffset) {
        return {box: i, pos:'tr'};
      } else if (box.y2 - lineOffset <  y && y < box.y2 + lineOffset) {
        return {box: i, pos:'br'};
      } else if (yCenter - lineOffset <  y && y < yCenter + lineOffset) {
        return {box: i, pos:'r'};
      }
    } else if (xCenter - lineOffset <  x && x < xCenter + lineOffset) {
      if (box.y1 - lineOffset <  y && y < box.y1 + lineOffset) {
        return {box: i, pos:'t'};
      } else if (box.y2 - lineOffset <  y && y < box.y2 + lineOffset) {
        return {box: i, pos:'b'};
      } else if (box.y1 - lineOffset <  y && y < box.y2 + lineOffset) {
        return {box: i, pos:'i'};
      }
    } else if (box.x1 - lineOffset <  x && x < box.x2 + lineOffset) {
      if (box.y1 - lineOffset <  y && y < box.y2 + lineOffset) {
        return {box: i, pos:'i'};
      }
    }
  }
  return {box: -1, pos:'o'};
}

function newBox(x1, y1, x2, y2) {
  boxX1 = x1 < x2 ? x1 : x2;
  boxY1 = y1 < y2 ? y1 : y2;
  boxX2 = x1 > x2 ? x1 : x2;
  boxY2 = y1 > y2 ? y1 : y2;
  if (boxX2 - boxX1 > lineOffset * 2 && boxY2 - boxY1 > lineOffset * 2) {
    return {x1: boxX1,
            y1: boxY1,
            x2: boxX2,
            y2: boxY2,
            lineWidth: 1,
            color: 'DeepSkyBlue'};
  } else {
    return null;
  }
}

function drawBoxOn(box, context) {
  xCenter = box.x1 + (box.x2 - box.x1) / 2;
  yCenter = box.y1 + (box.y2 - box.y1) / 2;

  context.strokeStyle = box.color;
  context.fillStyle = box.color;

  context.rect(box.x1, box.y1, (box.x2 - box.x1), (box.y2 - box.y1));
  context.lineWidth = box.lineWidth;
  context.stroke();

  context.fillRect(box.x1 - anchrSize, box.y1 - anchrSize, 2 * anchrSize, 2 * anchrSize);
  context.fillRect(box.x1 - anchrSize, yCenter - anchrSize, 2 * anchrSize, 2 * anchrSize);
  context.fillRect(box.x1 - anchrSize, box.y2 - anchrSize, 2 * anchrSize, 2 * anchrSize);
  context.fillRect(xCenter - anchrSize, box.y1 - anchrSize, 2 * anchrSize, 2 * anchrSize);
  context.fillRect(xCenter - anchrSize, yCenter - anchrSize, 2 * anchrSize, 2 * anchrSize);
  context.fillRect(xCenter - anchrSize, box.y2 - anchrSize, 2 * anchrSize, 2 * anchrSize);
  context.fillRect(box.x2 - anchrSize, box.y1 - anchrSize, 2 * anchrSize, 2 * anchrSize);
  context.fillRect(box.x2 - anchrSize, yCenter - anchrSize, 2 * anchrSize, 2 * anchrSize);
  context.fillRect(box.x2 - anchrSize, box.y2 - anchrSize, 2 * anchrSize, 2 * anchrSize);
}

*/

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
            }else {
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
    });
</script>
@endpush
