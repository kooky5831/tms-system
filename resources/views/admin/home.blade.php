@extends('admin.layouts.master')
@section('title', 'Dashboard')
@push('css')
<link href="{{ asset('assets/plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
<style>
    .paginate {float: right;margin-top: 30px;}
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
                        {{-- <li class="breadcrumb-item"><a href="javascript:void(0);">{{config('app.name')}}</a></li> --}}
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="dripicons-home"></i></a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
                <h4 class="page-title">Dashboard</h4>
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div><!--end row-->
    <!-- end page title end breadcrumb -->

    <div class="row dash-box">
        <div class="col-lg-3">
            <div class="card card-eco blue-back">
                <div class="card-body">
                    
                    <div class="d-flex ">
                        <i class="dripicons-cart card-eco-icon text-secondary align-self-center"></i>
                        <div>
                            <h4 class="title-text mt-0">New Students</h4>
                            <h3 class="font-weight-bold clr-wht">{{$data['newStudents']}}</h3>
                            {{-- <div class="progress">
                                <div class="progress-bar" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width:50%">
                                  <span class="sr-only">50% Complete</span>
                                </div>
                            </div>
                            <p class="sub-text"><span>20%</span> Increase in 30 Days</p> --}}
                        </div>
                    </div>
                   <!--  <p class="mb-0 text-muted text-truncate">This Month</p> -->
                </div><!--end card-body-->
            </div><!--end card-->
        </div><!--end col-->
        <div class="col-lg-3">
            <div class="card card-eco green-back">
                <div class="card-body">
                    <div class="d-flex ">
                        <i class="dripicons-heart card-eco-icon text-pink align-self-center"></i>
                        <div>
                            <h4 class="title-text mt-0">Total Students</h4>
                            <h3 class="font-weight-bold">{{$data['totalStudents']}}</h3>
                            {{-- <div class="progress">
                                <div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width:70%">
                                  <span class="sr-only">70% Complete</span>
                                </div>
                            </div>
                            <p class="sub-text"><span>40%</span> Increase in 10 Days</p> --}}
                        </div>
                    </div>
                    <!-- <p class="mb-0 text-muted text-truncate">This Month</p> -->
                    {{-- <p class="mb-0 text-muted text-truncate">Till today</p> --}}
                </div><!--end card-body-->
            </div><!--end card-->
        </div><!--end col-->
        <div class="col-lg-3">
            <div class="card card-eco purple-back">
                <div class="card-body">
                    <div class="d-flex ">
                        <i class="dripicons-user-group card-eco-icon text-warning  align-self-center"></i>
                        <div>
                            <h4 class="title-text mt-0">Total Courses</h4>
                            <h3 class="font-weight-bold">{{$data['totalCourses']}}</h3>
                            {{-- <div class="progress">
                                <div class="progress-bar" role="progressbar" aria-valuenow="90" aria-valuemin="0" aria-valuemax="100" style="width:90%">
                                  <span class="sr-only">90% Complete</span>
                                </div>
                            </div>
                            <p class="sub-text"><span>40%</span> Increase in 10 Days</p> --}}
                        </div>
                    </div>
                    <!-- <p class="mb-0 text-muted text-truncate">&nbsp;</p> -->
                    {{-- <p class="mb-0 text-muted text-truncate"><span class="text-danger"><i class="mdi mdi-trending-down"></i>3%</span> Down From Last Month</p> --}}
                </div><!--end card-body-->
            </div><!--end card-->
        </div><!--end col-->
        <div class="col-lg-3">
            <div class="card card-eco redish-back">
                <div class="card-body">
                    <div class="d-flex ">
                        <i class="dripicons-user card-eco-icon text-success align-self-center"></i>
                        <div>
                            <h4 class="title-text mt-0">Total Fees</h4>
                            <h3 class="font-weight-bold">${{number_format($data['totalFees'], 2)}}</h3>
                            {{-- <div class="progress">
                                <div class="progress-bar" role="progressbar" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100" style="width:30%">
                                  <span class="sr-only">30% Complete</span>
                                </div>
                            </div>
                            <p class="sub-text"><span>40%</span> Increase in 10 Days</p> --}}
                        </div>
                    </div>
                    {{-- <p class="mb-0 text-muted text-truncate"><span class="text-success"><i class="mdi mdi-trending-up"></i>10.5%</span> Up From Yesterday</p> --}}
                    <!-- <p class="mb-0 text-muted text-truncate">&nbsp;</p> -->
                </div>
            </div>
        </div>
    </div>

    {{-- Tasks List --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    {{-- {{dd($data['oldPendingTask'])}} --}}
                    {{-- {{dd($data['todaysTasks'])}} --}}

                    @if( count($data['oldPendingTask']) )
                    <h4 class="mt-0 header-title mb-4">Old Pending Tasks</h4>
                    <a class="btn btn-dark position-absolute btn-rounded float-right mark-complete-all" style="top: 25px; right: 20px;" href="javascript:void(0)" id="mark-complete-all"><i class="fas fa-tasks font-16"></i> Mark Complete </a>
                    <div class="row">
                        <div class="col-12">
                            <ul class="list-group">
                                @foreach( $data['oldPendingTask'] as $oldtask )
                                <li class="list-group-item w-100">
                                    <label class="customcheck">
                                        <input type="checkbox" class="multiComplete" name="oldIds" value="{{$oldtask->id}}">
                                        {{-- <input type="checkbox" checked="checked"> --}}
                                        <span class="checkmark"></span>
                                    </label>
                                        <div class="task-info">
                                            @if( $oldtask->status == 1 )
                                                <span class="badge badge-soft-primary">Created</span>
                                            @elseif( $oldtask->status == 2 )
                                                <span class="badge badge-soft-warning">Pending</span>
                                            @else
                                                <span class="badge badge-soft-success">Completed</span>
                                            @endif
                                            @if( $oldtask->course_id )
                                            {{ $oldtask->course->course_start_date }},
                                            {{ $oldtask->course->courseMain->name }}
                                            @endif
                                            <strong class="d-block mt-2">{{ triggerEventTypes($oldtask->task_type) }}
                                                <div class="d-inline-block">
                                                    @if( $oldtask->task_type == 3 )
                                                        - {{ $oldtask->task_text }}
                                                    @endif 
                                                </div>
                                            </strong>
                                            <div>
                                                @if(!empty($oldtask->notes))
                                                    <strong>Notes:</strong> {!! nl2br(e($oldtask->notes)) !!}
                                                @endif
                                            </div>
                                        </div>
                                        <span class="btn-icon-group-style d-flex justify-content-end">
                                            
                                            @if( $oldtask->task_type == 1 )
                                            <a href="{{route('admin.tasks.sendTaskEmail', $oldtask->id)}}" data-toggle="tooltip" data-placement="bottom" title="Send Email" class="btn btn-success btn-sm mr-2"><i class="mdi mdi-email-check-outline font-16"></i></a>
                                            @elseif( $oldtask->task_type == 2 )
                                            <a href="{{route('admin.tasks.sendTasksms', $oldtask->id)}}" data-toggle="tooltip" data-placement="bottom" title="Send SMS" class="btn btn-success btn-sm mr-2"><i class="fas fa-sms font-16"></i></a>
                                            @endif
                                            @if( $oldtask->status != 3 )
                                            <a href="javascript:void(0)" task_id="{{$oldtask->id}}" data-toggle="tooltip" data-placement="bottom" title="Mark Task as Completed" class="btn btn-dark btn-sm mr-2 marktaskComplete"><i class="fas fa-tasks font-16"></i></a>
                                            @endif
                                            <a href="javascript:void(0)" task_id="{{$oldtask->id}}" data-toggle="tooltip" data-placement="bottom" title="Update Note" class="btn btn-warning btn-sm mr-2 updatetasknote"><i class="mdi mdi-note font-16"></i></a>
                                            @if( $oldtask->course_id )
                                            <a href="{{ route('admin.course.courserunview', $oldtask->course_id) }}" data-toggle="tooltip" data-placement="bottom" title="View Course Run" class="btn btn-info btn-sm mr-2"><i class="fas fa-eye font-16"></i></a>
                                            @endif
                                            
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div><!--end row-->
                    <br />
                    @endif
                    <div class="position-relative">
                        <h4 class="mt-0 header-title mb-4">Todays Tasks</h4>
                        @if(count($data['todaysTasks']->get()))
                            <a class="btn btn-dark btn-rounded position-absolute top-0 float-right mark-today-complete-all" style="top: 25px; right: 20px;" href="javascript:void(0)" id="mark-complete-all"><i class="fas fa-tasks font-16"></i> Mark Complete </a>
                        @endif
                        <div class="row">
                            <div class="col-12">
                                <ul class="list-group">
                                    @if( count($data['todaysTasks']->get()) )
                                    @php
                                    $courseIds = $data['todaysTasks']->pluck('course_id')->toArray();
                                    $courseIdDone = [];
                                    $lastCourceId = 0;
                                    @endphp
                                    @foreach( $data['todaysTasks']->orderBy('course_id', 'ASC')->orderBy('priority','DESC')->get() as $task )
                                    <li class="list-group-item">
                                        @if (count(array_keys($courseIds, $task->course_id)) > 1)
                                            <?php
                                                $lastCourceId++;
                                                if( !in_array($task->course_id, $courseIdDone) ) {
                                                array_push($courseIdDone, $task->course_id);
                                                ?>
                                                <div class="collapse-task">
                                                    <h4>{{ $task->course->courseMain->name }}</h4> 
                                                    <button class="btn btn-success btn-dark courseMain_{{$task->course->courseMain->id}}" type="button" data-toggle="collapse" data-target="#courseMain_{{$task->course->courseMain->id}}" aria-expanded="false" aria-controls="collapseExample" onclick="collapseToggle({{$task->course->courseMain->id}})"><i class='fas fa-plus'></i></button>
                                                </div>
                                                    <ul class="list-group collapse collapse-task-ul" id="courseMain_{{$task->course->courseMain->id}}">
                                                    <li class="list-group-item">
                                            <?php } ?>
                                        @endif
                                        <label class="customcheck">
                                            <input type="checkbox" class="multiComplete" name="newIds" value="{{$task->id}}">
                                            {{-- <input type="checkbox" checked="checked"> --}}
                                            <span class="checkmark"></span>
                                        </label>
                                        <div class="task-status float-left">
                                            @if( $task->status == 1 )
                                                <span class="badge badge-soft-primary">Created</span>
                                            @elseif( $task->status == 2 )
                                                <span class="badge badge-soft-warning">Pending</span>
                                            @else
                                                <span class="badge badge-soft-success">Completed</span>
                                            @endif
                                            @if( $task->course_id )
                                            {{ $task->course->course_start_date }},
                                            {{ $task->course->courseMain->name }}
                                            @endif
                                            <strong class="d-block mt-2">{{ triggerEventTypes($task->task_type) }}
                                                <div class="d-inline-block">
                                                    @if( $task->task_type == 3 )
                                                    - {{ $task->task_text }}
                                                    @endif
                                                    @if( !is_null($task->completed_at) )
                                                        {{ $task->completed_at }} by - {{ $task->completedByUser->name }}
                                                    @endif 
                                                </div>
                                            </strong>
                                            <div>
                                                @if(!empty($task->notes))
                                                    <strong>Notes:</strong> {!! nl2br(e($task->notes)) !!}
                                                @endif
                                            </div>
                                            
                                        </div>
                                        <div class="float-right">
                                            @if( $task->task_type == 1 && $task->status != 3 )
                                            <a href="{{route('admin.tasks.sendTaskEmail', $task->id)}}" data-toggle="tooltip" data-placement="bottom" title="Send Email" class="btn btn-success btn-sm mr-2"><i class="mdi mdi-email-check-outline font-16"></i></a>
                                            @elseif( $task->task_type == 2 && $task->status != 3 )
                                            <a href="{{route('admin.tasks.sendTasksms', $task->id)}}" data-toggle="tooltip" data-placement="bottom" title="Send SMS" class="btn btn-success btn-sm mr-2"><i class="fas fa-sms font-16"></i></a>
                                            @endif
                                            @if( $task->status != 3 )
                                            <a href="javascript:void(0)" task_id="{{$task->id}}" data-toggle="tooltip" data-placement="bottom" title="Mark Task as Completed" class="btn btn-dark btn-sm mr-2 marktaskComplete"><i class="fas fa-tasks font-16"></i></a>
                                            @endif
                                            @if( $task->status == 3)
                                            <a href="javascript:void(0)" task_id="{{$task->id}}" data-toggle="tooltip" data-placement="bottom" title="Mark Task as Uncomplete" class="mr-2 edit-back marktaskUncomplete" >
                                                <i class="fas fa-history font-16"></i>
                                            </a>
                                            @endif
                                            <a href="javascript:void(0)" task_id="{{$task->id}}" data-toggle="tooltip" data-placement="bottom" title="Update Note" class="btn btn-warning btn-sm mr-2 updatetasknote"><i class="mdi mdi-note font-16"></i></a>
                                            @if( $task->course_id )
                                            <a href="{{ route('admin.course.courserunview', $task->course_id) }}" data-toggle="tooltip" data-placement="bottom" title="View Course Run" class="btn btn-info btn-sm mr-2"><i class="fas fa-eye font-16"></i></a>
                                            @endif
                                        </div>
                                        @if (count(array_keys($courseIds, $task->course_id)) == $lastCourceId)
                                            <?php $lastCourceId = 0; ?>
                                            </li>
                                            </ul>
                                        @endif
                                        </li>
                                    @endforeach
                                    @else
                                        <li class="list-group-item">No Task for Today</li>
                                    @endif
                                </ul>
                            </div>
                        </div><!--end row-->
                    </div>


                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body"> 
                    <h4 class="header-title mt-0 mb-3">Digital Marketing Career Programme</h4>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Student Name</th>
                                    <th>NRIC</th>
                                    <th>Email</th>
                                    <th>View Course Runs</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($courseCDMSData->isNotEmpty())
                                    @foreach($courseCDMSData as $cdms)
                                        <tr>
                                            <td>{{$cdms->student->name}}</td>
                                            <td>{{convertNricToView($cdms->student->nric)}}</td>
                                            <td>{{$cdms->student->email}}</td>
                                            <td><a class="cdmscourserun mr-2 eye-back" href="javascript:void(0)" student_id="{{$cdms->student_id}}" gform_id="{{$cdms->gform_id}}" data-toggle="tooltip" data-placement="bottom" title="View Course Runs"><i class="fas fa-eye text-info font-16"></i></a></td>
                                        </tr>
                                    @endforeach
                                @else
                                <tr>
                                    <td colspan="7" align="center">No Student Available</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="paginate">
                        {!! $courseCDMSData->links() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <a class="btn btn-secondary px-4 btn-rounded float-right mt-0 mb-3" href="{{ route('admin.studentenrolment.list') }}">View All</a>
                    <h4 class="header-title mt-0 mb-3">New Student List</h4>
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="">
                                <tr>
                                    <th>Student Name</th>
                                    <th>Nric</th>
                                    <th>Email</th>
                                    <th>Phone No</th>
                                    <th>Course Run</th>
                                    <th>Payment Status</th>
                                    <th>Action</th>
                                </tr><!--end tr-->
                            </thead>
                            <tbody>
                                @if( !$data['newstudentsList']->isEmpty() )
                                @foreach( $data['newstudentsList'] as $students )
                                <tr>
                                    <td>{{$students->student->name}}</td>
                                    <td>{{convertNricToView($students->student->nric)}}</td>
                                    <td>{{$students->email}}</td>
                                    <td>{{$students->mobile_no}}</td>
                                    <td>
                                        @if(!empty($students->courseRun) && !empty($students->courseRun->courseMain) )
                                        {{$students->courseRun->courseMain->name.": ".$students->courseRun->course_start_date." - ".$students->courseRun->course_end_date}}
                                        @endif
                                    </td>
                                    <td>
                                        @if($students->payment_status == \App\Models\StudentEnrolment::PAYMENT_STATUS_PENDING)
                                        <span class="badge badge-soft-danger">Pending</span>
                                        @elseif($students->payment_status == \App\Models\StudentEnrolment::PAYMENT_STATUS_PARTIAL)
                                        <span class="badge badge-soft-primary">Partial</span>
                                        @elseif($students->payment_status == \App\Models\StudentEnrolment::PAYMENT_STATUS_FULL)
                                        <span class="badge badge-soft-success">Full</span>
                                        @else
                                        <span class="badge badge-soft-info">Refund</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown dot-list">
                                            <a href="#" class="dropdown-toggle rounded-bdr" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="list-dots"></span></a>
                                            <ul  class="dropdown-menu">
                                                @can('studentenrolment-view')
                                                    <li><a href="{{route('admin.studentenrolment.view',$students->id)}}"><i class="fas fa-eye font-16"></i> View</a></li>
                                                @endcan
                                                <li><a href="{{route('admin.studentenrolment.edit',$students->id)}}"><i class="fas fa-pencil-alt font-16"></i>Edit</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="7" align="center">No New Student</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div><!--end card-body-->
            </div><!--end card-->
        </div><!--end col-->
    </div><!--end row-->

    <div class="row">

        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <a class="btn btn-secondary px-4 btn-rounded float-right mt-0 mb-3" href="{{ route('admin.course.listall') }}">View All</a>
                    <h4 class="header-title mt-0 mb-3">Upcoming Course Runs</h4>
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="">
                                <tr>
                                    <th>Code</th>
                                    <th>Course Run</th>
                                    <th>Type</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Slot</th>
                                    <th>Trainer</th>
                                    <th>Actions</th>
                                </tr><!--end tr-->
                            </thead>

                            <tbody>
                                @if( !$data['newcourserunList']->isEmpty() )
                                    @foreach( $data['newcourserunList'] as $course )
                                        @if($course->maintrainerUser)
                                            <tr>
                                                <td>{{$course->courseMain->reference_number}}</td>
                                                <td>{{$course->courseMain->name.":".$course->course_start_date." - ".$course->course_end_date}}</td>
                                                <td>{{getModeOfTraining($course->modeoftraining)}}</td>
                                                <td>{{$course->course_start_date}}</td>
                                                <td>{{$course->course_end_date}}</td>
                                                <td>{{$course->registeredusercount."/".$course->intakesize}}</td>
                                                <td>{{$course->maintrainerUser->name}}</td>
                                                <td>
                                                    <div class="dropdown dot-list">
                                                        <a href="#" class="dropdown-toggle rounded-bdr" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="list-dots"></span></a>
                                                        <ul  class="dropdown-menu">
                                                            <li><a href="{{route('admin.course.courserunview',$course->id)}}"><i class="fas fa-eye font-16"></i>View</a></li>
                                                            <li><a href="{{route('admin.course.student',$course->id)}}"><i class="fas fa-eye font-16"></i>Students List</a></li>
                                                            <li><a href="{{route('admin.course.get-attendance-assessment',$course->id)}}" ><i class="fas fa-eye font-16"></i>Attendance & Assessments</a></li>
                                                            <li><a href="{{route('admin.course.edit',$course->id)}}" ><i class="fas fa-pencil-alt font-16"></i>Edit</a></li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr><!--end tr-->
                                        @endif
                                    @endforeach
                                @else
                                <tr>
                                    <td colspan="8" align="center">No Upcoming Course Run</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div><!--end card-body-->
            </div><!--end card-->
        </div><!--end col-->
    </div><!--end row-->  
</div><!-- container -->

@endsection
@push('scripts')
<script src="{{ asset('assets/plugins/sweet-alert2/sweetalert2.min.js') }}"></script>

<script>
     $(document).ready(function() {
        $(document).on('click', '.cdmscourserun', function(e) {
            e.preventDefault();
            let _student_id = $(this).attr('student_id');
            let _gform_id = $(this).attr('gform_id');
            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                url: '{{ route('admin.students.trainee.courserun') }}',
                type: "POST",
                dataType: "JSON",
                data: {
                    student_id: _student_id,
                    gform_id: _gform_id
                },
                success: function(res) {
                    $('#modal-content').empty().html(res.html);
                    $('.model-box').modal();
                }
            }); // end ajax
        });
    });
</script>
@endpush
