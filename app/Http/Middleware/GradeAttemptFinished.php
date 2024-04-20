<?php

namespace App\Http\Middleware;

use App\Helpers\AppHelper;
use App\Models\Attempt;
use App\Models\Examination;
use App\Models\QuestionOrder;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class GradeAttemptFinished
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $examId = $request->query('id');
        $userId = auth()->user()->id;
        $now = AppHelper::getCurrentTime();

        $exam = (new Examination())->getExam($examId);

        $attemptNotGrades = Attempt::where('exam_id', $examId)
            ->where('user_id', $userId)
            ->where('time_end', '<=', $now)
            ->where('final_grade', null)
            ->get();

        foreach ($attemptNotGrades as $attempt) {
            $fullMark = 0;

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

            try {
                $attempt->save();
            } catch (\Throwable $th) {
                continue;
            }
        }

        return $next($request);
    }
}
