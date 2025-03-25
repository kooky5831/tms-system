$(document).on('click', '.holdenrolement', function () {
    let _enrolement_id = $(this).attr('enrolement_id');
    swal.fire({
        title: 'Are you sure?',
        text: "You want to move the student to the holding list?",
        input: "text",
        inputLabel: "Type HOLDLIST to confirm",
        inputPlaceholder: "Type HOLDLIST to confirm",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, hold it!',
        cancelButtonText: 'No',
        reverseButtons: true,
        inputValidator: (inputValue) => {
            if (inputValue === null) return false;
            if (inputValue === "") {
                return "You need to Type HOLDLIST to confirm!";
            }
            if (inputValue.toUpperCase() != "HOLDLIST") {
                return "You need to Type HOLDLIST to confirm!";
            }
        }
    }).then((result) => {
        if (result.value) {
            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                url: '{{ route('admin.ajax.studentEnrolment.modal.hold') }}',
                type: "POST",
                dataType: "JSON",
                data: {
                    id: _enrolement_id
                },
                success: function(res) {
                    console.log(res);
                    if( res.status == true ) {
                        swal.fire(
                            'MOVED TO HOLDLIST!',
                            'Your enrolement has been moved to holdlist.',
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
