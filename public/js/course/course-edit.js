var form = $('#form-data');

$(document).ready(function () {
    var input_fee = $('#fee-container');

    $('#enrolment-method').change(function (e) {
        e.preventDefault();
        if ($(this).val() == '2') {
            input_fee.show();
        } else {
            input_fee.hide();
        }
    });

    $('#course-fee').keyup(function (e) { 
        let inputValue = $(this).val();

        inputValue = inputValue.replace(/\D/g, '');

        let formattedValue = numberWithCommas(inputValue);
        $(this).val(formattedValue);
    });

    $('#form-data').submit(function (e) {
        e.preventDefault();
        const nameInput = $('#name');
        const methodValue = $('#enrolment-method').val();

        let valid = true;

        if (!nameInput.val()) {
            appendError(nameInput.parent(), 'Tên không để trống');
            focusError(nameInput);
            valid = false;
        }

        valid &= isDateTimeInputValid();
        if (methodValue === '2' && !(parseInt($('#course-fee').val(), 10) > 0)) {
            valid = false;
            appendError($('#course-fee').parent(), 'Giá khóa học không để trống');
            focusError($('#course-fee'));
        }

        if (methodValue !== '0' && methodValue !== '1' && methodValue !== '2') {
            valid = false;
        }

        var formData = new FormData(form[0]);

        $.ajax({
            url: '/course/update?id=' + id,
            method: 'POST',
            data: formData,
            dataType: "json",
            contentType: false,
            processData: false,
            success: function (response) {
                if (response.error) {
                    location.reload();
                } else {
                    toast_success('Sửa thành công');
                    window.location.href = "/course/view?id=" + id;
                }
                console.log(response);
            },
            error: function (xhr, status, error) {
                // Handle error
                console.error(xhr.responseText);
            }
        });

    });
});

function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}