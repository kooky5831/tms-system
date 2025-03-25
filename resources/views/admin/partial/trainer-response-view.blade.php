<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title mt-0" id="myLargeModalLabel">{{$records->name}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mt-0">{{$records->name}} - Response</h4>
                        @if( !is_null($records->trainer->tpgResponse) )
                            <?php $trainerRes = json_decode($records->trainer->tpgResponse); ?>
                            <?php dump($trainerRes); ?>
                        @endif
                    </div><!--end card-body-->
                </div><!--end card-->
            </div> <!--end col-->
        </div><!--end row-->

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
    </div>
</div>
