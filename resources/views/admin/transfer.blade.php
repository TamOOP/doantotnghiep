@extends('layout.app')

@push('style')
    <link rel="stylesheet" href="{{ asset('css/layout/sidebar.css') }}">
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
                <h4>Danh sách yêu cầu rút tiền</h4>
            </div>

            <table id="user-table" class="table mt-4 table-striped">
                <thead>
                    <tr>
                        <th scope="col">Mã</th>
                        <th scope="col">Giáo viên</th>
                        <th>Số tiền</th>
                        <th>Ngân hàng</th>
                        <th>Số tài khoản</th>
                        <th>Tên tài khoản</th>
                        <th scope="col">Ngày tạo</th>
                        <th style="width: 15%">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transfers as $transfer)
                        <tr>
                            <td>{{ $transfer->id }}</td>
                            <td>
                                <div class="d-flex flex-row align-items-center">
                                    <div class="avata">
                                        <img src="{{ asset($transfer->user->avata) }}"
                                            width="{{ $transfer->user->imageWidth == 'auto' ? 'auto' : '35' }}"
                                            height="{{ $transfer->user->imageHeight == 'auto' ? 'auto' : '35' }}">
                                    </div>
                                    <p class="user-name" style="margin-left:0.5rem">
                                        {{ $transfer->user->name }}
                                    </p>
                                </div>
                            </td>
                            <td>{{ number_format($transfer->amount) }} VND</td>

                            <td>
                                {{ ucfirst($transfer->bank->name) }}
                            </td>
                            <td>
                                {{ $transfer->bank->account_number }}
                            </td>
                            <td>
                                {{ $transfer->bank->account_name }}
                            </td>

                            <td>
                                {{ $transfer->create_at }}
                            </td>

                            <td>
                                <button class="btn btn-success" onclick="updateTransfer(this, {{ $transfer->id }}, 'done')">Thành công</button>
                                <button class="btn btn-danger" onclick="updateTransfer(this, {{ $transfer->id }}, 'fail')">Thất bại</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection


@push('script')
    <script src="{{ asset('js/admin/sidebar.js') }}"></script>
    <script src="{{ asset('js/admin/user.js') }}"></script>
    <script src="{{ asset('js/admin/transfer.js') }}"></script>
@endpush
