<?php

namespace App\Http\Middleware;

use App\Models\Examination;
use Closure;
use Illuminate\Http\Request;

class DontHaveAttemptValidate
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

        $exam = Examination::find($examId);
        
        if (count($exam->attempts) > 0) {
            return response()->json(['error' => 'Không thể xóa hoặc thêm câu hỏi']);
        }

        return $next($request);
    }
}
