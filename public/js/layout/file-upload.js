const file_drop_area = $('#file-drop-area');
const img_preview = $('.img-container');
const file_preview = $('#file-container');
const file_input = $('#file');

$(document).ready(function () {
    if (typeof path !== 'undefined' && typeof fileName !== 'undefined') {
        appendFileAndShow('/' + path, fileName);
    }

    $('#remove-icon').click(function (e) {
        e.preventDefault();
        img_preview.find('img').remove();
        file_preview.find('p').remove();

        file_drop_area.show();
        file_preview.hide();

        file_input.val('');
        $('#isDeleted').val('true');
    });

    file_drop_area.on('dragenter dragover dragleave drop', function (e) {
        e.preventDefault();
        e.stopPropagation();
    });

    // Handle the drop event
    file_drop_area.on('drop', function (e) {
        e.preventDefault();
        e.stopPropagation();

        file_drop_area.removeClass('highlight');

        const files = e.originalEvent.dataTransfer.files;
        if (files[0]) {
            displayFile(files[0]);
        }
    });
});

function displayFile(file) {
    if (file) {
        const reader = new FileReader();
        const isImage = file.type.startsWith('image/');

        reader.onload = function (e) {
            appendFileAndShow(isImage ? e.target.result : null, file.name);
        };

        reader.readAsDataURL(file);
    }
}

function appendFileAndShow(path, name) {
    var img = $('<img>')
        .addClass('uploaded-image')
        .attr('src', path ? path : '/image/file.jpg')
        .attr('onerror', 'this.onerror=null; this.src="/image/file.jpg";');

    var file_name = $('<p>')
        .text(name)
        .addClass('file-name');

    file_drop_area.hide();

    img_preview.append(img);
    file_preview.append(file_name);

    file_preview.show();
}

function excuteFile(input) {
    const reader = new FileReader();
    const files = input.files;

    if (files[0]) {
        displayFile(files[0]);
    }

    reader.readAsDataURL(files[0]);

}

function handleDragOver(event) {
    event.preventDefault();
    file_drop_area.addClass('highlight');
}

function handleDragLeave(event) {
    event.preventDefault();
    file_drop_area.removeClass('highlight');
}

function validateFileRequire() {
    var valid = true;
    if (typeof require !== 'undefined' && $('#file')[0].files.length == 0 && $('#isDeleted').val() == 'true') {
        valid = false;
        $('.warning-msg').show();
        file_drop_area.addClass('error');
        focusFileArea();
    } else {
        file_drop_area.removeClass('error');
        $('.warning-msg').hide();
    }
    return valid;
}

function focusFileArea() {
    var selectOffset = file_drop_area.offset().top;
    var windowHeight = $(window).height();
    var scrollTo = selectOffset - (windowHeight / 2);

    $("html").scrollTop(scrollTo);
}