<?php

namespace App\Http\Middleware;

use App\Helpers\AppHelper;
use App\Models\Assignment;
use Closure;
use Illuminate\Http\Request;

class AssignExpireCheck
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

        $assignId = $request->query('id');

        try {
            $assign = Assignment::find($assignId);
        } catch (\Throwable $th) {
            return response()->json(['error','Error']);
        }

        if (AppHelper::isTodayGreaterThan($assign->time_end)) {
            session()->flash('error', 'Bài tập đã đóng');
            return response()->json(['error' => 'Bài tập đã đóng']);
        }
        
        return $next($request);
    }
}
