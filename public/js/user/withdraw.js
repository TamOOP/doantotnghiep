$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
});

function deleteTransfer(e, transId) {
    if (confirm('Xác nhận xóa yêu cầu rút tiền?')) {
        $.ajax({
            type: "post",
            url: "transfer/delete?id=" + transId,
            success: function (response) {
                if (response.success) {
                    toast_success(response.success);
                    $(e).parents('tr').remove();
                } else {
                    console.log(response.error);
                }
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
                // location.reload();
            }
        });
    }

}

function toast_success(message) {
    toastr.success(message, 'Success', {
        closeButton: true,
        positionClass: 'toast-top-right',
        timeOut: 1000
    });
}