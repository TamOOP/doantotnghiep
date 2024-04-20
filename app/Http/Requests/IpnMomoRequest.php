<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IpnMomoRequest extends FormRequest
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
            'partnerCode' => 'required',
            'orderId' => 'required',
            'requestId' => 'required',
            'amount' => 'required',
            'orderInfo' => 'required',
            'orderType' => 'required',
            'transId' => 'required',
            'resultCode' => 'required',
            'message' => 'required',
            'payType' => 'required',
            'responseTime' => 'required',
            'signature' => 'required',
        ];
    }
}
