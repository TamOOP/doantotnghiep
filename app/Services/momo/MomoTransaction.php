<?php

namespace App\Services\momo;

use App\Helpers\PaymentHelper;

class MomoTransaction
{
    private const MAX_LENGTH_REQUEST_ID = 50;
    public string $requestId;
    public int $amount;
    public string $orderId;
    public string $orderInfo;
    public string $requestType;
    public string $extraData;
    public string $signature;

    public function __construct(string $orderId, int $amount, string $requestType, string $extraData = "")
    {
        $this->amount = $amount;
        $this->requestType = $requestType;
        $this->extraData = $extraData;
        $this->requestId = PaymentHelper::generateRequestId(self::MAX_LENGTH_REQUEST_ID);
        $this->orderId = $orderId;
        $this->orderInfo = "Thanh toán đơn hàng" . $this->orderId;
        $this->signature = MomoService::generateRequestSignature($this);
    }
}
