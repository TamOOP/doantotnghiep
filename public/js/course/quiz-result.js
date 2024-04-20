const query = new URLSearchParams(window.location.search);

const search_bar = $('.search-bar');
const search_option = $('#search-option');
const result = {
    '0': 'Trượt',
    '1': 'Qua'
};

const modal = $('.modal-layout');
const modal_close_icon = $('#close-modal');
const modal_close_btn = $('#btn-modal-close');
const table = $('#table-attempts');
const form_search = $('#form-filter');
const table_result = $('#table-result');

$(document).ready(function () {
    generateGradeChart();

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#search-condition').change(function (e) {
        e.preventDefault();
        var condition = $(this).val();
        if (condition == 'name' || condition == 'email') {
            search_bar.show();
            search_option.hide();
        } else if (condition == 'result') {
            search_bar.hide();
            search_option.empty();
            search_option.attr('name', 'status-option');

            for (var key in result) {
                search_option.append($('<option>', {
                    value: key,
                    text: result[key]
                }));
            }
            search_option.show();

        } else if (condition == 'grade-morethan') {
            search_bar.hide();
            search_option.empty();
            search_option.attr('name', 'grade-option');

            var gradeGap = gradeScale / 10;
            for (var i = gradeGap; i < gradeScale; i += gradeGap) {
                search_option.append($('<option>', {
                    value: i,
                    text: i + '.00'
                }));
            }
            search_option.show();
        }
    });

    form_search.submit(function (e) {
        e.preventDefault();

        $.ajax({
            type: "post",
            url: "result/search?id=" + query.get('id'),
            data: form_search.serialize(),
            dataType: "json",
            success: function (response) {
                if (!response.error) {
                    table_result.find('tbody').empty();
                    response.students.forEach(student => {
                        appendSearchResult(student);
                    });
                } else {
                    alert(response.error);
                }
            },
            error: function (xhr, status, error) {
                console.error('Request failed with error: ' + error);
                // location.reload();
            }
        });
    });

    modal_close_btn.click(function (e) {
        e.preventDefault();
        close_modal();
    });

    modal_close_icon.click(function (e) {
        e.preventDefault();
        close_modal();
    });

    table_result.children('tbody').on("click", ".info-icon", function (e) {
        var userId = $(this).attr('data-attempt');
        var userName = $(this).parents('tr').find('.user-name').text();

        $('#studentName').text(userName);
        table.children('tbody').empty();

        open_modal();
        sendGetAttemptsRequest(userId);
    });
});

function generateGradeChart() {
    var gradeRange = Object.keys(gradeStatis);

    var gradeCount = gradeRange.map(function (key) {
        return gradeStatis[key];
    });

    new Chart("myChart", {
        type: "bar",
        data: {
            labels: gradeRange,
            datasets: [{
                label: "Học sinh",
                backgroundColor: "purple",
                data: gradeCount
            }]
        },
    });
}

function sendGetAttemptsRequest(userId) {
    $.ajax({
        type: "post",
        url: "result/getAttempts?id=" + query.get('id') + "&userId=" + userId,
        success: function (response) {
            if (response.attempts) {
                response.attempts.forEach(attempt => {
                    appendAttempt(attempt);
                });
            }
        },
        error: function (xhr, status, error) {
            console.error('Request failed with error: ' + error);
        },
    });
}

function appendSearchResult(student) {
    var studentSnippet = `<tr>
        <td>
            <div class="d-flex flex-row align-items-center">
                <div class="avata">
                    <img class="user-img" src="/${student.avata}" alt="">
                </div>
                <p class="user-name" style="margin-left:0.5rem">
                    ${student.name}
                </p>
            </div>
        </td>
        <td>
            ${student.username}
        </td>
        <td>${student.finalGrade}</td>
        <td>
            <i class="fa ${student.status == 'Qua' ? 'fa-check success-icon' : 'fa-times fail-icon'} mr-2"
                aria-hidden="true"></i>
            <span>${student.status}</span>
        </td>
        <td>
            <i class="fa fa-info-circle info-icon mr-2" aria-hidden="true"
                data-attempt="${student.id}"></i>
        </td>
    </tr>`;

    table_result.children('tbody').append(studentSnippet);
}

function appendAttempt(attempt) {
    var index = $('.tr-attempt').length;

    var attemptSnippet = `<tr class="tr-attempt">
        <td>${index + 1}</td>
        <td>${attempt.status}</td>
        <td>${attempt.final_grade}</td>
        <td>${attempt.work_time}</td>
        <td>
            ${attempt.time_start}
        </td>
        <td>
            ${attempt.time_end}
        </td>
        <td>
            <a href="attempt/result?id=${query.get('id')}&attemptId=${attempt.id}">Xem lại</a>
        </td>
    </tr>`;

    table.children('tbody').append(attemptSnippet);
}

function close_modal() {
    $('body').removeClass('modal-open');
    modal.hide();
}

function open_modal() {
    $('body').addClass('modal-open');
    modal.show();
}