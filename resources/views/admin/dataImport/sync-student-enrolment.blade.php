@extends('admin.layouts.master')
@section('title', 'Sync Student Enrolment')
@section('content')
<div class="container-fluid">
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Sync Student Enrolment</a></li>
                        <li class="breadcrumb-item active">Sync</li>
                    </ol>
                </div>
                <h4 class="page-title">Sync Student Enrolment</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mt-0">Sync Student Enrolment - <span id="studentTitle"></span></h4>
                        <h4 class="header-title mt-0">Course Details - <span id="courseTitle"></span></h4>
                        <h4 class="header-title mt-0">Course Run - <span id="courseRun"></span></h4>
                        <div class="row">

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="enrolment_id">Enrolment Id <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="" name="enrolment_id" id="enrolment_id" placeholder="" />
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="studentName">Student Name</label>
                                    <br/>
                                    <span id="studentName"></span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="studentEmail">Student Email</label>
                                    <br/>
                                    <span id="studentEmail"></span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="studentNric">student NRIC</label>
                                    <br/>
                                    <span id="studentNric"></span>
                                </div>
                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="mobileNumber">Mobile Number</label>
                                    <br/>
                                    <span id="mobileNumber"></span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="dateOfBirth">Date Of Birth</label>
                                    <br/>
                                    <span id="dateOfBirth"></span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="enrolmentDate">Enrolment Date</label>
                                    <br/>
                                    <span id="enrolmentDate"></span>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <h3>Fees</h3>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="discountAmount">Discount Amount</label>
                                    <br/>
                                    <span id="discountAmount"></span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="collectionStatus">Fee Status</label>
                                    <br/>
                                    <span id="collectionStatus"></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <h3>Grant</h3>
                            </div>
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <th>Grant Status</th>
                                            <th>Grant Ref No</th>
                                            <th>Funding Scheme</th>
                                            <th>Funding Component</th>
                                            <th>Amount Estimated</th>
                                            <th>Amount Paid</th>
                                            <th>Amount Recovery</th>
                                        </thead>
                                        <tbody id="grantResBody">

                                        </tbody>
                                    </table>
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

    function setStudentEnrolmentData(enrolment, grantRes) {
        $('#studentTitle').text(`${enrolment.referenceNumber} - ${enrolment.status}`);
        $('#courseTitle').text(`${enrolment.course.title} - ${enrolment.course.referenceNumber}`);
        $('#courseRun').text(`${enrolment.course.run.id} (${enrolment.course.run.startDate} - ${enrolment.course.run.endDate})`);

        $('#studentName').text(enrolment.trainee.fullName);
        $('#studentEmail').text(enrolment.trainee.email.full);
        $('#studentNric').text(enrolment.trainee.id);
        $('#mobileNumber').text(`${enrolment.trainee.contactNumber.countryCode} ${enrolment.trainee.contactNumber.phoneNumber}`);
        $('#dateOfBirth').text(enrolment.trainee.dateOfBirth);
        $('#enrolmentDate').text(enrolment.trainee.enrolmentDate);
        $('#discountAmount').text(enrolment.trainee.fees.discountAmount);
        $('#collectionStatus').text(enrolment.trainee.fees.collectionStatus);
        // grant data
        grantRes.map(grant => {
            $('#grantResBody').append(`<tr>
                                        <td>${grant.status}</td>
                                        <td>${grant.referenceNumber}</td>
                                        <td>${grant.fundingScheme.code} - ${grant.fundingScheme.description}</td>
                                        <td>${grant.fundingComponent.code} - ${grant.fundingComponent.description}</td>
                                        <td>${grant.grantAmount.estimated}</td>
                                        <td>${grant.grantAmount.paid}</td>
                                        <td>${grant.grantAmount.recovery}</td>
                                    </tr>`);
        });
    }

    $(function () {

        // sync course run by id
        $(document).on('click', '#getFromTPG', function(e) {
            e.preventDefault();
            let _enrolment_id = $('#enrolment_id').val();
            if( $.trim(_enrolment_id) == "" ) {
                showToast("Please add enrolment id", 0);
                $('#enrolment_id').focus();
                return false;
            }
            var btn = $('#getFromTPG');
            BITBYTE.progress(btn);
            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                url: '{{ route('admin.ajax.getTpgStudentEnrolment') }}',
                type: "POST",
                dataType: "JSON",
                data: {
                    enrolment_id: _enrolment_id
                },
                success: function(res) {
                    BITBYTE.unprogress(btn);
                    if( res.status == true ) {
                        showToast(res.msg, 1);
                        $('#getFromTPG').addClass('d-none');
                        if(res.courserun == false ) {
                            showToast("Course run is not in our database. Please Sync Course run first", 0);
                        } else {
                            if( res.already == true ) {
                                $('#alreadyTMS').removeClass('d-none');
                            } else {
                                $('#saveToTMS').removeClass('d-none');
                            }
                        }
                        setStudentEnrolmentData(res.data, res.grantRes);
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
            let _enrolment_id = $('#enrolment_id').val();
            if( $.trim(_enrolment_id) == "" ) {
                showToast("Please add enrolment id", 0);
                $('#enrolment_id').focus();
                return false;
            }
            var btn = $('#saveToTMS');
            BITBYTE.progress(btn);
            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                url: '{{ route('admin.ajax.saveTpgStudentEnrolment') }}',
                type: "POST",
                dataType: "JSON",
                data: {
                    enrolment_id: _enrolment_id
                },
                success: function(res) {
                    BITBYTE.unprogress(btn);
                    if( res.status == true ) {
                        showToast(res.msg, 1);
                    } else {
                        showToast(res.msg, 0);
                    }
                    /*setTimeout(function(){
                        location.reload();
                    }, 2000);*/
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

