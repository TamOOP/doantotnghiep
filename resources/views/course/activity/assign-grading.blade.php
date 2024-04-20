<!doctype html>
<html lang="en">

<head>
    <title>@yield('title', 'Title')</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/course/assign-grading.css') }}">
</head>

<body>
    @if (is_null($submission))
        <div class="alert alert-warning mt-3 ml-3 mr-3">
            Chưa có bài nộp
        </div>
    @else
        <header class="header-grading">
            <a href="#previous" disabled="disabled">
                <button class="btn btn-blue btn-change" id="btn-prev">Trước</button>
            </a>

            <a href="" class="w-100" id="owner">
                <div class="user-container flex-middle">
                    <div class="avata-container">
                        <img src="{{ asset($submission->user->avata) }}" class="user-avata">
                    </div>
                    <div class="user-name ml-3">
                        <h4>{{ $submission->user->name }}</h4>
                        <h6>{{ $submission->user->username }}</h6>
                    </div>
                </div>
            </a>
            <a href="#next">
                <button class="btn btn-blue most-right btn-change" id="btn-next">Tiếp</button>
            </a>
        </header>
        <div class="main-content">
            <div class="main-inner">
                <h5 class="mt-4 mb-4" style="font-weight: bold;">Thông tin bài nộp</h5>

                <table class="table mt-4">
                    <thead>
                        <tr>
                            <th style="width: 33%;"></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="table-attribute">Điểm</td>
                            @if ($submission->grade > -1)
                                <td class="graded" id="grade-status">Đã chấm</td>
                            @else
                                <td id="grade-status">Chưa chấm</td>
                            @endif
                        </tr>
                        <tr>
                            <td class="table-attribute submitted">Nộp lúc</td>
                            <td class="submitted" id="submit-time">
                                {{ $submission->last_modified }}
                            </td>
                        </tr>
                        <tr>
                            <td class="table-attribute ">File nộp</td>
                            <td id="file">
                                <a href=" {{ asset($submission->file_path) }}"
                                    download="{{ basename($submission->file_path) }}">
                                    {{ basename($submission->file_path) }}
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <h5 class="mt-4">Điểm</h5>
                <p class="mt-2 mb-2">Điểm tối đa: {{ $submission->assign->max_grade }}</p>
                <p class="mt-2 mb-2">Điểm đạt: {{ $submission->assign->grade_pass }}</p>
                <div class="d-flex mt-3">
                    <label for="grade" class="bold mr-3">Điểm bài nộp</label>
                    <input name="grade" id="grade" class="form-control" style="max-width: 200px;"
                        autocomplete="off" {{ $submission->grade > -1 ? 'value=' . $submission->grade : '' }}>
                </div>
            </div>
        </div>

        <footer class="footer-grading">
            <button class="btn btn-blue mr-3" id="btn-submit">
                Lưu điểm
            </button>
            <button class="btn btn-gray" id="btn-back">Quay lại</button>
        </footer>
    @endif

    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/course/assign-grading.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
        integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.min.js"
        integrity="sha384-7VPbUDkoPSGFnVtYi0QogXtr74QeVeeIs99Qfg5YCF+TidwNdjvaKZX19NZ/e6oz" crossorigin="anonymous">
    </script>
</body>

</html>
