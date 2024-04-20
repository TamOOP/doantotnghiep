<?php

namespace App\Services\appota;

// require 'vendor/autoload.php';

use App\Helpers\PaymentHelper;
use Ramsey\Uuid\Uuid;
use App\Services\appota\Order;
use App\Services\appota\Transaction;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Http;

class AppotaService {
    private const JWT_EXPIRE_TIME = 30 * 60; // 30 min
    private const MAX_LENGTH_REQUEST_ID = 42;
    private string $jwt;
    private string $requestId;
    
    public function __construct() {
        //generate jwt 
        $exp = time() + self::JWT_EXPIRE_TIME;
        $currentTime = time();
        $headers = [
            "typ" => "JWT",
            "alg" => "HS256",
            "cty" => "appotapay-api;v=1"
        ];

        $payload = [
            'iss' => env("APPOTA_PARTNER_CODE"),
            'jti' => env("APPOTA_API_KEY") . "-" . $currentTime,
            'api_key' => env("APPOTA_API_KEY"),
            'exp' => $exp
        ];
        $this->jwt = JWT::encode($payload, env("APPOTA_SECRET_KEY"), 'HS256', null, $headers);

        //generate requestId
        $this->requestId = PaymentHelper::generateRequestId(self::MAX_LENGTH_REQUEST_ID);

    }
    

    public function sendPaymentRequest(Transaction $transaction, Order $order) {
        $apiEndpoint = config("payment.appota.domain") . "/api/v2/orders/payment";

        $data = [
            "transaction" => [
                "amount" => $transaction->amount,
                "currency" => $transaction->currency,
                "bankCode" => $transaction->bankCode,
                "paymentMethod" => $transaction->paymentMethod,
                "action" => $transaction->action
            ],
            "partnerReference" => [
                "order" => [
                    "id" => $order->id,
                    "info" => $order->info,
                ],
                "notificationConfig" => [
                    "notifyUrl" => config("payment.ipn_url"),
                    "redirectUrl" => config("payment.redirect_url")
                ]
            ]
        ];

        $headers = [
            "X-APPOTAPAY-AUTH" => $this->jwt,
            "Content-Type" => "application/json",
            "X-Request-ID" => $this->requestId,
            "X-Language" => "vi"
        ];
        // dd(json_encode($headers));
        return Http::withHeaders($headers)->post($apiEndpoint, $data);
    }
}