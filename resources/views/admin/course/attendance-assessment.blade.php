@extends('admin.layouts.master')
@section('title', 'Attendance Assessment List - Course Run')
@section('content')
<div class="container-fluid">
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Courses Runs - Attendance & Assessment</a></li>
                        <li class="breadcrumb-item active">List</li>
                    </ol>
                </div>
                <h4 class="page-title">Courses Run - Attendance & Assessment</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <!-- end page title end breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if( $result->courseMain->course_type_id == 1 )
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="float-right">
                            
                                <form class="d-inline-block" action="{{ route('admin.course.submit-payment-tpgateway', $result->id) }}" id="payment_submit_tpg" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" class="hiddenStudentsId" name="hiddenStudentsId" />
                                    <button type="submit" class="btn btn-success px-4 btn-rounded mt-0 mb-3">Submit Payment To TP Gateway</button>
                                </form>

                                <form class="d-inline-block" action="{{ route('admin.course.submit-attendance-tpgateway', $result->id) }}" id="attendance_submit_tpg" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" class="hiddenStudentsId" name="hiddenStudentsId" />
                                    <input type="hidden" class="hiddenStudentsRefresherId" name="hiddenStudentsRefresherId" />
                                    <button type="submit" class="btn btn-success px-4 btn-rounded mt-0 mb-3">Submit Attendance To TP Gateway</button>
                                </form>

                                <form class="d-inline-block" action="{{ route('admin.course.submit-assessment-tpgateway', $result->id) }}" id="assessment_submit_tpg" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" class="hiddenStudentsId" name="hiddenStudentsId" />
                                    <input type="hidden" class="hiddenStudentsRefresherId" name="hiddenStudentsRefresherId" />
                                    <button type="submit" class="btn btn-success px-4 btn-rounded mt-0 mb-3 mr-3">Submit Assessment To TP Gateway</button>
                                </form>
                           
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="float-right">

                                <form class="d-inline-block" action="{{ route('admin.course.get-payment-tpgateway', $result->id) }}" id="get_payment_submit_tpg" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    {{-- <input type="hidden" class="hiddenStudentsId" name="hiddenStudentsId" /> --}}
                                    <button type="submit" class="btn btn-info px-4 btn-rounded mt-0 mb-3">Sync Payment Status From TP Gateway</button>
                                </form>

                                <form class="d-inline-block" action="{{ route('admin.course.sync-attendance-tpgateway', $result->id) }}" id="attendance_sync_tpg" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <button type="submit" class="btn btn-info px-4 btn-rounded mt-0 mb-3">Sync Attendance From TP Gateway</button>
                                </form>

                                <form class="d-inline-block" action="{{ route('admin.course.sync-assessment-tpgateway', $result->id) }}" id="assessment_sync_tpg" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <button type="submit" class="btn btn-info px-4 btn-rounded mt-0 mb-3 mr-3">Sync Assessment From TP Gateway</button>
                                </form>

                            </div>
                        </div>
                    </div>
                    @endif
                    <form action="{{ route('admin.course.save-attendance-assessment', $result->id) }}" id="courserun_attendance_save" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row float-right marlr0">
                            <a class="btn btn-primary pt-10 px-4 btn-rounded mt-0 mb-3" type="button" href="{{ route('admin.course.list', $result->courseMain->id) }}">Go Back</a>
                        </div>
                        <h4 class="header-title mt-0">Attendance & Assessment List - {{$result->courseMain->name}}</h4>
                        <h4 class="header-title mt-0">Course Date - {{$result->course_start_date}} to {{$result->course_end_date}}</h4>
                        <div class="table-responsive dash-social course-attendance-assessment">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th><input type="checkbox" name="checkallstudent[]" id="checkallstudent" value=""></th>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>NRIC</th>
                                    <th>Email</th>
                                    <th>Payment Status</th>
                                    @foreach ($result->session as $k => $session)
                                        <th>Session {{ $k + 1 }}: {{$session->start_date}}:{{$session->start_time}}</th>
                                    @endforeach
                                    <th>Assessment</th>
                                    <th>Action</th>
                                </tr><!--end tr-->
                                </thead>

                                <tbody>
                                    <?php $isSubmitted = false; ?>
                                    @foreach ($result->courseActiveEnrolments as $s => $student)
                                        @php
                                        $studentAttendances = is_null($student->attendance) ? NULL : json_decode($student->attendance);
                                        @endphp
                                        @if( !is_null($studentAttendances) && !$isSubmitted )
                                        <?php $isSubmitted = true; ?>
                                        @endif
                                        <tr>
                                            <td><input type="checkbox" name="checkstudent[]" class="checksingle" value="{{$student->id}}"></td>
                                            <td>{{++$s}}</td>
                                            <td>{{$student->student->name}}</td>
                                            <td>{{convertNricToView($student->student->nric)}}</td>
                                            <td>{{$student->email}}</td>
                                            {{-- <td>{{getPaymentStatus($student->payment_status)}}</td> --}}
                                            
                                            <td>
                                                <select class="form-control select2 payment-dropdown" name="payment_{{$student->id}}">
                                                    <option value="3" {{ !is_null($student->payment_tpg_status) && getPaymentStatusForTPG($student->payment_tpg_status) == 'Full Payment' ? 'selected' : '' }}>Full Payment</option>
                                                    <option value="1" {{ !is_null($student->payment_tpg_status) && getPaymentStatusForTPG($student->payment_tpg_status) == 'Pending Payment' ? 'selected' : '' }}>Pending Payment</option>
                                                    <option value="2" {{ !is_null($student->payment_tpg_status) && getPaymentStatusForTPG($student->payment_tpg_status) == 'Partial Payment' ? 'selected' : '' }}>Partial Payment</option>
                                                    <option value="4" {{ !is_null($student->payment_tpg_status) && getPaymentStatusForTPG($student->payment_tpg_status) == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                </select>
                                                @if(!empty($student->tpg_payment_sync))
                                                    @if($student->tpg_payment_sync == 1)
                                                    <p class="sync_status green"><span><i class="fas fa-check-circle"></i></span></p>
                                                    @elseif($student->tpg_payment_sync == 2)
                                                    <p class="sync_status red"><span><i class="fas fa-times-circle"></i></span></p>
                                                    @endif
                                                @endif
                                            </td>
                                            @foreach ($result->session as $session)
                                                <?php $currentSession = $currentSessRemark = NULL; ?>
                                                @if( !is_null($studentAttendances) )
                                                    @foreach ($studentAttendances as $att)
                                                        @if( $att->start_date == $session->start_date && $att->start_time == $session->start_time )
                                                            <?php
                                                                $currentSession = $att->ispresent;
                                                                $currentSessRemark = $att->remark;
                                                                $currentSyncStatus = $att->att_sync ?? '';
                                                            ?>
                                                        @endif
                                                    @endforeach
                                                @endif
                                                <td>
                                                    <div class="">
                                                        <div class="form-check-inline my-1">
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" id="attendance_{{$student->id}}_{{$session->id}}_present" name="attendance_{{$student->id}}_{{$session->id}}" value="1" class="custom-control-input" {{ is_null($currentSession) ? 'checked' : '' }} {{ !is_null($currentSession) && $currentSession == 1 ? 'checked' : '' }}>
                                                                <label class="custom-control-label" for="attendance_{{$student->id}}_{{$session->id}}_present">Present</label>
                                                            </div>
                                                        </div>
                                                        <div class="form-check-inline my-1">
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" id="attendance_{{$student->id}}_{{$session->id}}_absent" name="attendance_{{$student->id}}_{{$session->id}}" value="0" class="custom-control-input" {{ !is_null($currentSession) && $currentSession == 0 ? 'checked' : '' }} />
                                                                <label class="custom-control-label" for="attendance_{{$student->id}}_{{$session->id}}_absent">Absent</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <input type="text" class="form-control" name="att_remark_{{$student->id}}_{{$session->id}}" value="{{ $currentSessRemark }}" placeholder="Remark" />
                                                    <input type="hidden" class="form-control" name="att_sync_{{$student->id}}_{{$session->id}}" value="{{$currentSyncStatus ?? ''}}"/>
                                                    @if(!empty($currentSyncStatus))
                                                        @if($currentSyncStatus == 1)
                                                        <p class="sync_status green"><span><i class="fas fa-check-circle"></i></span></p>
                                                        @elseif($currentSyncStatus == 2)
                                                        <p class="sync_status red"><span><i class="fas fa-times-circle"></i></span></p>
                                                        @endif
                                                    @endif
                                                </td>
                                            @endforeach
                                            <td>
                                                <div class="col-md-9">
                                                    <div class="form-check-inline my-1">
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" id="assessment_{{$student->id}}_c" name="assessment_{{$student->id}}" class="custom-control-input" value="c" {{ $student->assessment == 'nyc' ? '' : 'checked' }}>
                                                            <label class="custom-control-label" for="assessment_{{$student->id}}_c">C</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-check-inline my-1">
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" id="assessment_{{$student->id}}_nyc" name="assessment_{{$student->id}}" class="custom-control-input" value="nyc" {{ $student->assessment == 'nyc' ? 'checked' : '' }} />
                                                            <label class="custom-control-label" for="assessment_{{$student->id}}_nyc">NYC</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-check-inline my-1">
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" id="assessment_{{$student->id}}_void" name="assessment_{{$student->id}}" class="custom-control-input" value="void" {{ $student->assessment == 'void' ? 'checked' : '' }} />
                                                            <label class="custom-control-label" for="assessment_{{$student->id}}_void">Void</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <input type="text" class="form-control" name="assessment_remark_{{$student->id}}" value="{{ $student->assessment_remark }}" placeholder="Remark" />
                                                @if(!empty($student->assessment_sync))
                                                        @if($student->assessment_sync == 1)
                                                        <p class="sync_status green"><span><i class="fas fa-check-circle"></i></span></p>
                                                        @elseif($student->assessment_sync == 2)
                                                        <p class="sync_status red"><span><i class="fas fa-times-circle"></i></span></p>
                                                        @endif
                                                @endif
                                            </td>
                                            <td>
                                                @can('studentenrolment-view')
                                                    <div class="">
                                                    <a href="{{route('admin.studentenrolment.view', $student->id)}}" target="_blank" data-toggle="tooltip" data-placement="bottom" title="View Enrolment" class="d-inline-block mr-2 eye-back"><i class="fas fa-eye text-info font-16"></i></a>
                                                    </div>
                                                @endcan
                                                <div class="mt-3">
                                                    <a href="javascript:void(0);" data-enrolid="{{$student->id}}" data-toggle="tooltip" data-placement="bottom" title="Void Assessment" class="void-assessment d-inline-block mr-2 eye-back"><i class="fas fa-times-circle text-info font-16"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    @foreach ($result->courseActiveRefreshers as $s => $student)
                                        @php
                                        $studentAttendances = is_null($student->attendance) ? NULL : json_decode($student->attendance);
                                        @endphp
                                        @if( !is_null($studentAttendances) && !$isSubmitted )
                                        <?php $isSubmitted = true; ?>
                                        @endif
                                        <tr>
                                            <td><input type="checkbox" name="checkstudentRefreshers[]" class="checksingle refresher" value="{{$student->id}}"></td>
                                            <td>{{++$s}}</td>
                                            <td>{{$student->student->name}}</td>
                                            <td>{{convertNricToView($student->student->nric)}}</td>
                                            <td>{{$student->student->email}}</td>

                                            <td></td>
                                            @if( $student->isAttendanceRequired )
                                            @foreach ($result->session as $session)
                                                <?php $currentSession = $currentSessRemark = NULL; ?>
                                                @if( !is_null($studentAttendances) )
                                                    @foreach ($studentAttendances as $att)
                                                        @if( $att->start_date == $session->start_date && $att->start_time == $session->start_time )
                                                            <?php
                                                                $currentSession = $att->ispresent;
                                                                $currentSessRemark = $att->remark;
                                                                $currentSyncStatus = $att->att_sync ?? '';
                                                            ?>
                                                        @endif
                                                    @endforeach
                                                @endif
                                                <td>
                                                    <div class="">
                                                        <div class="form-check-inline my-1">
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" id="attendance_refreshers_{{$student->id}}_{{$session->id}}_present" name="attendance_refreshers_{{$student->id}}_{{$session->id}}" value="1" class="custom-control-input" {{ is_null($currentSession) ? 'checked' : '' }} {{ !is_null($currentSession) && $currentSession == 1 ? 'checked' : '' }}>
                                                                <label class="custom-control-label" for="attendance_refreshers_{{$student->id}}_{{$session->id}}_present">Present</label>
                                                            </div>
                                                        </div>
                                                        <div class="form-check-inline my-1">
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" id="attendance_refreshers_{{$student->id}}_{{$session->id}}_absent" name="attendance_refreshers_{{$student->id}}_{{$session->id}}" value="0" class="custom-control-input" {{ !is_null($currentSession) && $currentSession == 0 ? 'checked' : '' }} />
                                                                <label class="custom-control-label" for="attendance_refreshers_{{$student->id}}_{{$session->id}}_absent">Absent</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <input type="text" class="form-control" name="att_remark_refreshers_{{$student->id}}_{{$session->id}}" value="{{ $currentSessRemark }}" placeholder="Remark" />
                                                    <input type="hidden" class="form-control" name="att_sync_refreshers_{{$student->id}}_{{$session->id}}" value="{{$currentSyncStatus ?? ''}}"/>
                                                    @if(!empty($currentSyncStatus))
                                                        @if($currentSyncStatus == 1)
                                                        <p class="sync_status green"><span><i class="fas fa-check-circle"></i></span></p>
                                                        @elseif($currentSyncStatus == 2)
                                                        <p class="sync_status red"><span><i class="fas fa-times-circle"></i></span></p>
                                                        @endif
                                                    @endif
                                                </td>
                                            @endforeach
                                            @else
                                            <td></td>
                                            @endif
                                            @if( $student->isAssessmentRequired )
                                            <td>
                                                <div class="col-md-9">
                                                    <div class="form-check-inline my-1">
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" id="assessment_refreshers_{{$student->id}}_c" name="assessment_refreshers_{{$student->id}}" class="custom-control-input" value="c" {{ $student->assessment == 'nyc' ? '' : 'checked' }}>
                                                            <label class="custom-control-label" for="assessment_refreshers_{{$student->id}}_c">C</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-check-inline my-1">
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" id="assessment_refreshers_{{$student->id}}_nyc" name="assessment_refreshers_{{$student->id}}" class="custom-control-input" value="nyc" {{ $student->assessment == 'nyc' ? 'checked' : '' }} />
                                                            <label class="custom-control-label" for="assessment_refreshers_{{$student->id}}_nyc">NYC</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-check-inline my-1">
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" id="assessment_refreshers_{{$student->id}}_void" name="assessment_refreshers_{{$student->id}}" class="custom-control-input" value="void" {{ $student->assessment == 'void' ? 'checked' : '' }} />
                                                            <label class="custom-control-label" for="assessment_refreshers_{{$student->id}}_void">Void</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <input type="text" class="form-control" name="assessment_refreshers_remark_{{$student->id}}" value="{{ $student->assessment_remark }}" placeholder="Remark" />
                                                @if(!empty($student->assessment_sync))
                                                        @if($student->assessment_sync == 1)
                                                        <p class="sync_status green"><span><i class="fas fa-check-circle"></i></span></p>
                                                        @elseif($student->assessment_sync == 2)
                                                        <p class="sync_status red"><span><i class="fas fa-times-circle"></i></span></p>
                                                        @endif
                                                @endif
                                            </td>
                                            @else
                                            <td></td>
                                            @endif
                                            <td>

                                                <div>
                                                    <a href="{{route('admin.refreshers.view', $student->id)}}" target="_blank" data-toggle="tooltip" data-placement="bottom" title="View Enrolment" class="d-inline-block mr-2 eye-back"><i class="fas fa-eye text-info font-16"></i></a>
                                                </div>
                                                <div class="mt-3">
                                                    <a href="javascript:void(0);" data-enrolid="{{$student->id}}" data-toggle="tooltip" data-placement="bottom" title="Void Assessment" class="d-inline-block void-assessment mr-2 eye-back"><i class="fas fa-times-circle text-info font-16"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="row marlr0">
                            <button type="submit" class="btn btn-primary mar-r-10 mt-4"><?php echo $isSubmitted ? 'Update' : 'Save' ?></button>
                        </div>
                    </form>
                </div><!--end card-body-->
            </div><!--end card-->
        </div> <!--end col-->
    </div><!--end row-->

</div><!-- container -->
@endsection

@push('scripts')
<script type="text/javascript">
    $(function () {

        /* Void Assessment Start */
        $(document).on('click', '.void-assessment', function(e) {
            e.preventDefault();
            var btn = $(this).find('i');
            let _enrolement_id = $(this).data('enrolid');
            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                url: '{{ route('admin.ajax.voidAssessment') }}',
                type: "POST",
                dataType: "JSON",
                data: {
                    id: _enrolement_id
                },
                beforeSend: function(){
                    btn.removeClass('fas fa-times-circle text-info font-16').addClass('fas fa-spinner fa-spin text-info font-16');
                },
                success: function(res) {
                    btn.removeClass('fas fa-spinner fa-spin text-info font-16').addClass('fas fa-times-circle text-info font-16');
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
                    btn.removeClass('fas fa-spinner fa-spin text-info font-16').addClass('fas fa-times-circle text-info font-16');
                    if( err.status == 422 ) {
                        showToast(err.responseJSON.message, 0);
                        return false;
                    }
                }
            });  // end ajax
        });

        /* Void Assessment end */

        $("#attendance_submit_tpg, #assessment_submit_tpg, #payment_submit_tpg").submit(function(e){
            if($( 'input[class^="checksingle"]:checked' ).length === 0) {
                alert( 'Please select any one student' );
                e.preventDefault();
            }
        });

        $('.select2 ').select2();

        var hidden = $('.hiddenStudentsId');
        var hiddenRefresher = $('.hiddenStudentsRefresherId');
        $('#checkallstudent').click(function() {
            var state = $(this).prop('checked');
            if(state === false) {
            hidden.val('');
            }
            var vals = [];
            var refreshervals = [];
            $('.checksingle').each(function() {
                $(this).prop('checked', state);
                if( $(this).hasClass('refresher') ) {
                    if(this.checked === true) {
                        refreshervals.push($(this).val());
                    }
                } else {
                    if(this.checked === true) {
                        vals.push($(this).val());
                    }
                }
            });
            hidden.val(vals.toString());
            hiddenRefresher.val(refreshervals.toString());
        });
        $(".checksingle").change(function() {
            var values = [];
            let refreshervals = [];
            $('.checksingle').each(function(index, obj) {
            if(this.checked === true) {
                if( $(this).hasClass('refresher') ) {
                    refreshervals.push($(this).val());
                } else {
                    values.push($(this).val());
                }
            }
            });
            hidden.val(values);
            hiddenRefresher.val(refreshervals);
        });

        $("#checkallstudent").change(function(){
            if(this.checked){
            $(".checksingle").each(function(){
                this.checked=true;
            })              
            }else{
            $(".checksingle").each(function(){
                this.checked=false;
            })              
            }
        });

        $(".checksingle").click(function () {
            if ($(this).is(":checked")){
            var isAllChecked = 0;
            $(".checksingle").each(function(){
                if(!this.checked)
                isAllChecked = 1;
            })              
            if(isAllChecked == 0){ $("#checkallstudent").prop("checked", true); }     
            }else {
            $("#checkallstudent").prop("checked", false);
            }
        });

    });
</script>
@endpush
