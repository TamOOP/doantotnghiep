<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Http\Requests\StoreChoiceOrderRequest;
use App\Models\QuestionOrder;
use App\Http\Requests\StoreQuestionOrderRequest;
use App\Http\Requests\UpdateQuestionOrderRequest;
use App\Models\Question;
use Illuminate\Http\Request;
use Throwable;

class QuestionOrderController extends Controller
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
     * @param  \App\Http\Requests\StoreQuestionOrderRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreQuestionOrderRequest $request)
    {
        $attemptId = $request->attemptId;
        $questionId = $request->questionId;
        $index = $request->index;
        $randomAnswer = $request->randomAnswer;

        $qOrder = new QuestionOrder();
        $qOrder->attempt_id = $attemptId;
        $qOrder->question_id = $questionId;
        $qOrder->index = $index;
        try {
            $record = QuestionOrder::where('attempt_id', $attemptId)
                ->where('question_id', $questionId)
                ->first();

            if (is_null($record)) {
                $qOrder->save();
            } else {
                $qOrder = $record;
            }
        } catch (\Throwable $th) {
            return $th;
        }

        $question = Question::find($questionId);
        $choices = $question->choices;
        $cOrderArr = AppHelper::randomArrayInt(count($choices), $randomAnswer);
        foreach ($choices as $choice) {
            $cOrder = (new ChoiceOrderController)->store(new StoreChoiceOrderRequest([
                'qOrderId' => $qOrder->id,
                'choiceId' => $choice->id,
                'index' => $cOrderArr[0]
            ]));

            if ($cOrder instanceof Throwable) {
                return $cOrder;
            }
            $cOrderArr->splice(0, 1);
        }

        return $qOrder;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\QuestionOrder  $questionOrder
     * @return \Illuminate\Http\Response
     */
    public function show(QuestionOrder $questionOrder)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\QuestionOrder  $questionOrder
     * @return \Illuminate\Http\Response
     */
    public function edit(QuestionOrder $questionOrder)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateQuestionOrderRequest  $request
     * @param  \App\Models\QuestionOrder  $questionOrder
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
       
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\QuestionOrder  $questionOrder
     * @return \Illuminate\Http\Response
     */
    public function destroy(QuestionOrder $questionOrder)
    {
        //
    }
}
