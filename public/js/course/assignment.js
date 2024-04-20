const modal = $('.modal-layout');
const modal_close_icon = $('#close-modal');
const modal_close_btn = $('#btn-modal-close');
const open_modal_btn = $('#btn-enrol');

const query = new URLSearchParams(window.location.search);

if (typeof days !== 'undefined' && typeof hours !== 'undefined' && typeof minutes !== 'undefined' && typeof seconds !== 'undefined') {
    var assignRemainSecond = (days * 24 * 60 * 60) + (hours * 60 * 60) + (minutes * 60) + seconds;
}

$(document).ready(function () {
    if (typeof assignRemainSecond !== 'undefined') {
        assignCountdown = setInterval(function () {
            assignRemainSecond--;

            var days = Math.floor(assignRemainSecond / (24 * 60 * 60));
            var hours = Math.floor((assignRemainSecond % (24 * 60 * 60)) / (60 * 60));
            var minutes = Math.floor((assignRemainSecond % (60 * 60)) / 60);
            var seconds = assignRemainSecond % 60;

            $("#time-remain-text").html(
                (days > 0 ? days + " ngày " : '')
                + (hours > 0 ? hours + " giờ " : '')
                + (minutes > 0 ? minutes + " phút " : '')
                + (seconds > 0 ? seconds + " giây " : '')
            );

            if (assignRemainSecond <= 0) {
                clearInterval(assignCountdown);
                $("#time-remain-text").text("Bài tập đóng");
            }
        }, 1000);
    } else {
        $("#time-remain-text").text("Bài tập đóng");
    }

    $('#btn-confirm').click(function (e) {
        if ($('#file').val() !== null) {
            var formData = new FormData($('#form-submission')[0]);

            $.ajax({
                type: "post",
                url: "assign/submission/store?id=" + query.get('id'),
                data: formData,
                dataType: "json",
                contentType: false,
                processData: false,
                success: function (response) {
                    console.log(response);
                    if (response.success) {
                        toastr.success('Nộp bài thành công', 'Success', {
                            closeButton: true,
                            positionClass: 'toast-top-right',
                            timeOut: 1000
                        });

                    }
                },
                error: function (xhr, status, error) {
                    console.error('Request failed with error: ' + error);
                },
                complete: function () {
                    location.reload();
                }
            });
        }
    });

    $('#btn-submit').click(function (e) {
        open_modal();
    });

    modal_close_btn.click(function (e) {
        close_modal();
    });

    modal_close_icon.click(function (e) {
        close_modal();
    });
});

function close_modal() {
    $('body').removeClass('modal-open');
    modal.hide();
}

function open_modal() {
    $('body').addClass('modal-open');
    modal.show();
}