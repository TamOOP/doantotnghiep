<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IpnVnpayRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'vnp_TmnCode' => 'required|max:8',
            'vnp_Amount' => 'required|min:1|max:12',
            'vnp_BankCode' => 'required|min:3|max:20',
            'vnp_OrderInfo' => 'required|min:1|max:255',
            'vnp_TransactionNo' => 'required|min:1|max:15',
            'vnp_ResponseCode' => 'required|max:2',
            'vnp_TransactionStatus' => 'required|max:2',
            'vnp_TxnRef' => 'required|min:1|max:100',
            'vnp_SecureHash' => 'required|min:32|max:256',
        ];
    }
}
