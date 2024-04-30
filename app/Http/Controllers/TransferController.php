<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Models\Transfer;
use App\Http\Requests\StoreTransferRequest;
use App\Http\Requests\UpdateTransferRequest;
use App\Models\User;
use Illuminate\Http\Request;

class TransferController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transfers = Transfer::where('status', 'process')->get();
        foreach ($transfers as $transfer) {
            $transfer->create_at = AppHelper::formatDateTime($transfer->create_at, 'd/m/Y, h:i A');

            $imageSize = getimagesize(public_path($transfer->user->avata));
            $height = $imageSize[1];
            $width = $imageSize[0];

            $transfer->user->imageHeight = $height > $width ? 'auto' : '';
            $transfer->user->imageWidth = $height < $width ? 'auto' : '';
        }

        return view('admin.transfer', [
            'transfers' => $transfers
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreTransferRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTransferRequest $request)
    {
        $user = User::find(auth()->user()->id);
        $amount = str_replace(',', '', $request->amount);

        if ($user->cash < $amount) {
            session()->flash('error', 'Số dư không đủ');
        } elseif ($amount < 1000) {
            session()->flash('error', 'Số tiền tối thiểu là 1000');
        } else {
            $user->cash -= $amount;

            $transfer = new Transfer();
            $transfer->user_id = $user->id;
            $transfer->bank_id = $request->bankId;
            $transfer->amount = $amount;
            $transfer->create_at = AppHelper::getCurrentTime();
            $transfer->status = 'process';

            try {
                $user->save();
                $transfer->save();
            } catch (\Throwable $th) {
                session()->flash('error', $th->getMessage());
            }
        }

        return redirect('/user/withdraw');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Transfer  $transfer
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $transfers = auth()->user()->transfers;
        $banks = auth()->user()->banks;

        foreach ($transfers as $transfer) {
            $transfer->create_at = AppHelper::formatDateTime($transfer->create_at, 'd/m/Y, h:i A');
        }

        return view('user.withdraw', [
            'transfers' => $transfers,
            'banks' => $banks
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Transfer  $transfer
     * @return \Illuminate\Http\Response
     */
    public function edit(Transfer $transfer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateTransferRequest  $request
     * @param  \App\Models\Transfer  $transfer
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTransferRequest $request)
    {
        $transfer = Transfer::find($request->id);
        $user = User::find($transfer->user_id);

        $transfer->status = $request->status;
        if ($request->status == 'fail') {
            $user->cash += $transfer->amount;
        }

        try {
            $user->save();
            $transfer->save();
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }

        return response()->json(['success' => 'Xử lý thành công']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Transfer  $transfer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $trans = Transfer::find($request->id);
        try {
            $trans->delete();
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }

        return response()->json(['success' => 'Xóa thành công']);
    }
}
