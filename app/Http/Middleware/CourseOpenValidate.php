<?php

namespace App\Http\Middleware;

use App\Helpers\AppHelper;
use App\Models\Course;
use Closure;
use Illuminate\Http\Request;

class CourseOpenValidate
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
        $course = Course::find($request->id);
        if (!is_null($course->course_start) && !AppHelper::isTodayGreaterThan($course->course_start)) {
            session()->flash('error', 'Khóa học chưa mở, không thể đăng ký');
            return redirect()->back();
        }
        return $next($request);
    }
}
