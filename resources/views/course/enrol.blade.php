@extends('layout.app')

@section('title', 'Tham gia khóa học')

@push('style')
    <link rel="stylesheet" href="{{ asset('css/course/enrol.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout/payment.css') }}">
@endpush

@section('content')
    <div class="main-inner">
        <h3>
            My first course
        </h3>
        <h5 class="mt-4">
            Tham gia Khóa học
        </h5>
        @if (!$course->hasOpened)
            <div class="alert alert-warning ml-3 mr-3 mt-3">
                Khóa học chưa mở, không thể đăng ký
            </div>
        @endif

        @if ($course->enrolment_method == '0')
            <div class="announcement-container d-flex flex-column mt-3">
                <p class="announcement-content">
                    Không thể tự tham gia khóa học
                </p>
                <button id="btn-back" class="announcement-content btn-blue btn mt-3">
                    Quay lại
                </button>
            </div>
        @elseif ($course->enrolment_method == '1')
            <div class="announcement-container d-flex flex-column mt-3">
                <p class="announcement-content">
                    Chưa tham gia khóa học
                </p>
                <button class="announcement-content btn-blue btn mt-3" id="btn-enrol">
                    Đăng ký khóa học
                </button>
            </div>
        @else
            <div class="announcement-container d-flex flex-column mt-3">
                <p class="announcement-content">
                    Bạn cần thanh toán khóa học để tham gia
                </p>
                <h5 class="announcement-content">
                    {{ number_format($course->payment_cost) }} VNĐ
                </h5>
                <button id="btn-method" class="announcement-content btn-payment btn btn-blue mt-3">
                    Chọn phương thức thanh toán
                </button>
            </div>

            <div class='priority-screen'>
                <div class='priority-content'>
                    <h4 class="p-4">Phương thức thanh toán</h4>
                    <form class="p-4" id="form-payment" action="/course/pay?id={{ $course->id }}" method="post">
                        @csrf
                        @include('layout.payment')
                        <div class="d-flex mt-4 justify-content-center">
                            <button type="submit" class="btn btn-primary btn-payment mr-3" id=" btn-submit">
                                Thanh toán
                            </button>
                            <button class="btn btn-primary" id="btn-cancel">
                                Hủy bỏ
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

    </div>
@endsection

@push('script')
    <script src="{{ asset('js/course/enrol.js') }}"></script>
@endpush
