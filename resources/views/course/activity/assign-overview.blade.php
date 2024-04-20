@extends('layout.activity')

@push('style')
    <link rel="stylesheet" href="{{ asset('css/layout/file-upload.css') }}">
    <link rel="stylesheet" href="{{ asset('css/course/assignment.css') }}">
@endpush

@section('activity-icon')
    <img src="{{ asset('/image/activity/assignment.svg') }}" class="activity-icon">
@endsection

@section('activity-name')
    {{ $assign->activity->name }}
@endsection

@section('activity-nav')
    @include('layout.activity.assign-nav')
@endsection

@section('breadcrumb-item')
    <li>{{ $assign->activity->name }}</li>
@endsection

@section('activity-content')
    @if (!$assign->allowSubmit)
        <div class="alert alert-warning mt-3">
            Bài tập đang đóng, không thể nộp bài
        </div>
    @endif
    <div class="time-container">
        @if (!is_null($assign->time_start))
            <div class="time-row">
                <p class="time-title mr-2">Bắt đầu:</p>
                <p>{{ $assign->time_start }}</p>
            </div>
        @endif

        @if (!is_null($assign->time_end))
            <div class="time-row">
                <p class="time-title mr-2">Hạn chót:</p>
                <p> {{ $assign->time_end }}</p>
            </div>
        @endif

        <div class="description">
            {{ $assign->activity->description }}
        </div>

        @if (!is_null($assign->file_path))
            <div class="file-container">
                <div style="position: relative;top: -5px;">
                    <i class="fa fa-ellipsis-v v-branch branch" aria-hidden="true"></i>
                    <i class="fa fa-ellipsis-v v-branch branch" aria-hidden="true" style="top: 8px"></i>

                    <i class="fa fa-ellipsis-h h-branch branch" aria-hidden="true"></i>
                    <i class="fa fa-ellipsis-h h-branch branch" aria-hidden="true" style="left:8px"></i>
                </div>
                <a class="file-info" href=" {{ asset($assign->file_path) }}" download="{{ basename($assign->file_path) }}">
                    {{ basename($assign->file_path) }}
                </a>
            </div>
        @endif
    </div>
    @if (auth()->user()->role !== 'student')
        <div class="button-container mt-3">
            <a href="assign/submission?id={{ $assign->id }}" class="mr-3">
                <button class="btn btn-gray">Xem tất cả bài nộp</button>
            </a>
            <a href="assign/grading?id={{ $assign->id }}">
                <button class="btn btn-blue">Chấm điểm</button>
            </a>
        </div>

        <h3 class="mt-4 mb-4" style="font-weight: bold;">Tổng quan</h3>

        <table class="table mt-4 table-striped">
            <thead>
                <tr>
                    <th style="width: 33%;"></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="table-attribute">Tổng bài nộp</td>
                    <td>{{ count($assign->submissions) }}</td>
                </tr>
                <tr>
                    <td class="table-attribute">Chưa chấm</td>
                    <td>{{ count($assign->notGradeSubmissions) }}</td>
                </tr>
                <tr>
                    <td class="table-attribute">Thời gian còn lại</td>
                    <td id="time-remain-text"></td>
                </tr>
            </tbody>
        </table>
    @else
        <div class="button-container mt-3">
            @if ($assign->allowSubmit)
                @if (is_null($submission))
                    <button class="btn btn-blue" id="btn-submit">
                        Nộp bài
                    </button>
                @else
                    @if ($submission->grade == -1)
                        <button class="btn btn-blue" id="btn-submit">
                            Thay đổi bài nộp
                        </button>
                    @endif
                @endif
            @endif

        </div>
        <h3 class="mt-4 mb-4" style="font-weight: bold;">Thông tin bài nộp</h3>

        <table class="table mt-4 table-striped">
            <thead>
                <tr>
                    <th style="width: 33%;"></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @if (!is_null($submission))
                    <tr>
                        <td class="table-attribute">Trạng thái</td>
                        <td class="submitted">Đã nộp</td>
                    </tr>
                    <tr>
                        <td class="table-attribute">Điểm</td>
                        <td>
                            {{ $submission->grade > -1 ? $submission->grade : 'Chưa chấm' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="table-attribute">Thời gian còn lại</td>
                        <td class="submitted">
                            Đã nộp
                        </td>
                    </tr>
                    <tr>
                        <td class="table-attribute">File nộp</td>
                        <td>
                            <a href=" {{ asset($submission->file_path) }}"
                                download="{{ basename($submission->file_path) }}">
                                {{ basename($submission->file_path) }}
                            </a>
                        </td>
                    </tr>
                @else
                    <tr>
                        <td class="table-attribute">Trạng thái</td>
                        <td>Chưa nộp</td>
                    </tr>
                    <tr>
                        <td class="table-attribute">Điểm</td>
                        <td>Chưa chấm</td>
                    </tr>
                    <tr>
                        <td class="table-attribute">Thời gian còn lại</td>
                        <td id="time-remain-text"></td>
                    </tr>
                @endif

            </tbody>
        </table>
    @endif
@endsection

@section('modal')
    <div class="modal-enrol-container mt-4">
        <div class="d-flex align-items-center p-4">
            <h5 class="font-weight-bold">Nộp bài tập</h5>
            <i id="close-modal" class="fa fa-times mr-0 ml-auto" aria-hidden="true"></i>
        </div>
        <form class="modal-body p-4" id="form-submission" method="POST" enctype="multipart/form-data">
            @csrf
            @include('layout.file-upload', [
                'imageOnly' => false,
                'fileName' => 'file',
                'filePath' => null,
            ])
        </form>
        <div class="modal-footer p-4">
            <button id="btn-modal-close" class="btn btn-secondary btn-gray mr-2">Hủy bỏ</button>
            <button id="btn-confirm" class="btn btn-primary btn-blue">Nộp bài</button>
        </div>
    </div>
@endsection

@push('script')
    <script>
        @if (!is_null($assign->timeRemain))
            var timeRemain = {!! json_encode($assign->timeRemain) !!};
            var days = timeRemain.d;
            var hours = timeRemain.h;
            var minutes = timeRemain.i;
            var seconds = timeRemain.s;
        @endif
    </script>
    <script src="{{ asset('js/layout/file-upload.js') }}"></script>
    <script src="{{ asset('js/course/assignment.js') }}"></script>
@endpush
