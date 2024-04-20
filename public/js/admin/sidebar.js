$(document).ready(function () {
    var main_content = $('.main-content');
    var btn_open_sidebar = $('#btn-open-sidebar');
    var sidebar = $('.sidebar');
    var btn_close_sidebar = $('#btn-close-sidebar');
    var sidebar_branch = $('.sidebar-item');
    var sidebar_leaf = $('.sidebar-activity');


    btn_open_sidebar.click(function (e) {
        e.preventDefault();
        sidebar.addClass('sidebar-show');
        main_content.addClass('drawer-left-sidebar');
        btn_open_sidebar.addClass('hide-btn-open-sidebar');

        toggleSessionSidebar();
    });

    btn_close_sidebar.click(function (e) {
        e.preventDefault();
        sidebar.removeClass('sidebar-show');
        main_content.removeClass('drawer-left-sidebar');
        setTimeout(function () {
            btn_open_sidebar.removeClass('hide-btn-open-sidebar');
        }, 100);

        toggleSessionSidebar();
    });

    sidebar_branch.click(function (e) {
        $(this).find('.expand-icon').toggleClass('rotated');
        $(this).siblings().slideToggle();
        sidebar_branch.removeClass('selected');
        $(this).addClass('selected');
    });
});

function toggleSessionSidebar() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        method: "POST",
        url: "/toggleSidebar",
        data: {},
        dataType: "json",
        error: function () {
            location.reload();
        }
    });
}