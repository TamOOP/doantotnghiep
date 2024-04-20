const query = new URLSearchParams(window.location.search);

const content = $('.main-content');
const drawer_right = $('.drawer-right');
const btn_open_drawer = $('#btn-open-drawer');
const btn_close_drawer = $('#btn-close-drawer');
const timer = $('#timer');
var time_unit, time_alert;

$(document).ready(function () {
    if (typeof timeLimit !== 'undefined') {
        var now = new Date().getTime();

        var timeDifference = timeLimit - now;
        var hours = Math.floor(timeDifference / 3600);
        var minutes = Math.floor((timeDifference % 3600) / 60);

        if (hours > 0) {
            time_unit = 'hour';
            time_alert = 180;
        } else if (minutes > 0) {
            time_unit = 'minute';
            time_alert = 60;
        } else {
            time_unit = 'second';
            time_alert = 15;
        }
        var countdownInterval = setInterval(function () {
            var now = new Date().getTime();

            var timeDifference = timeLimit - now;

            var hours = Math.floor((timeDifference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((timeDifference % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((timeDifference % (1000 * 60)) / 1000);

            if (time_unit == 'hour') {
                var formattedTime = (hours < 10 ? "0" : "") + hours + ":" + (minutes < 10 ? "0" : "") + minutes + ":" + (seconds < 10 ? "0" : "") + seconds;
            } else if (time_unit == 'minute') {
                var formattedTime = (minutes < 10 ? "0" : "") + minutes + ":" + (seconds < 10 ? "0" : "") + seconds;
            } else {
                var formattedTime = (seconds < 10 ? "0" : "") + seconds;
            }

            timer.text(formattedTime);
            if (timeDifference <= time_alert) {
                timer.addClass('warning-msg');
            }

            if (timeDifference <= 0) {
                clearInterval(countdownInterval);
                timer.text("Time's up!");
            }
        }, 1000);

    }

    btn_open_drawer.click(function (e) {
        showRightDrawer();
    });

    btn_close_drawer.click(function (e) {
        hideRightDrawer();
    });

    $('.btn-back').click(function (e) {
        window.location.href = document.referrer;
    });

    $('#btn-submit').click(function (e) {
        e.preventDefault();
        sendGradeAttemptRequest();
    });

    $('#btn-back-attempt').click(function (e) {
        location.replace('/course/quiz/attempt?id=' + query.get('id'));
    });

    $('#btn-review').click(function (e) {
        var formData = new FormData($('#form-question')[0]);

        $.ajax({
            type: "post",
            url: "attempt/saveAnswer?id=" + query.get('id') + "&attemptId=" + attemptId,
            data: formData,
            dataType: "json",
            contentType: false,
            processData: false,
            success: function (response) {
                location.replace('/course/quiz/attempt/review?id=' + query.get('id') + "&attemptId=" + attemptId);
            },
            error: function (xhr, status, error) {
                console.error('Request failed with error: ' + error);
            },
        });
    });

    $('.clear-choice').click(function (e) {
        e.preventDefault();
        $(e.delegateTarget).siblings('.answer-container').find('input').prop('checked', false);
        $(e.delegateTarget).hide();
    });

    $('.choice').click(function (e) { 
        $(e.delegateTarget).parents('.answer-container').siblings('.clear-choice').show();        
    });
});

function sendGradeAttemptRequest() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        type: "post",
        url: "/course/quiz/attempt/grading?id=" + query.get('id') + "&attemptId=" + query.get('attemptId'),
        success: function (response) {
            if (response.redirect) {
                location.replace(response.redirect);
            }
        },
        error: function (xhr, status, error) {
            console.error('Request failed with error: ' + error);
            location.reload();
        },
    });
}


function showRightDrawer() {
    drawer_right.addClass('drawer-right-show');
    content.addClass('drawer-right-draw');
    btn_open_drawer.addClass('btn-hide');
}

function hideRightDrawer() {
    drawer_right.removeClass('drawer-right-show');
    content.removeClass('drawer-right-draw');
    setTimeout(function () {
        btn_open_drawer.removeClass('btn-hide');
    }, 100);
}

function updateTimer() {
    var hours = Math.floor(countdownTime / 3600);
    var minutes = Math.floor((countdownTime % 3600) / 60);
    var seconds = countdownTime % 60;

    if (time_unit == 'hour') {
        var formattedTime = (hours < 10 ? "0" : "") + hours + ":" + (minutes < 10 ? "0" : "") + minutes + ":" + (seconds < 10 ? "0" : "") + seconds;
    } else if (time_unit == 'minute') {
        var formattedTime = (minutes < 10 ? "0" : "") + minutes + ":" + (seconds < 10 ? "0" : "") + seconds;
    } else {
        var formattedTime = (seconds < 10 ? "0" : "") + seconds;
    }

    timer.text(formattedTime);
    if (countdownTime <= time_alert) {
        timer.addClass('warning-msg');
    }
    countdownTime--;

    if (countdownTime < 0) {
        clearInterval(timerInterval);
        timer.text("Time's up!");
    }

}