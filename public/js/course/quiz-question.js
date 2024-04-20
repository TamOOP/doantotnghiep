var query = new URLSearchParams(window.location.search);
var field_require = [
    $('#description'),
    $('#question-mark'),
];

const modal = $('.modal-layout');
const modal_close_icon = $('#close-modal');
const modal_close_btn = $('#btn-modal-close');

$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    if ($('.answer-block').length == 0) {
        for (let i = 1; i < 5; i++) {
            addAnswerBlock(i);
        }
    } else {
        for (let i = 0; i < $('.answer-block').length; i++) {
            addGradeOption($('.answer-block').eq(i).find('select'));
        }
    }


    $('#btn-add-answer').click(function (e) {
        e.preventDefault();
        addAnswerBlock($('.answer-block').length + 1);
    });

    $('#form-question').submit(function (e) {
        e.preventDefault();
        if (validateRequireField() && validateGradeAnswer()) {
            $.ajax({
                type: "post",
                url: "store?id=" + query.get('id'),
                data: $(this).serialize(),
                dataType: "json",
                success: function (response) {
                    if (response.success) {
                        toast_success(response.success);
                    } else {
                        alert(response.error);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Request failed with error: ' + error);
                },
                complete: function () {
                    location.reload();
                }
            });
        }
    });

    $('#btn-cancel').click(function (e) {
        window.location.href = document.referrer;
    });

    $('#form-edit-question').submit(function (e) {
        e.preventDefault();
        var formData = new FormData($('#form-edit-question')[0]);

        if (validateRequireField() && validateGradeAnswer()) {
            $.ajax({
                type: "post",
                url: "update?type=question&id=" + query.get('id'),
                data: formData,
                dataType: "json",
                contentType: false,
                processData: false,
                success: function (response) {
                    if (response.success) {
                        toast_success(response.success);
                        window.location.href = document.referrer;
                    } else {
                        alert(response.error)
                    }
                },
                error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                    location.reload();
                }
            });
        }
    });

    modal_close_btn.click(function (e) {
        close_modal();
    });

    modal_close_icon.click(function (e) {
        close_modal();
    });

});

function validateRequireField() {
    var valid = true;
    var count_answer = 0;

    for (let i = 0; i < $('.answer-grade').length; i++) {
        var grade_select = $('.answer-grade').eq(i);

        if (grade_select.val() != '0') {
            var answer_content = grade_select.parents('.answer-block').find('textarea');
            if (!answer_content.val().trim()) {
                appendError(answer_content, 'Hãy nhập nội dung câu trả lời');
                focusElement(answer_content);
                valid = false;
            } else {
                removeError(answer_content);
            }
        }

        if (grade_select.val() != '0'
            || grade_select.parents('.answer-block').find('textarea').val().trim() !== '') {
            count_answer++;
        }
    }

    if (count_answer < 2) {
        valid = false;
        appendError($('.answer-block').eq(0).find('textarea'), 'Cần ít nhất 2 câu trả lời');
        focusElement($('.answer-block').eq(0).find('textarea'));
    }

    for (let i = 0; i < field_require.length; i++) {
        if (!field_require[i].val()) {
            appendError(field_require[i], 'Hãy nhập trường thông tin');
            focusElement(field_require[i]);
            valid = false;
        }
        else {
            removeError(field_require[i]);
        }
    }

    return valid;
}

function validateGradeAnswer() {
    var valid = false;
    var type_answer = $('#answer-type').val();
    var grade_index = 0;

    if (type_answer == '0') {
        for (let i = 0; i < $('.answer-block').length; i++) {
            removeError($('.answer-grade').eq(i));

            if ($('.answer-grade').eq(i).val() != '0') {
                grade_index = i;
            }

            if ($('.answer-grade').eq(i).val() == '1.0') {
                valid = true;
            }
        }

        if (!valid) {
            appendError($('.answer-grade').eq(grade_index), 'Một trong số câu trả lời phải có điểm tối đa');
            focusElement($('.answer-grade').eq(grade_index));
        }
    } else {
        var totalGrade = 0;

        for (let i = 0; i < $('.answer-block').length; i++) {
            removeError($('.answer-grade').eq(i));

            var answer_grade = parseFloat($('.answer-grade').eq(i).val());
            if (answer_grade != '0') {
                grade_index = i;
            }

            if (answer_grade > 0) {
                totalGrade += answer_grade;
            }
        }

        if (totalGrade != 1) {
            appendError($('.answer-grade').eq(grade_index), 'Tổng điểm dương của câu trả lời phải bằng 100. Tổng điểm hiện tại là ' + (totalGrade * 100));
            focusElement($('.answer-grade').eq(grade_index));
        } else {
            valid = true;
        }
    }

    return valid;
}

function reviewQuestion(event, id) {
    var index = $(event).parents('td').siblings('td').eq(0).text();
    modal.find('.modal-body').empty();
    open_modal();
    $.ajax({
        type: "post",
        url: "question/show?id=" + query.get('id') + "&questionId=" + id,
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                console.log(response.question);
                appendQuestion(response.question, index);
            } else {
                alert(response.error);
            }
        },
        error: function (xhr, status, error) {
            console.error('Request failed with error: ' + error);
        }
    });

}

function deleteQuestion(id) {
    if (confirm('Xác nhận xóa câu hỏi?')) {
        $.ajax({
            type: "post",
            url: "question/delete?id=" + query.get('id'),
            data: {
                questionId: id
            },
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    toast_success(response.success);
                } else {
                    alert(response.error);
                }
            },
            error: function (xhr, status, error) {
                console.error('Request failed with error: ' + error);
            },
            complete: function () {
                location.reload();
            }
        });
    };
}

function appendQuestion(question, index) {
    var questionSnippet = `<div class="question-container mt-4 p-4">
        <div class="info">
            <h3 class="bold">
                <span>Câu hỏi</span>
                <span style="font-size: 1.2rem">${index}</span>
            </h3>
            <p>Chưa trả lời</p>
            <p>Điểm / ${question.mark}</p>
            <div class="mt-2">
                <a href="/course/activity/edit?type=question&id=${question.id}">
                    <i class="fa fa-cog setting-icon mr-2" aria-hidden="true"></i>
                    <span>Sửa câu hỏi</span>
                </a>
            </div>
        </div>
        <div class="content">
            <p class="topic">${question.content}</p>
            <div class="answer-container clearfix">
                
            </div>
        </div>
    </div>`;

    $('.modal-body').append(questionSnippet);

    question.choices.forEach(choice => {
        var choiceSnippet = `
            <div class="answer-item" >
                <input type="${question.multi_answer ? 'checkbox' : 'radio'}" name="${question.multi_answer ? 'question[]' : 'question'}" class="choice">
                <span>${choice.number}</span>
                <span>${choice.content}</span>
            </div>`;

        $('.answer-container').append(choiceSnippet);
    });
}

function addAnswerBlock(index) {
    const answer_snippet = `
    <div class="answer-block mt-3" id="ans-`+ index + `">
        <div class="group-input">
            <p class="input-label" class="p-2">
                Lựa chọn` + index + `
            </p>
            <div class="course-input w-100" style="max-width: none">
                <textarea class="form-control course-input" name="choices[]" cols="80" rows="3"></textarea>
            </div>
        </div>
        <div class="group-input">
            <p class="input-label" class="p-2">
                Điểm
            </p>
            <div class="form-inline course-input">
                <div style="position: relative">
                    <select class="form-select answer-grade" name="choice-grades[]">
                        <option value="0">Không có điểm</option>
                    </select>
                    <i class="warning-icon fa fa-exclamation-circle" style="display:none"></i>
                </div>
                <p class="warning-msg"></p>
            </div>
        </div>
    </div>`;

    $('#answer-container').append(answer_snippet);
    addGradeOption($('#ans-' + index).find('select'));
}

function addGradeOption(select) {
    const grades = [
        '1.0', '0.9', '0.8333333', '0.8', '0.75', '0.7',
        '0.6666667', '0.6', '0.5', '0.4', '0.3333333', '0.3',
        '0.25', '0.2', '0.1666667', '0.125', '0.1111111', '0.1'
    ];
    var selectedValue = parseFloat(select.attr('data-selected')).toFixed(2);

    for (let i = 0; i < grades.length; i++) {
        select.append($('<option>', {
            value: grades[i],
            text: grades[i] * 100 + '%',
            selected: selectedValue == parseFloat(grades[i]).toFixed(2)
        }));
    }

    grades.sort();

    for (let i = 0; i < grades.length; i++) {
        select.append($('<option>', {
            value: -grades[i],
            text: -grades[i] * 100 + '%',
            selected: selectedValue == parseFloat(-grades[i]).toFixed(2)
        }));
    }
}

function focusElement(element) {
    var selectOffset = element.offset().top;
    var windowHeight = $(window).height();
    var scrollTo = selectOffset - (windowHeight / 2);

    $("html").scrollTop(scrollTo);
    element.focus();
}

function appendError(target, message) {
    target.addClass('warning-input')

    var errorSnippet = $('<p>', {
        text: message,
        class: 'warning-msg'
    });
    target.parent().find('.warning-msg').remove();
    target.parent().append(errorSnippet);
}

function removeError(target) {
    target.parent().find('.warning-msg').remove();
    target.removeClass('warning-input');
}

function gradeErrorAlert(index, message) {
    var answer_block = $('.answer-block').eq(index);
    answer_block.find('.answer-grade').addClass('warning-input');
    answer_block.find('.warning-icon').show();
    answer_block.find('.warning-msg').text(message);
}

function toast_success(message) {
    toastr.success(message, 'Success', {
        closeButton: true,
        positionClass: 'toast-top-right',
        timeOut: 1000
    });
}

function close_modal() {
    $('body').removeClass('modal-open');
    modal.hide();
}

function open_modal() {
    $('body').addClass('modal-open');
    modal.show();
}