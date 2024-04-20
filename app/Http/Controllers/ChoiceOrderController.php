<?php

namespace App\Http\Controllers;

use App\Models\ChoiceOrder;
use App\Http\Requests\StoreChoiceOrderRequest;
use App\Http\Requests\UpdateChoiceOrderRequest;

class ChoiceOrderController extends Controller
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
     * @param  \App\Http\Requests\StoreChoiceOrderRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreChoiceOrderRequest $request)
    {
        $qOrderId = $request->qOrderId;
        $choiceId = $request->choiceId;
        $index = $request->index;

        $cOrder = new ChoiceOrder();
        $cOrder->qorder_id = $qOrderId;
        $cOrder->choice_id = $choiceId;
        $cOrder->index = $index;
        try {
            $record = ChoiceOrder::where('qorder_id', $qOrderId)
                ->where('choice_id', $choiceId)
                ->first();

            if (is_null($record)) {
                $cOrder->save();
            } else {
                $cOrder = $record;
            }
        } catch (\Throwable $th) {
            return $th;
        }

        return $cOrder;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ChoiceOrder  $choiceOrder
     * @return \Illuminate\Http\Response
     */
    public function show(ChoiceOrder $choiceOrder)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ChoiceOrder  $choiceOrder
     * @return \Illuminate\Http\Response
     */
    public function edit(ChoiceOrder $choiceOrder)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateChoiceOrderRequest  $request
     * @param  \App\Models\ChoiceOrder  $choiceOrder
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateChoiceOrderRequest $request, ChoiceOrder $choiceOrder)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ChoiceOrder  $choiceOrder
     * @return \Illuminate\Http\Response
     */
    public function destroy(ChoiceOrder $choiceOrder)
    {
        //
    }
}
