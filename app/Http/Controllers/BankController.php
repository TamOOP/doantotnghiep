<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Http\Requests\StoreBankRequest;
use App\Http\Requests\UpdateBankRequest;
use Illuminate\Http\Request;

class BankController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \App\Http\Requests\StoreBankRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBankRequest $request)
    {
        $userId = auth()->user()->id;

        $bank = Bank::where('user_id', $userId)
            ->where('account_number', $request->accountNumber)
            ->first();

        if (is_null($bank)) {
            $bank = new Bank();
            $bank->user_id = $userId;
            $bank->name = $request->bankCode;
            $bank->account_number = $request->accountNumber;
            $bank->account_name = $request->accountOwner;

            try {
                $bank->save();
                session()->flash('success', 'Thêm thành công');
            } catch (\Throwable $th) {
                session()->flash('error', $th->getMessage());
            }
        }

        $banks = auth()->user()->banks;

        return redirect('/user/bank');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function show(Bank $bank)
    {
        $banks = auth()->user()->banks;

        return view('user.bank', [
            'banks' => $banks
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function edit(Bank $bank)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateBankRequest  $request
     * @param  \App\Models\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBankRequest $request, Bank $bank)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $bank = Bank::find($request->id);
        try {
            $bank->delete();
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }

        return response()->json(['success' => 'Xóa thành công']);
    }
}
