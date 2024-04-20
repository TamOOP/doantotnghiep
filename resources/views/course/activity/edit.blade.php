@extends('layout.activity')

@push('style')
    @if (request('type') == 'assign')
        <link rel="stylesheet" href="{{ asset('css/course/assignment.css') }}">
    @elseif (request('type') == 'quiz' || request('type') == 'question')
        <link rel="stylesheet" href="{{ asset('css/course/quiz.css') }}">
    @endif
    <link rel="stylesheet" href="{{ asset('css/course/edit.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout/collapse.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout/file-upload.css') }}">
@endpush

@section('activity-icon')
    @if (request('type') == 'assign')
        <img src="{{ asset('/image/activity/assignment.svg') }}" class="activity-icon">
    @elseif (request('type') == 'quiz' || request('type') == 'question')
        <img src="{{ asset('/image/activity/quiz.svg') }}" class="activity-icon">
    @else
        <img src="{{ asset('/image/activity/file.svg') }}" class="activity-icon">
    @endif
@endsection

@section('activity-name')
    @if (request('type') == 'assign')
        {{ $assign->activity->name }}
    @elseif (request('type') == 'quiz' || request('type') == 'question')
        {{ $exam->activity->name }}
    @else
        {{ $file->activity->name }}
    @endif
@endsection

@section('activity-nav')
    @if (request('type') == 'assign')
        @include('layout.activity.assign-nav')
    @elseif (request('type') == 'quiz' || request('type') == 'question')
        @include('layout.activity.quiz-nav')
    @else
        @include('layout.activity.file-nav')
    @endif
@endsection

@section('breadcrumb-item')
    <li>
        @if (request('type') == 'assign')
            {{ $assign->activity->name }}
        @elseif (request('type') == 'quiz' || request('type') == 'question')
            {{ $exam->activity->name }}
        @else
            {{ $file->activity->name }}
        @endif
    </li>
@endsection

@section('activity-content')
    @if (request('type') == 'assign')
        <h3 class="mt-4">Sửa bài tập</h3>
        <form class="collapse-container mt-5" method="post" enctype="multipart/form-data">
            @csrf
            <ul>
                <li class="collapse-item">
                    <div class="collapse d-flex">
                        <i class="collapse-icon fa fa-angle-down mr-3" aria-hidden="true"></i>
                        <h4 class="collapse-title" style="font-weight:700">
                            Thông tin chung
                        </h4>
                    </div>
                    <div class="collapse-content">
                        <div class="group-input mt-4">
                            <p class="input-label">
                                Tên bài tập
                                <i class="fa fa-info-circle require-icon ml-3" aria-hidden="true"></i>
                            </p>
                            <div class="course-input w-100" style="max-width: none">
                                <input class="course-input form-control" type="text" name="name" id="name"
                                    value="{{ $assign->activity->name }}">
                            </div>
                        </div>
                        <div class="group-input">
                            <p class="input-label" class="p-2">Mô tả bài tập</p>
                            <textarea class="form-control course-input" name="description" id="description" cols="80" rows="10">{{ $assign->activity->description }}</textarea>
                        </div>
                        <div class="group-input">
                            <p class="input-label" class="p-2">Tệp bổ sung</p>

                            @include('layout.file-upload', [
                                'imageOnly' => false,
                                'fileName' => 'file',
                                'filePath' => $assign->file_path,
                            ])

                        </div>
                    </div>
                </li>
                <li class="collapse-item">
                    <div class="collapse d-flex">
                        <i class="collapse-icon fa fa-angle-down mr-3" aria-hidden="true"></i>
                        <h4 class="collapse-title" style="font-weight:700">
                            Thời gian hiệu lực
                        </h4>
                    </div>
                    <div class="collapse-content">
                        @include('layout.datetime-select', [
                            'title' => 'Cho phép nộp bài từ',
                            'time' => $assign->time_start,
                            'cbName' => 'cb-start',
                            'dateName' => 'date-start',
                            'hourName' => 'start-hour',
                            'minuteName' => 'start-minute',
                        ])

                        @include('layout.datetime-select', [
                            'title' => 'Thời hạn nộp bài',
                            'time' => $assign->time_end,
                            'cbName' => 'cb-end',
                            'dateName' => 'date-end',
                            'hourName' => 'end-hour',
                            'minuteName' => 'end-minute',
                        ])
                    </div>
                </li>
                <li class="collapse-item">
                    <div class="collapse d-flex">
                        <i class="collapse-icon fa fa-angle-down mr-3" aria-hidden="true"></i>
                        <h4 class="collapse-title" style="font-weight:700">
                            Điểm
                        </h4>
                    </div>
                    <div class="collapse-content">
                        <div class="group-input mt-4">
                            <p class="input-label">
                                Điểm tối đa
                                <i class="fa fa-info-circle require-icon ml-3" aria-hidden="true"></i>
                            </p>
                            <div class="course-input w-100" style="max-width: none">
                                <input class="course-input form-control" type="number" name="max-grade" id="max-grade"
                                    value="{{ $assign->max_grade }}" style="max-width: 20%;" min="1">
                            </div>
                        </div>
                        <div class="group-input mt-4">
                            <p class="input-label">
                                Điểm qua
                                <i class="fa fa-info-circle require-icon ml-3" aria-hidden="true"></i>
                            </p>
                            <div class="course-input w-100" style="max-width: none">
                                <input class="course-input form-control" type="number" name="grade-pass" id="grade-pass"
                                    style="max-width: 20%;" value="{{ $assign->grade_pass }}" min="0">
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
            <div class="action-block mt-3 justify-content-center d-flex">
                <button id="btn-submit" type="submit" class="btn btn-primary mr-3">
                    Lưu thay đổi
                </button>
                <button class="btn btn-primary" id="btn-cancel">
                    Hủy bỏ
                </button>
            </div>
        </form>
    @elseif (request('type') == 'quiz')
        <h3 class="mt-4">Sửa bài trắc nghiệm</h3>
        <div class="collapse-container mt-5">
            <form id="form-exam">
                @csrf
                <ul>
                    <li class="collapse-item">
                        <div class="collapse d-flex">
                            <i class="collapse-icon fa fa-angle-down mr-3" aria-hidden="true"></i>
                            <h4 class="collapse-title" style="font-weight:700">
                                Thông tin chung
                            </h4>
                        </div>
                        <div class="collapse-content">
                            <div class="group-input mt-4">
                                <p class="input-label">
                                    Tên
                                    <i class="fa fa-info-circle require-icon ml-3" aria-hidden="true"></i>
                                </p>
                                <div class="course-input w-100" style="max-width: none">
                                    <input class="course-input form-control" type="text" name="name"
                                        value="{{ $exam->activity->name }}" id="name" required>
                                </div>
                            </div>
                            <div class="group-input">
                                <p class="input-label" class="p-2">Mô tả</p>
                                <textarea class="form-control course-input" name="description" id="description" cols="80" rows="10">{{ $exam->activity->description }}</textarea>
                            </div>
                        </div>
                    </li>
                    <li class="collapse-item">
                        <div class="collapse d-flex">
                            <i class="collapse-icon fa fa-angle-down mr-3" aria-hidden="true"></i>
                            <h4 class="collapse-title" style="font-weight:700">
                                Thời gian
                            </h4>
                        </div>
                        <div class="collapse-content">
                            @include('layout.datetime-select', [
                                'title' => 'Bắt đầu được phép truy cập',
                                'time' => $exam->time_start,
                                'cbName' => 'cb-start',
                                'dateName' => 'date-start',
                                'hourName' => 'start-hour',
                                'minuteName' => 'start-minute',
                            ])

                            @include('layout.datetime-select', [
                                'title' => 'Đóng bài trắc nghiệm tại',
                                'time' => $exam->time_end,
                                'cbName' => 'cb-end',
                                'dateName' => 'date-end',
                                'hourName' => 'end-hour',
                                'minuteName' => 'end-minute',
                            ])

                            <div class="group-input mt-4">
                                <p class="input-label" class="p-2">Giới hạn thời gian</p>
                                <div class="datepicker-container">
                                    <input class="enable-checkbox" name="cb-limit" type="checkbox"
                                        {{ !is_null($exam->time_unit) ? 'checked' : '' }}>
                                    <p class="mr-3 p-2">Bật</p>
                                    <input type="number" class="form-control w-50 mr-2" name="time-limit"
                                        style="width: 80px !important" min="0" value="{{ $exam->time_limit }}"
                                        {{ is_null($exam->time_unit) ? 'disabled' : '' }}>
                                    <select class="form-select " name="time-unit" id="time-unit"
                                        style="width: auto !important" {{ is_null($exam->time_unit) ? 'disabled' : '' }}>
                                        <option value="60" {{ $exam->time_unit == 60 ? 'selected' : '' }}>phút
                                        </option>
                                        <option value="86400" {{ $exam->time_unit == 86400 ? 'selected' : '' }}>ngày
                                        </option>
                                        <option value="3600" {{ $exam->time_unit == 3600 ? 'selected' : '' }}>giờ
                                        </option>
                                        <option value="1" {{ $exam->time_unit == 1 ? 'selected' : '' }}>giây</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="collapse-item">
                        <div class="collapse d-flex">
                            <i class="collapse-icon fa fa-angle-down mr-3" aria-hidden="true"></i>
                            <h4 class="collapse-title" style="font-weight:700">
                                Điểm
                            </h4>
                        </div>
                        <div class="collapse-content">
                            <div class="group-input mt-4">
                                <p class="input-label">
                                    Thang điểm
                                    <i class="fa fa-info-circle require-icon ml-3" aria-hidden="true"></i>
                                </p>
                                <div class="course-input w-100" style="max-width: none">
                                    <input class="course-input form-control" type="number" name="grade-scale"
                                        value="{{ $exam->grade_scale }}" min="1" id="grade-scale"
                                        style="max-width: 15%;">
                                </div>
                            </div>
                            <div class="group-input mt-4">
                                <p class="input-label">
                                    Điểm qua
                                    <i class="fa fa-info-circle require-icon ml-3" aria-hidden="true"></i>
                                </p>
                                <div class="course-input w-100" style="max-width: none">
                                    <input class="course-input form-control" type="number" name="grade-pass"
                                        value="{{ $exam->grade_pass }}" min="0" id="grade-pass"
                                        style="max-width: 15%;">
                                </div>
                            </div>
                            <div class="group-input mt-4">
                                <p class="input-label">Số lần làm bài</p>
                                <select class="form-select course-input" name="attempt-allow"
                                    style="max-width: max-content" id="attemptAllow"
                                    data-selected="{{ $exam->attempt_allow }}">
                                    <option value="0">Không giới hạn</option>
                                </select>
                            </div>
                            <div class="group-input mt-4">
                                <p class="input-label">Cách tính điểm</p>
                                <select class="form-select course-input" name="grading-method"
                                    style="max-width: max-content">
                                    <option value="0" {{ $exam->grading_method == '0' ? 'selected' : '' }}>Điểm cao
                                        nhất</option>
                                    <option value="1" {{ $exam->grading_method == '1' ? 'selected' : '' }}>Điểm trung
                                        bình</option>
                                    <option value="2" {{ $exam->grading_method == '2' ? 'selected' : '' }}>Lần làm
                                        đầu tiên</option>
                                    <option value="3" {{ $exam->grading_method == '3' ? 'selected' : '' }}>Lần làm
                                        cuối cùng</option>
                                </select>
                            </div>
                        </div>
                    </li>
                    <li class="collapse-item">
                        <div class="collapse d-flex">
                            <i class="collapse-icon fa fa-angle-down mr-3" aria-hidden="true"></i>
                            <h4 class="collapse-title" style="font-weight:700">
                                Tùy chọn
                            </h4>
                        </div>
                        <div class="collapse-content">
                            <div class="group-input mt-4">
                                <p class="input-label">Số câu hỏi trên 1 trang</p>
                                <select class="form-select course-input" name="question-per-page" id="questionPerPage"
                                    data-selected="{{ $exam->question_per_page }}">
                                    <option value="0">Tất cả trên 1 trang</option>
                                </select>
                            </div>
                            <div class="group-input mt-4">
                                <p class="input-label">Thay đổi vị trí câu hỏi</p>
                                <div class="datepicker-container">
                                    <select class="form-select course-input" name="shuffle-question">
                                        <option value="1" {{ $exam->shuffle_question ? 'selected' : '' }}>Có</option>
                                        <option value="0" {{ !$exam->shuffle_question ? 'selected' : '' }}>Không
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="group-input mt-4">
                                <p class="input-label">Thay đổi vị trí đáp án trong câu hỏi</p>
                                <div class="datepicker-container">
                                    <select class="form-select course-input" name="random-answer">
                                        <option value="1" {{ $exam->random_answer ? 'selected' : '' }}>Có</option>
                                        <option value="0" {{ !$exam->random_answer ? 'selected' : '' }}>Không
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="group-input mt-4">
                                <p class="input-label">Hiển thị đáp án sau khi nộp bài</p>
                                <div class="datepicker-container">
                                    <select class="form-select course-input" name="show-answer">
                                        <option value="1" {{ $exam->show_answer ? 'selected' : '' }}>Có</option>
                                        <option value="0" {{ !$exam->show_answer ? 'selected' : '' }}>Không</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
                <div class="action-block mt-3 justify-content-center d-flex">
                    <button type="submit" id="btn-submit" class="btn btn-primary mr-3">
                        Lưu thay đổi
                    </button>
                    <button class="btn btn-primary" id="btn-cancel">
                        Hủy bỏ
                    </button>
                </div>
            </form>
        </div>
    @elseif (request('type') == 'question')
        <h3 class="mt-4">Sửa câu hỏi trắc nghiệm</h3>
        <div class="collapse-container mt-5">
            <form id="form-edit-question">
                @csrf
                <ul>
                    <li class="collapse-item">
                        <div class="collapse d-flex">
                            <i class="collapse-icon fa fa-angle-down mr-3" aria-hidden="true"></i>
                            <h4 class="collapse-title" style="font-weight:700">
                                Thông tin chung
                            </h4>
                        </div>
                        <div class="collapse-content">
                            <div class="group-input">
                                <p class="input-label" class="p-2">
                                    <span>Đề bài</span>
                                    <i class="fa fa-info-circle require-icon " aria-hidden="true"></i>
                                </p>
                                <div class="course-input w-100" style="max-width: none">
                                    <textarea class="form-control course-input" name="question-description" id="description" cols="80"
                                        rows="10">{{ $question->content }}</textarea>
                                </div>
                            </div>
                            <div class="group-input mt-4">
                                <p class="input-label align-items-center">
                                    <span>Điểm</span>
                                    <i class="fa fa-info-circle require-icon " aria-hidden="true"></i>
                                </p>
                                <div class="course-input w-100" style="max-width: none">
                                    <input class="course-input form-control" name="question-mark" value="1"
                                        id="question-mark" value="{{ $question->mark }}" required>
                                </div>
                            </div>
                            <div class="group-input mt-4">
                                <p class="input-label align-items-center">
                                    Kiểu trả lời
                                </p>
                                <select class="form-select course-input" name="multi-answer" id="answer-type">
                                    <option value="0" {{ !$question->multi_answer ? 'selected' : '' }}>
                                        Một đáp án
                                    </option>
                                    <option value="1" {{ $question->multi_answer ? 'selected' : '' }}>
                                        Nhiều đáp án
                                    </option>
                                </select>
                            </div>
                            <div class="group-input mt-4">
                                <p class="input-label align-items-center">
                                    Đánh số lựa chọn
                                </p>
                                <select class="form-select course-input" name="choice-numbering">
                                    <option value="abc" {{ $question->choice_numbering == 'abc' ? 'selected' : '' }}>
                                        a., b., c., ...</option>
                                    <option value="ABCD" {{ $question->choice_numbering == 'ABCD' ? 'selected' : '' }}>
                                        A., B., C., ...</option>
                                    <option value="iii" {{ $question->choice_numbering == 'iii' ? 'selected' : '' }}>
                                        i., ii., iii., ...</option>
                                    <option value="IIII" {{ $question->choice_numbering == 'IIII' ? 'selected' : '' }}>
                                        I., II., III., ...</option>
                                    <option value="none" {{ $question->choice_numbering == 'none' ? 'selected' : '' }}>
                                        Không đánh số</option>
                                </select>
                            </div>
                        </div>
                    </li>
                    <li class="collapse-item">
                        <div class="collapse d-flex">
                            <i class="collapse-icon fa fa-angle-down mr-3" aria-hidden="true"></i>
                            <h4 class="collapse-title" style="font-weight:700">
                                Đáp án
                            </h4>
                        </div>
                        <div class="collapse-content">
                            <div id="answer-container">
                                @foreach ($question->choices as $i => $choice)
                                    <div class="answer-block mt-3" id="ans-{{ $choice->id }}">
                                        <div class="group-input">
                                            <p class="input-label" class="p-2">
                                                Lựa chọn {{ $i + 1 }}
                                            </p>
                                            <div class="course-input w-100" style="max-width: none">
                                                <textarea class="form-control course-input" name="choices[]" cols="80" rows="3">{{ $choice->content }}</textarea>
                                            </div>
                                        </div>
                                        <div class="group-input">
                                            <p class="input-label" class="p-2">
                                                Điểm
                                            </p>
                                            <div class="form-inline course-input">
                                                <div style="position: relative">
                                                    <select class="form-select answer-grade" name="choice-grades[]"
                                                        data-selected="{{ $choice->grade }}">
                                                        <option value="0">Không có điểm</option>
                                                    </select>
                                                    <i class="warning-icon fa fa-exclamation-circle"
                                                        style="display:none"></i>
                                                </div>
                                                <p class="warning-msg"></p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <button class="btn btn-gray mt-3 margin-middle" id="btn-add-answer">
                                Thêm 1 câu trả lời
                            </button>
                        </div>
                    </li>
                </ul>
                <div class="action-block mt-3 justify-content-center d-flex">
                    <button type="submit" class="btn btn-blue mr-3" id="btn-submit">
                        Lưu thay đổi
                    </button>
                    <button class="btn btn-primary" id="btn-cancel">
                        Hủy bỏ
                    </button>
                </div>
            </form>
        </div>
    @else
        <h3 class="mt-4">Sửa tài liệu</h3>
        <div class="collapse-container mt-5">
            <form id="form-file">
                @csrf
                <ul>
                    <li class="collapse-item">
                        <div class="collapse d-flex">
                            <i class="collapse-icon fa fa-angle-down mr-3" aria-hidden="true"></i>
                            <h4 class="collapse-title" style="font-weight:700">
                                Thông tin chung
                            </h4>
                        </div>
                        <div class="collapse-content">
                            <div class="group-input mt-4">
                                <div class="d-flex" style="flex-basis: 25%">
                                    <p class="input-label" style="flex-basis: 80%">
                                        Tên
                                    </p>
                                    <i class="fa fa-info-circle require-icon ml-3" aria-hidden="true"
                                        style="flex-basis: 20%"></i>
                                </div>
                                <div class="course-input w-100" style="max-width: none">
                                    <input class="course-input form-control" type="text" name="name"
                                        id="name" value="{{ $file->activity->name }}">
                                </div>
                            </div>
                            <div class="group-input">
                                <p class="input-label" class="p-2">Mô tả</p>
                                <textarea class="form-control course-input" name="description" id="description" cols="80" rows="10"
                                    >{{ $file->activity->description }}</textarea>
                            </div>
                            <div class="group-input">
                                <div class="d-flex" style="flex-basis: 25%">
                                    <p class="input-label" style="flex-basis: 80%">
                                        Tệp tin
                                    </p>
                                    <i class="fa fa-info-circle require-icon ml-3" aria-hidden="true"
                                        style="flex-basis: 20%"></i>
                                </div>
                                @include('layout.file-upload', [
                                    'imageOnly' => false,
                                    'fileName' => 'file',
                                    'filePath' => $file->file_path,
                                    'require' => true
                                ])
                            </div>
                        </div>
                    </li>

                    @include('layout.error')
                </ul>
                <div class="action-block mt-3 justify-content-center d-flex">
                    <button type="submit" id="btn-submit" class="btn btn-primary mr-3">
                        Lưu thay đổi
                    </button>
                    <button class="btn btn-primary" id="btn-cancel">
                        Hủy bỏ
                    </button>
                </div>
            </form>
        </div>
    @endif
@endsection

@push('script')
    <script src="{{ asset('js/course/edit-activity.js') }}"></script>
    <script src="{{ asset('js/course/edit.js') }}"></script>
    <script src="{{ asset('js/layout/collapse.js') }}"></script>
    <script src="{{ asset('js/layout/datetime-select.js') }}"></script>
    <script src="{{ asset('js/layout/file-upload.js') }}"></script>
    @if (request('type') == 'question')
        <script src="{{ asset('js/course/quiz-question.js') }}"></script>
    @endif
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
@endpush
