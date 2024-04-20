@extends('layout.app')

@push('style')
    <link rel="stylesheet" href="{{ asset('css/layout/course-nav.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout/collapse.css') }}">
    @if (request('type') == 'course' && request()->path() == 'course/add' && auth()->user()->role == 'admin')
        <link rel="stylesheet" href="{{ asset('css/admin/sidebar.css') }}">
    @endif
@endpush

@section('sidebar')
    @if (request('type') !== 'course' || request()->path() !== 'course/add')
        @include('layout.sidebar')
    @elseif (request('type') == 'course' && request()->path() == 'course/add' && auth()->user()->role == 'admin')
        @include('layout.admin.sidebar')
    @endif
@endsection


@section('content')
    <div class="main-inner">
        @if (request('type') !== 'course' || request()->path() !== 'course/add')
            <h3 style="font-weight: 700" class="mt-3">
                @yield('course-name')
            </h3>

            @include('layout.course-nav')
        @endif
        <div style="border-top: 1px solid #dddfe0">
            @yield('content-inner')
        </div>
    </div>
@endsection


@push('script')
    <script src="{{ asset('js/layout/collapse.js') }}"></script>
    @if (request('type') == 'course' && request()->path() == 'course/add' && auth()->user()->role == 'admin')
        <script src="{{ asset('js/admin/sidebar.js') }}"></script>
    @else
        <script src="{{ asset('js/layout/sidebar.js') }}"></script>
    @endif
@endpush
