@extends('layout.app')

@section('title', 'Rút tiền')

@push('style')
    <link rel="stylesheet" href="{{ asset('css/user/withdraw.css') }}">
@endpush

@section('content')
    <div class="main-inner">
        <h4>Rút tiền</h4>

        @include('layout.validate-error')

        <p class="mt-5">Số dư: {{ number_format(auth()->user()->cash) }} VNĐ</p>

        <form action="transfer/store" method="post">
            @csrf
            <div class="input-container">
                <p class="input-label">Số tiền rút:</p>
                <input type="text" class="form-control number-format" name="amount" id="amount" placeholder="Số tiền">
            </div>
            <div class="input-container">
                <p class="input-label">Chọn ngân hàng</p>
                <select class="form-select" name="bankId" id="bank">
                    @foreach ($banks as $bank)
                        <option value="{{ $bank->id }}">
                            {{ ucfirst($bank->name) }} / STK: {{ $bank->account_number }}, CTK: {{ $bank->account_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            @include('layout.success')

            @include('layout.error')

            <div class="input-container">
                <p class="input-label"></p>
                <div class="w-100">
                    <button class="btn btn-blue" id="btn-withdraw">Rút tiền</button>
                </div>
            </div>
        </form>
        <h4 class="mt-5">Lịch sử rút tiền</h4>

        <table class="table table-striped mt-3">
            <thead>
                <th style="width: 5%">Mã</th>
                <th>Số tiền</th>
                <th>Ngân hàng</th>
                <th>Tên tài khoản</th>
                <th>Số tài khoản</th>
                <th>Trạng thái</th>
                <th>Ngày tạo</th>
                <th>Thao tác</th>
            </thead>
            <tbody>
                @foreach ($transfers as $transfer)
                    <tr>
                        <td>{{ $transfer->id }}</td>
                        <td>{{ number_format($transfer->amount) }} VND</td>
                        <td>{{ ucfirst($transfer->bank->name) }}</td>
                        <td>{{ $transfer->bank->account_name }}</td>
                        <td>{{ $transfer->bank->account_number }}</td>

                        @if ($transfer->status == 'process')
                            <td class="waiting-text">Đang chờ</td>
                        @elseif ($transfer->status == 'done')
                            <td class="success-text">Hoàn thành</td>
                        @else
                            <td class="fail-text">Thất bại</td>
                        @endif

                        <td>{{ $transfer->create_at }}</td>
                        <td align="center">
                            <i class="fa fa-trash delete-icon" aria-hidden="true" title="xóa"
                                style="color: #0f6cbf;cursor: pointer;"
                                onclick="deleteTransfer(this, {{ $transfer->id }})"></i>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

@push('script')
    <script src="{{ asset('js/user/withdraw.js') }}"></script>
@endpush
