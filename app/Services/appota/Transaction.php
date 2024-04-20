<?php

namespace App\Services\appota;


class Transaction {
    public int $amount;
    public string $currency;
    public string $bankCode;
    public string $paymentMethod;
    public string $action;

    public function __construct(int $amount, string $currency, string $bankCode, string $paymentMethod, string $action) {
        $this->amount = $amount;
        $this->currency = $currency;
        $this->bankCode = $bankCode;
        $this->paymentMethod = $paymentMethod;
        $this->action = $action;
    }
}