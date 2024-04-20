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
        $enrolmentId = (new EnrolmentController)->store($course->id, auth()->user()->id, '2');

        if ($enrolmentId instanceof Throwable) {
            return response()->json(['error' => $enrolmentId->getMessage()]);
        }
        $orderId = PaymentHelper::generateOrderId();

        $payment = new Payment();
        $payment->orderId = $orderId;
        $payment->enrolment_id = $enrolmentId;
        $payment->value = $course->payment_cost;
        $payment->payment_date = AppHelper::getCurrentTime();
        $payment->payment_status = 'done';

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
        $course = $payment->enrolment->course;

        session()->flash('success', 'Đã thanh toán thành công và được đăng ký vào khóa học');

        return redirect('/course/view?id='.$course->id);
    }
    public function receiveMomoResponse(IpnMomoRequest $request)
    {
        return redirect('/course/view');
    }
}
