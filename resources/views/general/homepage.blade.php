@extends('layout.app')

@section('title', 'Trang chủ')

@push('style')
    <link rel="stylesheet" href="{{ asset('css/general/homepage.css') }}">
@endpush

@include('layout.error')


@section('content')
    <div class="main-inner">
        <h2>
            Khóa học hiện có
        </h2>
        @if ($courses)
            @foreach ($courses as $course)
                <div class="course-item mt-3">
                    <div class="course-name">
                        <a class="course-link mr-3" href="/course/view?id={{ $course->id }}">
                            {{ $course->name }}
                        </a>
                        <i
                            class="fas 
                        {{ $course->enrolment_method == 0
                            ? 'fa-lock'
                            : ($course->enrolment_method == 1
                                ? 'fa-sign-in'
                                : 'fa-credit-card') }}">
                        </i>
                    </div>
                    <div class="course-des mt-3">
                        @if (!is_null($course->image_path))
                            <div>
                                <img class="course-image" src="{{ asset($course->image_path) }}">
                            </div>
                        @endif

                        <div class="course-summary ml-3">
                            <div class="no-overflow">
                                <p>{{ $course->description }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="course-teacher mt-3">
                        <h6>Giáo viên:</h6>
                        <a class="teacher-link" href="">{{ $course->teacher->name }}</a>
                    </div>
                </div>
            @endforeach
        @endif

    </div>
@endsection

@push('script')
    <script src="{{ asset('js/general/homepage.js') }}"></script>
@endpush
