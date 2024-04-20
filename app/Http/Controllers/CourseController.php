<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Http\Requests\SearchCourseRequest;
use App\Http\Requests\SearchStudentRequest;
use App\Models\Course;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Models\Assignment;
use App\Models\Enrolment;
use App\Models\Examination;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\MessageBag;
use Throwable;

class CourseController extends Controller
{
    private Enrolment $enrolment;

    public function __construct()
    {
        $this->enrolment = new Enrolment();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $courses = Course::all()->where('status', 1);
        return view('general.homepage', [
            'courses' => $courses
        ]);
    }

    public function list()
    {
        $courses = Course::all()->where('status', '!=', '-1');

        foreach ($courses as $course) {
            switch ($course->enrolment_method) {
                case '0':
                    $course->enrolment_method = 'Đăng ký thủ công';
                    break;

                case '1':
                    $course->enrolment_method = 'Tự tham gia';
                    break;

                case '2':
                    $course->enrolment_method = 'Thanh toán để tham gia';
                    break;
            }
        }
        return view('admin.course', [
            'courses' => $courses
        ]);
    }

    public function getAllCourse()
    {
        $user = auth()->user();

        $courses = $user->role == 'student' ? $user->courses : $user->courseTeaching;
        foreach ($courses as $course) {
            $totalActivity = 0;
            $finishedActivity = 0;
            foreach ($course->topics as $topic) {
                foreach ($topic->activities as $activity) {
                    $totalActivity++;
                    $userPivot = $activity->students()->where('user_id', $user->id)->first();
                    if (!is_null($userPivot) && $userPivot->pivot->marked) {
                        $finishedActivity++;
                    }
                }
            }

            $course->process = $totalActivity == 0 ? 0 : round($finishedActivity / $totalActivity * 100, 2);
        }

        return view('course.overview', [
            'courses' => $courses
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCourseRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCourseRequest $request)
    {
        $course = new Course();

        $course->user_id = auth()->user()->role == 'admin'
            ? $request->teacherId
            : auth()->user()->id;

        $course->name = $request->name;
        $course->description = $request->has('description') ? $request->description : null;

        $course->image_path = $request->has('image')
            ? AppHelper::saveImageOnServerAndRename($request->file('image'))
            : null;

        $course->course_start = $request->has('cb-start') ? $request->input('date-start') . ' ' . $request->input('start-hour') . ':' . $request->input('start-minute') . ':' . '00' : null;

        $course->course_end = $request->has('cb-end') ? $request->input('date-end') . ' ' . $request->input('end-hour') . ':' . $request->input('end-minute') . ':' . '00' : null;

        $course->enrolment_method = $request->input('enrolment-method');
        $course->payment_cost = $request->input('enrolment-method') === '2'
            ? str_replace(',', '', $request->input('course-fee')) : 0;

        $course->date_modified = AppHelper::getCurrentTime();

        try {
            $course->save();
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }

        return response()->json([
            'success' => 'Thêm thành công',
            'redirect' => '/course/view?id=' . $course->id
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $course = $this->getCourse($request->query('id'));
        $userId = auth()->user()->id;

        foreach ($course->topics as $topics) {
            foreach ($topics->activities as $activity) {
                $user = $activity->students()->where('user_id', $userId)->first();
                if (is_null($user) || !$user->pivot->marked) {
                    $activity->marked = 0;
                } else {
                    $activity->marked = 1;
                }
            }
        }
        return view('course.detail', [
            'course' => $course
        ]);
    }

    public function getStudents(Request $request)
    {
        $course = $this->getCourse($request->query('id'));
        $course->hasOpened = !(!is_null($course->course_start) && !AppHelper::isTodayGreaterThan($course->course_start));

        $students = $course->students;
        foreach ($students as $student) {
            $student->pivot->last_access = $this->parseTimeToString(AppHelper::calcTimeDiff($student->pivot->last_access));
        }

        return view('course.member', [
            'course' => $course,
            'students' => $students
        ]);
    }

    public function removeStudent(Request $request)
    {
        $request->validate([
            'course_id' => 'required',
            'student_id' => 'required'
        ]);

        try {
            $enrolment = $this->enrolment->getEnrolment($request->course_id, $request->student_id);

            if ($enrolment === null) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không có học sinh trong khóa học'
                ]);
            }

            $enrolment->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Xóa thành công'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không thể truy cập dữ liệu'
            ]);
        }
    }

    public function search(SearchCourseRequest $request)
    {

        switch ($request->input('search-condition')) {
            case 'name':
                if ($request->has('search-keyword')) {
                    $courses = $this->searchCourseByName($request->input('search-keyword'));
                }
                break;

            case 'method':

                if ($request->has('method-option')) {
                    $courses = $this->searchCourseByEnrolMethod($request->input('method-option'));
                }
                break;

            case 'teacher':
                if ($request->has('teacherId')) {
                    $courses = $this->searchCourseByTeacher($request->teacherId);
                }
                break;
        }

        if ($courses instanceof Throwable) {
            return response()->error(['error' => $courses->getMessage()]);
        }

        if (!isset($courses)) {
            $courses = Course::where('status', '!=', '-1')->get();
        }

        if (!is_null($courses)) {
            foreach ($courses as $course) {
                $course->teacher = $course->teacher->name;
            }
            return response()->json(['courses' => $courses]);
        }

        return response()->json(['error' => 'Không tìm thấy khóa học']);
    }

    public function searchStudent(Request $request)
    {
        $course_id = $request->course_id;
        $isAll = false;

        switch ($request->input('search-condition')) {
            case 'name':
                if ($request->input('search-keyword') !== null) {
                    $students = $this->searchStudentByName($course_id, $request->input('search-keyword'));
                }
                break;
            case 'email':
                if ($request->input('search-keyword') !== null) {
                    $students = $this->searchStudentByEmail($course_id, $request->input('search-keyword'));
                }
                break;
            case 'enrol-method':
                if ($request->input('method-option') !== null) {
                    $students = $this->searchStudentByEnrolMethod($course_id, $request->input('method-option'));
                }
                break;
            case 'last-active':
                if ($request->input('time-option') !== null) {
                    $students = $this->searchStudentInactiveMoreThan($course_id, $request->input('time-option'));
                }
                break;
            default:
                $users = $this->searchUserForEnrol($course_id, $request->keyword);

                if (!empty($request->input('exist_user'))) {
                    $existUsers = $request->input('exist_user');

                    $users = array_filter($users, function ($users) use ($existUsers) {
                        foreach ($existUsers as $existUser) {
                            if ($users->id == $existUser['id']) {
                                return false;
                            }
                        }
                        return true;
                    });
                }

                return response()->json([
                    'users' => $users
                ]);

                break;
        }

        if (!isset($students)) {
            $isAll = true;
            $students = $this->getCourse($course_id)->students;
        }

        foreach ($students as $student) {
            $student->last_access = $this->parseTimeToString(AppHelper::calcTimeDiff($isAll ? $student->pivot->last_access : $student->last_access));
        }



        return response()->json([
            'students' => $students,
        ]);
    }

    public function enrolStudentManual(Request $request)
    {
        $request->validate([
            'course_id' => 'required',
            'users' => 'required|array'
        ]);

        $courseId = $request->course_id;

        Log::info($courseId);

        foreach ($request->users as $user) {
            Log::info($user['id']);
            if (
                $this->enrolment->getEnrolment($courseId, $user['id'])
                || $user['role'] !== 'student'
            ) {
                continue;
            }

            (new EnrolmentController)->store($courseId, $user['id'], '0');
        }

        return response()->json(['status' => 'success']);
    }

    public function selfEnrolStudent(Request $request)
    {
        $request->validate([
            'course_id' => 'required'
        ]);

        if ($this->enrolment->getEnrolment($request->course_id, $request->student_id)) {
            return response()->json(['status' => 'fail']);
        }

        $enrolment = (new EnrolmentController)->store($request->course_id, auth()->user()->id, '1');

        return response()->json([
            'status' => !$enrolment ? 'fail' : 'success'
        ]);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $course = $this->getCourse($request->query('id'));

        $course->course_start = AppHelper::splitDateTime($course->course_start);
        $course->course_end = AppHelper::splitDateTime($course->course_end);

        return view('course.edit', [
            'course' => $course
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCourseRequest  $request
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCourseRequest $request)
    {
        $course = $this->getCourse($request->query('id'));

        if ($course) {
            $course->name = $request->name;
            $course->description = $request->has('description') ? $request->description : null;

            $course->image_path = $request->has('image')
                ? AppHelper::saveImageOnServerAndRename($request->file('image'))
                : ($request->isDeleted == 'true' ? null : $course->image_path);

            $course->course_start = $request->has('cb-start') ? $request->input('date-start') . ' ' . $request->input('start-hour') . ':' . $request->input('start-minute') . ':' . '00' : null;

            $course->course_end = $request->has('cb-end') ? $request->input('date-end') . ' ' . $request->input('end-hour') . ':' . $request->input('end-minute') . ':' . '00' : null;

            $course->enrolment_method = $request->input('enrolment-method');
            $course->payment_cost = $request->input('enrolment-method') === '2'
                ? str_replace(',', '', $request->input('course-fee')) : 0;

            $course->date_modified = AppHelper::getCurrentTime();

            try {
                $course->save();
            } catch (\Throwable $th) {
                return response()->json(['error' => $th->getMessage()]);
            }
        }

        return response()->json([
            'status' => 'success'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $course = Course::find($request->id);

        $course->status = '-1';
        try {
            $course->save();
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }

        return response()->json(['success' => 'Đã xóa khóa học ' . $course->name]);
    }

    public function suspend(Request $request)
    {
        $course = Course::find($request->id);

        $course->status = '0';
        try {
            $course->save();
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }

        return response()->json(['success' => 'Đã đình chỉ khóa học ' . $course->name]);
    }

    public function active(Request $request)
    {
        $course = Course::find($request->id);

        $course->status = '1';
        try {
            $course->save();
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }

        return response()->json(['success' => 'Đã kích hoạt khóa học ' . $course->name]);
    }

    public function getCourse($id)
    {
        try {
            $course = Course::find($id);

            if (!$course) {
                throw new Exception('Không có khóa học');
            } elseif ($course->status != 1) {
                throw new Exception('Khóa học đã xóa');
            }

            return $course;
        } catch (Exception $e) {
            session()->flash('error', $e->getMessage());

            return null;
        }
    }

    protected function parseTimeToString($timeDiff)
    {
        if (is_null($timeDiff)) {
            return null;
        }

        $timeDiffString = ($timeDiff->days !== 0 ? $timeDiff->days . ' ngày ' : '')
            . ($timeDiff->h !== 0 ? $timeDiff->h . ' giờ ' : '')
            . ($timeDiff->i !== 0 ? $timeDiff->i . ' phút ' : '')
            . $timeDiff->s . ' giây trước';

        return $timeDiffString;
    }

    protected function addMessageError($message)
    {
        $errors = new MessageBag();
        $errors->add($message, $message);
        return $errors;
    }

    protected function searchCourseByName($keyword)
    {
        try {
            $course = Course::where('name', 'like', '%' . $keyword . '%')
                ->where('status', '!=', '-1')
                ->get();

            return $course;
        } catch (\Throwable $th) {
            return $th;
        }
    }

    protected function searchCourseByEnrolMethod($method)
    {
        try {
            $course = Course::where('enrolment_method', $method)
                ->where('status', '!=', '-1')
                ->get();

            return $course;
        } catch (\Throwable $th) {
            return $th;
        }
    }

    protected function searchCourseByTeacher($teacherId)
    {
        try {
            $course = Course::where('user_id', $teacherId)
                ->where('status', '!=', '-1')
                ->get();

            return $course;
        } catch (\Throwable $th) {
            return $th;
        }
    }

    protected function searchStudentByName($course_id, $keyword)
    {
        try {
            $users = User::where('name', 'like', '%' . $keyword . '%')
                ->where('status', '1')
                ->where('role', 'student')
                ->join('enrolments', 'users.id', '=', 'enrolments.user_id')
                ->where('course_id', $course_id)
                ->select('users.*', 'enrolments.last_access', 'enrolments.enrolment_method', 'enrolments.enrol_date')
                ->get();

            return $users;
        } catch (\Throwable $th) {
            return null;
        }
    }

    protected function searchStudentByEmail($course_id, $keyword)
    {
        try {
            $users = User::where('username', 'like', '%' . $keyword . '%')
                ->where('status', '1')
                ->where('role', 'student')
                ->join('enrolments', 'users.id', '=', 'enrolments.user_id')
                ->where('course_id', $course_id)
                ->select('users.*', 'enrolments.last_access', 'enrolments.enrolment_method', 'enrolments.enrol_date')
                ->get();
            return $users;
        } catch (\Throwable $th) {
            return null;
        }
    }
    protected function searchStudentByEnrolMethod($course_id, $method_id)
    {
        try {
            $users = User::where('status', '1')
                ->where('role', 'student')
                ->join('enrolments', 'users.id', '=', 'enrolments.user_id')
                ->where('course_id', $course_id)
                ->where('enrolment_method', $method_id)
                ->select('users.*', 'enrolments.last_access', 'enrolments.enrolment_method', 'enrolments.enrol_date')
                ->get();

            return $users;
        } catch (\Throwable $th) {
            return null;
        }
    }

    protected function searchStudentInactiveMoreThan($course_id, $time)
    {
        $date = Carbon::now()->setTimezone('Asia/Bangkok')
            ->modify('-' . $time)
            ->format('Y-m-d H:i:s');

        try {
            $users = User::where('status', '1')
                ->where('role', 'student')
                ->join('enrolments', 'users.id', '=', 'enrolments.user_id')
                ->where('course_id', $course_id)
                ->where('last_access', '<=', $date)
                ->select('users.*', 'enrolments.last_access', 'enrolments.enrolment_method', 'enrolments.enrol_date')
                ->get();

            return $users;
        } catch (\Throwable $th) {
            return null;
        }
    }

    protected function searchUserForEnrol($course_id, $keyword)
    {
        try {
            $usersWithName = User::where('name', 'like', '%' . $keyword . '%')
                ->where('role', 'student')
                ->where('status', '1')
                ->leftJoin('enrolments', function ($join) use ($course_id) {
                    $join->on('users.id', '=', 'enrolments.user_id')
                        ->where('enrolments.course_id', '=', $course_id);
                })
                ->whereNull('enrolments.user_id')
                ->select('users.*')
                ->get();

            $usersWithEmail = User::where('username', 'like', '%' . $keyword . '%')
                ->where('role', 'student')
                ->where('status', '1')
                ->leftJoin('enrolments', function ($join) use ($course_id) {
                    $join->on('users.id', '=', 'enrolments.user_id')
                        ->where('enrolments.course_id', '=', $course_id);
                })
                ->whereNull('enrolments.user_id')
                ->select('users.*')
                ->get();
        } catch (\Throwable $th) {
            return null;
        }


        $groupedUsers = $usersWithName->merge($usersWithEmail)->groupBy('id');

        $users = $groupedUsers->map(function ($group) {
            return $group->last();
        })->values()->all();

        return $users;
    }
}
