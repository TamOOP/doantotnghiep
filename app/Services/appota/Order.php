<?php

namespace App\Services\appota;

use App\Helpers\PaymentHelper;

class Order {
    const MAX_LENGTH = 150;
    public string $id;
    public string $info; 

    public function __construct(string $info) {
        $this->id = PaymentHelper::generateOrderId();

        if (strlen($info) > self::MAX_LENGTH) {
            $this->info = substr($info, 0, self::MAX_LENGTH);
        } else {
            $this->info = $info;
        }
    }
}
