const query = new URLSearchParams(window.location.search);

$(document).ready(function () {
    $('#btn-submit').click(function (e) {
        e.preventDefault();

        if (!$('#name').val().trim()) {
            appendError($('#name').val().parent(), 'Hãy nhập trường thông tin');
            focusError($('#name').val());
        } else {
            var formData = new FormData($('#form-topic')[0]);

            $.ajax({
                type: "post",
                url: "update?id=" + query.get('id'),
                data: formData,
                dataType: "json",
                contentType: false,
                processData: false,
                success: function (response) {
                    if (response.success) {
                        toast_success(response.success);
                        window.location.href = document.referrer;
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
    });
});