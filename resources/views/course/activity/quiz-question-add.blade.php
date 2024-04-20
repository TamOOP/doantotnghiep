@extends('layout.activity')

@push('style')
    <link rel="stylesheet" href="{{ asset('css/course/quiz.css') }}">
    <link rel="stylesheet" href="{{ asset('css/course/edit.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout/collapse.css') }}">
@endpush

@section('activity-icon')
    <img src="{{ asset('/image/activity/quiz.svg') }}" class="activity-icon">
@endsection

@section('activity-name')
    {{ $exam->activity->name }}
@endsection

@section('activity-nav')
    @include('layout.activity.quiz-nav')
@endsection

@section('breadcrumb-item')
    <li>{{ $exam->activity->name }}</li>
@endsection

@section('activity-content')
    <h3 class="mt-4">Thêm câu hỏi trắc nghiệm</h3>
    <div class="collapse-container mt-5">
        <form id="form-question">
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
                                <textarea class="form-control course-input" name="question-description" id="description" cols="80" rows="10"></textarea>
                            </div>
                        </div>
                        <div class="group-input mt-4">
                            <p class="input-label align-items-center">
                                <span>Điểm</span>
                                <i class="fa fa-info-circle require-icon " aria-hidden="true"></i>
                            </p>
                            <div class="course-input w-100" style="max-width: none">
                                <input class="course-input form-control" name="question-mark" id="question-mark" value="1" min="0" required>
                            </div>
                        </div>
                        <div class="group-input mt-4">
                            <p class="input-label align-items-center">
                                Kiểu trả lời
                            </p>
                            <select class="form-select course-input" name="multi-answer" id="answer-type">
                                <option value="0">Một đáp án</option>
                                <option value="1">Nhiều đáp án</option>
                            </select>
                        </div>
                        <div class="group-input mt-4">
                            <p class="input-label align-items-center">
                                Đánh số lựa chọn
                            </p>
                            <select class="form-select course-input" name="choice-numbering">
                                <option value="abc">a., b., c., ...</option>
                                <option value="ABCD">A., B., C., ...</option>
                                <option value="iii">i., ii., iii., ...</option>
                                <option value="IIII">I., II., III., ...</option>
                                <option value="none">Không đánh số</option>
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
@endsection

@push('script')
    <script src="{{ asset('js/layout/collapse.js') }}"></script>
    <script src="{{ asset('js/course/quiz-question.js') }}"></script>
@endpush
