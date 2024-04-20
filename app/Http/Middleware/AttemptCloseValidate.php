<?php

namespace App\Http\Middleware;

use App\Helpers\AppHelper;
use App\Models\Attempt;
use App\Models\Examination;
use Closure;
use Illuminate\Http\Request;

class AttemptCloseValidate
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

        $exam = (new Examination())->getExam($examId);

        if (AppHelper::isTodayGreaterThan($exam->time_end) && auth()->user()->role == 'student') {
            return response()->json(['error' => 'Bài thi đã đóng']);
        } 

        return $next($request);
    }
}
