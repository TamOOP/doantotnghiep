@extends('layout.app')

@push('style')
    <link rel="stylesheet" href="{{ asset('css/layout/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout/collapse.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout/file-upload.css') }}">
    <link rel="stylesheet" href="{{ asset('css/course/edit.css') }}">
@endpush

@section('sidebar')
    @include('layout.admin.sidebar')
@endsection


@section('content')
    <div class="main-inner">
        <h4>Thêm người dùng mới</h4>
        <div class="collapse-container mt-5">
            <form method="POST" id="form-user" enctype="multipart/form-data">
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
                                        Tài khoản
                                    </p>
                                    <i class="fa fa-info-circle require-icon ml-3" aria-hidden="true"
                                        style="flex-basis: 20%"></i>
                                </div>
                                <div class="course-input w-100" style="max-width: none">
                                    <input class="course-input form-control" type="text" name="username" id="username" autocomplete="off">
                                </div>
                            </div>
                            <div class="group-input mt-4">
                                <div class="d-flex" style="flex-basis: 25%">
                                    <p class="input-label" style="flex-basis: 80%">
                                        Mật khẩu
                                    </p>
                                    <i class="fa fa-info-circle require-icon ml-3" aria-hidden="true"
                                        style="flex-basis: 20%"></i>
                                </div>
                                <div class="course-input w-100" style="max-width: none">
                                    <input class="course-input form-control" type="password" name="password" id="password" autocomplete="off">
                                </div>
                            </div>
                            <div class="group-input mt-4">
                                <div class="d-flex" style="flex-basis: 25%">
                                    <p class="input-label" style="flex-basis: 80%">
                                        Tên
                                    </p>
                                    <i class="fa fa-info-circle require-icon ml-3" aria-hidden="true"
                                        style="flex-basis: 20%"></i>
                                </div>
                                <div class="course-input w-100" style="max-width: none">
                                    <input class="course-input form-control" type="text" name="name" id="name">
                                </div>
                            </div>
                            <div class="group-input mt-4">
                                <div class="d-flex" style="flex-basis: 25%">
                                    <p class="input-label" style="flex-basis: 80%">
                                        Quyền
                                    </p>
                                </div>
                                <div class="course-input w-100" style="max-width: none">
                                    <select class="form-select" name="role" id="role"
                                        style="width:fit-content !important">
                                        <option value="student">Học sinh</option>
                                        <option value="teacher">Giáo viên</option>
                                    </select>
                                </div>
                            </div>
                            <div class="group-input">
                                <p class="input-label" class="p-2">Số điện thoại</p>
                                <input class="course-input form-control" type="text" name="phone" id="phone">
                            </div>
                            <div class="group-input">
                                <p class="input-label" class="p-2">Mô tả</p>
                                <textarea class="form-control course-input" id="description" cols="80" rows="10" name="description"></textarea>
                            </div>
                        </div>
                    </li>
                    <li class="collapse-item">
                        <div class="collapse d-flex">
                            <i class="collapse-icon fa fa-angle-down mr-3" aria-hidden="true"></i>
                            <h4 class="collapse-title" style="font-weight:700">
                                Ảnh đại diện
                            </h4>
                        </div>
                        <div class="collapse-content">
                            <div class="group-input">
                                <p class="input-label" class="p-2">Chọn ảnh đại diện</p>

                                @include('layout.file-upload', [
                                    'imageOnly' => true,
                                    'fileName' => 'image',
                                    'filePath' => null,
                                ])
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
    </div>
@endsection


@push('script')
    <script src="{{ asset('js/layout/collapse.js') }}"></script>
    <script src="{{ asset('js/admin/sidebar.js') }}"></script>
    <script src="{{ asset('js/layout/file-upload.js') }}"></script>
    <script src="{{ asset('js/admin/user.js') }}"></script>
    <script src="{{ asset('js/course/edit.js') }}"></script>

@endpush
