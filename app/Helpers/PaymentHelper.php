<?php

namespace App\Helpers;

use App\Http\Repositories\ReceiptRepository;
use App\Http\Repositories\UserRepository;
use App\Models\Payment;
use App\Models\Receipt;
use App\Models\User;
use App\Services\momo\MomoService;
use App\Services\momo\MomoTransaction;
use App\Services\vnpay\VnpayService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class PaymentHelper
{
    public static function payOrder(string $orderId, string $paymentMethod)
    {
        $payment = Payment::where('orderId', $orderId)->first();
        $value = $payment->value;

        switch ($paymentMethod) {
            case 'momo':
                $transaction = new MomoTransaction($orderId, $value, config("payment.momo.request_type.wallet"));
                try {
                    $response = MomoService::sendPaymentRequest($transaction);
                    if ($response->successful()) {
                        $data = $response->json();
                        if (!isset($data["payUrl"])) {
                            return redirect()->back();
                        }

                        return redirect($data["payUrl"]);
                    } else {
                        Log::info($response->body());
                    }
                } catch (\Exception $exception) {
                    Log::info('Request failed: ' . $exception->getMessage());
                }
                break;

            case 'vnpay':
                try {
                    return redirect(VnpayService::createPaymentUrl($orderId, $value));
                } catch (\Exception $exception) {
                    Log::info('Request failed: ' . $exception->getMessage());
                }
                break;
        }

        return redirect()->back();
    }

    public static function check()
    {
        return session('payment') != null && !empty(session('payment'));
    }

    public static function getPaymentProducts()
    {
        return session('payment')['products'];
    }

    public static function getOwnerInfo()
    {
        return session('payment')['user'];
    }

    public static function clearPaymentSession(): void
    {
        session()->forget('payment');
    }

    public static function generateRequestId(int $maxLength): string
    {
        //use uuid4
        do {
            $uuid = Uuid::uuid4()->toString();
            $trimmedUuid = substr($uuid, 0, $maxLength);
        } while (!self::isIdUnique($trimmedUuid));

        return $trimmedUuid;
    }

    public static function generateOrderId($prefix = 'ORD'): string
    {
        $orderId = $prefix;

        $orderId .= date('YmdHis');

        $orderId .= rand(0, 9999);

        return $orderId;
    }

    private static function isIdUnique(string $requestId): bool
    {
        return true;
    }
}
