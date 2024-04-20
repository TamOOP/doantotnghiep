<?php

namespace App\Http\Middleware;

use App\Helpers\AppHelper;
use App\Http\Controllers\CourseController;
use Closure;
use Illuminate\Http\Request;

class CourseExpireCheck
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

        $courseId = isset($request->course_id)
            ? $request->course_id
            : AppHelper::getCourseIdFromUrl();

        if ($courseId == null) {
            return redirect()->route('homepage');
        }

        $course = (new CourseController)->getCourse($courseId);

        if (AppHelper::isTodayGreaterThan($course->course_end)) {
            session()->flash('error', 'Khóa học đã đóng');
            return response()->json(['error' => 'Khóa học đã đóng']);
        }

        return $next($request);
    }
}
