@extends('layout.app')

@section('title', 'Khóa học của tôi')

@push('style')
    <link rel="stylesheet" href="{{ asset('css/course/overview.css') }}">
@endpush

@section('content')
    <div class="main-inner">
        <div class="d-flex">
            <h4>Danh sách khóa học</h4>
            @if (auth()->user()->role == 'teacher')
                <a href="/course/add?type=course" class="most-right">
                    <button class="btn btn-blue">Thêm khóa học</button>
                </a>
            @endif
        </div>
        @if (count($courses) == 0)
            <div class="alert alert-warning ml-3 mr-3 mt-3">
                Chưa có khóa học
            </div>
        @endif
        @if ($courses)
            <div class="course-container mt-4 pt-4">
                @foreach ($courses as $course)
                    <div class="course-item">
                        <a class="card-head" href="/course/view?id={{ $course->id }}"
                            style="background-image: url({{ $course->image_path ? $course->image_path : 'image/default.svg' }})">
                        </a>
                        <div class="card-body p-2">
                            <a class="course-name" href="/course/view?id={{ $course->id }}">
                                {{ $course->name }}
                            </a>
                            <div class="option-item d-flex align-items-center mt-2">
                                <p class="course-des">Giáo viên: </p>
                                <a class="ml-2 teacher-link" href="">{{ $course->teacher->name }}</a>
                            </div>
                        </div>
                        @if (auth()->user()->role == 'student')
                            <div class="card-foot p-2">
                                <p class="course-progress">Hoàn thành {{ $course->process }}%</p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

    </div>
@endsection

@push('script')
    <script src="{{ asset('js/course/overview.js') }}"></script>
@endpush
