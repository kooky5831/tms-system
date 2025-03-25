<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title mt-0" id="myLargeModalLabel">Grant Log Remarks</h5>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <form action="#" id="grant_action_form" method="POST" enctype="multipart/form-data">
        <div class="modal-body">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <?php 
                                $label = "Add Remarks";
                                if(!empty($data->notes)){
                                    $label = "Update Remarks";
                                }
                            ?>
                            <input type="hidden" name="grant_id" value="{{$data->id}}">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="notes"><?php echo $label; ?></label>
                                        <textarea id="notes" class="form-control" required rows="5" name="notes">{{ $data->notes }}</textarea>
                                        @error('notes')
                                            <label class="form-text text-danger">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                        </div><!--end card-body-->
                    </div><!--end card-->
                </div> <!--end col-->
            </div><!--end row-->

        </div>
        <div class="modal-footer">
            <button type="submit" id="submit_grant_action" class="btn btn-secondary waves-effect"><?php echo $label; ?></button>
            <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">Close</button>
        </div>
    </form>
</div>
