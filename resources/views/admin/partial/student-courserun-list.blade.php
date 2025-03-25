<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title mt-0" id="myLargeModalLabel">{{$student->name}} - {{convertNricToView($student->nric)}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">

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
                                        <th>Payment Status</th>
                                        <th>Enrollment Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if( count($records) )
                                    <?php $r = 1; ?>
                                    @foreach ($records as $k => $d)
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
                                            <td>{{getPaymentStatus($d->payment_status)}}</td>
                                            <td>
                                                @if( $d->status == 1 )
                                                    <span class="badge badge-soft-danger">Enrolment Cancelled</span>
                                                    @elseif( $d->status == 2 )
                                                    <span class="badge badge-soft-danger">Holding List</span>
                                                    @elseif( $d->status == 0 )
                                                    <span class="badge badge-soft-success">Enrolled</span>
                                                    @else
                                                    <span class="badge badge-soft-danger">Not Enrolled</span>
                                                @endif
                                            </td>
                                            <td>@can('studentenrolment-view') <a href="{{route('admin.studentenrolment.view',$d->id)}}"><i class="fas fa-eye font-16"></i> </a>@endcan</td>
                                        </tr>
                                    @endforeach
                                    @foreach($refreshers as $refresher)
                                        <tr>
                                            <td>{{$r++}}</td>
                                            <td>
                                                @if(!is_null($refresher->course->tpgateway_id))
                                                    {{$refresher->course->tpgateway_id}} -
                                                @endif
                                                {{$refresher->course->courseMain->name}}
                                            </td>
                                            <td>{{$refresher->course->course_start_date}}</td>
                                            <td>{{$refresher->course->course_end_date}}</td>
                                            <td>Refresher</td>
                                            <td>
                                                @if( $refresher->status == 1 )
                                                <span class="badge badge-soft-success">Accepted</span>
                                                @elseif( $refresher->status == 2 )
                                                <span class="badge badge-soft-danger">Cancelled</span>
                                                @else
                                                <span class="badge badge-soft-danger">Pending</span>
                                                @endif
                                            </td>
                                            <td></td>
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
