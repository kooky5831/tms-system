<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title mt-0" id="myLargeModalLabel">Upload Course run Documents</h5>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <form action="#" id="courserun_documnet_upload_edit" method="POST" enctype="multipart/form-data">
        <div class="modal-body">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <input type="hidden" name="courserun_id" value="{{$record->course_id}}">
                            <input type="hidden" name="coursedoc_id" value="{{$record->id}}">
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="category_id">Select Category <span class="text-danger">*</span></label>
                                    <select name="category_id" id="category_id" required class="form-control">
                                        <option value="">Select Category</option>
                                        @foreach(getAttAssCategory() as $categorykey => $categoryType)
                                        <option value="{{$categorykey}}" {{ $record->category == $categorykey ? 'selected' : '' }}>{{ $categoryType }} </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <label class="form-text text-danger">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="file_name">Select Document</label>
                                        <div class="custom-file">
                                            <input type="file" name="file_name" class="custom-file-input" id="file_name">
                                            <label class="custom-file-label" for="file_name">Choose File</label>
                                            @error('file_name')
                                                <label class="form-text text-danger">{{ $message }}</label>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!--end card-body-->
                    </div><!--end card-->
                </div> <!--end col-->
            </div><!--end row-->

        </div>
        <div class="modal-footer">
            <button type="submit" id="submit_document_edit" class="btn btn-secondary waves-effect">Submit</button>
            <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">Close</button>
        </div>
    </form>
</div>
