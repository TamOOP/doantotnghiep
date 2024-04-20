const course_table = $('#user-table');
const search_bar = $('.search-bar');
const search_option = $('#method-option');
const teacher_list = $('#search-result-list');

$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).click(function (event) {
        if (!teacher_list.parent().is(event.target)
            && teacher_list.parent().has(event.target).length === 0) {
                teacher_list.parent().hide();
        }
    });

    $('#search-condition').change(function (e) {
        e.preventDefault();
        var condition = $(this).val();
        if (condition == 'name' || condition == 'teacher') {
            search_bar.show();
            search_option.hide();
        } else if (condition == 'method') {
            search_bar.hide();
            search_option.show();
        }
    });

    $('#form-filter').submit(function (e) {
        e.preventDefault();
    });

    let typingTimer;
    const doneTypingInterval = 300;
    $('.search-input').keyup(function (e) {
        e.preventDefault();

        if ($('#search-condition').val() === 'teacher') {
            teacher_list.parents('#search-result-box').show();
            var keyword = $(e.delegateTarget).val();
            teacher_list.empty();
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
                        });
                        teacher_list.show();
                    },
                    error: function (xhr, status, error) {
                        console.error('Request failed with error: ' + error);
                        // location.reload();
                    }
                });
            }, doneTypingInterval);
        }
    });
});

function appendSearchTeacherResult(teacher) {
    var userSnippet = `
        <li class="search-result-user" data-user="`+ teacher.id + `" onclick="searchCourseTeacher(${teacher.id})">
            <div class="user-selected-img-container">
                <img src="/` + teacher.avata + `" width="${teacher.width == 'auto' ? 'auto' : '20'}" height="${teacher.height == 'auto' ? 'auto' : '20'}">
            </div>
            <span class="ml-2 user-selected-name" style="color:black">
                ` + teacher.name + ` (` + teacher.username + `)
            </span>
        </li>`;

    teacher_list.append(userSnippet);
}

function searchCourseTeacher(id) {
    teacher_list.parent().hide();
    course_table.children('tbody').empty();

    $.ajax({
        type: "post",
        url: "course/search",
        data: {
            'search-condition': 'teacher',
            'teacherId': id
        },
        dataType: "json",
        success: function (response) {
            console.log(response);
            if (response.courses) {
                response.courses.forEach(course => {
                    appendSearchCourse(course);
                });
            } else {
                console.log(response.error);
            }
        },
        error: function (xhr, status, error) {
            console.error('Request failed with error: ' + error);
            // location.reload();
        }
    });
}

function searchCourse() {
    course_table.children('tbody').empty();

    $.ajax({
        type: "post",
        url: "course/search",
        data: $('#form-filter').serialize(),
        dataType: "json",
        success: function (response) {
            console.log(response);
            if (response.courses) {
                response.courses.forEach(course => {
                    appendSearchCourse(course);
                });
            } else {
                console.log(response.error);
            }
        },
        error: function (xhr, status, error) {
            console.error('Request failed with error: ' + error);
            // location.reload();
        }
    });
}

function appendSearchCourse(course) {
    var courseSnippet = `<tr>
        <td>
            <div class="d-flex flex-row align-items-center">
                <p class="user-name" style="margin-left:0.5rem">
                    ${course.name}
                </p>
            </div>
        </td>
        <td>
            ${course.teacher.name}
        </td>
        <td>
            ${course.course_start ? course.course_start : 'Không'}
        </td>
        <td>
            ${course.course_end ? course.course_end : 'Không'}
        </td>
        <td>
            ${course.enrolment_method}
        </td>
        <td>
            <a href="/course/edit?type=course&id=${course.id}">
                <i class="fa fa-cog delete-icon" aria-hidden="true" style="margin-right: 5px"
                    title="Sửa"></i>
            </a>
            <i class="fa ${course.status == '1' ? 'fa-eye' : 'fa-eye-slash'} delete-icon"
                title="${course.status == '1' ? 'Đình chỉ khóa học' : 'Kích hoạt khóa học'}"
                aria-hidden="true" style="margin-right: 5px"
                onclick="${course.status == '1' ? 'suspendCourse' : 'activeCourse'}(this,${course.id})"></i>

            <i class="fa fa-trash delete-icon" aria-hidden="true" title="xóa"
                onclick="deleteCourse(this, ${course.id})" style="margin-right: 5px"></i>
        </td>
    </tr>`;

    course_table.children('tbody').append(courseSnippet);
}

function suspendCourse(e, id) {
    var courseName = $(e).parents('tr').find('.user-name').text().trim();
    if (confirm('Xác nhận đình chỉ khóa học ' + courseName + ' ?')) {
        $.ajax({
            type: "post",
            url: "course/suspend?id=" + id,
            success: function (response) {
                if (response.success) {
                    toast_success(response.success);
                    location.reload();
                } else {
                    console.log(response.error);
                }
            },
            error: function (xhr, status, error) {
                console.error('Request failed with error: ' + error);
                location.reload();
            }
        });
    }
}

function activeCourse(e, id) {
    var courseName = $(e).parents('tr').find('.user-name').text().trim();
    if (confirm('Xác nhận kích hoạt khóa học ' + courseName + ' ?')) {
        $.ajax({
            type: "post",
            url: "course/active?id=" + id,
            success: function (response) {
                if (response.success) {
                    toast_success(response.success);
                    location.reload();
                } else {
                    console.log(response.error);
                }
            },
            error: function (xhr, status, error) {
                console.error('Request failed with error: ' + error);
                location.reload();
            }
        });
    }
}

function deleteCourse(e, id) {
    var courseName = $(e).parents('tr').find('.user-name').text().trim();
    if (confirm('Xác nhận xóa khóa học ' + courseName + ' ?')) {
        $.ajax({
            type: "post",
            url: "course/delete?id=" + id,
            success: function (response) {
                if (response.success) {
                    toast_success(response.success);
                    location.reload();
                } else {
                    console.log(response.error);
                }
            },
            error: function (xhr, status, error) {
                console.error('Request failed with error: ' + error);
                location.reload();
            }
        });
    }

}

function toast_success(message) {
    toastr.success(message, 'Success', {
        closeButton: true,
        positionClass: 'toast-top-right',
        timeOut: 1000
    });
}