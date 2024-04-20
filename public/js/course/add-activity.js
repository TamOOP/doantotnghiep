var query = new URLSearchParams(window.location.search);
const activity_type = query.get('type');
const topicId = query.get('id');

switch (activity_type) {
    case 'assign':
        var field_require = [
            $('#name'),
            $('#max-grade'),
            $('#grade-pass')
        ];
        var form = $('#form-assign');
        break;

    case 'quiz':
        var field_require = [
            $('#name'),
            $('#grade-scale'),
            $('#grade-pass')
        ];
        var form = $('#form-quiz');
        break;

    case 'topic':
        var field_require = [
            $('#name'),
        ];
        var form = $('#form-topic');
        break;

    case 'file':
        var field_require = [
            $('#name'),
        ];
        var form = $('#form-file');
        break;
}

$(document).ready(function () {
    $('#btn-submit').click(function (e) {
        e.preventDefault();
        if (validateRequireField()) {
            switch (activity_type) {
                case 'assign':
                    sendAddAssignRequest();
                    break;

                case 'quiz':
                    sendAddExamRequest();
                    break;

                case 'topic':
                    sendAddTopicRequest();
                    break;

                case 'file':
                    if (validateFileRequire()) {
                        sendAddFileRequest();
                    }
                    break;
            }
        }
    });
});

function sendAddAssignRequest() {
    var formData = new FormData(form[0]);

    $.ajax({
        type: "post",
        url: "store/assign?id=" + topicId,
        data: formData,
        dataType: "json",
        contentType: false,
        processData: false,
        success: function (response) {
            if (response.success) {
                toast_success(response.success);
                window.location.href = document.referrer;
            } else {
                // location.reload();
            }
        },
        error: function (xhr, status, error) {
            console.error(xhr.responseText);
            // location.reload();
        }
    });
}

function sendAddExamRequest() {
    var formData = new FormData(form[0]);

    $.ajax({
        type: "post",
        url: "store/quiz?id=" + topicId,
        data: formData,
        dataType: "json",
        contentType: false,
        processData: false,
        success: function (response) {
            if (response.success) {
                toast_success(response.success);
                window.location.href = document.referrer;
            } else {
                alert(response.error);
                location.reload();
            }
        },
        error: function (xhr, status, error) {
            console.error(xhr.responseText);
            location.reload();
        }
    });
}

function sendAddTopicRequest() {
    var formData = new FormData(form[0]);

    $.ajax({
        type: "post",
        url: "/course/topic/store?id=" + query.get('id'),
        data: formData,
        dataType: "json",
        contentType: false,
        processData: false,
        success: function (response) {
            if (response.success) {
                toast_success(response.success);
                window.location.href = document.referrer;
            } else {
                alert(response.error);
                location.reload();
            }
        },
        error: function (xhr, status, error) {
            console.error(xhr.responseText);
            location.reload();
        }
    });
}

function sendAddFileRequest() {
    var formData = new FormData(form[0]);

    $.ajax({
        type: "post",
        url: "store/file?id=" + query.get('id'),
        data: formData,
        dataType: "json",
        contentType: false,
        processData: false,
        success: function (response) {
            if (response.success) {
                toast_success(response.success);
                window.location.href = document.referrer;
            } else {
                alert(response.error);
                location.reload();
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