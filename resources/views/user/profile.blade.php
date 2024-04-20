@extends('layout.app')

@section('title', 'Thông tin cá nhân')

@push('style')
    <link rel="stylesheet" href="{{ asset('css/user/profile.css') }}">
@endpush

@section('content')
    <div class="main-inner">
        <div class="d-flex">
            <div class="avata-container">
                <img src="{{ asset($user->avata) }}" class="avata" width="{{ $width }}" height="{{ $height }}">
            </div>
            <h3 id="user-name">{{ $user->name }}</h3>
        </div>

        <p class="mt-3">{{ $user->description }}</p>

        <div class="profile-container">
            <div class="d-flex">
                <span class="lead">Thông tin chi tiết</span>
                <a href="edit?type=profile" class="most-right">Sửa thông tin</a>
            </div>
            <div class="attr-container">
                <p class="profile-attr">Tài khoản</p>
                <p class="profile-value">{{ $user->username }}</p>
            </div>
            <div class="attr-container">
                <p class="profile-attr">Số điện thoại</p>
                <p class="profile-value">{{ $user->phone }}</p>
            </div>
            <div class="attr-container">
                <p class="profile-attr">Quyền</p>
                <p class="profile-value">{{ $user->role }}</p>
            </div>
        </div>
    </div>
@endsection
