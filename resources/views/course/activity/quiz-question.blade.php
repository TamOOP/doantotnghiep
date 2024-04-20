@extends('layout.activity')

@push('style')
    <link rel="stylesheet" href="{{ asset('css/course/quiz.css') }}">
    <link rel="stylesheet" href="{{ asset('css/course/member.css') }}">
    <link rel="stylesheet" href="{{ asset('css/course/quiz-attempt.css') }}">
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
    @if (count($exam->attempts) > 0)
        <div class="alert alert-warning mt-3">
            Không thể thêm hoặc xóa câu hỏi do đã có lượt làm bài.
            Lượt làm bài: {{ count($exam->attempts) }}
        </div>
    @endif

    <h4 class="mt-3">Danh sách câu hỏi</h4>

    <div class="table-container mt-4">
        <div class="d-flex">
            <a href="/course/quiz/question/add?id={{ $exam->id }}">
                <i class="fa fa-plus" aria-hidden="true"></i>
                <span>Thêm câu hỏi</span>
            </a>
        </div>
        <table class="table  table-question mt-3">
            <thead>
                <tr>
                    <th style="width: 2%"></th>
                    <th style="width: 60%">Đề bài</th>
                    <th style="width: 20%">Loại câu hỏi</th>
                    <th style="width: 5%">Điểm</th>
                    <th style="width: 12%">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @for ($i = 0; $i < count($exam->questions); $i++)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>
                            {{ $exam->questions[$i]->content }}
                        </td>
                        <td>
                            {{ $exam->questions[$i]->multi_answer ? 'Nhiều đáp án' : 'Một đáp án' }}
                        </td>
                        <td>
                            {{ $exam->questions[$i]->mark }}
                        </td>
                        <td>
                            <div>
                                <i class="fa fa-info-circle info-icon mr-2" aria-hidden="true"
                                    onclick="reviewQuestion(this, {{ $exam->questions[$i]->id }})"></i>
                                <a href="/course/activity/edit?type=question&id={{ $exam->questions[$i]->id }}">
                                    <i class="fa fa-cog setting-icon mr-2" aria-hidden="true"></i>
                                </a>
                                <i class="fa fa-trash delete-icon" aria-hidden="true"
                                    onclick="deleteQuestion({{ $exam->questions[$i]->id }})"></i>
                            </div>
                        </td>
                    </tr>
                @endfor
            </tbody>
        </table>
        <div class="d-flex question-summary">
            <p>Tổng câu hỏi: {{ count($exam->questions) }}</p>
            <p class="most-right">Tổng điểm: {{ $exam->total_mark }}</p>
        </div>
    </div>
@endsection

@section('modal')
    <div class="modal-enrol-container mt-4">
        <div class="d-flex align-items-center p-4">
            <h5 class="font-weight-bold">Xem câu hỏi</h5>
            <i id="close-modal" class="fa fa-times mr-0 ml-auto" aria-hidden="true"></i>
        </div>
        <div class="modal-body p-4">
            <div class="question-container mt-4 p-4">
                <div class="info">
                    <h3 class="bold">
                        <span>Câu hỏi</span>
                        <span style="font-size: 1.2rem">1</span>
                    </h3>
                    <p>Chưa trả lời</p>
                    <p>Điểm / 10</p>
                    <div class="mt-2">
                        <a href="/course/activity/edit?type=question">
                            <i class="fa fa-cog setting-icon mr-2" aria-hidden="true"></i>
                            <span>Sửa câu hỏi</span>
                        </a>
                    </div>
                </div>
                <div class="content">
                    <p class="topic">aaaa</p>
                    <div class="answer-container clearfix">
                        <div class="answer-item">
                            <input type="checkbox" class="choice">
                            <span>A.</span>
                            <span>abc</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer p-4">
            <button id="btn-modal-close" class="btn btn-secondary btn-gray mr-2">Đóng</button>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('js/course/quiz-question.js') }}"></script>
@endpush
