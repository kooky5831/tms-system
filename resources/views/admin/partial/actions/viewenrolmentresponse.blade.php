$(document).on('click', '.viewenrolmentresponse', function(e) {
    e.preventDefault();
    let _enrolement_id = $(this).attr('enrolement_id');
    let _type = $(this).attr('type');
    $.ajax({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        url: '{{ route('admin.ajax.studentEnrolmentViewResponse.modal.view') }}',
        type: "POST",
        dataType: "JSON",
        data: {
            id: _enrolement_id,
            type: _type
        },
        success: function(res) {
            $('#modal-content').empty().html(res.html);
            $('.model-box').modal();
        }
    }); // end ajax
});
