<?php

namespace App\Http\Middleware;

use App\Models\Enrolment;
use Closure;
use Illuminate\Http\Request;

class EnrolAuth
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
        $course_id = $request->query('id');

        if ($course_id == null) {
            return redirect()->route('homepage');
        }

        $user_id = auth()->user()->id;
        $enrolment = Enrolment::where('course_id', $course_id)
            ->where('user_id', $user_id)
            ->first();
        
        if (!$enrolment){
            return $next($request);
        } else {
            return redirect('/course/view?id=' . $course_id);
        }
    }
}
