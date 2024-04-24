var query = new URLSearchParams(window.location.search);
const edit_type = query.get('type');

switch (edit_type) {
    case 'profile':
        var field_require = [
            $('#name')
        ];
        break;

    case 'password':
        var field_require = [
            $('#oldPass'),
            $('#newPass'),
            $('#newPassConfirm')
        ];
        break;
}


$(document).ready(function () {

    $('#btn-submit').click(function (e) {
        e.preventDefault();

        if (validateRequireField()) {
            switch (edit_type) {
                case 'password':
                    $(this).prop('disabled', true);
                    sendUpdatePasswordRequest();
                    break;

                case 'profile':
                    sendUpdateProfileRequest();
                    break;
            }
        }
    });
});

function sendUpdateProfileRequest() {
    var formData = new FormData($('#form-user')[0]);
    var url = "update" + (query.get('id') !== null ? "?id=" + query.get('id') : '');

    $.ajax({
        type: "post",
        url: url,
        data: formData,
        dataType: "json",
        contentType: false,
        processData: false,
        success: function (response) {
            if (response.success) {
                toast_success(response.success);
                window.location.href = document.referrer;
            } else {
                console.log(response.error);
                location.reload();
            }
        },
        error: function (xhr, status, error) {
            console.error(xhr.responseText);
            location.reload();
        }
    });
}

function sendUpdatePasswordRequest() {
    $.ajax({
        type: "post",
        url: "updatePassword",
        data: $('#form-password').serialize(),
        dataType: "json",
        success: function (response) {
            if (response.success) {
                toast_success(response.success);
                location.reload();

            } else {
                console.log(response.error);
                $('#btn-submit').prop('disabled', false);
                $('#password-error').text(response.error);
                $('#password-error').show();
            }
        },
        error: function (xhr, status, error) {
            console.error(xhr.responseText);
            location.reload();
        }
    });
}

function validateRequireField() {
    var valid = true;
    for (let i = 0; i < field_require.length; i++) {
        if (!field_require[i].val()) {
            appendError(field_require[i].parent(), 'Hãy nhập trường thông tin');
            focusError(field_require[i]);
            valid = false;
        }
        else {
            removeError(field_require[i].parent());
        }
    }

    return valid;
}