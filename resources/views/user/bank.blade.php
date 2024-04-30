@extends('layout.app')

@section('title', 'Ngân hàng')

@push('style')
    <link rel="stylesheet" href="{{ asset('css/user/withdraw.css') }}">
@endpush

@section('content')
    <div class="main-inner">
        <h4>Thêm ngân hàng</h4>

        @include('layout.validate-error')

        <form action="bank/store" method="post">
            @csrf
            <div class="input-container">
                <p class="input-label">Chọn ngân hàng</p>
                <select class="form-select" name="bankCode" id="bank">
                    <option value="">--- Chọn ngân hàng ---</option>
                    <option value="vietcombank">Ngân hàng Vietcombank</option>
                </select>
            </div>
            <div class="input-container">
                <p class="input-label">Số tài khoản:</p>
                <input type="text" class="form-control" name="accountNumber" placeholder="Số tài khoản">
            </div>

            <div class="input-container">
                <p class="input-label">Chủ tài khoản:</p>
                <input type="text" class="form-control" name="accountOwner" placeholder="Chủ tài khoản">
            </div>

            @include('layout.success')

            @include('layout.error')

            <div class="input-container">
                <p class="input-label"></p>
                <div class="w-100">
                    <button class="btn btn-blue" id="btn-withdraw">Thêm ngân hàng</button>
                </div>
            </div>
        </form>

        <h4 class="mt-5">Ngân hàng</h4>

        <table class="table mt-3">
            <thead>
                <th>STT</th>
                <th>Ngân hàng</th>
                <th>Số tài khoản</th>
                <th>Tên tài khoản</th>
                <th style="width:10%">Thao tác</th>
            </thead>
            <tbody>
                @foreach ($banks as $i => $bank)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ ucfirst($bank->name) }}</td>
                        <td>{{ $bank->account_number }}</td>
                        <td>{{ $bank->account_name }}</td>
                        <td align="center">
                            <i class="fa fa-trash delete-icon" aria-hidden="true" title="xóa"
                                style="color: #0f6cbf;cursor: pointer;" onclick="deleteBank(this, {{ $bank->id }})"></i>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

@push('script')
    <script src="{{ asset('js/user/bank.js') }}"></script>
@endpush
