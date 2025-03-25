<div class="modal-content">
    <div class="modal-header">
        @if(empty($data->name))
        <h5 class="modal-title mt-0" id="myLargeModalLabel">{{$data->student->name}} - {{convertNricToView($data->student->nric)}}</h5>
        @else
        <h5 class="modal-title mt-0" id="myLargeModalLabel">{{$data->name}} - {{convertNricToView($data->nric)}}</h5>
        @endif
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <p>{{$data->notes ? $data->notes : 'No notes added' }}</p>
                    </div><!--end card-body-->
                </div><!--end card-->
            </div> <!--end col-->
        </div><!--end row-->

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
    </div>
</div>
