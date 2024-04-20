@extends('layout.app')

@section('title', 'Danh sách thành viên')

@push('style')
    <link rel="stylesheet" href="{{ asset('css/course/member.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout/course-nav.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout/collapse.css') }}">
@endpush

@section('sidebar')
    @include('layout.sidebar')
@endsection


@section('content')
    <div class="main-inner">
        <h3 style="font-weight: 700" class="mt-3 center">My first course</h3>

        <div class="center">
            @include('layout.course-nav')
        </div>

        <div class="course-content">
            <div class="mt-4 mb-4 d-flex align-items-center">
                <h4>Danh sách thành viên</h4>
                @if (auth()->user()->role !== 'student')
                    <button class="btn-blue btn btn-primary ml-3" id="btn-enrol">Thêm thành viên</button>
                @endif
            </div>
            @if (!$course->hasOpened)
                <div class="alert alert-warning ml-3 mr-3 mt-3">
                    Khóa học chưa mở không thể đăng ký người dùng
                </div>
            @endif
            <form class=" mt-3 mb-4" id="form-filter">
                @csrf
                <div class="filter-box">
                    <div class="search-condition mr-3">
                        <select class=" form-control " name="search-condition" id="search-condition">
                            <option value="name">Tìm theo tên</option>
                            <option value="email">Tìm email</option>
                            <option value="enrol-method">Phương thức tham gia</option>
                            <option value="last-active">Không hoạt động trong hơn</option>
                        </select>
                    </div>
                    <div>
                        <select class="form-select" id="search-option"></select>
                    </div>

                    <div class="search-bar">
                        <input class=" form-control search-input" type="text" name="search-keyword" id="conversation"
                            placeholder="Từ khóa">
                        <i class="fa fa-search search-icon" aria-hidden="true"></i>
                    </div>
                </div>
                <button class="filter-button btn btn-primary mt-3" onclick="searchStudent()">
                    Tìm kiếm
                </button>
            </form>

            <table id="student-table" class="table mt-4 table-striped">
                <thead>
                    <tr>
                        <th scope="col" style="width: 30%;">Họ và tên</th>
                        <th scope="col" style="width: 20%;">Email</th>
                        <th scope="col" style="width: 10%;">Quyền</th>
                        <th scope="col" style="width: 15%;">Truy cập gần nhất</th>
                        @if (auth()->user()->role !== 'student')
                            <th scope="col" style="width: 10%;">Thao tác</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($students as $student)
                        <tr>
                            <td>
                                <div class="d-flex flex-row align-items-center">
                                    <div class="avata">
                                        <img class="user-img" src="{{ asset($student->avata) }}" alt="">
                                    </div>
                                    <p class="user-name" style="margin-left:0.5rem">
                                        {{ $student->name }}
                                    </p>
                                </div>
                            </td>
                            <td>
                                {{ $student->username }}
                            </td>
                            <td>
                                {{ $student->role == 'student' ? 'Học sinh' : '' }}
                            </td>
                            <td>
                                {{ $student->pivot->last_access !== null ? $student->pivot->last_access : 'Chưa truy cập' }}
                            </td>
                            @if (auth()->user()->role !== 'student')
                                <td>
                                    <i class="fa fa-trash delete-icon" aria-hidden="true"
                                        onclick="remove_user_course(this, {{ $student->id }})"></i>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection

@section('modal')
    <div class="modal-enrol-container mt-4">
        <div class="d-flex align-items-center p-4">
            <h5 class="font-weight-bold">Thêm học sinh vào khóa học</h5>
            <i id="close-modal" class="fa fa-times mr-0 ml-auto" aria-hidden="true"></i>
        </div>
        <div class="modal-body p-4">
            <div class="user-selected-box overflow-y">
                <h6>Người dùng đã chọn</h6>
                <ul id="user-selected-list">
                </ul>
            </div>
            <div class="d-flex align-items-center mt-3">
                <form id="form-search-user">
                    @csrf
                    <div class="modal-search-bar">
                        <input id="search-user-input" class=" form-control search-input" type="text" name="search-user"
                            placeholder="Tìm người dùng" autocomplete="off">
                        <i class="fa fa-search search-icon" aria-hidden="true"></i>
                        <div class="overflow-y" id="search-result-box">
                            <ul id="search-result-list">

                            </ul>
                        </div>
                    </div>
                </form>
                <button id="btn-clear-user" class="btn btn-secondary">Xóa tất cả</button>
            </div>
        </div>
        <div class="modal-footer p-4">
            <button id="btn-modal-close" class="btn btn-secondary btn-gray mr-2">Hủy bỏ</button>
            <button id="btn-confirm-enrol" class="btn btn-primary btn-blue">Xác nhận thêm</button>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('js/course/member.js') }}"></script>
    <script src="{{ asset('js/layout/sidebar.js') }}"></script>
@endpush
