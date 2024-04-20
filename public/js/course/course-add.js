var form = $('#form-course');
var result_box = $('#search-result-box');
const result_teacher = result_box.children('#search-result-list');
var teacherId;

$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).click(function (event) {
        if (!result_box.is(event.target)
            && result_box.has(event.target).length === 0) {
            result_box.hide();
        }
    });

    $('#course-fee').keyup(function (e) { 
        let inputValue = $(this).val();

        inputValue = inputValue.replace(/\D/g, '');

        let formattedValue = numberWithCommas(inputValue);
        $(this).val(formattedValue);
    });

    $('#btn-submit').click(function (e) {
        e.preventDefault();
        if (validateRequireField()) {
            var formData = new FormData(form[0]);
            formData.append('teacherId', teacherId);

            $.ajax({
                type: "post",
                url: "add?type=course",
                data: formData,
                dataType: "json",
                contentType: false,
                processData: false,
                success: function (response) {
                    if (response.success) {
                        toast_success(response.success);
                        window.location.href = response.redirect;
                    } else {
                        console.log(response.error);
                        // location.reload();
                    }
                },
                error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                    // location.reload();
                }
            });
        }
    });

    if ($('#teacher').length > 0) {
        let typingTimer;
        const doneTypingInterval = 300;
        $('#teacher').keyup(function (e) {
            e.preventDefault();

            var keyword = $(e.delegateTarget).val();
            result_teacher.empty();
            clearTimeout(typingTimer);

            typingTimer = setTimeout(function () {
                $.ajax({
                    type: "post",
                    url: "/admin/user/search",
                    data: {
                        role: 'teacher',
                        keyword: keyword
                    },
                    dataType: "json",
                    success: function (response) {
                        console.log(response);
                        response.users.forEach(teacher => {
                            appendSearchTeacherResult(teacher);
                            result_box.show();
                        });
                    },
                    error: function (xhr, status, error) {
                        console.error('Request failed with error: ' + error);
                        // location.reload();
                    }
                });
            }, doneTypingInterval);
        });
    }
});

function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function appendSearchTeacherResult(teacher) {

    var userSnippet = `
        <li class="search-result-user" data-user="`+ teacher.id + `" onclick="pickupTeacher('${teacher.id}','${teacher.username}', '${teacher.name}')">
            <div class="user-selected-img-container">
                <img src="/` + teacher.avata + `" width="${teacher.width == 'auto' ? 'auto' : '20'}" height="${teacher.height == 'auto' ? 'auto' : '20'}">
            </div>
            <span class="ml-2 user-selected-name" style="color:black">
                ` + teacher.name + ` (` + teacher.username + `)
            </span>
        </li>`;

    result_teacher.append(userSnippet);
}

function pickupTeacher(id, email, name) {
    result_box.hide();

    teacherId = id;
    $('#teacher').val(name + '(' + email + ')');
}
function validateRequireField() {
    var valid = true;
    if (!$('#name').val().trim()) {
        valid = false;
        appendError($('#name').parent(), 'Hãy nhập trường thông tin');
        focusError($('#name'));
    }

    if ($('#enrolment-method') == '2' && !$('#course-fee').val().trim()) {
        valid = false;
        appendError($('#course-fee').parent(), 'Hãy nhập trường thông tin');
        focusError($('#course-fee'));
    }

    if ($('#teacher').length > 0 && !$('#teacher').val().trim()) {
        valid = false;
        appendError($('#teacher').parent(), 'Hãy nhập trường thông tin');
        focusError($('#teacher'));
    }

    return valid;
}