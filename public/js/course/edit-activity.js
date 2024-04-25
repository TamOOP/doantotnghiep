

const query = new URLSearchParams(window.location.search);
const activity_type = query.get('type');
const id = query.get('id');

switch (activity_type) {
    case 'assign':
        var field_require = [
            $('#name'),
            $('#max-grade'),
            $('#grade-pass')
        ];
        break;

    case 'quiz':
        var field_require = [
            $('#name'),
            $('#grade-scale'),
            $('#grade-pass')
        ];
        break;
    case 'file':
        var field_require = [
            $('#name')
        ];
        break;
}
$(document).ready(function () {
    if (activity_type == 'quiz') {
        generateOptions('#questionPerPage', 1, 50);
        generateOptions('#attemptAllow', 1, 10);
    }

    $('#btn-submit').click(function (e) {
        e.preventDefault();
        var valid = true;
        for (let i = 0; i < field_require.length; i++) {
            if (!field_require[i].val().trim()) {
                appendError(field_require[i].parent(), 'Hãy nhập trường thông tin');
                focusError(field_require[i]);
                valid = false;
            }
            else {
                removeError(field_require[i].parent());
            }
        }

        if (valid) {
            switch (activity_type) {
                case 'assign':
                    sendAssignUpdateRequest($(e.delegateTarget).parents('form'));
                    break;

                case 'quiz':
                    sendExamUpdateRequest($(e.delegateTarget).parents('form'));
                    break;

                case 'file':
                    if (validateFileRequire()){
                        sendFileUpdateRequest();
                    }
                    break;
            }
        }
    });
});

function sendFileUpdateRequest() {
    var formData = new FormData($('#form-file')[0]);

    $.ajax({
        type: "post",
        url: "/course/file/update?type=file&id=" + id,
        data: formData,
        dataType: "json",
        contentType: false,
        processData: false,
        success: function (response) {
            if (response.success) {
                toast_success(response.success);
                window.location.href = "/course/file?id=" + id;
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

function sendAssignUpdateRequest(form) {
    var formData = new FormData(form[0]);

    $.ajax({
        type: "post",
        url: "update?type=assign&id=" + id,
        data: formData,
        dataType: "json",
        contentType: false,
        processData: false,
        success: function (response) {
            if (response.success) {
                toast_success(response.success);
                window.location.href = "/course/assign?id=" + id;
            } else {
                location.reload();
            }

        },
        error: function (xhr, status, error) {
            console.error(xhr.responseText);
            location.reload();
        }
    });
}

function sendExamUpdateRequest(form) {
    var formData = new FormData(form[0]);

    $.ajax({
        type: "post",
        url: "update?type=quiz&id=" + id,
        data: formData,
        dataType: "json",
        contentType: false,
        processData: false,
        success: function (response) {
            if (response.success) {
                toast_success(response.success);
                window.location.href = "/course/quiz?id=" + id;
            } else {
                location.reload();
            }
        },
        error: function (xhr, status, error) {
            console.error('Request failed with error: ' + error);
            location.reload();
        },
    });
}

function generateOptions(selectObj, start, end) {
    const select = $(selectObj);
    const selectedValue = select.attr('data-selected');

    for (let i = start; i <= end; i++) {
        var optionText = (i < 10) ? '0' + i : i;
        let optionSnippet = $('<option>', {
            value: i,
            text: optionText,
            selected: i == selectedValue
        });

        select.append(optionSnippet);
    }
}

function toast_success(message) {
    toastr.success(message, 'Success', {
        closeButton: true,
        positionClass: 'toast-top-right',
        timeOut: 1000
    });
}