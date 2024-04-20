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
            <button class="btn btn-gray mb-5 btn-back">
                Quay lại
            </button>
            <form id="form-question">
                @csrf
                @foreach ($questions as $index => $question)
                    <div class="question-container mt-4" id="q-{{ $question->id }}">
                        <div class="info {{ !isset($question->answered) ? '' : 'answered' }}">
                            <h3 class="bold">
                                <span>Câu hỏi</span>
                                <span style="font-size: 1.2rem">{{ $index + 1 }}</span>
                            </h3>
                            <p>{{ isset($question->answered) ? 'Chưa trả lời' : 'Đã trả lời' }}</p>
                            <p>Điểm / {{ $question->mark }}</p>
                            @if (auth()->user()->role != 'student')
                                <div class="mt-2">
                                    <a href="/course/activity/edit?type=question">
                                        <i class="fa fa-cog setting-icon mr-2" aria-hidden="true"></i>
                                        <span>Sửa câu hỏi</span>
                                    </a>
                                </div>
                            @endif
                        </div>
                        <div class="content">
                            <p class="topic">{{ $question->content }}</p>
                            <div class="answer-container clearfix">
                                @foreach ($question->choices as $choice)
                                    <div class="answer-item">
                                        @if (!$question->multi_answer)
                                            <input type="radio" class="choice" name="question[{{ $question->id }}]"
                                                value="{{ $choice->id }}"
                                                {{ !is_null($choice->pivot) ? ($choice->pivot->selected ? 'checked' : '') : '' }}>
                                        @else
                                            <input type="checkbox" class="choice"
                                                name="multiAnswer[{{ $question->id }}][{{ $choice->id }}]"
                                                {{ !is_null($choice->pivot) ? ($choice->pivot->selected ? 'checked' : '') : '' }}>
                                        @endif
                                        <span>{{ is_null($choice->number) ? '' : $choice->number . '. ' }}</span>
                                        <span>{{ $choice->content }}</span>
                                    </div>
                                @endforeach
                            </div>
                            <span class="clear-choice"
                                style="{{ !isset($question->answered) ? 'display:none' : '' }}">Xóa lựa chọn</span>
                        </div>
                    </div>
                @endforeach
            </form>
            <div align="right">
                @if (auth()->user()->role !== 'student')
                    <button class="btn btn-blue mt-5 btn-back">Hoàn thành xem trước</button>
                @else
                    <button class="btn btn-blue mt-5" id="btn-review">Nộp bài</button>
                    <script>
                        var attemptId = "{{ $attempt->id }}"
                    </script>
                @endif
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
