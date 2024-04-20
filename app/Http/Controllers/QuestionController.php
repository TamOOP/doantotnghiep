<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Models\Question;
use App\Http\Requests\StoreQuestionRequest;
use App\Http\Requests\UpdateQuestionRequest;
use App\Models\Choice;
use App\Models\Examination;
use App\Rules\AtLeastTwoNonNullValues;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QuestionController extends Controller
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
    public function create(Request $request)
    {
        $exam = (new Examination())->getExam($request->query('id'));
        return view('course.activity.quiz-question-add', [
            'exam' => $exam
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreQuestionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreQuestionRequest $request)
    {
        if ($request->input('multi-answer')) {
            $totalGrade = 0;
            foreach ($request->input('choice-grades') as $grade) {
                if ($grade > 0) {
                    $totalGrade += $grade;
                }
            }

            if ($totalGrade != 1) {
                return response()->json(['error' => 'Tổng điểm dương phải bằng 1']);
            }
        } else {
            $valid = false;
            foreach ($request->input('choice-grades') as $grade) {
                if ($grade == 1) {
                    $valid = true;
                    break;
                }
            }
            if (!$valid) {
                return response()->json(['error' => 'Phải có 1 đáp án được tối đa điểm']);
            }
        }

        $question = new Question();
        $question->exam_id = $request->id;
        $question->content = $request->input('question-description');
        $question->multi_answer = $request->input('multi-answer');
        $question->mark = $request->input('question-mark');
        $question->choice_numbering = $request->input('choice-numbering');

        try {
            $question->save();
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }

        $questionId = $question->id;

        foreach ($request->input('choices') as $index => $choiceContent) {
            if (is_null($choiceContent)) {
                continue;
            }

            $choice = new Choice();
            $choice->question_id = $questionId;
            $choice->content = $choiceContent;
            $choice->grade = $request->input('choice-grades')[$index];

            try {
                $choice->save();
            } catch (\Throwable $th) {
                return response()->json(['error' => $th->getMessage()]);
            }
        }

        return response()->json(['success' => 'Thêm thành công câu hỏi']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $question = Question::find($request->questionId);
        foreach ($question->choices as $i => $choice) {
            switch ($question->choice_numbering) {
                case 'abc':
                    $choice->number = Str::lower(AppHelper::intToAlphabet($i + 1));
                    break;
                case 'ABCD':
                    $choice->number = AppHelper::intToAlphabet($i + 1);
                    break;
                case 'iii':
                    $choice->number = Str::lower(AppHelper::intToRoman($i + 1));
                    break;
                case 'IIII':
                    $choice->number = AppHelper::intToRoman($i + 1);
                    break;
                case 'none':
                    $choice->number = null;
                    break;
            }
        }
        return response()->json([
            'success' => 'ok',
            'question' => $question,
            'choices' => $question->choices
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function edit(Question $question)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateQuestionRequest  $request
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateQuestionRequest $request)
    {
        $request->validate([
            'question-description' => 'required|string',
            'question-mark' => 'required|numeric|min:0',
            'multi-answer' => 'required|integer|in:0,1',
            'choice-numbering' => 'required|string|in:abc,ABCD,iii,IIII,none',
            'choices' => ['required', 'array', 'min:2', new AtLeastTwoNonNullValues],
            'choice-grades' => ['required', 'array', 'min:2', new AtLeastTwoNonNullValues],
            'choice-grades.*' => 'required|numeric|between:-1,1',
        ]);

        if ($request->input('multi-answer')) {
            $totalGrade = 0;
            foreach ($request->input('choice-grades') as $grade) {
                if ($grade > 0) {
                    $totalGrade += $grade;
                }
            }

            if ($totalGrade != 1) {
                return response()->json(['error' => 'Tổng điểm dương phải bằng 1']);
            }
        } else {
            $valid = false;
            foreach ($request->input('choice-grades') as $grade) {
                if ($grade == 1) {
                    $valid = true;
                    break;
                }
            }
            if (!$valid) {
                return response()->json(['error' => 'Phải có 1 đáp án được tối đa điểm']);
            }
        }

        $question = Question::find($request->id);
        $question->content = $request->input('question-description');
        $question->multi_answer = $request->input('multi-answer');
        $question->mark = $request->input('question-mark');
        $question->choice_numbering = $request->input('choice-numbering');

        try {
            $question->save();
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }

        $question->choices()->delete();

        foreach ($request->input('choices') as $index => $choiceContent) {
            if (is_null($choiceContent)) {
                continue;
            }

            $choice = new Choice();
            $choice->question_id = $question->id;
            $choice->content = $choiceContent;
            $choice->grade = $request->input('choice-grades')[$index];

            try {
                $choice->save();
            } catch (\Throwable $th) {
                return response()->json(['error' => $th->getMessage()]);
            }
        }

        return response()->json(['success' => 'Sửa thành công câu hỏi']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        try {
            $question = Question::find($request->questionId);
            $question->status = '-1';
            $question->save();
            return response()->json(['success' => 'Xóa thành công']);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Gặp lỗi khi xóa']);
        }
    }
}
