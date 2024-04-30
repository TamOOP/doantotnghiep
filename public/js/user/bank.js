$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
});

function deleteBank(e, bankId) {
    if (confirm('Xác nhận xóa ngân hàng?')) {
        $.ajax({
            type: "post",
            url: "bank/delete?id=" + bankId,
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