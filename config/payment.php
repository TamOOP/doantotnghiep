<?php

return [
    'currency' => 'VND',

    'lang' => 'vi',


    'momo' => [
        'url' => "https://test-payment.momo.vn/v2/gateway/api/create",
        'hash_algo' => 'sha256',
        'ipn_url' => env('APP_URL') . '/api/ipn/momo',
        'redirect_url' => env('APP_URL') . '/payment/momo',
        'partner_code' => env("MOMO_PARTNER_CODE"),
        'access_key' => env("MOMO_ACCESS_KEY"),
        'secret_key' => env("MOMO_SECRET_KEY"),
        'request_type' => [
            'wallet' => 'captureWallet',
            'atm' => 'payWithATM'
        ]
    ],

    'vnpay' => [
        'url' => "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html",
        'hash_algo' => 'sha512',
        'ipn_url' => env('APP_URL') . '/api/ipn/vnpay',
        'redirect_url' => env('APP_URL') . '/payment/vnpay',
        'version' => "2.1.0",
        'tmn_code' => env("VNPAY_TMNCODE"),
        'secret_key' => env("VNPAY_SECRET_KEY")
    ],

    'appota' => [
        'domain' => "https://gateway.dev.appotapay.com"
    ]
];