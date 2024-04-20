@extends('layout.activity')

@push('style')
    <link rel="stylesheet" href="{{ asset('css/course/assignment.css') }}">
    <link rel="stylesheet" href="{{ asset('css/course/member.css') }}">
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
    <h4 class="mt-3">Thông tin bài nộp</h4>

    <form action="/course/assign/submission/search?id=1" method="post" class=" mt-3 mb-4" id="form-filter">
        @csrf
        <div class="filter-box">
            <div class="search-condition mr-3">
                <select class=" form-control " name="search-condition" id="search-condition">
                    <option value="name">Tìm theo tên</option>
                    <option value="email">Tìm theo email</option>
                    <option value="grade-status">Trạng thái chấm điểm</option>
                </select>
            </div>

            <div>
                <select name="search-option" class="form-select" id="search-option">
                    <option value="not-grade">Chưa chấm</option>
                    <option value="grade">Đã chấm</option>
                </select>
            </div>

            <div class="search-bar">
                <input class=" form-control search-input" type="text" name="search-keyword" id="conversation"
                    placeholder="Từ khóa">
                <i class="fa fa-search search-icon" aria-hidden="true"></i>
            </div>
        </div>

        <button class="filter-button btn btn-primary mt-3">
            Tìm kiếm
        </button>
    </form>

    <div style="overflow: auto">
        <table class="table mt-4" id="submission-list">
            <thead>
                <tr>
                    <th scope="col" style="width: 15%;">Họ và tên</th>
                    <th scope="col" style="width: 10%;">Email</th>
                    <th scope="col" style="width: 10%;">Trạng thái</th>
                    <th scope="col" style="width: 5%;">Điểm</th>
                    <th scope="col" style="width: 20%;">Chấm lần cuối</th>
                    <th scope="col" style="max-width: 40%; width: fit-content">File nộp</th>
                    <th scope="col" style="width: 20%;">Nộp bài lần cuối</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($assign->submissions as $submission)
                    <tr>
                        <td>
                            <div class="d-flex flex-row align-items-center">
                                <div class="avata">
                                    <img class="user-img" src="{{ asset($submission->user->avata) }}" alt="">
                                </div>
                                <p class="user-name" style="margin-left:0.5rem">
                                    {{ $submission->user->name }}
                                </p>
                            </div>
                        </td>
                        <td>
                            {{ $submission->user->username }}
                        </td>
                        <td>
                            @if ($submission->grade == -1)
                                <p class="grade-status not-grade">Chưa chấm</p>
                            @else
                                <p class="grade-status graded">Đã chấm</p>
                            @endif
                        </td>
                        <td>
                            @if ($submission->grade > -1)
                                {{ $submission->grade }}/{{ $assign->max_grade }}
                            @endif
                        </td>
                        <td>
                            {{ $submission->last_grade }}
                        </td>
                        <td>
                            <a href=" {{ asset($submission->file_path) }}"
                                download="{{ basename($submission->file_path) }}">
                                <p>{{ basename($submission->file_path) }}</p>
                            </a>
                            <a href="grading?id={{ $submission->assign_id }}&userId={{ $submission->user_id }}">
                                <button class="btn btn-blue" style="padding: 3px 10px; margin-top: 5px">Chấm</button>
                            </a>
                        </td>
                        <td>
                            {{ $submission->last_modified }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

@push('script')
    <script src="{{ asset('js/course/submission-search.js') }}"></script>
@endpush
