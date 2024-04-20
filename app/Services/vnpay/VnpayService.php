<?php

namespace App\Services\vnpay;

use App\Helpers\PaymentHelper;
use App\Http\Repositories\ReceiptRepository;
use App\Http\Requests\IpnVnpayRequest;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Log;

date_default_timezone_set('Asia/Ho_Chi_Minh');

class VnpayService
{
    public static function createPaymentUrl(string $orderId, int $amount, string $ipAddr = '127.0.0.1'): string
    {
        // data yêu cầu của vnpay
        $request = array(
            "vnp_Version" => config("payment.vnpay.version"),
            "vnp_TmnCode" => config("payment.vnpay.tmn_code"),
            "vnp_Amount" => $amount * 100,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => config("payment.currency"),
            "vnp_IpAddr" => $ipAddr,
            "vnp_Locale" => config("payment.lang"),
            "vnp_OrderInfo" => "Thanh toan GD:" . $orderId,
            "vnp_OrderType" => "other",
            "vnp_ReturnUrl" => config("payment.vnpay.redirect_url"),
            "vnp_TxnRef" => $orderId,
            "vnp_ExpireDate" => now()->addMinutes(15)->format('YmdHis'),
            'vnp_BankCode' => 'NCB'
        );

        //tạo mã kiểm tra checksum 
        $request["vnp_SecureHash"] = self::generateHashKey($request);

        //tạo query cho url thanh toán vnpay
        $query = "";
        foreach ($request as $key => $value) {
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }
        $vnp_Url = config("payment.vnpay.url") . "?" . substr($query, 0, -1); // xóa ký tự '&' cuối cùng

        return $vnp_Url;
    }

    public static function generateHashKey(array $data): string
    {
        $hashData = "";
        if (isset($data['vnp_SecureHash'])) {
            unset($data['vnp_SecureHash']);
        }
        ksort($data);
        foreach ($data as $key => $value) {
            $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
        }
        $hashKey = hash_hmac(config('payment.vnpay.hash_algo'), substr($hashData, 1), config("payment.vnpay.secret_key")); // xoá ký tự '&' đầu 

        return $hashKey;
    }

    public static function validateResponseData(IpnVnpayRequest $request)
    {
        $responses = $request->toArray();
        $secureHash = VnpayService::generateHashKey($responses);

        // $returnData['valid'] = false;
        $returnData['valid'] = true;
        $returnData['RspCode'] = '00';
        $returnData['Message'] = 'Confirm Success';
        // try {
        //     //kiểm tra checksum
        //     if ($secureHash == $responses['vnp_SecureHash']) {
        //         //kiểm tra order
        //         $receipt = (new ReceiptRepository)->getByOrderId($responses['vnp_TxnRef']);
        //         if ($receipt) {
        //             //kiểm tra số tiền giao dịch
        //             if ($receipt->receipt_value == $responses['vnp_Amount'] / 100) {
        //                 //kiểm tra trạng thái đơn hàng
        //                 if ($receipt->receipt_status == 0) {
        //                     //kiểm tra momo result code 
        //                     if ($responses['vnp_ResponseCode'] == '00' || $responses['vnp_TransactionStatus'] == '00') {
        //                         $returnData['valid'] = true;
        //                     } else {
        //                         Log::info('Thanh toán thất bại');
        //                     }
        //                     $returnData['RspCode'] = '00';
        //                     $returnData['Message'] = 'Confirm Success';
        //                 } else {
        //                     $returnData['RspCode'] = '02';
        //                     $returnData['Message'] = 'Order already confirmed';
        //                     Log::info('Đơn hàng đã được xử lý');
        //                 }
        //             } else {
        //                 $returnData['RspCode'] = '04';
        //                 $returnData['Message'] = 'invalid amount';
        //                 Log::info('Số tiền giao dịch không hợp lệ');
        //             }
        //         } else {
        //             $returnData['RspCode'] = '01';
        //             $returnData['Message'] = 'Order not found';
        //             Log::info('Đơn hàng không tồn tại');
        //         }
        //     } else {
        //         $returnData['RspCode'] = '97';
        //         $returnData['Message'] = 'Invalid signature';
        //         Log::info('Chữ ký không hợp lệ');
        //     }
        // } catch (\Throwable $th) {
        //     $returnData['RspCode'] = '99';
        //     $returnData['Message'] = 'Unknow error';
        //     Log::info('Có lỗi xảy ra');
        // }

        return $returnData;
    }
}
