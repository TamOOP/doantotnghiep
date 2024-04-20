<?php

namespace App\Http\Middleware;

use App\Helpers\AppHelper;
use App\Models\Enrolment;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class CourseAccessValidate
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
        if ($request->query('type') == 'course' && $request->path() == 'course/add') {
            return $next($request);
        }

        $courseId = isset($request->course_id)
            ? $request->course_id
            : AppHelper::getCourseIdFromUrl();
        if ($courseId == null) {
            return redirect()->route('homepage');
        }

        try {
            $user = auth()->user();
        } catch (\Throwable $th) {
            return redirect()->route('homepage');
        }

        switch ($user->role) {
            case 'student':
                $enrolment = (new Enrolment())->getEnrolment($courseId, $user->id);
                if ($enrolment === null) {
                    return redirect('/course/enrol?id=' . $courseId);
                }

                $enrolment->last_access = AppHelper::getCurrentTime();

                try {
                    $enrolment->save();
                } catch (\Throwable $th) {
                    return redirect()->back();
                }
                return $next($request);
                break;

            case 'teacher':
                $courseTeaching = $user->courseTeaching;
                if (!$courseTeaching->isEmpty()) {
                    foreach ($courseTeaching as $course) {
                        if ($course->id == $courseId) {
                            return $next($request);
                        }
                    }
                }
                return redirect()->back();

                break;

            case 'admin':
                return $next($request);
                break;

            default:
                return redirect()->back();
                break;
        }
    }
}
