@extends('layout.app')

@section('title', 'Thông tin cá nhân')

@push('style')
    <link rel="stylesheet" href="{{ asset('css/user/profile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/course/edit.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout/collapse.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout/file-upload.css') }}">
@endpush

@section('content')
    <div class="main-inner">
        <div class="d-flex">
            <div class="avata-container">
                <img src="{{ asset($user->avata) }}" class="avata"  width="{{ $width }}" height="{{ $height }}">
            </div>
            <h3 id="user-name">{{ $user->name }}</h3>
        </div>
        @if (request()->query('type') == 'profile')
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
                                    <p class="input-label">
                                        Tên
                                        <i class="fa fa-info-circle require-icon ml-3" aria-hidden="true"></i>
                                    </p>
                                    <div class="course-input w-100" style="max-width: none">
                                        <input class="course-input form-control" type="text" name="name"
                                            id="name" value="{{ $user->name }}">
                                    </div>
                                </div>
                                <div class="group-input">
                                    <p class="input-label" class="p-2">Số điện thoại</p>
                                    <input class="course-input form-control" type="text" name="phone" id="phone"
                                        value="{{ $user->phone }}">
                                </div>
                                <div class="group-input">
                                    <p class="input-label" class="p-2">Mô tả bản thân</p>
                                    <textarea class="form-control course-input" id="description" cols="80" rows="10" name="description">{{ $user->description }}</textarea>
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
                                    <p class="input-label" class="p-2">Thay đổi ảnh đại diện</p>

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
        @elseif (request()->query('type') == 'password')
            <div class="collapse-container mt-5">
                <form method="POST" id="form-password" enctype="multipart/form-data">
                    @csrf
                    <ul>
                        <li class="collapse-item">
                            <div class="collapse d-flex">
                                <i class="collapse-icon fa fa-angle-down mr-3" aria-hidden="true"></i>
                                <h4 class="collapse-title" style="font-weight:700">
                                    Mật khẩu
                                </h4>
                            </div>
                            <div class="collapse-content">
                                <div class="group-input mt-4">
                                    <div class="d-flex" style="flex-basis: 40%">
                                        <p class="input-label" style="flex-basis: 80%">
                                            Mật khẩu hiện tại
                                        </p>
                                        <i class="fa fa-info-circle require-icon ml-3" aria-hidden="true"
                                            style="flex-basis: 20%"></i>
                                    </div>

                                    <div class="course-input w-100" style="max-width: none">
                                        <input class="course-input form-control" type="password" name="oldPass"
                                            id="oldPass">
                                    </div>
                                </div>
                                <div class="group-input mt-4">
                                    <div class="d-flex" style="flex-basis: 40%">
                                        <p class="input-label" style="flex-basis: 80%">
                                            Mật khẩu mới
                                        </p>
                                        <i class="fa fa-info-circle require-icon ml-3" aria-hidden="true"
                                            style="flex-basis: 20%"></i>
                                    </div>
                                    <div class="course-input w-100" style="max-width: none">
                                        <input class="course-input form-control" type="password" name="newPass"
                                            id="newPass">
                                    </div>
                                </div>
                                <div class="group-input mt-4">
                                    <div class="d-flex" style="flex-basis: 40%">
                                        <p class="input-label" style="flex-basis: 80%">
                                            Nhập lại mật khẩu
                                        </p>
                                        <i class="fa fa-info-circle require-icon ml-3" aria-hidden="true"
                                        style="flex-basis: 20%"></i>
                                    </div>
                                    <div class="course-input w-100" style="max-width: none">
                                        <input class="course-input form-control" type="password" name="newPassConfirm"
                                            id="newPassConfirm">
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                    
                    <div class="alert alert-danger ml-3 mr-3 mt-3" id="password-error" style="display: none">
                        
                    </div>

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
    </div>
@endsection

@push('script')
    <script src="{{ asset('js/layout/file-upload.js') }}"></script>
    <script src="{{ asset('js/course/edit.js') }}"></script>
    <script src="{{ asset('js/user/edit.js') }}"></script>
    <script src="{{ asset('js/layout/collapse.js') }}"></script>
@endpush
