<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title mt-0" id="myLargeModalLabel">Remark Student, {{  $getStudentEnroll->student->name }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <form action="#" method="POST" class="remarkStudent" id="" enctype="multipart/form-data">
        <div class="modal-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="col-md-12">
                                <label for="course_main_id">Add Remark <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="student_exam_remarks" id="student_exam_remarks">
                                @error('student_exam_remarks')
                                    <label class="form-text text-danger">{{ $message }}</label>
                                @enderror
                            </div>
                        </div><!--end card-body-->
                    </div><!--end card-->
                </div> <!--end col-->
            </div><!--end row-->

        </div>
        <input type="hidden" name="course_id" value="{{ $getStudentEnroll->course_id }}">
        <input type="hidden" name="exam_id" value="{{ $getExamId->id }}">
        <input type="hidden" name="student_enrol_id" value="{{ $getStudentEnroll->id }}">
        <div class="modal-footer">
            <button type="submit" id="remarks_selection_btn" class="btn btn-secondary waves-effect">Add</button>
            <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">Close</button>
        </div>
    </form>
</div>
<script>
        $(document).ready(function() {
            $(document).on('click', '#remarks_selection_btn', function(e) {
                e.preventDefault();
                $.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    url: '{{ route('trainer.exam-settings.remark_student',  $getStudentEnroll->id) }}',
                    data: $('form.remarkStudent').serialize(),
                    type: "POST",
                    success: function(res, data) {
                        if( res.status ) {
                            showToast(res.msg, 1);
                            window.location.replace('{{ route('trainer.exam-settings.review_studetn_exam_list_data', $getStudentEnroll->course_id) }}');
                        }
                        else{
                            showToast(res.msg, 0);
                        }
                    }
                }); // end ajax
            });
        });
</script>

