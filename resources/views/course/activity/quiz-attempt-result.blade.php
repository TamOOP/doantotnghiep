@extends('layout.activity')

@push('style')
    <link rel="stylesheet" href="{{ asset('css/course/quiz.css') }}">
    <link rel="stylesheet" href="{{ asset('css/course/quiz-attempt.css') }}">
@endpush

@section('activity-icon')
    <img src="{{ asset('/image/activity/quiz.svg') }}" class="activity-icon">
@endsection

@section('activity-name')
    {{ $exam->activity->name }}
@endsection

@section('activity-nav')
    @include('layout.activity.quiz-nav')
@endsection

@section('breadcrumb-item')
    <li>{{ $exam->activity->name }}</li>
@endsection

@section('activity-content')
    <table class="mt-3 table-result">
        <tr>
            <th>Bắt đầu làm bài</th>
            <td>{{ $attempt->time_start }}</td>
        </tr>
        <tr>
            <th>Kết thúc làm bài</th>
            <td>{{ $attempt->time_end }}</td>
        </tr>
        <tr>
            <th>Thời gian làm bài</th>
            <td>{{ $attempt->work_time }}</td>
        </tr>
        <tr>
            <th>Điểm câu hỏi</th>
            <td>{{ $attempt->total_mark }}/{{ $attempt->full_mark }}</td>
        </tr>
        <tr>
            <th>Điểm</th>
            <td>{{ round($attempt->final_grade, 2) }}/{{ $exam->grade_scale }}
                ({{ round(($attempt->final_grade / $exam->grade_scale) * 100, 2) }}%)</td>
        </tr>
    </table>
    @foreach ($questions as $index => $question)
        <div class="question-container mt-4" id="q-{{ $question->id }}">
            <div class="info {{ !isset($question->answered) ? '' : 'answered' }}">
                <h3 class="bold">
                    <span>Câu hỏi</span>
                    <span style="font-size: 1.2rem">{{ $index + 1 }}</span>
                </h3>
                <p>{{ isset($question->answered) ? 'Chưa trả lời' : 'Đã trả lời' }}</p>
                <p>{{ $question->pivot->grade }} / {{ $question->mark }}</p>
            </div>
            <div class="content clearfix">
                <p class="topic">{{ $question->content }}</p>
                <div class="answer-container clearfix">
                    @foreach ($question->choices as $choice)
                        <div class="answer-item">
                            @if (!$question->multi_answer)
                                <input type="radio" disabled
                                    {{ !is_null($choice->pivot) ? ($choice->pivot->selected ? 'checked' : '') : '' }}>
                            @else
                                <input type="checkbox" disabled
                                    {{ !is_null($choice->pivot) ? ($choice->pivot->selected ? 'checked' : '') : '' }}>
                            @endif
                            <span>{{ is_null($choice->number) ? '' : $choice->number . '. ' }}</span>
                            <span>{{ $choice->content }}</span>
                            <i class="fa {{ $choice->pivot->selected
                                ? (($choice->grade == 1 && !$question->multi_answer) || ($question->multi_answer && $choice->grade > 0)
                                    ? 'fa-check success-icon'
                                    : 'fa-times fail-icon')
                                : '' }} ml-2"
                                aria-hidden="true"></i>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="alert alert-answer alert-warning">
                Đáp án: {{ $question->result }}
            </div>
        </div>
    @endforeach
    
    <div class="mt-3" align='right'>
        <a href="/course/quiz?id={{ request()->query('id') }}">Hoàn thành xem </a>
    </div>
@endsection

@push('script')
@endpush
