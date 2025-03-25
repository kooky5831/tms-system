<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title mt-0" id="myLargeModalLabel">Task Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        @if( !is_null($data->completed_at) )
                            <h3>Completed at: {{ $data->completed_at->format('Y-m-d h:i A') }} by - {{ $data->completedByUser->name }}</h3>
                        @endif
                        @if( $data->course_id )
                        <p>Course Start Date: {{ $data->course->course_start_date }}</p>
                        <p>Course Name: {{ $data->course->courseMain->name }}</p>
                        @endif
                        <p>Task Type: <strong>{{ triggerEventTypes($data->task_type) }}</strong></p>
                        @if( $data->task_type == 3 )
                        Text Task - {{ $data->task_text }}
                        @endif
                        <p>Notes: {!! nl2br(e($data->notes)) !!}</p>

                    </div><!--end card-body-->
                </div><!--end card-->
            </div> <!--end col-->
        </div><!--end row-->

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">Close</button>
    </div>
</div>
