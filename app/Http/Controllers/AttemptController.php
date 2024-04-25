<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Http\Requests\GradeAttemptRequest;
use App\Models\Attempt;
use App\Http\Requests\StoreAttemptRequest;
use App\Http\Requests\StoreChoiceOrderRequest;
use App\Http\Requests\StoreQuestionOrderRequest;
use App\Http\Requests\StoreQuestionRequest;
use App\Http\Requests\UpdateAttemptRequest;
use App\Models\Choice;
use App\Models\ChoiceOrder;
use App\Models\Examination;
use App\Models\Question;
use App\Models\QuestionOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Throwable;
use Illuminate\Support\Str;

class AttemptController extends Controller
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
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreAttemptRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $userId = auth()->user()->id;
        $examId = $request->query('id');
        $passwordAttempt = $request->password;

        $exam = (new Examination())->getExam($examId);

        if (auth()->user()->role !== 'student') {
            return view('course.activity.quiz-attempt', [
                'questions' => $exam->questions
            ]);
        }

        $attemptNotFinished = (new Attempt())->getAttemptNotFinished($examId, $userId);

        if (is_null($attemptNotFinished)) {
            if (!is_null($exam->password) && $exam->password !== $passwordAttempt) {
                return response()->json(['error' => 'Mật khẩu làm bài không đúng']);
            }

            $attempt = new Attempt();
            $attempt->user_id = $userId;
            $attempt->exam_id = $examId;
            $attempt->time_start = AppHelper::getCurrentTime();
            $attempt->time_end = !is_null($exam->time_limit) ? Carbon::parse($attempt->time_start)->addSeconds($exam->time_limit * $exam->time_unit)->format('Y-m-d H:i:s') : null;
            try {
                $attempt->save();
            } catch (\Throwable $th) {
                return response()->json(['error' => $th->getMessage()]);
            }

            $questions = $exam->questions;
            $qOrderArr = AppHelper::randomArrayInt(count($questions), $exam->shuffle_question);
            foreach ($questions as $question) {
                $qOrder = (new QuestionOrderController)->store(new StoreQuestionOrderRequest([
                    'questionId' => $question->id,
                    'attemptId' => $attempt->id,
                    'index' => $qOrderArr[0],
                    'randomAnswer' => $exam->random_answer
                ]));

                $qOrderArr->splice(0, 1);

                if ($qOrder instanceof Throwable) {
                    return response()->json(['error' => $qOrder->getMessage()]);
                }
            }
        } else {
            $attempt = $attemptNotFinished;
        }

        $questions = $attempt->questions()->orderBy('index')->get();
        $questions = $this->indexChoiceAndCheckAnswerQuestion($questions);

        return response()->json(['redirect' => '/course/quiz/attempt?id=' . $examId . '&attemptId=' . $attempt->id]);
    }

    public function saveAnswer(Request $request)
    {
        $attemptId = $request->attemptId;
        $examId = $request->id;
        $multiAnswer = $request->multiAnswer;

        $questions = Examination::find($examId)->questions;

        foreach ($questions as $question) {
            $questionId = $question->id;
            $questionOrd = QuestionOrder::where('attempt_id', $attemptId)
                ->where('question_id', $questionId)
                ->first();
            if ($question->multi_answer) {
                $choiceIdArr = !is_null($multiAnswer) ? (array_key_exists($questionId, $multiAnswer) ? $multiAnswer[$questionId] : []) : [];

                foreach ($questionOrd->choices as $choice) {
                    $questionOrd->choices()->updateExistingPivot($choice->id, [
                        'selected' => array_key_exists($choice->id, $choiceIdArr) ? 1 : 0
                    ]);
                }
            } else {
                $choiceIdSelected = !is_null($request->question) ? (array_key_exists($questionId, $request->question) ? $request->question[$questionId] : null) : null;

                foreach ($questionOrd->choices as $choice) {
                    $questionOrd->choices()->updateExistingPivot($choice->id, [
                        'selected' => $choice->id == $choiceIdSelected ? 1 : 0
                    ]);
                }
            }
        }

        return response()->json();
    }

    public function review(Request $request)
    {
        $attemptId = $request->query('attemptId');

        $attempt = Attempt::find($attemptId);

        if (is_null($attempt)) {
            return redirect()->back();
        }

        $questions = $attempt->questions()->orderBy('index')->get();
        $questions = $this->indexChoiceAndCheckAnswerQuestion($questions);

        return view('course.activity.quiz-attempt-review', [
            'attempt' => $attempt,
            'questions' => $questions
        ]);
    }

    public function grading(Request $request)
    {
        $attemptId = $request->query('attemptId');
        $examId = $request->query('id');

        $dateSubmit = AppHelper::getCurrentTime();
        $fullMark = 0;

        $exam = (new Examination())->getExam($examId);

        $attempt = Attempt::find($attemptId);

        if (!is_null($attempt->total_mark)) {
            return response()->json([
                'error' => 'Bài làm đã được chấm',
                'redirect' => '/course/quiz/attempt/result?id=' . $examId . '&attemptId=' . $attemptId
            ]);
        }

        foreach ($attempt->questions as $question) {
            $grade = 0;
            $questionOrder = QuestionOrder::find($question->pivot->id);
            foreach ($questionOrder->choices as $choice) {
                if ($choice->pivot->selected) {
                    $grade += $choice->grade;
                }
            }

            $question->grade = $grade > 0 ? $grade * $question->mark : 0;

            $question->attempts()->updateExistingPivot($attempt->id, [
                'grade' => $question->grade
            ]);

            $attempt->total_mark += $question->grade;
            $fullMark += $question->mark;
        }

        $attempt->final_grade = ($attempt->total_mark / $fullMark) * $exam->grade_scale;
        if (Carbon::parse($dateSubmit, 'Asia/Bangkok')->lt(Carbon::parse($attempt->time_end, 'Asia/Bangkok'))) {
            $attempt->time_end = $dateSubmit;
        }

        try {
            $attempt->save();
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }

        return response()->json([
            'success' => 'Chấm bài thành công',
            'redirect' => '/course/quiz/attempt/result?id=' . $examId . '&attemptId=' . $attemptId
        ]);
    }

    public function result(Request $request)
    {
        $examId = $request->query('id');
        $attemptId = $request->query('attemptId');

        $exam = (new Examination())->getExam($examId);

        $attempt = $this->formatAttemptAttr($attemptId);

        if (is_null($attempt)) {
            return response()->json(['error' => 'Không có bài làm']);
        }

        $questions = $attempt->questions()->orderBy('index')->get();
        $questions = $this->indexChoiceAndCheckAnswerQuestion($questions);

        if ($exam->show_answer) {
            foreach ($questions as $question) {
                $result = '';
                foreach ($question->choices as $choice) {
                    if (($question->multi_answer && $choice->grade > 0)
                        || (!$question->multi_answer && $choice->grade == 1)
                    ) {
                        $result .= (!is_null($choice->number) ? $choice->number : $choice->content) . ' ';
                    }
                }
                $question->result = str_replace(' ', ', ', trim($result));
            }
        }

        return view('course.activity.quiz-attempt-result', [
            'exam' => $exam,
            'attempt' => $attempt,
            'questions' => $questions
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Attempt  $attempt
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $attempt = Attempt::find($request->attemptId);
        $questions = $attempt->questions()->orderBy('index')->get();
        $questions = $this->indexChoiceAndCheckAnswerQuestion($questions);

        return view('course.activity.quiz-attempt', [
            'attempt' => $attempt,
            'questions' => $questions
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Attempt  $attempt
     * @return \Illuminate\Http\Response
     */
    public function edit(Attempt $attempt)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateAttemptRequest  $request
     * @param  \App\Models\Attempt  $attempt
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAttemptRequest $request, Attempt $attempt)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Attempt  $attempt
     * @return \Illuminate\Http\Response
     */
    public function destroy(Attempt $attempt)
    {
        //
    }

    public function indexChoiceAndCheckAnswerQuestion($questions)
    {
        foreach ($questions as $question) {
            $questionOrder = QuestionOrder::find($question->pivot->id);
            $question->choices = $questionOrder->choices()->orderBy('index')->get();
            foreach ($question->choices as $choice) {
                if ($choice->pivot->selected) {
                    $question->answered = true;
                }
                switch ($question->choice_numbering) {
                    case 'abc':
                        $choice->number = Str::lower(AppHelper::intToAlphabet($choice->pivot->index));
                        break;
                    case 'ABCD':
                        $choice->number = AppHelper::intToAlphabet($choice->pivot->index);
                        break;
                    case 'iii':
                        $choice->number = Str::lower(AppHelper::intToRoman($choice->pivot->index));
                        break;
                    case 'IIII':
                        $choice->number = AppHelper::intToRoman($choice->pivot->index);
                        break;
                    case 'none':
                        $choice->number = null;
                        break;
                }
            }
        }

        return $questions;
    }

    public function formatAttemptAttr($attemptId)
    {
        $attempt = Attempt::find($attemptId);
        if (is_null($attempt)) {
            return null;
        }

        $timeDiff = AppHelper::calcTimeDiff($attempt->time_start, $attempt->time_end);

        foreach ($attempt->questions as $question) {
            $attempt->full_mark += $question->mark;
        }
        $attempt->time_start = AppHelper::formatDateTime($attempt->time_start, 'd/m/Y, h:i A');
        $attempt->time_end = AppHelper::formatDateTime($attempt->time_end, 'd/m/Y, h:i A');
        $attempt->work_time =  ($timeDiff->days !== 0 ? $timeDiff->days . ' ngày ' : '')
            . ($timeDiff->h !== 0 ? $timeDiff->h . ' giờ ' : '')
            . ($timeDiff->i !== 0 ? $timeDiff->i . ' phút ' : '')
            . ($timeDiff->s !== 0 ? $timeDiff->s . ' giây  ' : '');
        return $attempt;
    }
}
