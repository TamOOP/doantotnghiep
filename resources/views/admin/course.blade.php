@extends('layout.app')

@push('style')
    <link rel="stylesheet" href="{{ asset('css/layout/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout/collapse.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/course/member.css') }}">
    <style>
        .search-result-user:hover span{
            color: white !important; 
        }
    </style>
@endpush

@section('sidebar')
    @include('layout.admin.sidebar')
@endsection


@section('content')
    <div class="main-inner">
        <div class="course-content">
            <div class="mt-4 mb-4 d-flex align-items-center">
                <h4>Danh sách khóa học</h4>
            </div>
            <form class=" mt-3 mb-4" id="form-filter">
                @csrf
                <div class="filter-box">
                    <div class="search-condition mr-3">
                        <select class=" form-select " name="search-condition" id="search-condition">
                            <option value="name">Tên</option>
                            <option value="teacher">Giáo viên</option>
                            <option value="method">Phương thức tham gia</option>
                        </select>
                    </div>
                    <div>
                        <select class="form-select" id="method-option" name="method-option" style="display: none">
                            <option value="0">Đăng ký thủ công</option>
                            <option value="1">Tự tham gia</option>
                            <option value="2">Thanh toán để tham gia</option>
                        </select>
                    </div>

                    <div class="search-bar">
                        <input class=" form-control search-input" type="text" name="search-keyword" id="conversation"
                            placeholder="Từ khóa" autocomplete="off">
                        <i class="fa fa-search search-icon" aria-hidden="true"></i>
                        <div class="overflow-y" id="search-result-box">
                            <ul id="search-result-list">

                            </ul>
                        </div>
                    </div>
                </div>
                <button class="filter-button btn btn-primary mt-3" onclick="searchCourse()">
                    Tìm kiếm
                </button>
            </form>

            <table id="user-table" class="table mt-4 table-striped">
                <thead>
                    <tr>
                        <th scope="col" style="width: 30%;">Tên khóa học</th>
                        <th scope="col" style="width: 15%;">Giáo viên</th>
                        <th scope="col" style="width: 10%;">Ngày bắt đầu</th>
                        <th scope="col" style="width: 10%;">Ngày kết thúc</th>
                        <th scope="col" style="width: 10%;">Phương thức tham gia</th>
                        <th scope="col" style="width: 10%;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($courses as $course)
                        <tr>
                            <td>
                                <div class="d-flex flex-row align-items-center">
                                    <p class="user-name" style="margin-left:0.5rem">
                                        {{ $course->name }}
                                    </p>
                                </div>
                            </td>
                            <td>
                                {{ $course->teacher->name }}
                            </td>
                            <td>
                                {{ $course->course_start ? $course->course_start : 'Không' }}
                            </td>
                            <td>
                                {{ $course->course_end ? $course->course_end : 'Không' }}
                            </td>
                            <td>
                                {{ $course->enrolment_method }}
                            </td>
                            <td>
                                <a href="/course/edit?type=course&id={{ $course->id }}">
                                    <i class="fa fa-cog delete-icon" aria-hidden="true" style="margin-right: 5px"
                                        title="Sửa"></i>
                                </a>
                                <i class="fa {{ $course->status == '1' ? 'fa-eye' : 'fa-eye-slash' }} delete-icon"
                                    title="{{ $course->status == '1' ? 'Đình chỉ khóa học' : 'Kích hoạt khóa học' }}"
                                    aria-hidden="true" style="margin-right: 5px"
                                    onclick="{{ $course->status == '1' ? 'suspendCourse(this, ' . $course->id . ')' : 'activeCourse(this, ' . $course->id . ')' }}"></i>

                                <i class="fa fa-trash delete-icon" aria-hidden="true" title="xóa"
                                    onclick="deleteCourse(this, {{ $course->id }})" style="margin-right: 5px"></i>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection


@push('script')
    <script src="{{ asset('js/layout/collapse.js') }}"></script>
    <script src="{{ asset('js/admin/sidebar.js') }}"></script>
    <script src="{{ asset('js/admin/course.js') }}"></script>
@endpush
