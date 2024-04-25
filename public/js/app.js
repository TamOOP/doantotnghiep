$(document).ready(function () {
    $('.password-icon').click(function (e) {
        var icon = $(e.delegateTarget);
        var input = icon.siblings('#password');
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye-slash');
            icon.addClass('fa-eye');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye');
            icon.addClass('fa-eye-slash');
        }
    });
});