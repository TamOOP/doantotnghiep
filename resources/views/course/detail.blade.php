@extends('course.course-layout')

@section('title', 'Khóa học')

@push('style')
    <link rel="stylesheet" href="{{ asset('css/course/detail.css') }}">
@endpush

@include('layout.error')

@if (isset($course))
    @section('course-name')
        {{ $course->name }}
    @endsection

    @section('content-inner')
        <div class="course-content mt-3">
            @if (session('edit'))
                <div class="mt-4 mb-3">
                    <a href="/course/topic/add?id={{ $course->id }}&type=topic" class=" add-topic-link d-flex align-items-center">
                        <i class="fa fa-plus" aria-hidden="true" style="color: #0f6cbf; margin-right:5px"></i>
                        <p href="">Thêm chủ đề mới</p>
                    </a>
                </div>
            @endif
            @if (session('success'))
                <div class="alert alert-success ml-3 mr-3 mt-3">
                    {{ session('success') }}
                </div>
            @endif

            @if (count($course->topics) > 0)
                <ul class="topics">
                    @foreach ($course->topics as $topic)
                        <li class="topic-item" id="{{ 'topic' . $topic->id }}">
                            <div class="collapse d-flex">
                                <i class="collapse-icon fa fa-angle-down mr-3" aria-hidden="true"></i>
                                <h4 class="collapse-title" style="font-weight:700">
                                    {{ $topic->name }}
                                </h4>
                                @if (session('edit'))
                                    <div class="topic-option circle-border">
                                        <i class="option-icon fa fa-ellipsis-v" aria-hidden="true"></i>
                                        <div class="option-content" style="display: none">
                                            <a href="/course/topic/edit?id={{ $topic->id }}">
                                                <div
                                                    class="option-item d-flex align-items-center justify-content-center p-1">
                                                    <i class="fa fa-cog"></i>
                                                    <p>Sửa chủ đề</p>
                                                </div>
                                            </a>
                                            <div class="option-item d-flex align-items-center justify-content-center p-1"
                                                onclick="deleteTopic(this, {{ $topic->id }})">
                                                <i class="fa fa-trash"></i>
                                                <p>Xóa chủ đề</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="collapse-content">
                                <div class="topic-des mt-3 mb-3">
                                    {{ $topic->description }}
                                </div>
                                @foreach ($topic->activities as $activity)
                                    <div class="activity-container mt-3">
                                        <div>
                                            <div class="modal-icon {{ $activity->type == 'file' ? 'file' : 'assignment' }}">
                                                <img class="modal-svg"
                                                    src="{{ asset(
                                                        $activity->type == 'assign'
                                                            ? 'image/activity/assignment.svg'
                                                            : ($activity->type == 'exam'
                                                                ? 'image/activity/quiz.svg'
                                                                : 'image/activity/file.svg'),
                                                    ) }}">
                                            </div>
                                        </div>
                                        <a class="activity-link"
                                            href="{{ $activity->type == 'assign' ? '/course/assign' : ($activity->type == 'exam' ? '/course/quiz' : '/course/file') }}?id={{ $activity->derived->id }}">
                                            {{ $activity->name }}
                                        </a>
                                        @if (auth()->user()->role == 'student')
                                            @if ($activity->marked)
                                                <button class="btn-done btn-toggle-process" process-toggletype="undone"
                                                    onclick="toggleProcess(this,{{ $activity->id }})">
                                                    <span class="loading-spinner"></span>
                                                    <div class="btn-content btn-done" process-toggletype='undone'>
                                                        <i class="fa fa-check" aria-hidden="true"
                                                            style="margin-right:5px"></i>
                                                        <span>Hoàn thành</span>
                                                    </div>
                                                </button>
                                            @else
                                                <button class="btn-inprogress btn-toggle-process" process-toggletype="done"
                                                    onclick="toggleProcess(this,{{ $activity->id }})">
                                                    <span class="loading-spinner"></span>
                                                    <div class="btn-content">
                                                        <span>Đánh dấu hoàn thành</span>
                                                    </div>
                                                </button>
                                            @endif
                                        @endif

                                        @if (session('edit'))
                                            <div class="topic-option circle-border" style="cursor: pointer">
                                                <i class="option-icon fa fa-ellipsis-v" aria-hidden="true"></i>
                                                <div class="option-content" style="display: none; z-index: 10">
                                                    <a
                                                        href="/course/activity/edit?type={{ $activity->type == 'exam' ? 'quiz' : $activity->type }}&id={{ $activity->derived->id }}">
                                                        <div
                                                            class="option-item d-flex align-items-center justify-content-center p-1">
                                                            <i class="fa fa-cog"></i>
                                                            <p>Sửa hoạt động</p>
                                                        </div>
                                                    </a>
                                                    <div class="option-item d-flex align-items-center justify-content-center p-1"
                                                        onclick="deleteActivity(this,'{{ $activity->type }}',{{ $activity->derived->id }})">
                                                        <i class="fa fa-trash"></i>
                                                        <p>Xóa hoạt động</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach

                                @if (session('edit'))
                                    <div class="activity-container mt-3 add-activity-container"
                                        onclick="openModal({{ $topic->id }})">
                                        <div class="add-activity-icon circle-border">
                                            <i class="fa fa-plus"></i>
                                        </div>
                                        <a class="add-activity-link ml-3">Thêm hoạt động hoặc tài liệu mới</a>
                                    </div>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="alert alert-warning ml-3 mr-3 mt-3">
                    Khóa học chưa có chủ đề
                </div>
            @endif

        </div>
    @endsection

    @section('modal')
        <div class="activity-modal-container h-100">
            <div class="d-flex align-items-center p-4">
                <h5 class="font-weight-bold">Thêm hoạt động hoặc tài liệu</h5>
                <i id="close-activity" class="fa fa-times mr-0 ml-auto" aria-hidden="true"></i>
            </div>
            <div class="activity-modal-content p-4">
                <ul class="d-flex flex-wrap">
                    <a href="#all" class="modal-nav-tab active" data-toggle="tab">Tất cả</a>
                    <a href="#activity" class="modal-nav-tab" data-toggle="tab">Hoạt động</a>
                    <a href="#resource" class="modal-nav-tab" data-toggle="tab">Tài liệu</a>
                </ul>
                <div class="h-100">
                    <div class="modal-pane p-1 active" id="all">
                        <a class="modal-pane-item m-1" href="/course/add?type=assign&id=">
                            <div class="modal-icon assignment">
                                <img class="modal-svg" src="{{ asset('image/activity/assignment.svg') }}" alt="">
                            </div>
                            <p>Bài tập</p>
                        </a>
                        <a class="modal-pane-item m-1" href="/course/add?type=quiz&id=">
                            <div class="modal-icon quiz">
                                <img class="modal-svg" src="{{ asset('image/activity/quiz.svg') }}" alt="">
                            </div>
                            <p>Trắc nghiệm</p>
                        </a>
                        <a class="modal-pane-item m-1" href="/course/add?type=file&id=">
                            <div class="modal-icon file">
                                <img class="modal-svg" src="{{ asset('image/activity/file.svg') }}" alt="">
                            </div>
                            <p>Tập tin</p>
                        </a>
                    </div>
                    <div class="modal-pane p-1" id="activity">
                        <a class="modal-pane-item m-1" href="/course/add?type=assign&id=">
                            <div class="modal-icon assignment">
                                <img class="modal-svg" src="{{ asset('image/activity/assignment.svg') }}"
                                    alt="">
                            </div>
                            <p>Bài tập</p>
                        </a>
                        <a class="modal-pane-item m-1" href="/course/add?type=quiz&id=">
                            <div class="modal-icon quiz">
                                <img class="modal-svg" src="{{ asset('image/activity/quiz.svg') }}" alt="">
                            </div>
                            <p>Trắc nghiệm</p>
                        </a>
                    </div>
                    <div class="modal-pane p-1" id="resource">
                        <a class="modal-pane-item m-1" href="/course/add?type=file&id=">
                            <div class="modal-icon file">
                                <img class="modal-svg" src="{{ asset('image/activity/file.svg') }}" alt="">
                            </div>
                            <p>Tập tin</p>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    @endsection

@endif

@push('script')
    <script src="{{ asset('js/course/detail.js') }}"></script>
@endpush
