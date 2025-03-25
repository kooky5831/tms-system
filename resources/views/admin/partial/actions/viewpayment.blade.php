$(document).on('click', '.viewpayment', function(e) {
    e.preventDefault();
    var btn = $('#enrolagain');
    BITBYTE.progress(btn);
    let _enrolement_id = $(this).attr('enrolement_id');
    $.ajax({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        url: '{{ route('admin.ajax.studentEnrolmentPayment.modal.list') }}',
        type: "POST",
        dataType: "JSON",
        data: {
            id: _enrolement_id
        },
        success: function(res) {
            BITBYTE.unprogress(btn);
            $('#modal-content').empty().html(res.html);
            $('.model-box').modal();
        },
        error: function(err) {
            BITBYTE.unprogress(btn);
            if( err.status == 422 ) {
                // display error
                showToast(err.responseJSON.message, 0);
                return false;
            }
        }
    }); // end ajax
    });
