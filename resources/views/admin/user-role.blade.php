@extends('layout.app')

@push('style')
    <link rel="stylesheet" href="{{ asset('css/layout/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout/collapse.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/role.css') }}">
@endpush

@section('sidebar')
    @include('layout.admin.sidebar')
@endsection


@section('content')
    <div class="main-inner">
        <div class="course-content">
            <div class="mt-4 mb-4 d-flex align-items-center">
                <h4>Chỉ định vai trò người dùng</h4>
            </div>
            <div class="box-container">
                <div class="column-user-container">
                    <h5 class="role-title">Học sinh</h5>
                    <div class="list-container overflow-y mt-3" id="student-box">
                        @foreach ($students as $student)
                            <p class="user" data-id={{ $student->id }} onclick="userSelected(this)">
                                {{ $student->name }} ({{ $student->username }})
                            </p>
                        @endforeach
                    </div>
                    <div class="search-container">
                        <span>Tìm kiếm</span>
                        <input class="form-control search-input" type="text" name="student" id="student">
                    </div>
                </div>
                <div class="column-btn-container">
                    <button class="btn btn-gray btn-change" data-target="student" id="btn-left">
                        <span>Thay đổi</span>
                        <div class="triangle-left"></div>
                    </button>
                    <button class="btn btn-gray mt-3 btn-change" data-target="teacher" id="btn-right">
                        <span>Thay đổi</span>
                        <div class="triangle-right"></div>
                    </button>
                </div>
                <div class="column-user-container" id="teacher-box">
                    <h5 class="role-title">Giáo viên</h5>
                    <div class="list-container mt-3 overflow-y">
                        @foreach ($teachers as $teacher)
                            <p class="user" data-id={{ $teacher->id }} onclick="userSelected(this)">
                                {{ $teacher->name }} ({{ $teacher->username }})
                            </p>
                        @endforeach
                    </div>
                    <div class="search-container">
                        <p>Tìm kiếm</p>
                        <input class="form-control search-input" type="text" name="teacher" id="teacher">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('script')
    <script src="{{ asset('js/layout/collapse.js') }}"></script>
    <script src="{{ asset('js/admin/sidebar.js') }}"></script>
    <script src="{{ asset('js/admin/user.js') }}"></script>
    <script src="{{ asset('js/course/edit.js') }}"></script>
@endpush
