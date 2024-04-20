@extends('layout.activity')

@push('style')
    <link rel="stylesheet" href="{{ asset('css/course/quiz.css') }}">
@endpush

@section('activity-icon')
    <img src="{{ asset('/image/activity/file.svg') }}" class="activity-icon">
@endsection

@section('activity-name')
    {{ $file->activity->name }}
@endsection

@section('activity-nav')
    @include('layout.activity.file-nav')
@endsection

@section('breadcrumb-item')
    <li>{{ $file->activity->name }}</li>
@endsection

@section('activity-content')
    @if ($file->activity->description !== null)
        <div class="alert alert-secondary mt-3">
            {{ $file->activity->description }}
        </div>
    @endif

    @if ($file->type == 'video')
        <div class="flex-middle mt-3" style="flex-direction:column">
            <video width="500" height="300" autoplay muted controls>
                <source src="{{ asset($file->file_path) }}" type="video/mp4">
                Your browser does not support the video tag.
            </video>
            <p>{{ basename($file->file_path) }}</p>
        </div>
    @elseif ($file->type == 'audio')
        <div class="flex-middle mt-3" style="flex-direction:column">
            <audio controls autoplay>
                <source src="{{ asset($file->file_path) }}" type="audio/mpeg">
                Your browser does not support the audio element.
            </audio>
            <p>{{ basename($file->file_path) }}</p>
        </div>
    @elseif ($file->type == 'document')
        <div class="mt-3">
            <span>Nhấp vào</span>
            <a href=" {{ asset($file->file_path) }}" download="{{ basename($file->file_path) }}">
                {{ basename($file->file_path) }}
            </a>
            <span>để tải file</span>
        </div>
    @else
        <img src="{{ asset($file->file_path) }}" alt="" class="w-100 mt-3">
    @endif
@endsection

@push('script')
@endpush
