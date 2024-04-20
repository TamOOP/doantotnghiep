<?php

namespace App\Http\Middleware;

use App\Helpers\AppHelper;
use App\Models\Attempt;
use Closure;
use Illuminate\Http\Request;

class AttemptFinishedValidate
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

        $attempt = Attempt::find($attemptId);
        if (AppHelper::isTodayGreaterThan($attempt->time_end)) {
            return $next($request);
        }

        return redirect()->back();
    }
}
