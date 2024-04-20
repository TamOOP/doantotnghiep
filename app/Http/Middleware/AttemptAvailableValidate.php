<?php

namespace App\Http\Middleware;

use App\Models\Attempt;
use Closure;
use Illuminate\Http\Request;

class AttemptAvailableValidate
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
        $attemptId = $request->query('attemptId');
        $examId = $request->query('id');
        $userId = auth()->user()->id;

        if (auth()->user()->role != 'student') {
            $attempt = Attempt::find($attemptId);
        } else {
            $attempt = Attempt::where('user_id', $userId)
                ->where('exam_id', $examId)
                ->where('id', $attemptId)
                ->first();
        }

        if (!is_null($attempt)) {
            return $next($request);
        }

        return response()->json(['error' => 'valid fail']);
    }
}
