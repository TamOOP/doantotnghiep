@extends('layout.app')

@push('style')
    <link rel="stylesheet" href="{{ asset('css/layout/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout/collapse.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/course/member.css') }}">
@endpush

@section('sidebar')
    @include('layout.admin.sidebar')
@endsection


@section('content')
    <div class="main-inner">
        <div class="course-content">
            <div class="mt-4 mb-4 d-flex align-items-center">
                <h4>Danh sách người dùng</h4>
            </div>
            <form class=" mt-3 mb-4" id="form-filter">
                @csrf
                <div class="filter-box">
                    <div class="search-condition mr-3">
                        <select class=" form-select" name="search-condition" id="search-condition">
                            <option value="name">Tìm theo tên</option>
                            <option value="email">Tìm email</option>
                            <option value="role">Tìm theo quyền</option>
                        </select>
                    </div>
                    <div>
                        <select class="form-select" id="role-option" name="role-option" style="display: none">
                            <option value="student">Học sinh</option>
                            <option value="teacher">Giáo viên</option>
                        </select>
                    </div>

                    <div class="search-bar">
                        <input class=" form-control search-input" type="text" name="search-keyword" id="conversation"
                            placeholder="Từ khóa">
                        <i class="fa fa-search search-icon" aria-hidden="true"></i>
                    </div>
                </div>
                <button class="filter-button btn btn-primary mt-3" onclick="searchUser()">
                    Tìm kiếm
                </button>
            </form>

            <table id="user-table" class="table mt-4 table-striped">
                <thead>
                    <tr>
                        <th scope="col" style="width: 30%;">Họ và tên</th>
                        <th scope="col" style="width: 20%;">Email</th>
                        <th scope="col" style="width: 10%;">Quyền</th>
                        <th scope="col" style="width: 10%;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>
                                <div class="d-flex flex-row align-items-center">
                                    <div class="avata">
                                        <img src="{{ asset($user->avata) }}" width="{{ $user->width }}"
                                            height="{{ $user->height }}">
                                    </div>
                                    <p class="user-name" style="margin-left:0.5rem">
                                        {{ $user->name }}
                                    </p>
                                </div>
                            </td>
                            <td>
                                {{ $user->username }}
                            </td>
                            <td>
                                {{ $user->role == 'student' ? 'Học sinh' : 'Giáo viên' }}
                            </td>
                            <td>
                                <i class="fa fa-cog delete-icon" aria-hidden="true" style="margin-right: 5px" title="Sửa"></i>
                                <i class="fa {{ $user->status == '1' ? 'fa-eye' : 'fa-eye-slash' }} delete-icon" style="margin-right: 5px"
                                    title="{{ $user->status == '1' ? 'Đình chỉ người dùng' : 'Kích hoạt người dùng' }}" aria-hidden="true"
                                    onclick="{{ $user->status == '1' ? 'suspendUser(this, '.$user->id.')' : 'activeUser(this, '.$user->id.')' }}"></i>

                                <i class="fa fa-trash delete-icon" aria-hidden="true" title="xóa"
                                    onclick="deleteUser(this, {{ $user->id }})" style="margin-right: 5px"></i>
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
    <script src="{{ asset('js/admin/user.js') }}"></script>
@endpush
