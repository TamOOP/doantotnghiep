<?php

namespace App\Providers;

use App\Helpers\AppHelper;
use App\Http\Controllers\CourseController;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\Examination;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (session('sidebar') == null) {
            session(['sidebar' => true]);
        }

        View::composer('layout.header', function ($view) {
            $user = auth()->user();

            if (!is_null($user)) {
                $imageSize = getimagesize(public_path($user->avata));
                $height = $imageSize[1];
                $width = $imageSize[0];

                $view->with('height', $height > $width ? 'auto' : '40')
                    ->with('width', $height < $width ? 'auto' : '40');
            }

            $view->with('user', $user);
        });

        View::composer(['layout.sidebar', 'layout.course-nav', 'layout.activity.breadcrumb'], function ($view) {
            try {
                $course = Course::find(AppHelper::getCourseIdFromUrl());

                if (is_null($course)) {
                    throw new Exception('Không có khóa học');
                } elseif ($course->status != 1) {
                    throw new Exception('Khóa học đã xóa');
                }

                $view->with('course', $course);
            } catch (Exception $e) {
                session()->flash('error', $e->getMessage());
            }
        });
    }
}
