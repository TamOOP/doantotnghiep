var btn_login = $('.register-button');
var form = $('.register-form');
var field_require = [
    $('#username'),
    $('#name'),
    $('#password'),
];

$(document).ready(function () {
    btn_login.click(function (e) {
        e.preventDefault();
        if (validateRequireField()) {
            $.ajax({
                type: "post",
                url: "register",
                data: form.serialize(),
                dataType: "json",
                success: function (response) {
                    if (response.success) {
                        toast_success(response.success);
                        window.location.href = '/';
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
    });

});

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