$(document).on('click', '.cancelenrolement', function () {
    let _enrolement_id = $(this).attr('enrolement_id');
    swal.fire({
        title: 'Are you sure?',
        text: "You want to cancel the enrolement!",
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
                url: '{{ route('admin.ajax.studentEnrolment.modal.cancel') }}',
                type: "POST",
                dataType: "JSON",
                data: {
                    id: _enrolement_id
                },
                success: function(res) {
                    console.log(res);
                    if( res.status == true ) {
                        swal.fire(
                            'Cancelled!',
                            'Your enrolement has been cancelled.',
                            'success'
                        )
                        location.reload();
                    } else {
                        swal.fire(
                            'Opps',
                            'Some error occured, Please try again.',
                            'error'
                        )
                    }
                }
            }); // end ajax
        }
    });
});
