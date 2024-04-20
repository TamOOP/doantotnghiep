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
    <link rel="stylesheet" href="{{ asset('css/course/quiz-attempt.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fontawesome/css/all.min.css') }}">

</head>

<body>
    <div class="main-content drawer-right-draw">
        <div class="main-inner">
            <table class="table table-review">
                <thead>
                    <tr>
                        <th>Câu hỏi</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($questions as $question)
                        <tr class="{{ is_null($question->answered) ? 'not-answered' : 'answered'}}">
                            <td>
                                <a href="/course/quiz/attempt?id={{ $attempt->exam_id }}#q-{{ $question->id }}">
                                    {{ $question->pivot->index }}
                                </a>
                            </td>
                            <td>
                                <span>{{ is_null($question->answered) ? 'Chưa trả lời' : 'Đã trả lời'}}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div align="center">
                <button class="btn btn-gray mt-5" id="btn-back-attempt">Quay lại bài làm</button>
            </div>
            <div class="mt-3" align="center" style="border-top: 1px solid rgb(223, 223, 223)">
                <button class="btn btn-blue mt-3" id="btn-submit">Hoàn thành nộp bài</button>
            </div>
        </div>
        <div id="btn-open-drawer">
            <i class="fa fa-angle-left" aria-hidden="true"></i>
        </div>
    </div>

    @include('layout.attempt-drawer')

    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/course/quiz-attempt.js') }}"></script>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
        integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.min.js"
        integrity="sha384-7VPbUDkoPSGFnVtYi0QogXtr74QeVeeIs99Qfg5YCF+TidwNdjvaKZX19NZ/e6oz" crossorigin="anonymous">
    </script>
</body>

</html>
