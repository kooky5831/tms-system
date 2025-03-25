@extends('admin.layouts.master')
@section('title', 'Sync Course Run')
@section('content')
<div class="container-fluid">
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Sync Course Run</a></li>
                        <li class="breadcrumb-item active">Sync</li>
                    </ol>
                </div>
                <h4 class="page-title">Sync Course Run</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mt-0">Sync Course Run - <span id="courseTitle"></span></h4>
                        <div class="row">

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="courserun_id">Course Run Id <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="" name="courserun_id" id="courserun_id" placeholder="" />
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="maintrainer">Main Trainer</label>
                                    <br/>
                                    <span id="maintrainer"></span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="modeoftraining">Mode Of Training </label>
                                    <select name="modeoftraining" id="modeoftraining" disabled class="form-control select2">
                                        <option value="">Select Mode</option>
                                        @foreach( getModeOfTraining() as $key => $modeoftraining )
                                        <option value="{{ $key }}">{{ $modeoftraining }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="venue_id">Course Primary Venue</label>
                                    <br/>
                                    <span id="venue_id"></span>
                                </div>
                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="registration_opening_date">Registration Opening Date</label>
                                    <br/>
                                    <span id="registration_opening_date"></span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="registration_closing_date">Registration Closing Date</label>
                                    <br/>
                                    <span id="registration_closing_date"></span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="course_start_date">Course Start Date</label>
                                    <br/>
                                    <span id="course_start_date"></span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="course_end_date">Course End Date</label>
                                    <br/>
                                    <span id="course_end_date"></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="coursevacancy_code">Course Vacancy Code<span class="text-danger">*</span></label>
                                    <select class="form-control select2" name="coursevacancy_code" id="coursevacancy_code" disabled>
                                        <option value="">Select Vacancy Code</option>
                                        @foreach( getCourseVacancy() as $key => $vacancy )
                                        <option value="{{ $key }}">{{ $vacancy }}</option>
                                        @endforeach
                                    </select>
                                    @error('coursevacancy_code')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>

                        </div>

                        <div id="sessionsDiv">
                        </div>

                        <hr />

                        <div class="row mt-4">

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="intakesize">Max Intake Size : <span id="intakesize"></span></label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="threshold">Threshold : <span id="threshold"></span></label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="threshold">Registered User : <span id="registeredUserCount"></span></label>
                                </div>
                            </div>

                        </div>

                    </div>

                    <!-- </div> -->
                    <div class="card-footer m-0 clearfix">
                        <p id="alreadyTMS" class="text-danger d-none">Already Exist in TMS</p>
                        <button type="button" class="btn btn-primary mar-r-10" id="getFromTPG">Get From TP Gateway</button>
                        <button type="button" class="btn btn-primary mar-r-10 d-none" id="saveToTMS">Save to TMS</button>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-danger">Cancel</a>
                    </div>
            </div><!--end card-->
        </div> <!--end col-->
    </div><!--end row-->

</div><!-- container -->
@endsection
@push('scripts')
<script type="text/javascript">

    function setCourseRunData(data) {
        let {course} = data;
        $('#courseTitle').text(`${course.title} - ${course.referenceNumber}`);
        $('#venue_id').text(`${course.run.venue.building} ${course.run.venue.street} - ${course.run.venue.postalCode}`);
        if( course.run.linkCourseRunTrainer[0] ) {
            let trainer = course.run.linkCourseRunTrainer[0].trainer;
            $('#maintrainer').text(`${trainer.name} ${trainer.email}`);
        }
        let regOpenDate = course.run.registrationOpeningDate.toString();
        let registrationOpeningDate = new Date(regOpenDate.substr(0, 4), regOpenDate.substr(4, 2) - 1, regOpenDate.substr(6, 2));
        $('#registration_opening_date').text(registrationOpeningDate.toDateString());
        let regCloseDate = course.run.registrationClosingDate.toString();
        let registrationClosingDate = new Date(regCloseDate.substr(0, 4), regCloseDate.substr(4, 2) - 1, regCloseDate.substr(6, 2));
        $('#registration_closing_date').text(registrationClosingDate.toDateString());
        // courseStartDate
        let courseStrDate = course.run.courseStartDate.toString();
        let courseStartDate = new Date(courseStrDate.substr(0, 4), courseStrDate.substr(4, 2) - 1, courseStrDate.substr(6, 2));
        $('#course_start_date').text(courseStartDate.toDateString());
        // courseEndDate
        let courseEdDate = course.run.courseEndDate.toString();
        let courseEndDate = new Date(courseEdDate.substr(0, 4), courseEdDate.substr(4, 2) - 1, courseEdDate.substr(6, 2));
        $('#course_end_date').text(courseEndDate.toDateString());
        $('#coursevacancy_code').val(course.run.courseVacancy.code);
        $('#intakesize').text(course.run.intakeSize);
        $('#modeoftraining').val(course.run.modeOfTraining);
        $('#registeredUserCount').text(course.run.registeredUserCount);
        // scheduleInfo
        // scheduleInfoType.code
        $('#threshold').text(course.run.threshold);
    }

    $(function () {

        // sync course run by id
        $(document).on('click', '#getFromTPG', function(e) {
            e.preventDefault();
            let _courserun_id = $('#courserun_id').val();
            if( $.trim(_courserun_id) == "" ) {
                showToast("Please add course run id", 0);
                $('#courserun_id').focus();
                return false;
            }
            var btn = $('#getFromTPG');
            BITBYTE.progress(btn);
            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                url: '{{ route('admin.ajax.getTpgCourseRun') }}',
                type: "POST",
                dataType: "JSON",
                data: {
                    courserun_id: _courserun_id
                },
                success: function(res) {
                    BITBYTE.unprogress(btn);
                    if( res.status == true ) {
                        showToast(res.msg, 1);
                        $('#getFromTPG').addClass('d-none');
                        if( res.sessions ) {
                            $('#sessionsDiv').append(`<h2>Sessions :</h2>`);
                            res.sessions.map((session, i) => {
                                let startDate = session.startDate;
                                let strDate = new Date(startDate.substr(0, 4), startDate.substr(4, 2) - 1, startDate.substr(6, 2));
                                let endDate = session.endDate;
                                let edDate = new Date(endDate.substr(0, 4), endDate.substr(4, 2) - 1, endDate.substr(6, 2));
                                $('#sessionsDiv').append(`<div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Start Date : <span>${strDate.toDateString()}</span></label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Start Time : <span>${session.startTime}</span></label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>End Date : <span>${edDate.toDateString()}</span></label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>End Time : <span>${session.endTime}</span></label>
                                        </div>
                                    </div>
                                </div>`);
                            });
                        }
                        if( res.already == true ) {
                            $('#alreadyTMS').removeClass('d-none');
                        } else {
                            $('#saveToTMS').removeClass('d-none');
                        }
                        setCourseRunData(res.data);
                    } else {
                        showToast(res.msg, 0);
                    }
                },
                error: function(err) {
                    BITBYTE.unprogress(btn);
                    if( err.status == 422 ) {
                        // display error
                        showToast(err.responseJSON.message, 0);
                        return false;
                    }
                }
            }); // end ajax
        });

        // save to database
        $(document).on('click', '#saveToTMS', function(e) {
            e.preventDefault();
            let _courserun_id = $('#courserun_id').val();
            if( $.trim(_courserun_id) == "" ) {
                showToast("Please add course run id", 0);
                $('#courserun_id').focus();
                return false;
            }
            var btn = $('#saveToTMS');
            BITBYTE.progress(btn);
            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                url: '{{ route('admin.ajax.saveTpgCourseRun') }}',
                type: "POST",
                dataType: "JSON",
                data: {
                    courserun_id: _courserun_id
                },
                success: function(res) {
                    BITBYTE.unprogress(btn);
                    if( res.status == true ) {
                        showToast(res.msg, 1);
                    } else {
                        showToast(res.msg, 0);
                    }
                    setTimeout(function(){
                        location.reload();
                    }, 2000);
                },
                error: function(err) {
                    BITBYTE.unprogress(btn);
                    if( err.status == 422 ) {
                        // display error
                        showToast(err.responseJSON.message, 0);
                        return false;
                    }
                }
            }); // end ajax
        });

    });
</script>
@endpush

