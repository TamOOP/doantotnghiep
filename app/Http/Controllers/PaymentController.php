<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Helpers\MailHelper;
use App\Helpers\PaymentHelper;
use App\Http\Repositories\ReceiptRepository;
use App\Http\Requests\IpnMomoRequest;
use App\Http\Requests\IpnVnpayRequest;
use App\Models\Cart;
use App\Models\Course;
use App\Models\Enrolment;
use App\Models\Payment;
use App\Models\Receipt;
use App\Models\User;
use App\Rules\PaymentProvider;
use App\Services\momo\MomoService;
use App\Services\vnpay\VnpayService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Throwable;

class PaymentController extends Controller
{

    public function __construct()
    {
    }

    public function payCourse(Request $request)
    {
        $request->validate([
            'payment_method' => ['required', new PaymentProvider],
            'id' => 'required'
        ]);

        $course = Course::find($request->id);

        $orderId = PaymentHelper::generateOrderId();

        $payment = new Payment();
        $payment->orderId = $orderId;
        $payment->user_id = auth()->user()->id;
        $payment->course_id = $course->id;
        $payment->value = $course->payment_cost;
        $payment->payment_date = AppHelper::getCurrentTime();
        $payment->payment_status = 'process';

        try {
            $payment->save();
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }

        return PaymentHelper::payOrder($orderId, $request->payment_method);
    }

    public function receiveVnPayResponse(Request $request)
    {
        $payment = Payment::where('orderId', $request->vnp_TxnRef)->first();

        if ($request->vnp_ResponseCode == '00' || $request->vnp_TransactionStatus == '00') {
            $payment = $this->updateSuccessPaymentThenEnrolStudent($request->vnp_TxnRef);

            if ($payment instanceof Throwable) {
                return response()->json(['error' => $payment->getMessage()]);
            }

            session()->flash('success', 'Đã thanh toán thành công và được đăng ký vào khóa học');

            return redirect('/course/view?id=' . $payment->course_id);
        } else {
            return redirect('/course/enrol?id=' . $payment->course_id);
        }
    }

    public function receiveMomoResponse(IpnMomoRequest $request)
    {
        $payment = Payment::where('orderId', $request->orderId)->first();

        if ($request->resultCode == '0') {
            $payment = $this->updateSuccessPaymentThenEnrolStudent($request->orderId);

            if ($payment instanceof Throwable) {
                return response()->json(['error' => $payment->getMessage()]);
            }

            session()->flash('success', 'Đã thanh toán thành công và được đăng ký vào khóa học');

            return redirect('/course/view?id=' . $payment->course_id);
        } else {
            return redirect('/course/enrol?id=' . $payment->course_id);
        }
    }

    protected function updateSuccessPaymentThenEnrolStudent($orderId)
    {
        $payment = Payment::where('orderId', $orderId)->first();
        $payment->payment_status = 'done';

        try {
            $payment->save();
        } catch (\Throwable $th) {
            return $th;
        }

        $enrolmentId = (new EnrolmentController)->store($payment->course_id, $payment->user_id, '2');

        if ($enrolmentId instanceof Throwable) {
            return $enrolmentId;
        }

        return $payment;
    }
}
