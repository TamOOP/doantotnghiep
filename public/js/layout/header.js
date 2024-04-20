$(document).ready(function () {
    var user_dropdown = $('.user-dropdown');
    var notify_dropdown = $('.notification-dropdown');
    var message_drawer = $('.message-drawer');
    var avata = $('#avata');
    var notify = $('#notify-icon');
    var message = $('#message-icon');

    notify.click(function (e) {
        notify_dropdown.slideToggle();
    });
    avata.click(function (e) {
        user_dropdown.toggle();
    });
    message.click(function (e) {
        e.preventDefault();
        message_drawer.toggleClass('show');
    });
    $('.close-icon').click(function (e) {
        e.preventDefault();
        message_drawer.removeClass('show');
    });

    $(document).click(function (event) {

        // Check if the click is outside the dropdown block and the toggle button
        if (!user_dropdown.is(event.target) && user_dropdown.has(event.target).length === 0 &&
            !avata.is(event.target) && avata.has(event.target).length === 0) {
            user_dropdown.hide();
        }

        if (!notify_dropdown.is(event.target) && notify_dropdown.has(event.target).length === 0 &&
            !notify.is(event.target) && notify.has(event.target).length === 0) {
            notify_dropdown.slideUp(1000);
        }

    });
    $('#toggle-mode-cb').change(function (e) {
        e.preventDefault();
        $(this).prop("disabled", true);
    });
});

function toggleMode() {
    var formData = $('#form-editmode').serialize();

    $.ajax({
        type: "POST",
        url: "/editmode",
        data: formData,
        dataType: "json",
        success: function (response) {
            location.reload();
        },

    });
}   
