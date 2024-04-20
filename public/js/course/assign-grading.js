
const avata_container = $('.avata-container');
const name_container = $('.user-name');
const grade_status_td = $('#grade-status');
const submit_time_td = $('#submit-time');
const file_td = $('#file');
const grade_input = $('#grade');
const body = $('.main-inner');
const submission_owner = $('.user-container');

$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('.btn-change').click(function (e) {
        e.preventDefault();
        var action = $(this).parent().attr('href').substring(1);
        var query = new URLSearchParams(window.location.search);

        if (action == 'next' || action == 'previous') {
            $('.btn-change').prop('disabled', true);
            $('#btn-prev').parent().removeAttr('href');
            $('#btn-next').parent().removeAttr('href');

            var id = query.get('id');
            body.hide();
            submission_owner.hide();

            $.ajax({
                type: "post",
                url: "grading/change?id=" + id + "&userId=" + query.get('userId'),
                data: {
                    action: action
                },
                dataType: "json",
                success: function (response) {
                    emptySubmission();
                    changeSubmission(response.submission);
                    body.fadeIn();
                    submission_owner.fadeIn();

                    var newUrl = '/course/assign/grading?id=' + id + '&userId=' + response.submission.user_id
                    history.replaceState(null, null, newUrl);
                },
                error: function (xhr, status, error) {
                    console.error('Request failed with error: ' + error);
                    location.reload();
                },
                complete: function () {
                    $('.btn-change').prop('disabled', false);
                    $('#btn-prev').parent().attr('href', '#previous');
                    $('#btn-next').parent().attr('href', '#next');
                }
            });
        }
    });

    $('#btn-submit').click(function (e) {
        e.preventDefault();
        var query = new URLSearchParams(window.location.search);
        var grade = grade_input.val();

        body.hide();
        submission_owner.hide();

        $.ajax({
            type: "post",
            url: "grading/update?id=" + query.get('id') + "&userId=" + query.get('userId'),
            data: {
                grade: grade
            },
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    body.fadeIn();
                    submission_owner.fadeIn();
                    if (grade) {
                        grade_status_td.text('Đã chấm');
                        grade_status_td.addClass('graded');
                    } else {
                        grade_status_td.text('Chưa chấm');
                        grade_status_td.removeClass('graded');
                    }


                    toastr.success(response.success, 'Success', {
                        closeButton: true,
                        positionClass: 'toast-top-right',
                        timeOut: 1000
                    });
                } else {
                    alert(response.error);
                }
            },
            error: function (xhr, status, error) {
                console.error('Request failed with error: ' + error);
                location.reload();
            }
        });

    });

    $('#btn-back').click(function (e) {
        window.location.href = document.referrer;
    });
});

function changeSubmission(submission) {
    avata_container.append($('<img>', {
        src: "/" + submission.avata,
        class: 'user-avata'
    }));
    name_container.append($('<h4>', {
        text: submission.name
    }), $('<h6>', {
        text: submission.username
    }));

    if (submission.grade > -1) {
        grade_status_td.text('Đã chấm');
        grade_status_td.addClass('graded');

        grade_input.val(submission.grade);
    } else {
        grade_status_td.text('Chưa chấm');
    }

    submit_time_td.text(submission.last_modified);
    file_td.append($('<a>', {
        href: "/" + submission.file_path,
        download: submission.file_path.replace(/^.*[\\\/]/, ''),
        text: submission.file_path.replace(/^.*[\\\/]/, '')
    }));

}

function emptySubmission() {
    avata_container.empty();
    name_container.empty();
    grade_status_td.empty();
    grade_status_td.removeClass('graded');
    submit_time_td.empty();
    file_td.empty();
    grade_input.val('');

}