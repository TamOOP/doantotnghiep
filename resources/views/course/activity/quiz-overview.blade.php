@extends('layout.activity')

@push('style')
    <link rel="stylesheet" href="{{ asset('css/course/quiz.css') }}">
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
    <div class="time-container">
        <div class="time-row">
            <p class="time-title">Mở bài trắc nghiệm:</p>
            <p> {{ $exam->time_start }}</p>
        </div>
        <div class="time-row">
            <p class="time-title">Đóng bài trắc nghiệm:</p>
            <p> {{ $exam->time_end }}</p>
        </div>
        <div class="description">
            {{ $exam->activity->description }}
        </div>
    </div>
    @if (!is_null($exam->time_unit))
        <p class="mt-3">Thời gian làm bài: {{ $exam->time_limit }}</p>
    @endif
    <p class="mt-3">Số lần làm bài giới hạn: {{ $exam->attempt_allow == 0 ? 'Không giới hạn' : $exam->attempt_allow }}</p>
    <p class="mt-3">Cách tính điểm: {{ $exam->grading_method }}</p>

    @if (auth()->user()->role !== 'student')
        @if ($exam->questions->isEmpty())
            <a href="quiz/question/add?id={{ $exam->id }}">
                <button class="btn btn-blue mt-3">Thêm câu hỏi</button>
            </a>
            <div class="alert alert-warning mt-3">
                Chưa có câu hỏi
            </div>
        @else
            <div style="text-align: center">
                <a href="/course/quiz/attempt?id={{ $exam->id }}">
                    <button class="btn btn-blue mt-3" align="center">Xem trước</button>
                </a>
            </div>
        @endif
    @else
        @if (!$exam->allow)
            <div class="alert alert-warning mt-3">
                Bài kiểm tra đóng
            </div>
        @else
            @if ($exam->attempt_allow == 0 || count($attempts) < $exam->attempt_allow)
                <div style="text-align: center">
                    <a href="/course/quiz/attempt?id={{ $exam->id }}">
                        <button class="btn btn-blue mt-3" align="center">
                            {{ $exam->hasAttemptNotFinish ? 'Tiếp tục bài làm' : 'Làm bài' }}
                        </button>
                    </a>
                </div>
            @endif
        @endif

        @if (count($attempts) > 0)
            <h4 class="mt-3 mb-3">Các lần làm bài</h4>

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">Lần làm bài</th>
                        <th scope="col">Trạng thái</th>
                        <th scope="col">Điểm/10.00</th>
                        <th scope="col">Thời gian làm bài</th>
                        <th scope="col">Bắt đầu lúc</th>
                        <th scope="col">Kết thúc lúc</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($attempts as $i => $attempt)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $attempt->status }}</td>
                            <td>{{ $attempt->final_grade }}</td>
                            <td>{{ $attempt->work_time }}</td>
                            <td>
                                {{ $attempt->time_start }}
                            </td>
                            <td>
                                {{ $attempt->time_end }}
                            </td>
                            <td>
                                @if ($attempt->status == 'Đang tiến hành')
                                    <a href="quiz/attempt?id={{ $exam->id }}">
                                        Tiếp tục làm bài
                                    </a>
                                @else
                                    <a href="quiz/attempt/result?id={{ $exam->id }}&attemptId={{ $attempt->id }}">
                                        Xem lại
                                    </a>
                                @endif

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @endif
@endsection

@push('script')
@endpush