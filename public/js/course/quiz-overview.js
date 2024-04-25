const modal = $('.modal-layout');
var query = new URLSearchParams(window.location.search);

$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#close-modal').click(function (e) {
        close_modal();
    });

    $('#btn-modal-close').click(function (e) {
        close_modal();
    });

    $('#btn-confirm-password').click(function (e) {
        $('.alert-danger').hide();

        sendCreateAttemptRequest();
    });
});

function sendCreateAttemptRequest() {
    var password = $('#password').val();
    $.ajax({
        type: "post",
        url: "quiz/attempt?id=" + query.get('id'),
        data: {
            password: password
        },
        dataType: "json",
        success: function (response) {
            if (response.error) {
                $('.alert-danger').text(response.error);
                $('.alert-danger').show();
            } else {
                window.location.href = response.redirect;
            }
        },
        error: function (xhr, status, error) {
            console.error(xhr.responseText);
            // location.reload();
        }
    });
}

function enterPassword() {
    open_modal();
}

function close_modal() {
    $('body').removeClass('modal-open');
    modal.hide();
    $('#password').val('');
}

function open_modal() {
    $('body').addClass('modal-open');
    modal.show();
}