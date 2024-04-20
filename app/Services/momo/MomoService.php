<?php

namespace App\Services\momo;

use App\Http\Repositories\ReceiptRepository;
use App\Http\Requests\IpnMomoRequest;
use App\Services\momo\MomoTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MomoService
{
    public static function sendPaymentRequest(MomoTransaction $transaction)
    {
        $apiEndpoint = config("payment.momo.url");

        $data = array(
            "partnerCode" => config("payment.momo.partner_code"),
            "accessKey" => config("payment.momo.access_key"),
            "requestId" => $transaction->requestId,
            "amount" => $transaction->amount,
            "orderId" => $transaction->orderId,
            "orderInfo" => $transaction->orderInfo,
            "redirectUrl" => config("payment.momo.redirect_url"),
            "ipnUrl" => config("payment.momo.ipn_url"),
            "requestType" => $transaction->requestType,
            "extraData" => $transaction->extraData,
            "lang" => config("payment.lang"),
            "signature" => $transaction->signature
        );
        
        return Http::post($apiEndpoint, $data);
    }
    
    public static function generateRequestSignature(MomoTransaction $data): string
    {
        $rawHash = "accessKey=" . config("payment.momo.access_key")
            . "&amount=" . $data->amount
            . "&extraData=" . $data->extraData
            . "&ipnUrl=" . config("payment.momo.ipn_url")
            . "&orderId=" . $data->orderId
            . "&orderInfo=" . $data->orderInfo
            . "&partnerCode=" . config("payment.momo.partner_code")
            . "&redirectUrl=" . config("payment.momo.redirect_url")
            . "&requestId=" . $data->requestId
            . "&requestType=" . $data->requestType;

        return hash_hmac(config('payment.momo.hash_algo'), $rawHash, config("payment.momo.secret_key"));
    }

    public static function generateResponseSignature(IpnMomoRequest $data): string
    {
        $rawHash = "accessKey=" . config("payment.momo.access_key")
            . "&amount=" . $data->amount
            . "&extraData=" . $data->extraData
            . "&message=" . $data->message
            . "&orderId=" . $data->orderId
            . "&orderInfo=" . $data->orderInfo
            . "&orderType=" . $data->orderType
            . "&partnerCode=" . config("payment.momo.partner_code")
            . "&payType=" . $data->payType
            . "&requestId=" . $data->requestId
            . "&responseTime=" . $data->responseTime
            . "&resultCode=" . $data->resultCode
            . "&transId=" . $data->transId;

        return hash_hmac(config('payment.momo.hash_algo'), $rawHash, config("payment.momo.secret_key"));
    }

    public static function validateResponseData(IpnMomoRequest $request) : bool {
        $responses = $request->toArray();
        $signature = MomoService::generateResponseSignature($request);
        return true;
        // try {
        //     //kiểm tra chữ ký
        //     if ($signature == $responses['signature']) {
        //         //kiểm tra mã order
        //         $receipt = (new ReceiptRepository)->getByOrderId($responses['orderId']);
        //         if ($receipt) {
        //             //kiểm tra số tiền giao dịch
        //             if ($receipt->receipt_value / 1000 == $responses['amount']) {
        //                 //kiểm tra trạng thái đơn hàng
        //                 if ($receipt->receipt_status == 0) {
        //                     //kiểm tra momo result code 
        //                     if ($responses['resultCode'] == 0) {
        //                         return true;
        //                     } else {
        //                         Log::info($responses['message']);
        //                     }
        //                 } else {
        //                     Log::info('Đơn hàng đã được xử lý');
        //                 }
        //             } else {
        //                 Log::info('Số tiền giao dịch không hợp lệ');
        //             }
        //         } else {
        //             Log::info('Đơn hàng không tồn tại');
        //         }
        //     } else {
        //         Log::info('Chữ ký không hợp lệ');
        //     }
        // } catch (\Throwable $th) {
        //     Log::info('Có lỗi xảy ra');
        // }
        
        return false;
    }
}
