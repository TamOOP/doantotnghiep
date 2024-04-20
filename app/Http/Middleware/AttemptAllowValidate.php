<?php

namespace App\Http\Middleware;

use App\Models\Attempt;
use App\Models\Examination;
use Closure;
use Illuminate\Http\Request;

class AttemptAllowValidate
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

        $exam = Examination::find($examId);
        $countAttempt = Attempt::where('user_id', $userId)
            ->where('exam_id', $examId)
            ->count();
        
        if ($countAttempt < $exam->attempt_allow || $exam->attempt_allow == 0) {
            return $next($request);
        }

        return redirect()->back();
    }
}
