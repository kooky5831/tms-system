<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title mt-0" id="myLargeModalLabel">{{$student->name}} - {{convertNricToView($student->nric)}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
        {{-- @dd($getStudentData) --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mt-0">{{$student->name}} - {{convertNricToView($student->nric)}} Course Runs List</h4>
                        <div class="table-responsive dash-social">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Course Name</th>
                                        <th>Course Start Date</th>
                                        <th>Course End Date</th>
                                        <th>Course Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if( count($getStudentData) )
                                    <?php $r = 1; ?>
                                    @foreach ($getStudentData as $k => $d)
                                        <tr>
                                            <td>{{$r++}}</td>
                                            <td>
                                                @if(!is_null($d->courseRun->tpgateway_id))
                                                    {{$d->courseRun->tpgateway_id}} -
                                                @endif
                                                {{$d->courseRun->courseMain->name}}
                                            </td>
                                            <td>{{$d->courseRun->course_start_date}}</td>
                                            <td>{{$d->courseRun->course_end_date}}</td>
                                            <td>
                                                @php $coursePast = Carbon\Carbon::createFromFormat('Y-m-d', $d->courseRun->course_end_date)->isPast() @endphp
                                                @if( $coursePast )
                                                    <span class="badge badge-soft-primary">Completed</span>                                                
                                                    @else
                                                    <span class="badge badge-soft-success">Upcoming Course</span>
                                                @endif
                                            </td>                                    
                                        </tr>
                                    @endforeach
                                    @else
                                        <tr><td colspan="5" align="center">No record found</td></tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div><!--end card-body-->
                </div><!--end card-->
            </div> <!--end col-->
        </div><!--end row-->

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
    </div>
</div>
