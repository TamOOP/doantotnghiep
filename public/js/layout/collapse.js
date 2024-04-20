$(document).ready(function () {
    $('.collapse-title').click(function (e) {
        $(this).parent().find('.collapse-icon').toggleClass('rotated');
        $(this).parent().siblings('.collapse-content').slideToggle();
    });
    $('.collapse-icon').click(function (e) {
        $(this).parent().find('.collapse-icon').toggleClass('rotated');
        $(this).parent().siblings('.collapse-content').slideToggle();
    });
});