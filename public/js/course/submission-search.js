const search_bar = $('.search-bar');
const search_option = $('#search-option');
const form_search = $('#form-filter');
const submisson_table = $('#submission-list');
const assign_id = new URLSearchParams(window.location.search).get('id');

$(document).ready(function () {

    $('#search-condition').change(function (e) {
        e.preventDefault();
        var condition = $(this).val();

        if (condition == 'name' || condition == 'email') {
            search_bar.show();
            search_option.hide();
        } else if (condition == 'grade-status') {
            search_bar.hide();
            search_option.show();
        }
    });

    form_search.submit(function (e) {
        e.preventDefault();

        $.ajax({
            type: "post",
            url: "submission/search?id=" + assign_id,
            data: form_search.serialize(),
            dataType: "json",
            success: function (response) {
                if (!response.error) {
                    submisson_table.find('tbody').empty();
                    response.submissions.forEach(submission => {
                        appendSearchResult(submission);
                    });
                }
            },
            error: function (xhr, status, error) {
                console.error('Request failed with error: ' + error);
                location.reload();
            }
        });

    });
});

function appendSearchResult(submission) {
    var subSnippet = `<tr>
        <td>
            <div class="d-flex flex-row align-items-center">
                <div class="avata">
                    <img class="user-img" src="/${submission.avata}" alt="">
                </div>
                <p class="user-name" style="margin-left:0.5rem">
                    ${submission.name}
                </p>
            </div>
        </td>
        <td>
            ${submission.username}
        </td>
        <td>
            <p class="grade-status ${submission.grade == -1 ? 'not-grade' : 'graded'}">
                ${submission.grade == -1 ? 'Chưa chấm' : 'Đã chấm'}
            </p>
        </td>
        <td>
            ${submission.grade > -1 ? submission.grade : ''}
        </td>
        <td>
            ${submission.last_grade ? submission.last_grade : ''}
        </td>
        <td>
            <a href="${submission.file_path}" download="${submission.file_path.replace(/^.*[\\\/]/, '')}">
                <p>${submission.file_path.replace(/^.*[\\\/]/, '')}</p>
            </a>
            <a href="grading?id=${submission.assign_id}&userId=${submission.user_id}">
                <button class="btn btn-blue" style="padding: 3px 10px; margin-top: 5px">Chấm</button>
            </a>
        </td>
        <td>
            ${submission.last_modified}
        </td>
    </tr>`;

    submisson_table.children('tbody').append(subSnippet);
}
