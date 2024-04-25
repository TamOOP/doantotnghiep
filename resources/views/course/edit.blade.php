@extends('course.course-layout')

@section('title', 'Sửa khóa học')

@push('style')
    <link rel="stylesheet" href="{{ asset('css/course/edit.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout/collapse.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout/file-upload.css') }}">
@endpush

@include('layout.error')

@if (isset($course))
    @section('course-name')
        {{ $course->name }}
    @endsection

    @section('content-inner')
        @include('layout.validate-error')

        @if (request('type') == 'course')
            <div class="collapse-container mt-5">
                <form method="POST" id="form-data" enctype="multipart/form-data">
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
                                        Tên khóa học
                                        <i class="fa fa-info-circle require-icon ml-3" aria-hidden="true"></i>
                                    </p>
                                    <div class="course-input w-100" style="max-width: none">
                                        <input class="course-input form-control" type="text" name="name"
                                            id="name" value="{{ $course->name }}">
                                    </div>

                                </div>
                                <div class="group-input">
                                    <p class="input-label" class="p-2">Tổng quan khóa học</p>
                                    <textarea class="form-control course-input" id="description" cols="80" rows="10" name="description"
                                        >{{ $course->description }}</textarea>
                                </div>
                                <div class="group-input">
                                    <p class="input-label" class="p-2">Ảnh khóa học</p>

                                    @include('layout.file-upload', [
                                        'imageOnly' => true,
                                        'fileName' => 'image',
                                        'filePath' => $course->image_path,
                                    ])
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
                                    'title' => 'Ngày bắt đầu khóa học',
                                    'time' => $course->course_start,
                                    'cbName' => 'cb-start',
                                    'dateName' => 'date-start',
                                    'hourName' => 'start-hour',
                                    'minuteName' => 'start-minute',
                                ])

                                @include('layout.datetime-select', [
                                    'title' => 'Ngày kết thúc khóa học',
                                    'time' => $course->course_end,
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
                                    Tuyển sinh
                                </h4>
                            </div>
                            <div class="collapse-content">
                                <div class="group-input mt-4">
                                    <p class="input-label">Phương thức tham gia</p>
                                    <div class="course-input position-relative">
                                        <select class="course-input form-select" name="enrolment-method"
                                            id="enrolment-method">
                                            <option value="0" {{ $course->enrolment_method == '0' ? 'selected' : '' }}>Đăng ký thủ công</option>
                                            <option value="1" {{ $course->enrolment_method == '1' ? 'selected' : '' }}>Tự tham gia</option>
                                            <option value="2" {{ $course->enrolment_method == '2' ? 'selected' : '' }}>Thanh toán để tham gia</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="group-input mt-4" id="fee-container" style="{{ $course->enrolment_method == '2' ? '' : 'display: none' }}">
                                    <p class="input-label">Giá khóa học</p>
                                    <div class="course-input w-100" style="max-width: none">
                                        <input class="course-input form-control" type="text" name="course-fee"
                                            id="course-fee" style="max-width: 30% !important" value="{{ number_format($course->payment_cost) }}">
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                    <div class="action-block mt-3 justify-content-center d-flex">
                        <button type="submit" class="btn btn-primary mr-3" id="btn-submit">
                            Lưu thay đổi
                        </button>
                        <button class="btn btn-primary" id="btn-cancel">
                            Hủy bỏ
                        </button>
                    </div>
                </form>
            </div>
        @else
            <h3 class="mt-4">Sửa chủ đề</h3>
            <div class="collapse-container mt-5">
                <form id="form-topic">
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
                                            value="{{ $topic->name }}" id="name" required>
                                    </div>
                                </div>
                                <div class="group-input">
                                    <p class="input-label" class="p-2">Mô tả</p>
                                    <textarea class="form-control course-input" name="description" id="description" cols="80" rows="10">{{ $topic->description }}</textarea>
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
        @endif
    @endsection
@endif
@push('script')
    @if (request('type') == 'course')
        <script>
            const id = {{ request()->query('id') }}
        </script>
        <script src="{{ asset('js/course/course-edit.js') }}"></script>
    @else
        <script src="{{ asset('js/course/edit-topic.js') }}"></script>
    @endif
    <script src="{{ asset('js/course/edit.js') }}"></script>
    <script src="{{ asset('js/layout/datetime-select.js') }}"></script>
    <script src="{{ asset('js/layout/file-upload.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
@endpush
