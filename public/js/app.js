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

    $('.number-format').keyup(function (e) { 
        let inputValue = $(this).val();

        inputValue = inputValue.replace(/\D/g, '');

        let formattedValue = numberWithCommas(inputValue);
        $(this).val(formattedValue);
    });
});

function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}