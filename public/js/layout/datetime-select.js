$(document).ready(function () {
    setDateDefault();

    for (let i = 0; i < $('.hour-select').length; i++) {
        generateOptionDateTime('.hour-select', 0, 23, i);
    }

    for (let i = 0; i < $('.minute-select').length; i++) {
        generateOptionDateTime('.minute-select', 0, 60, i);
    }

    const enable_checkbox = $('.enable-checkbox');

    enable_checkbox.change(function (e) {
        e.preventDefault();

        var inputs = $(this).siblings('input');
        var selects = $(this).siblings('select');

        inputs.prop('disabled', !inputs.prop('disabled'));
        selects.prop('disabled', !selects.prop('disabled'));
    });
});

function generateOptionDateTime(selectObj, start, end, index) {
    const select = $(selectObj).eq(index);
    const selectedValue = select.attr('data-selected');

    for (let i = start; i <= end; i++) {
        var optionText = (i < 10) ? '0' + i : i;
        let optionSnippet = $('<option>', {
            value: i,
            text: optionText,
            selected: i == selectedValue
        });

        select.append(optionSnippet);
    }
}

function isDateTimeInputValid() {
    for (let i = 0; i < $('.enable-checkbox').length; i++) {
        const checkbox = $('.enable-checkbox').eq(i);
        if (checkbox.prop('checked') && checkbox.parent().find('.datepicker').length > 0) {
            let dateValue = checkbox.parent().find('.datepicker').val();
            let hourValue = parseInt(checkbox.parent().find('.hour-select').val(), 10);
            let minuteValue = parseInt(checkbox.parent().find('.minute-select').val(), 10);

            if (!dateValue) {
                appendError(checkbox.parent().parent(), 'Ngày không hợp lệ');
                focusError(checkbox.siblings('.datepicker'));
                return false;
            } else if (hourValue == null || !(hourValue >= 0 && hourValue <= 23)) {
                appendError(checkbox.parent().parent(), 'Giờ không hợp lệ');
                focusError(checkbox.siblings('.hour-select'));
                return false;
            } else if (minuteValue == null || !(minuteValue >= 0 && minuteValue <= 60)) {
                appendError(checkbox.parent().parent(), 'Phút không hợp lệ');
                focusError(checkbox.siblings('.minute-select'));
                return false;
            }
        }
    }
    return true;
}

function setDateDefault() {
    var today = new Date();
    var newDate = today.toISOString().split('T')[0];

    for (let i = 0; i < $('.datepicker').length; i++) {
        if (!$('.datepicker').eq(i).val()) {
            $('.datepicker').eq(i).val(newDate);
        }
    }
}