$(document).ready(function () {
    var fragment = window.location.hash;
    scrollToSection(fragment.substring(1));

    var main_content = $('.main-content');
    var btn_open_sidebar = $('#btn-open-sidebar');
    var sidebar = $('.sidebar');
    var btn_close_sidebar = $('#btn-close-sidebar');
    var icon_expand = $('.expand-icon');
    var sidebar_item = $('.sidebar-item');

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

    icon_expand.click(function (e) {
        e.preventDefault();
        $(this).toggleClass('rotated');
        $(this).parent().siblings().slideToggle();
        $('.sidebar-topic').removeClass('active');
        $(this).parent().parent().addClass('active');
    });

    sidebar_item.click(function (e) {
        if (!$('.expand-icon').is(e.target)) {
            $(this).find('.expand-icon').removeClass('rotated');
            $(this).siblings().slideDown();
            sidebar_item.removeClass('active');
            $(this).addClass('active');

            if (window.location.pathname == '/course/view') {
                var hrefParts = $(this).find('a').attr('href').split('#');
                var sectionId = hrefParts.length > 1 ? hrefParts[1] : null;

                e.preventDefault();
                scrollToSection(sectionId);
            }
        }
    });


});

function scrollToSection(sectionId) {

    if (sectionId == null || sectionId.trim() === '') {
        return;
    }
    var sectionOffset = $("#" + sectionId).offset().top - 90;

    window.scrollTo({
        top: sectionOffset,
        behavior: 'smooth'
    });
}

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