@extends('course.course-layout')

@section('title', 'Thêm hoạt động')

@push('style')
    <link rel="stylesheet" href="{{ asset('css/course/edit.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout/file-upload.css') }}">
    @if (request('type') == 'course')
        <link rel="stylesheet" href="{{ asset('css/course/member.css') }}">
    @endif
    <style>
        .search-result-user:hover span {
            color: white !important;
        }
    </style>
@endpush

@if (!is_null($course))
    @section('course-name', $course->name)
@endif

@section('content-inner')
    @if (request('type') == 'assign')
        <h3 class="mt-4">Thêm bài tập mới</h3>
        <div class="collapse-container mt-5">
            <form id="form-assign">
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
                                    Tên bài tập
                                    <i class="fa fa-info-circle require-icon ml-3" aria-hidden="true"></i>
                                </p>
                                <div class="course-input w-100" style="max-width: none">
                                    <input class="course-input form-control" type="text" name="name" id="name">
                                </div>
                            </div>
                            <div class="group-input">
                                <p class="input-label" class="p-2">Mô tả bài tập</p>
                                <textarea class="form-control course-input" name="description" id="description" cols="80" rows="10"></textarea>
                            </div>
                            <div class="group-input">
                                <p class="input-label" class="p-2">Tệp bổ sung</p>

                                @include('layout.file-upload', [
                                    'imageOnly' => false,
                                    'fileName' => 'file',
                                    'filePath' => null,
                                ])

                            </div>
                        </div>
                    </li>
                    <li class="collapse-item">
                        <div class="collapse d-flex">
                            <i class="collapse-icon fa fa-angle-down mr-3" aria-hidden="true"></i>
                            <h4 class="collapse-title" style="font-weight:700">
                                Thời gian hiệu lực
                            </h4>
                        </div>
                        <div class="collapse-content">
                            @include('layout.datetime-select', [
                                'title' => 'Cho phép nộp bài từ',
                                'time' => null,
                                'cbName' => 'cb-start',
                                'dateName' => 'date-start',
                                'hourName' => 'start-hour',
                                'minuteName' => 'start-minute',
                            ])

                            @include('layout.datetime-select', [
                                'title' => 'Thời hạn nộp bài',
                                'time' => null,
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
                                Điểm
                            </h4>
                        </div>
                        <div class="collapse-content">
                            <div class="group-input mt-4">
                                <p class="input-label">
                                    Điểm tối đa
                                    <i class="fa fa-info-circle require-icon ml-3" aria-hidden="true"></i>
                                </p>
                                <div class="course-input w-100" style="max-width: none">
                                    <input class="course-input form-control" type="number" name="max-grade" id="max-grade"
                                        value="100" style="max-width: 20%;" min="1">
                                </div>
                            </div>
                            <div class="group-input mt-4">
                                <p class="input-label">
                                    Điểm qua
                                    <i class="fa fa-info-circle require-icon ml-3" aria-hidden="true"></i>
                                </p>
                                <div class="course-input w-100" style="max-width: none">
                                    <input class="course-input form-control" type="number" name="grade-pass"
                                        id="grade-pass" style="max-width: 20%;" min="0" value="0">
                                </div>
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
    @elseif (request('type') == 'quiz')
        <h3 class="mt-4">Thêm bài trắc nghiệm</h3>
        <div class="collapse-container mt-5">
            <form id="form-quiz">
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
                                    <input class="course-input form-control" type="text" name="name" id="name"
                                        required>
                                </div>
                            </div>
                            <div class="group-input">
                                <p class="input-label" class="p-2">Mô tả</p>
                                <textarea class="form-control course-input" name="description" id="description" cols="80" rows="10"></textarea>
                            </div>
                        </div>
                        <div class="group-input">
                            <p class="input-label" class="p-2">Mật khẩu làm bài</p>

                            <input class="enable-checkbox" name="cb-password" type="checkbox"
                                {{ !is_null($exam->password) ? 'checked' : '' }}>
                            <p class="mr-3 p-2">Bật</p>
                            <input type="password" class="form-control course-input" name="password" id="password" disabled>
                            <i class="fa fa-eye-slash ml-3 password-icon" style="cursor: pointer"
                                aria-hidden="true"></i>
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
                                'title' => 'Bắt đầu được phép truy cập',
                                'time' => null,
                                'cbName' => 'cb-start',
                                'dateName' => 'date-start',
                                'hourName' => 'start-hour',
                                'minuteName' => 'start-minute',
                            ])

                            @include('layout.datetime-select', [
                                'title' => 'Đóng bài trắc nghiệm tại',
                                'time' => null,
                                'cbName' => 'cb-end',
                                'dateName' => 'date-end',
                                'hourName' => 'end-hour',
                                'minuteName' => 'end-minute',
                            ])

                            <div class="group-input mt-4">
                                <p class="input-label" class="p-2">Giới hạn thời gian</p>
                                <div class="datepicker-container">
                                    <input class="enable-checkbox" name="cb-limit" type="checkbox" checked>
                                    <p class="mr-3 p-2">Bật</p>
                                    <input type="number" name="time-limit" class="form-control w-50 mr-2"
                                        value="0" style="width: 80px !important" min="0">
                                    <select class="form-select " name="time-unit" id="time-unit"
                                        style="width: auto !important">
                                        <option value="60">phút</option>
                                        <option value="86400">ngày</option>
                                        <option value="3600">giờ</option>
                                        <option value="1">giây</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="collapse-item">
                        <div class="collapse d-flex">
                            <i class="collapse-icon fa fa-angle-down mr-3" aria-hidden="true"></i>
                            <h4 class="collapse-title" style="font-weight:700">
                                Điểm
                            </h4>
                        </div>
                        <div class="collapse-content">
                            <div class="group-input mt-4">
                                <p class="input-label">
                                    Thang điểm
                                    <i class="fa fa-info-circle require-icon ml-3" aria-hidden="true"></i>
                                </p>
                                <div class="course-input w-100" style="max-width: none">
                                    <input class="course-input form-control" type="number" name="grade-scale"
                                        id="grade-scale" style="max-width: 15%;" min="1" value="10">
                                </div>
                            </div>
                            <div class="group-input mt-4">
                                <p class="input-label">
                                    Điểm qua
                                    <i class="fa fa-info-circle require-icon ml-3" aria-hidden="true"></i>
                                </p>
                                <div class="course-input w-100" style="max-width: none">
                                    <input class="course-input form-control" type="number" name="grade-pass"
                                        id="grade-pass" style="max-width: 15%;" min="0" value="0">
                                </div>
                            </div>
                            <div class="group-input mt-4">
                                <p class="input-label">Số lần làm bài</p>
                                <select class="form-select course-input" name="attempt-allow"
                                    style="max-width: max-content">
                                    <option value="0">Không giới hạn</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                    <option value="9">9</option>
                                    <option value="10">10</option>
                                </select>
                            </div>
                            <div class="group-input mt-4">
                                <p class="input-label">Cách tính điểm</p>
                                <select class="form-select course-input" name="grading-method"
                                    style="max-width: max-content">
                                    <option value="0">Điểm cao nhất</option>
                                    <option value="1">Điểm trung bình</option>
                                    <option value="2">Lần làm đầu tiên</option>
                                    <option value="3">Lần làm cuối cùng</option>
                                </select>
                            </div>
                        </div>
                    </li>
                    <li class="collapse-item">
                        <div class="collapse d-flex">
                            <i class="collapse-icon fa fa-angle-down mr-3" aria-hidden="true"></i>
                            <h4 class="collapse-title" style="font-weight:700">
                                Tùy chọn
                            </h4>
                        </div>
                        <div class="collapse-content">
                            <div class="group-input mt-4">
                                <p class="input-label">Số câu hỏi trên 1 trang</p>
                                <select class="form-select course-input" name="question-per-page" id="questionPerPage">
                                    <option value="0">Tất cả trên 1 trang</option>
                                </select>
                            </div>
                            <div class="group-input mt-4">
                                <p class="input-label">Thay đổi vị trí câu hỏi</p>
                                <div class="datepicker-container">
                                    <select class="form-select course-input" name="shuffle-question">
                                        <option value="1">Có</option>
                                        <option value="0">Không
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="group-input mt-4">
                                <p class="input-label">Thay đổi vị trí đáp án trong câu hỏi</p>
                                <div class="datepicker-container">
                                    <select class="form-select course-input" name="random-answer">
                                        <option value="1">Có</option>
                                        <option value="0">Không</option>
                                    </select>
                                </div>
                            </div>
                            <div class="group-input mt-4">
                                <p class="input-label">Hiển thị đáp án sau khi nộp bài</p>
                                <div class="datepicker-container">
                                    <select class="form-select course-input" name="show-answer">
                                        <option value="1">Có</option>
                                        <option value="0">Không</option>
                                    </select>
                                </div>
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
    @elseif (request('type') == 'topic')
        <h3 class="mt-4">Thêm chủ đề mới</h3>
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
                                        id="name" required>
                                </div>
                            </div>
                            <div class="group-input">
                                <p class="input-label" class="p-2">Mô tả</p>
                                <textarea class="form-control course-input" name="description" id="description" cols="80" rows="10"></textarea>
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
    @elseif (request('type') == 'file')
        <h3 class="mt-4">Thêm tài liệu</h3>
        <div class="collapse-container mt-5">
            <form id="form-file">
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
                                <div class="d-flex" style="flex-basis: 25%">
                                    <p class="input-label" style="flex-basis: 80%">
                                        Tên
                                    </p>
                                    <i class="fa fa-info-circle require-icon ml-3" aria-hidden="true"
                                        style="flex-basis: 20%"></i>
                                </div>
                                <div class="course-input w-100" style="max-width: none">
                                    <input class="course-input form-control" type="text" name="name"
                                        id="name">
                                </div>
                            </div>
                            <div class="group-input">
                                <p class="input-label" class="p-2">Mô tả</p>
                                <textarea class="form-control course-input" name="description" id="description" cols="80" rows="10"></textarea>
                            </div>
                            <div class="group-input">
                                <div class="d-flex" style="flex-basis: 25%">
                                    <p class="input-label" style="flex-basis: 80%">
                                        Tệp tin
                                    </p>
                                    <i class="fa fa-info-circle require-icon ml-3" aria-hidden="true"
                                        style="flex-basis: 20%"></i>
                                </div>
                                @include('layout.file-upload', [
                                    'imageOnly' => false,
                                    'fileName' => 'file',
                                    'filePath' => null,
                                    'require' => true,
                                ])
                            </div>
                        </div>
                    </li>

                    @include('layout.error')
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
    @else
        <h3 class="mt-4">Thêm khóa học mới</h3>

        <div class="collapse-container mt-5">
            <form id="form-course" enctype="multipart/form-data">
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
                                <div class="d-flex" style="flex-basis: 25%">
                                    <p class="input-label" style="flex-basis: 80%">
                                        Tên khóa học
                                    </p>
                                    <i class="fa fa-info-circle require-icon ml-3" aria-hidden="true"
                                        style="flex-basis: 20%"></i>
                                </div>
                                <div class="course-input w-100" style="max-width: none">
                                    <input class="course-input form-control" type="text" name="name"
                                        id="name">
                                </div>
                            </div>
                            @if (auth()->user()->role == 'admin')
                                <div class="group-input mt-4">
                                    <div class="d-flex" style="flex-basis: 25%">
                                        <p class="input-label" style="flex-basis: 80%">
                                            Giáo viên
                                        </p>
                                        <i class="fa fa-info-circle require-icon ml-3" aria-hidden="true"
                                            style="flex-basis: 20%"></i>

                                    </div>
                                    <div class="course-input w-100" style="max-width: none;position: relative;">
                                        <input class="course-input form-control" type="text" id="teacher">
                                        <div class="overflow-y" id="search-result-box" style="width: auto;">
                                            <ul id="search-result-list">

                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="group-input">
                                <p class="input-label" class="p-2">Tổng quan khóa học</p>
                                <textarea class="form-control course-input" id="description" cols="80" rows="10" name="description"></textarea>
                            </div>
                            <div class="group-input">
                                <p class="input-label" class="p-2">Ảnh khóa học</p>

                                @include('layout.file-upload', [
                                    'imageOnly' => true,
                                    'fileName' => 'image',
                                    'filePath' => null,
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
                                'time' => null,
                                'cbName' => 'cb-start',
                                'dateName' => 'date-start',
                                'hourName' => 'start-hour',
                                'minuteName' => 'start-minute',
                            ])

                            @include('layout.datetime-select', [
                                'title' => 'Ngày kết thúc khóa học',
                                'time' => null,
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
                                        <option value="0">Đăng ký thủ công</option>
                                        <option value="1">Tự tham gia</option>
                                        <option value="2">Thanh toán để tham gia</option>
                                    </select>
                                </div>
                            </div>
                            <div class="group-input mt-4" id="fee-container" style="display: none">
                                <p class="input-label">Giá khóa học</p>
                                <div class="course-input w-100 d-flex align-items-center" style="max-width: none">
                                    <input class="course-input form-control" type="text" name="course-fee"
                                        id="course-fee" value="1" min="1" style="max-width: 30% !important">
                                    <p class="ml-3">VND</p>
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
    @endif
@endsection

@push('script')
    <script src="{{ asset('js/layout/datetime-select.js') }}"></script>
    <script src="{{ asset('js/layout/file-upload.js') }}"></script>
    <script src="{{ asset('js/course/edit.js') }}"></script>

    @if (request('type') == 'course')
        <script src="{{ asset('js/course/course-edit.js') }}"></script>
        <script src="{{ asset('js/course/course-add.js') }}"></script>
    @else
        <script src="{{ asset('js/course/add-activity.js') }}"></script>
    @endif

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
@endpush
