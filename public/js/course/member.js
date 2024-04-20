const course_id = new URLSearchParams(window.location.search).get('id');

const search_bar = $('.search-bar');
const search_option = $('#search-option');
const enrolment_method = {
    '0': 'Thêm thủ công',
    '2': 'Thanh toán để tham gia',
    '1': 'Tự tham gia'
};
const last_active = {
    '1 day': '1 ngày',
    '2 day': '2 ngày',
    '3 day': '3 ngày',
    '4 day': '4 ngày',
    '5 day': '4 ngày',
    '6 day': '4 ngày',
    '1 week': '1 tuần',
    '2 week': '2 tuần',
    '3 week': '3 tuần',
    '4 week': '4 tuần',
};

const modal = $('.modal-layout');
const modal_close_icon = $('#close-modal');
const modal_close_btn = $('#btn-modal-close');
const open_modal_btn = $('#btn-enrol');

const selected_user_list = $('#user-selected-list');
const confirm_enrol_btn = $('#btn-confirm-enrol');
const search_user_input = $('#search-user-input');
const clear_user_btn = $('#btn-clear-user');
const remove_user_icon = $('.remove-user');
const search_result_box = $('#search-result-box');
const search_result_list = $('#search-result-list');
const student_table = $('#student-table');

let enroled_user = [];
let db_searched_user = [];

$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).click(function (event) {
        if (!search_result_box.is(event.target)
            && search_result_box.has(event.target).length === 0
            && !search_user_input.is(event.target)) {
            search_result_box.hide();
        }
    });

    $('#search-condition').change(function (e) {
        e.preventDefault();
        var condition = $(this).val();
        if (condition == 'name' || condition == 'email') {
            search_bar.show();
            search_option.hide();
        } else if (condition == 'enrol-method') {
            search_bar.hide();
            search_option.empty();

            for (var key in enrolment_method) {
                search_option.append($('<option>', {
                    value: key,
                    text: enrolment_method[key]
                }));
            }

            search_option.attr('name', 'method-option');
            search_option.show();

        } else if (condition == 'last-active') {
            search_bar.hide();
            search_option.empty();

            for (var key in last_active) {
                search_option.append($('<option>', {
                    value: key,
                    text: last_active[key]
                }));
            }

            search_option.attr('name', 'time-option');
            search_option.show();
        }
    });

    modal_close_btn.click(function (e) {
        e.preventDefault();
        close_modal();
    });

    modal_close_icon.click(function (e) {
        e.preventDefault();
        close_modal();
    });

    open_modal_btn.click(function (e) {
        e.preventDefault();
        open_modal();
    });

    let typingTimer;
    const doneTypingInterval = 300;
    search_user_input.keyup(function (e) {
        e.preventDefault();

        clearTimeout(typingTimer);

        typingTimer = setTimeout(function () {
            $.ajax({
                type: "post",
                url: "member/search",
                data: {
                    course_id: course_id,
                    keyword: search_user_input.val(),
                    exist_user: enroled_user,
                },
                dataType: "json",
                success: function (response) {
                    search_result_list.empty();
                    db_searched_user = [];
                    response.users.forEach(student => {
                        append_search_result(student);
                        db_searched_user.push(student);
                    });
                },
                error: function (xhr, status, error) {
                    console.error('Request failed with error: ' + error);
                    location.reload();
                }
            });
        }, doneTypingInterval);


        search_result_box.show();

    });

    search_result_box.on("click", ".search-result-user", function () {
        image = $(this).find('img').attr('src');
        username = $(this).find('.user-selected-name').text().trim();
        uid = $(this).attr('data-user');

        db_searched_user.forEach(user => {
            if (user.id == uid) {
                for (let i = 0; i < enroled_user.length; i++) {
                    if (enroled_user[i].id == uid) {
                        return true;
                    }
                }

                enroled_user.push(user);
                add_user_enrol(user);
                return true;
            }
        });

        $(this).remove();
        return false;
    });

    clear_user_btn.click(function (e) {
        selected_user_list.empty();
        enroled_user = [];
    });

    confirm_enrol_btn.click(function (e) {
        confirm_enrol_btn.prop('disabled', true);

        if (enroled_user.length > 0) {
            $.ajax({
                type: "post",
                url: "member/enrol?id=" + course_id,
                data: {
                    course_id: course_id,
                    users: enroled_user
                },
                dataType: "json",
                success: function (response) {
                    if (response.status == 'success') {
                        enroled_user.forEach(user => {
                            add_user_course(user);
                        });
                        close_modal();
                        toast_success('Thêm học sinh thành công');
                    } else {
                        // location.reload();
                    }

                },
                error: function (xhr, status, error) {
                    console.error('Request failed with error: ' + error);
                    // location.reload();   
                }

            });
        }

        confirm_enrol_btn.prop('disabled', false);
    });

    $('#form-filter').submit(function (e) {
        e.preventDefault();
    });
});


function searchStudent() {
    var formData = new FormData($('#form-filter')[0]);
    formData.append('course_id', course_id);

    $.ajax({
        type: "post",
        url: "member/search",
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            student_table.children('tbody').empty();
            response.students.forEach(student => {
                add_user_course(student);
            });
        },
        error: function (xhr, status, error) {
            console.error('Request failed with error: ' + error);
            location.reload();
        }
    });
}

function remove_user_course(event, student_id) {
    var username = $(event).parents('tr').find('.user-name').text().trim();
    let confirm_delete = confirm('Xác nhận xóa "' + username + '" ra khỏi khóa học?');

    if (!confirm_delete) { return; }

    $.ajax({
        url: "member/remove",
        method: "POST",
        data: {
            course_id: course_id,
            student_id: student_id
        },
        dataType: "json",
        success: function (response) {
            if (response.status == 'success') {
                $(event).parents('tr').remove();
                toast_success(response.message);
            }
            else {
                location.reload();
            }
        },
        error: function (xhr, status, error) {
            console.error('Request failed with error: ' + error);
            location.reload();
        }
    });
}

function add_user_course(student) {
    var userSnippet = `<tr>
            <td>
                <div class="d-flex flex-row align-items-center">
                    <div class="avata">
                        <img class="user-img" src="/`+ student.avata + `" alt="">
                    </div>
                    <p class="user-name" style="margin-left:0.5rem">
                        ` + student.name + `
                    </p>
                </div>
            </td>
            <td>
                `+ student.username + `
            </td>
            <td>
                `+ (student.role == 'student' ? 'Học sinh' : '') + `
            </td>
            <td>
                `+ (student.hasOwnProperty('last_access') && student.last_access !== null ? student.last_access : 'Chưa truy cập') + `
            </td>
            <td>
                <i class="fa fa-trash delete-icon" aria-hidden="true" onclick="remove_user_course(this, `+ student.id + `)"></i>
            </td>
        </tr>`;

    student_table.children('tbody').append(userSnippet);
}

function append_search_result(student) {
    var htmlSnippet = `
        <li class="search-result-user" data-user="`+ student.id + `">
            <div class="user-selected-img-container">
                <img src="/` + student.avata + `" class="user-selected-img" alt="">
            </div>
            <span class="ml-2 user-selected-name">
                ` + student.name + ` (` + student.username + `)
            </span>
        </li>`;

    search_result_list.append(htmlSnippet);
}

function add_user_enrol(user) {
    var userSnippet = `
        <li class="user-selected-container" data-user="`+ user.id + `">
            <div class="user-selected-img-container">
                <img src="/`+ user.avata + `" class="user-selected-img" alt="">
            </div>
            <span class="user-selected-name ml-2">
                `+ user.name + ` (` + user.username + `)
            </span>
            <span class="remove-user fa fa-times" 
                aria-hidden="true"
                onclick="remove_user_enrol(this)">
            </span>
        </li>`;

    selected_user_list.append(userSnippet);
}

function remove_user_enrol(event) {
    var container = $(event).parent();
    var uid = container.attr('data-user');
    enroled_user.forEach((user, index) => {
        if (user.id == uid) {
            enroled_user.splice(index, 1);
            container.remove();

            return true;
        }
    });
}

function close_modal() {
    $('body').removeClass('modal-open');
    selected_user_list.empty();
    search_user_input.val('');
    modal.hide();

    enroled_user = [];
}

function open_modal() {
    $('body').addClass('modal-open');
    modal.show();
}


function toast_success(message) {
    toastr.success(message, 'Success', {
        closeButton: true,
        positionClass: 'toast-top-right',
        timeOut: 1000
    });
}