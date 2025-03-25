$(document).on('click', '.cancelcourserun', function () {
    let _courserun_id = $(this).attr('courserun_id');
    swal.fire({
        title: 'Are you sure?',
        text: "You want to cancel the course run!",
        input: "text",
        inputLabel: "Type DELETE to confirm",
        inputPlaceholder: "Type DELETE to confirm",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, cancel it!',
        cancelButtonText: 'No',
        reverseButtons: true,
        inputValidator: (inputValue) => {
            if (inputValue === null) return false;
            if (inputValue === "") {
                return "You need to Type DELETE to confirm!";
            }
            if (inputValue.toUpperCase() != "DELETE") {
                return "You need to Type DELETE to confirm!";
            }
        }
    }).then((result) => {
        if (result.value) {
            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                url: '{{ route('admin.course.ajax.courserun.modal.cancel') }}',
                type: "POST",
                dataType: "JSON",
                data: {
                    id: _courserun_id
                },
                beforeSend: function() {
                    $(".ajax-loader").show();
                },
                complete: function(){
                    $(".ajax-loader").hide();
                },
                success: function(res) {
                    console.log(res);
                    $(".ajax-loader").hide();
                    if( res.status == true ) {
                        $("#frm_course_run_edit").find('#publish').prop('disabled', 'disabled');
                        $("#frm_course_run_edit").find('.cancelcourserun').hide(); 
                        swal.fire(
                            'Success!',
                            res.msg,
                            'success'
                        )
                        location.reload();
                    } else {
                        swal.fire(
                            'Failed',
                            res.msg,
                            'error'
                        )
                    }
                }
            }); // end ajax
        }
    });
});
