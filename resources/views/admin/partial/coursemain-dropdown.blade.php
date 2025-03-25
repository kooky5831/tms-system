<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title mt-0" id="myLargeModalLabel">Select Course</h5>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <form action="#" id="coursemain_selection" method="POST" enctype="multipart/form-data">
        <div class="modal-body">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="col-md-12">
                                <label for="course_main_id">Select Course <span class="text-danger">*</span></label>
                                <select name="course_main_id" id="course_main_id" required class="form-control select2">
                                    <option value="">Select Course</option>
                                    @foreach($records as $record)
                                    <option value="{{$record->id}}" {{ old('course_main_id') == $record->id ? 'selected' : '' }}>{{ $record->name }} - {{ $record->reference_number }}</option>
                                    @endforeach
                                </select>
                                @error('course_main_id')
                                    <label class="form-text text-danger">{{ $message }}</label>
                                @enderror
                            </div>
                        </div><!--end card-body-->
                    </div><!--end card-->
                </div> <!--end col-->
            </div><!--end row-->

        </div>
        <div class="modal-footer">
            <button type="submit" id="coursemain_selection_btn" class="btn btn-secondary waves-effect">Add</button>
            <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">Close</button>
        </div>
    </form>
</div>
<script type="text/javascript">
    $(".select2").select2({ width: '100%' });
    $(document).on('click', '#coursemain_selection_btn', function(e) {
        e.preventDefault();
        let _courseMain = $('#course_main_id').val();
        if( _courseMain != "" ) {
            window.location.href = '{{route('admin.course.add')}}/'+_courseMain;
        }
    });
</script>
