$(document).ready(function () {

    $("#date-start").change(function () {
        $("#dateEnd").attr("min", $("#date-start").val());
    });
    $("#dateEnd").change(function () {
        $("#date-start").attr("max", $("#dateEnd").val());
    });

    $('#btn-cancel').click(function (e) {
        e.preventDefault();

        var isConfirmed = confirm("Thay đổi sẽ không được lưu lại. Xác nhận hủy bỏ?");
        if (isConfirmed) {
            window.location.href = document.referrer;
        }
    });
});

function appendError(target, message) {
    var errorSnippet = $('<p>', {
        text: message,
        class: 'warning-msg'
    });
    target.find('.warning-msg').remove();
    target.append(errorSnippet);
}

function removeError(target) {
    target.find('.warning-msg').remove();
    target.find('input').removeClass('warning-input');
}

function focusError(target) {
    target.addClass('warning-input');
    target.focus();
}

function toast_success(message) {
    toastr.success(message, 'Success', {
        closeButton: true,
        positionClass: 'toast-top-right',
        timeOut: 1000
    });
}



