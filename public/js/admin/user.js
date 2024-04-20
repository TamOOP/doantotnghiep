const search_bar = $('.search-bar');
const search_option = $('#role-option');
const user_table = $('#user-table');

var field_require = [
    $('#name'),
    $('#password'),
    $('#username'),
]

$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#search-condition').change(function (e) {
        e.preventDefault();
        var condition = $(this).val();
        if (condition == 'name' || condition == 'email') {
            search_bar.show();
            search_option.hide();
        } else if (condition == 'role') {
            search_bar.hide();
            search_option.show();
        }
    });

    $('#form-filter').submit(function (e) {
        e.preventDefault();
    });

    $('#btn-submit').click(function (e) {
        e.preventDefault();
        if (validateRequireField()) {
            sendAddUserRequest();
        }
    });

    $('.btn-change').click(function (e) {
        var userId = $('.selected').attr('data-id');
        var role = $(e.delegateTarget).attr('data-target');
        sendChangeRoleRequest(userId, role);
    });

    let typingTimer;
    const doneTypingInterval = 300;
    $('.search-input').keyup(function (e) {
        e.preventDefault();

        var role = $(e.delegateTarget).attr('name');
        var keyword = $(e.delegateTarget).val();
        var box = $(e.delegateTarget).parents('.column-user-container').find('.list-container');
        box.empty();

        clearTimeout(typingTimer);

        typingTimer = setTimeout(function () {
            $.ajax({
                type: "post",
                url: "/admin/user/search",
                data: {
                    role: role,
                    keyword: keyword
                },
                dataType: "json",
                success: function (response) {
                    response.users.forEach(user => {
                        appendUserRoleSearch(user, box);
                    });
                },
                error: function (xhr, status, error) {
                    console.error('Request failed with error: ' + error);
                    // location.reload();
                }
            });
        }, doneTypingInterval);

    });
});

function appendUserRoleSearch(user, target) {
    var userSnippet = `
        <p class="user" data-id=${user.id} onclick="userSelected(this)">
            ${user.name} (${user.username})
        </p>`;

    target.append(userSnippet);
}

function userSelected(e) {
    $('.user').removeClass('selected');
    $(e).addClass('selected');
    var box_side = $(e).parents('.list-container').attr('id');
    $('.btn').prop('disabled', false);

    $(box_side == 'student-box' ? '#btn-left' : '#btn-right').prop('disabled', true);
}

function sendChangeRoleRequest(id, role) {
    $.ajax({
        type: "post",
        url: "changeRole",
        data: {
            'id': id,
            'role': role
        },
        dataType: "json",
        success: function (response) {
            if (response.success) {
                toast_success(response.success);
            } else {
                console.log(response.error);
            }
        },
        error: function (xhr, status, error) {
            console.error(xhr.responseText);
        },
        complete: function () {
            location.reload();
        }
    });
}

function sendAddUserRequest() {
    var formData = new FormData($('#form-user')[0]);

    $.ajax({
        type: "post",
        url: "store",
        data: formData,
        dataType: "json",
        contentType: false,
        processData: false,
        success: function (response) {
            if (response.success) {
                toast_success(response.success);
            } else {
                console.log(response.error);
            }
        },
        error: function (xhr, status, error) {
            console.error(xhr.responseText);
        },
        complete: function () {
            location.reload();
        }
    });
}

function searchUser() {
    user_table.children('tbody').empty();

    $.ajax({
        type: "post",
        url: "user/search",
        data: $('#form-filter').serialize(),
        dataType: "json",
        success: function (response) {
            if (response.users) {
                response.users.forEach(user => {
                    appendSearchUser(user);
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

function suspendUser(e, id) {
    var username = $(e).parents('tr').find('.user-name').text().trim();
    if (confirm('Xác nhận đình chỉ người dùng ' + username + ' ?')) {
        $.ajax({
            type: "post",
            url: "user/suspend?id=" + id,
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

function activeUser(e, id) {
    var username = $(e).parents('tr').find('.user-name').text().trim();
    if (confirm('Xác nhận kích hoạt người dùng ' + username + ' ?')) {
        $.ajax({
            type: "post",
            url: "user/active?id=" + id,
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

function deleteUser(e, id) {
    var username = $(e).parents('tr').find('.user-name').text().trim();
    if (confirm('Xác nhận xóa người dùng ' + username + ' ?')) {
        $.ajax({
            type: "post",
            url: "user/delete?id=" + id,
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

function appendSearchUser(user) {
    var userSnippet = `<tr>
        <td>
            <div class="d-flex flex-row align-items-center">
                <div class="avata">
                    <img src="/${user.avata}" width="${user.width}"
                        height="${user.height}">
                </div>
                <p class="user-name" style="margin-left:0.5rem">
                    ${user.name}
                </p>
            </div>
        </td>
        <td>
            ${user.username}
        </td>
        <td>
            ${user.role == 'student' ? 'Học sinh' : 'Giáo viên'}
        </td>
        <td>
            <i class="fa fa-cog delete-icon" aria-hidden="true" style="margin-right: 5px" title="Sửa"></i>
            
            <i class="fa ${user.status == '1' ? 'fa-eye' : 'fa-eye-slash'} delete-icon"
                title="${user.status == '1' ? 'Đình chỉ người dùng' : 'Kích hoạt người dùng'}" aria-hidden="true"
                onclick="${user.status == '1' ? 'suspendUser' : 'activeUser'}(this, ${user.id})" style="margin-right: 5px"></i>

            <i class="fa fa-trash delete-icon" aria-hidden="true" title="xóa"
                onclick="deleteUser(this, ${user.id})" style="margin-right: 5px"></i>
        </td>
    </tr>`

    user_table.children('tbody').append(userSnippet);
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

function toast_success(message) {
    toastr.success(message, 'Success', {
        closeButton: true,
        positionClass: 'toast-top-right',
        timeOut: 1000
    });
}