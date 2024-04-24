$(document).ready(function () {
    const payment_method = $('.priority-screen');
    const course_id = new URLSearchParams(window.location.search).get('id');
    
    $('#btn-cancel').click(function (e) {
        e.preventDefault();
        payment_method.hide();
    });

    $('#btn-method').click(function (e) {
        e.preventDefault();
        payment_method.show();
    });

    $('.payment-method-radio').first().prop('checked', true);
    $('.payment-method-item').click(function (e) {
        $(e.delegateTarget).children('.payment-method-radio').prop('checked', true);
    });

    $('#btn-back').click(function (e) {
        window.location.replace(document.referrer);
    });

    $('#btn-enrol').click(function (e) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: "/course/enrol?id="+course_id,
            data: {course_id: course_id},
            dataType: "json",
            success: function (response) {
                if (response.status == 'success'){
                    toast_success('Đăng ký thành công vào khóa học');
                    window.location.replace('/course/view?id=' + course_id);
                }
                else{
                    alert('Có lỗi xảy ra khi đăng ký');
                }
            },
            error: function(){
                location.reload();
            }
        });
    });
});

function toast_success(message) {
    toastr.success(message, 'Success', {
        closeButton: true,
        positionClass: 'toast-top-right',
        timeOut: 1000
    });
}