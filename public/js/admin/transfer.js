$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
});

function updateTransfer(e, transId, status) {
    if (confirm('Xác nhận yêu cầu xử lý ' + (status == 'done' ? 'Thành công' : 'Thất bại') + '?')) {
        $.ajax({
            type: "post",
            url: "transfer/update",
            data: {
                id: transId,
                status: status
            },
            dataType: "json",
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