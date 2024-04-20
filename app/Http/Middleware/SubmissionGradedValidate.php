<?php

namespace App\Http\Middleware;

use App\Models\Submission;
use Closure;
use Illuminate\Http\Request;

class SubmissionGradedValidate
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
        $userId = auth()->user()->id;
        $assignId = $request->query('id');

        try {
            $submission = Submission::where('user_id', $userId)
                ->where('assign_id', $assignId)
                ->first();
        } catch (\Throwable $th) {
            return response()->json(['error', 'Không tìm thấy bài tập']);
        }

        if (!is_null($submission)) {
            if ($submission->grade > -1) {
                session()->flash('error', 'Bài tập đã được chấm');
                return response()->json(['error', 'Bài tập đã được chấm']);
            }
        }

        return $next($request);
    }
}
