@extends('layout.app')

@section('title')
    Hoạt động
@endsection

@push('style')
    <link rel="stylesheet" href="{{ asset('css/layout/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout/activity.css') }}">
@endpush

@section('sidebar')
    @include('layout.sidebar');
@endsection

@section('content')
    <div class="main-inner">
        @include('layout.activity.breadcrumb')
        
        <div class="activity-title mt-3">
            <div class="icon-container mr-3 {{ strpos(request()->path(), 'file') !== false || request('type') == 'file' ? 'file' : 'assignment'}}">
                @yield('activity-icon')
            </div>
            <h4 class="activity-name">
                @yield('activity-name')
            </h4>
        </div>
        
        <nav class="course-nav mt-3">
            @yield('activity-nav')
        </nav>

        <div class="activity-container">
            @yield('activity-content')
        </div>
    </div>
@endsection


@push('script')
    <script src="{{ asset('js/layout/sidebar.js') }}"></script>
@endpush
