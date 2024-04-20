@extends('layout.activity')

@push('style')
    <link rel="stylesheet" href="{{ asset('css/course/quiz.css') }}">
    <link rel="stylesheet" href="{{ asset('css/course/member.css') }}">
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
    <h4 class="mt-3">Kết quả bài trắc nghiệm</h4>
    <p class="mt-3">Cách tính điểm: {{ $exam->grading_method }}</p>
    <form class=" mt-3 mb-4" id="form-filter">
        @csrf
        <div class="filter-box">
            <div class="search-condition mr-3">
                <select class=" form-select " name="search-condition" id="search-condition">
                    <option value="name">Tìm theo tên</option>
                    <option value="email">Tìm email</option>
                    <option value="result">Kết quả</option>
                    <option value="grade-morethan">Điểm lớn hơn</option>
                </select>
            </div>
            <div>
                <select class="form-select" name="search-option" id="search-option"></select>
            </div>

            <div class="search-bar">
                <input class=" form-control search-input" type="text" name="search-keyword" id="conversation"
                    placeholder="Từ khóa">
                <i class="fa fa-search search-icon" aria-hidden="true"></i>
            </div>
        </div>
        <button class="filter-button btn btn-primary mt-3">
            Tìm kiếm
        </button>
    </form>

    <table class="table mt-4 table-striped" id="table-result">
        <thead>
            <tr>
                <th scope="col">Họ và tên</th>
                <th scope="col">Email</th>
                <th scope="col">Điểm/10.00</th>
                <th scope="col">Kết quả</th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($students as $i => $student)
                <tr>
                    <td>
                        <div class="d-flex flex-row align-items-center">
                            <div class="avata">
                                <img class="user-img" src="{{ asset($student->avata) }}" alt="">
                            </div>
                            <p class="user-name" style="margin-left:0.5rem">
                                {{ $student->name }}
                            </p>
                        </div>
                    </td>
                    <td>
                        {{ $student->username }}
                    </td>
                    <td>{{ $student->finalGrade }}</td>
                    <td>
                        <i class="fa {{ $student->status == 'Qua' ? 'fa-check success-icon' : 'fa-times fail-icon' }} mr-2"
                            aria-hidden="true"></i>
                        <span>{{ $student->status }}</span>
                    </td>
                    <td>
                        <i class="fa fa-info-circle info-icon mr-2" aria-hidden="true"
                            data-attempt="{{ $student->id }}"></i>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <h4 class="mt-5 mb-3" style="">Sơ đồ điểm</h4>
    <div style="overflow: auto">
        <div class="chart-container">
            <canvas id="myChart"></canvas>
        </div>
    </div>
@endsection

@section('modal')
    <div class="modal-container">
        <div class="d-flex align-items-center p-4">
            <h5 class="font-weight-bold">Tổng quan các lần làm bài</h5>
            <i id="close-modal" class="fa fa-times mr-0 ml-auto" aria-hidden="true"></i>
        </div>
        <div class="modal-body p-4">
            <div class="d-flex mb-4">
                <p class="mr-2">Học sinh:</p>
                <p style="font-weight: bold" id="studentName"></p>
            </div>

            <table class="table table-striped" id="table-attempts">
                <thead>
                    <tr>
                        <th scope="col">Lần làm bài</th>
                        <th scope="col">Trạng thái</th>
                        <th scope="col">Điểm/10.00</th>
                        <th scope="col">Thời gian làm bài</th>
                        <th scope="col">Bắt đầu lúc</th>
                        <th scope="col">Kết thúc lúc</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <div class="modal-footer p-4">
            <button id="btn-modal-close" class="btn btn-secondary btn-gray mr-2">Đóng</button>
        </div>
    </div>
@endsection

@push('script')
    <script>
        var gradeScale = {{ $exam->grade_scale }}
        var gradeStatis = {!! json_encode($gradeStatis) !!};
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <script src="{{ asset('js/course/quiz-result.js') }}"></script>
@endpush
