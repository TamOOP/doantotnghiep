@extends('layout.activity')

@push('style')
    <link rel="stylesheet" href="{{ asset('css/course/quiz.css') }}">
    <link rel="stylesheet" href="{{ asset('css/course/edit.css') }}">
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
                    @if (!is_null($exam->attemptNotFinish))
                        <a href="/course/quiz/attempt?id={{ $exam->id }}&attemptId={{ $exam->attemptNotFinish->id }}">
                            <button class="btn btn-blue mt-3" align="center">
                                Tiếp tục làm bài
                            </button>
                        </a>
                    @else
                        <button class="btn btn-blue mt-3" align="center"
                            {{ !is_null($exam->password) ? 'onclick=enterPassword(this)' : 'onclick=sendCreateAttemptRequest()' }}>
                            Làm bài
                        </button>
                    @endif
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

@section('modal')
    <div class="modal-enrol-container mt-4">
        <div class="d-flex align-items-center p-4">
            <h5 class="font-weight-bold">Nhập mật khẩu làm bài</h5>
            <i id="close-modal" class="fa fa-times mr-0 ml-auto" aria-hidden="true"></i>
        </div>
        <div class="modal-body p-4">
            <div class="group-input">
                <p class="input-label" class="p-2">Mật khẩu làm bài</p>
                <input type="password" class="form-control course-input" name="password" id="password" autocomplete="off">
                <i class="fa fa-eye-slash ml-3 password-icon" style="cursor: pointer" aria-hidden="true"></i>
            </div>
            <div class="alert alert-danger ml-3 mr-3 mt-3"
                style="display:none;margin-left: 0 !important; margin-right:0 !important; margin-bottom: 0 !important">

            </div>
        </div>
        <div class="modal-footer p-4">
            <button id="btn-modal-close" class="btn btn-secondary btn-gray mr-2">Hủy bỏ</button>
            <button id="btn-confirm-password" class="btn btn-primary btn-blue">Xác nhận</button>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('js/course/quiz-overview.js') }}"></script>
@endpush
