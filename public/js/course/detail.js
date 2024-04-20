var topic_option = $('.topic-option');
var option_content = $('.option-content');
var tab_pane = $('.modal-pane');
var nav_tab = $('.modal-nav-tab');
var modal_select_activity = $('.modal-layout');
var open_select_activity = $('.add-activity-container');
var toggle_process_btn = $('.btn-toggle-process');
var process_content_done = "<div class='btn-content'><i class='fa fa-check' aria-hidden='true' style='margin-right:5px'></i><span>Hoàn thành</span></div>";
var process_content_undo = '<div class="btn-content"><span>Đánh dấu hoàn thành</span></div>';

$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    nav_tab.click(function (e) {
        var tab_target = $(this).attr('href');

        nav_tab.removeClass('active');
        tab_pane.removeClass('active');
        $(this).addClass('active');
        $(tab_target).addClass('active');
    });

    topic_option.click(function (e) {
        topic_option.removeClass('selected');
        $(this).addClass('selected');
        $(this).find('.option-content').toggle();
    });

    toggle_process_btn.click(function (e) {
        e.preventDefault();

    });

    $(document).click(function (event) {
        if (!topic_option.is(event.target) && topic_option.has(event.target).length === 0) {
            topic_option.removeClass('selected');
            option_content.hide();
        }
    });
});

function toggleProcess(e, activityId) {
    var toggle_btn = $(e);
    var process_type = $(e).attr('process-toggletype');
    toggle_btn.addClass('disabled');
    toggle_btn.prop('disabled', true);
    toggle_btn.find('.btn-content').remove();

    $.ajax({
        type: "post",
        url: "process/toggle?id=" + activityId,
        success: function (response) {
            if (response.success) {
                if (process_type == 'done') {
                    toggle_btn.append(process_content_done);
                    toggle_btn.removeClass('btn-inprogress');
                    toggle_btn.addClass('btn-done')
                    toggle_btn.attr('process-toggletype', 'undone');
                } else if (process_type == 'undone') {
                    toggle_btn.append(process_content_undo);
                    toggle_btn.attr('process-toggletype', 'done');
                    toggle_btn.addClass('btn-inprogress');
                    toggle_btn.removeClass('btn-done')
                }

                toggle_btn.prop('disabled', false);
                toggle_btn.removeClass('disabled');
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

function deleteTopic(e, id) {
    var topicName = $(e).parents('.topic-option').siblings('.collapse-title').text().trim();
    if (confirm('Xác nhận xóa chủ đề ' + topicName + ' ?')) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "post",
            url: "topic/delete?id=" + id,
            success: function (response) {
                if (response.success) {
                    toast_success(response.success);
                    $(e).parents('.topic-item').remove();
                } else {
                    console.log(response);
                    location.reload();
                }
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
                location.reload();
            }
        });
    }

}

function deleteActivity(e, type, id) {
    var url;
    switch (type) {
        case 'exam':
            url = 'quiz/delete?id=' + id;
            break;
        case 'assign':
            url = 'assign/delete?id=' + id;
            break;
        case 'file':
            url = 'file/delete?id=' + id;
            break;
    }

    var activityName = $(e).parents('.activity-container').find('.activity-link').text().trim();
    if (confirm('Xác nhận xóa chủ đề ' + activityName + ' ?')) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "post",
            url: url,
            success: function (response) {
                if (response.success) {
                    toast_success(response.success);
                    $(e).parents('.activity-container').remove();
                } else {
                    console.log(response);
                    // location.reload();
                }
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
                // location.reload();
            }
        });
    }

}

function openModal(id) {
    $('body').addClass('modal-open');
    modal_select_activity.show();

    var activity_link = $('.modal-pane').find('a');
    for (let i = 0; i < activity_link.length; i++) {
        var href = activity_link.eq(i).attr('href');
        activity_link.eq(i).attr('href', href.replace('id=', 'id=' + id));
    }

    $('#close-activity').attr('onclick', 'closeModal(' + id + ')');
}

function closeModal(id) {
    $('body').removeClass('modal-open');
    modal_select_activity.hide();

    var activity_link = $('.modal-pane').find('a');
    for (let i = 0; i < activity_link.length; i++) {
        var href = activity_link.eq(i).attr('href');
        activity_link.eq(i).attr('href', href.replace('id=' + id, 'id='));
    }
}

function toast_success(message) {
    toastr.success(message, 'Success', {
        closeButton: true,
        positionClass: 'toast-top-right',
        timeOut: 1000
    });
}