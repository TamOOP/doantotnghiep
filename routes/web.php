<?php

use App\Helpers\AppHelper;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\AttemptController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ExaminationController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProcessController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\QuestionOrderController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AttemptAllowValidate;
use App\Http\Middleware\AttemptAvailableValidate;
use App\Http\Middleware\AttemptCloseValidate;
use App\Http\Middleware\AttemptFinishedValidate;
use App\Http\Middleware\AttemptValidate;
use App\Http\Middleware\CourseOpenValidate;
use App\Http\Middleware\DontHaveAttemptValidate;
use App\Http\Middleware\GradeAttemptFinished;
use App\Http\Middleware\SubmissionGradedValidate;
use App\Http\Middleware\TeacherAdminValidate;
use App\Http\Middleware\TeacherValidate;
use App\Models\Examination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [CourseController::class, 'index'])->name('homepage');

Route::get('/login', function () {
    return view('general.login');
})->name('login');

Route::post('/login', [UserController::class, 'login']);

Route::get('/logout', [UserController::class, 'logout']);

Route::get('/register', function () {
    return view('general.register');
});

Route::post('/register', [UserController::class, 'register']);

Route::post('/editmode', function (Request $request) {
    if (auth()->user()->role !== 'student') {
        session(['edit' => !$request->edit ? false : true]);
    }
    return response()->json();
});

Route::post('/toggleSidebar', function (Request $request) {
    session(['sidebar' => !session('sidebar')]);
    return response()->json();
});
Route::middleware(['auth:web'])->group(function () {
    Route::prefix('course')->group(function () {
        Route::get('/', [CourseController::class, 'getAllCourse']);

        Route::get('/enrol', function () {
            $course = (new CourseController)->getCourse(request()->query('id'));
            $course->hasOpened = !(!is_null($course->course_start) && !AppHelper::isTodayGreaterThan($course->course_start));
            return view('course.enrol', [
                'course' => $course
            ]);
        })->middleware(['enrolauth', 'student'])->name('enrol');

        Route::post('/enrol', [CourseController::class, 'selfEnrolStudent'])
            ->middleware(['student', CourseOpenValidate::class]);

        Route::post('/pay', [PaymentController::class, 'payCourse'])
            ->middleware(CourseOpenValidate::class);

        Route::middleware(['courseaccess'])->group(function () {
            Route::get('/view', [CourseController::class, 'show']);

            Route::post('/process/toggle', [ProcessController::class, 'update']);

            Route::middleware(['coursemanage'])->group(function () {
                Route::middleware(['courseexpire'])->group(function () {
                    Route::prefix('member')->group(function () {
                        Route::post('/remove', [CourseController::class, 'removeStudent']);

                        Route::post('/enrol', [CourseController::class, 'enrolStudentManual'])
                            ->middleware(CourseOpenValidate::class);
                    });
                    Route::prefix('activity')->group(function () {
                        Route::post('/update', [ActivityController::class, 'update']);
                    });

                    Route::prefix('topic')->group(function () {
                        Route::post('/store', [TopicController::class, 'store']);

                        Route::post('/update', [TopicController::class, 'update']);

                        Route::post('/delete', [TopicController::class, 'destroy']);
                    });

                    Route::post('/file/delete', [FileController::class, 'destroy']);
                    Route::post('/assign/delete', [AssignmentController::class, 'destroy']);
                    Route::post('/quiz/delete', [ExaminationController::class, 'destroy']);

                    Route::prefix('store')->group(function () {
                        Route::post('/file', [FileController::class, 'store']);

                        Route::post('/assign', [AssignmentController::class, 'store']);

                        Route::post('/quiz', [ExaminationController::class, 'store']);
                    });
                });

                Route::prefix('topic')->group(function () {
                    Route::get('/add', [TopicController::class, 'create']);

                    Route::get('/edit', [TopicController::class, 'edit']);
                });

                Route::get('/add', [ActivityController::class, 'create']);

                Route::post('/add', [CourseController::class, 'store']);

                Route::get('/edit', [CourseController::class, 'edit']);

                Route::post('/update', [CourseController::class, 'update']);

                Route::get('activity/edit', [ActivityController::class, 'edit']);

                Route::prefix('file')->group(function () {
                    Route::post('/update', [FileController::class, 'update']);
                });

                Route::prefix('assign')->group(function () {
                    Route::get('/submission', [AssignmentController::class, 'showSubmissions']);

                    Route::post('/submission/search', [AssignmentController::class, 'searchSubmissions']);

                    Route::get('/grading', [SubmissionController::class, 'show']);

                    Route::post('/grading/change', [SubmissionController::class, 'change']);

                    Route::post('/grading/update', [SubmissionController::class, 'update']);
                });

                Route::prefix('quiz')->group(function () {
                    Route::get('/result', [ExaminationController::class, 'result']);

                    Route::post('/result/getAttempts', [ExaminationController::class, 'getAttempts']);

                    Route::post('/result/search', [ExaminationController::class, 'search']);

                    Route::prefix('question')->group(function () {
                        Route::get('/', [ExaminationController::class, 'showQuestion']);

                        Route::post('/show', [QuestionController::class, 'show']);

                        Route::get('/add', [QuestionController::class, 'create']);

                        Route::middleware(DontHaveAttemptValidate::class)->group(function () {
                            Route::post('/delete', [QuestionController::class, 'destroy']);

                            Route::post('/store', [QuestionController::class, 'store']);
                        });
                    });
                });
            });

            Route::prefix('member')->group(function () {
                Route::get('/', [CourseController::class, 'getStudents']);

                Route::post('/search', [CourseController::class, 'searchStudent']);
            });

            Route::get('/file', [FileController::class, 'show']);

            Route::prefix('assign')->group(function () {
                Route::get('/', [AssignmentController::class, 'show']);

                Route::post('/submission/store', [SubmissionController::class, 'store'])
                    ->middleware(['student', 'courseexpire', 'assignexpire', SubmissionGradedValidate::class]);
            });

            Route::prefix('quiz')->middleware(GradeAttemptFinished::class)->group(function () {
                Route::get('/', [ExaminationController::class, 'show']);

                Route::prefix('attempt')->group(function () {
                    Route::post('/', [AttemptController::class, 'store'])->middleware(AttemptCloseValidate::class, AttemptAllowValidate::class);

                    Route::get('/', [AttemptController::class, 'show'])->middleware(AttemptCloseValidate::class, AttemptAllowValidate::class);

                    Route::middleware(AttemptAvailableValidate::class)->group(function () {
                        Route::post('/saveAnswer', [AttemptController::class, 'saveAnswer']);

                        Route::get('/review', [AttemptController::class, 'review']);

                        Route::post('/grading', [AttemptController::class, 'grading']);

                        Route::get('/result', [AttemptController::class, 'result'])->middleware(AttemptFinishedValidate::class);
                    });
                });
            });
        });
    });

    Route::prefix('user')->group(function () {
        Route::get('/profile', [UserController::class, 'show']);

        Route::get('/edit', [UserController::class, 'edit']);

        Route::get('/password', [UserController::class, 'editPassword']);

        Route::post('/update', [UserController::class, 'update']);

        Route::post('/updatePassword', [UserController::class, 'updatePassword']);

        Route::get('/withdraw', [TransferController::class, 'show']);

        Route::prefix('transfer')->middleware([TeacherValidate::class])->group(function () {
            Route::post('/store', [TransferController::class, 'store']);

            Route::post('/delete', [TransferController::class, 'destroy']);
        });

        Route::prefix('bank')->middleware([TeacherValidate::class])->group(function () {
            Route::get('/', [BankController::class, 'show']);

            Route::post('/store', [BankController::class, 'store']);

            Route::post('/delete', [BankController::class, 'destroy']);
        });
    });

    Route::prefix('admin')->middleware(['admin'])->group(function () {
        Route::prefix('course')->group(function () {
            Route::get('/', [CourseController::class, 'list']);

            Route::post('/search', [CourseController::class, 'search']);

            Route::post('/suspend', [CourseController::class, 'suspend']);

            Route::post('/active', [CourseController::class, 'active']);

            Route::post('/delete', [CourseController::class, 'destroy']);
        });

        Route::prefix('user')->group(function () {
            Route::get('/', [UserController::class, 'index']);

            Route::post('/search', [UserController::class, 'search']);

            Route::post('/store', [UserController::class, 'store']);

            Route::post('/suspend', [UserController::class, 'suspend']);

            Route::post('/active', [UserController::class, 'active']);

            Route::post('/delete', [UserController::class, 'destroy']);

            Route::get('/add', [UserController::class, 'create']);

            Route::get('/role', [UserController::class, 'role']);

            Route::post('/changeRole', [UserController::class, 'changeRole']);
        
            Route::get('/transfer', [TransferController::class, 'index']);
        
            Route::post('/transfer/update', [TransferController::class, 'update']);
        });
    });

    Route::get('/payment/vnpay', [PaymentController::class, 'receiveVnPayResponse']);
    Route::get('/payment/momo', function () {
        return view('course.detail');
    });
});
