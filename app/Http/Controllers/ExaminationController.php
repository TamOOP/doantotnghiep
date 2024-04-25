<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Http\Requests\SearchStudentAttemptExamRequest;
use App\Http\Requests\SearchStudentAttemptRequest;
use App\Http\Requests\StoreAttemptRequest;
use App\Models\Examination;
use App\Http\Requests\StoreExaminationRequest;
use App\Http\Requests\UpdateExaminationRequest;
use App\Models\Assignment;
use App\Models\Attempt;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Throwable;

class ExaminationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
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
     * @param  \App\Http\Requests\StoreExaminationRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreExaminationRequest $request)
    {
        $activityId = (new ActivityController())->store(new Request([
            'topicId' => $request->id,
            'name' => $request->name,
            'description' => $request->description,
            'type' => 'exam'
        ]));

        if ($activityId instanceof Throwable) {
            return response()->json(['error' => $activityId->getMessage()]);
        }

        $exam = new Examination();
        $exam->activity_id = $activityId;
        $exam->password = $request->has('cb-password') ? $request->password : null;
        $exam->time_start = $request->has('cb-start') ? $request->input('date-start') . ' ' . $request->input('start-hour') . ':' . $request->input('start-minute') . ':' . '00' : null;
        $exam->time_end = $request->has('cb-end') ? $request->input('date-end') . ' ' . $request->input('end-hour') . ':' . $request->input('end-minute') . ':' . '00' : null;
        $exam->time_limit = ($request->has('cb-limit') && $request->has('time-limit') && $request->has('time-unit')) ? $request->input('time-limit') : 0;
        $exam->time_unit = ($request->has('cb-limit') && $request->has('time-limit') && $request->has('time-unit')) ? $request->input('time-unit') : null;
        $exam->grade_pass = $request->input('grade-pass');
        $exam->grade_scale = $request->input('grade-scale');
        $exam->attempt_allow = $request->input('attempt-allow');
        $exam->grading_method = $request->input('grading-method');
        $exam->question_per_page = $request->input('question-per-page');
        $exam->random_answer = $request->input('random-answer');
        $exam->shuffle_question = $request->input('shuffle-question');
        $exam->show_answer = $request->input('show-answer');

        try {
            $exam->save();
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        return response()->json(['success' => 'Thêm thành công bài thi']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Examination  $examination
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $userId = auth()->user()->id;
        $examId = $request->query('id');

        $exam = (new Examination())->getExam($examId);

        $exam->allow = !$this->isExamClose($exam) && $this->isExamStart($exam);
        $exam->time_start = AppHelper::formatDateTime($exam->time_start, 'd/m/Y, h:i A');
        $exam->time_end = AppHelper::formatDateTime($exam->time_end, 'd/m/Y, h:i A');
        if (!is_null($exam->time_unit)) {
            $totalSec = $exam->time_unit * $exam->time_limit;
            $now = Carbon::now();
            $interval = $now->copy()->addSeconds($totalSec)->diff($now);

            $years = $interval->y != 0 ? $interval->y . ' năm ' : '';
            $months = $interval->m != 0 ? $interval->m . ' tháng ' : '';
            $days = $interval->d != 0 ? $interval->d . ' ngày ' : '';
            $hours = $interval->h != 0 ? $interval->h . ' giờ ' : '';
            $minutes = $interval->i != 0 ? $interval->i . ' phút ' : '';
            $seconds = $interval->s != 0 ? $interval->s . ' giây ' : '';

            $exam->time_limit = $years . $months . $days . $hours . $minutes . $seconds;
        }

        switch ($exam->grading_method) {
            case '0':
                $exam->grading_method = 'Điểm cao nhất';
                break;

            case '1':
                $exam->grading_method = 'Điểm trung bình';
                break;

            case '2':
                $exam->grading_method = 'Lần làm đầu tiên';
                break;

            case '3':
                $exam->grading_method = 'Lần làm cuối cùng';
                break;
            default:
                return response()->json();
                break;
        }

        $attempts = Attempt::where('user_id', $userId)
            ->where('exam_id', $examId)
            ->get();

        $exam->attemptNotFinish = (new Attempt())->getAttemptNotFinished($examId, $userId);

        foreach ($attempts as $attempt) {
            $attemptFormat = (new AttemptController)->formatAttemptAttr($attempt->id);
            if (AppHelper::isTodayGreaterThan($attempt->time_end)) {
                $attempt->time_end = $attemptFormat->time_end;
                $attempt->work_time = $attemptFormat->work_time;
                $attempt->status = 'Hoàn thành';
            } else {
                $attempt->status = 'Đang tiến hành';
                $attempt->time_end = null;
            }
            $attempt->time_start = $attemptFormat->time_start;
        }

        return view('course.activity.quiz-overview', [
            'exam' => $exam,
            'attempts' => $attempts
        ]);
    }

    public function showQuestion(Request $request)
    {
        $exam = (new Examination())->getExam($request->query('id'));
        $totalMark = 0;

        foreach ($exam->questions as $question) {
            $totalMark += $question->mark;
        }

        $exam->total_mark = $totalMark;

        return view('course.activity.quiz-question', [
            'exam' => $exam
        ]);
    }

    public function result(Request $request)
    {
        $examId = $request->query('id');

        $exam = (new Examination())->getExam($examId);

        $students =  $exam->users->groupBy('id')->map(function ($group) {
            return $group->last();
        })->values()->all();

        foreach ($students as $student) {
            $student->finalGrade = $this->getStudentFinalGrade($student, $exam);
            $student->status = $student->finalGrade >= $exam->grade_pass ? 'Qua' : 'Trượt';
        }

        switch ($exam->grading_method) {
            case '0':
                $exam->grading_method = 'Điểm cao nhất';
                break;

            case '1':
                $exam->grading_method = 'Điểm trung bình';
                break;

            case '2':
                $exam->grading_method = 'Lần làm đầu tiên';
                break;

            case '3':
                $exam->grading_method = 'Lần làm cuối cùng';
                break;
            default:
                return response()->json();
                break;
        }

        $gradeScale = $exam->grade_scale;
        $gradeStatis = [];
        $gradeGap = $gradeScale / 10;
        for ($i = $gradeGap; $i <= $gradeScale; $i += $gradeGap) {
            $count = 0;
            foreach ($students as $student) {
                if ($student->finalGrade >= $i - $gradeGap && $student->finalGrade <= $i) {
                    $count++;
                }
            }
            $key = $i - $gradeGap . '-' . $i;
            $gradeStatis[$key] = $count;
        }

        return view('course.activity.quiz-result', [
            'exam' => $exam,
            'students' => $students,
            'gradeStatis' => $gradeStatis
        ]);
    }

    public function search(SearchStudentAttemptRequest $request)
    {
        $examId = $request->query('id');

        $exam = Examination::find($examId);

        switch ($request->input('search-condition')) {
            case 'name':
                if (!is_null($request->input('search-keyword'))) {
                    $students = $this->searchStudentByName($examId, $request->input('search-keyword'));
                }
                break;

            case 'email':
                if (!is_null($request->input('search-keyword'))) {
                    $students = $this->searchStudentByEmail($examId, $request->input('search-keyword'));
                }
                break;

            case 'result':
                if (!is_null($request->input('status-option'))) {
                    $students = $this->searchStudentByStatus($examId, $request->input('status-option'));
                }
                break;

            case 'grade-morethan':
                if (!is_null($request->input('grade-option'))) {
                    $students = $this->searchStudentGradeMoreThan($examId, $request->input('grade-option'));
                }
                break;

            default:
                return response()->json(['error' => 'Missing field require']);
                break;
        }

        foreach ($students as $student) {
            $student->finalGrade = $this->getStudentFinalGrade($student, $exam);
            $student->status = $student->finalGrade >= $exam->grade_pass ? 'Qua' : 'Trượt';
        }

        return response()->json([
            'students' => $students
        ]);
    }



    public function getAttempts(Request $request)
    {
        $examId = $request->query('id');
        $userId = $request->query('userId');

        $attempts = Attempt::where('user_id', $userId)
            ->where('exam_id', $examId)
            ->get();

        foreach ($attempts as $attempt) {
            $attemptFormat = (new AttemptController)->formatAttemptAttr($attempt->id);
            if (AppHelper::isTodayGreaterThan($attempt->time_end)) {
                $attempt->time_end = $attemptFormat->time_end;
                $attempt->work_time = $attemptFormat->work_time;
                $attempt->status = 'Hoàn thành';
            } else {
                $attempt->status = 'Đang tiến hành';
                $attempt->time_end = null;
            }
            $attempt->time_start = $attemptFormat->time_start;
        }

        return response()->json(['attempts' => $attempts]);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Examination  $examination
     * @return \Illuminate\Http\Response
     */
    public function edit(Examination $examination)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateExaminationRequest  $request
     * @param  \App\Models\Examination  $examination
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateExaminationRequest $request)
    {
        $id = $request->id;
        $exam = (new Examination())->getExam($id);

        if (!is_null($exam)) {
            $exam->password = $request->has('cb-password') ? $request->password : null;
            $exam->time_start = $request->has('cb-start') ? $request->input('date-start') . ' ' . $request->input('start-hour') . ':' . $request->input('start-minute') . ':' . '00' : null;
            $exam->time_end = $request->has('cb-end') ? $request->input('date-end') . ' ' . $request->input('end-hour') . ':' . $request->input('end-minute') . ':' . '00' : null;
            $exam->time_limit = ($request->has('cb-limit') && $request->has('time-limit') && $request->has('time-unit')) ? $request->input('time-limit') : 0;
            $exam->time_unit = ($request->has('cb-limit') && $request->has('time-limit') && $request->has('time-unit')) ? $request->input('time-unit') : null;
            $exam->grade_pass = $request->input('grade-pass');
            $exam->grade_scale = $request->input('grade-scale');
            $exam->attempt_allow = $request->input('attempt-allow');
            $exam->grading_method = $request->input('grading-method');
            $exam->question_per_page = $request->input('question-per-page');
            $exam->random_answer = $request->input('random-answer');
            $exam->shuffle_question = $request->input('shuffle-question');
            $exam->show_answer = $request->input('show-answer');

            try {
                $exam->save();
            } catch (Exception $e) {
                return response()->json(['error' => $e->getMessage()], 400);
            }
        }
        return response()->json(['success' => 'Sửa thành công']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Examination  $examination
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $exam = Examination::find($request->id);

        $activity = (new ActivityController)->destroy($exam->activity->id);

        if ($activity instanceof Throwable) {
            return response()->json(['error' => $activity->getMessage()]);
        }

        return response()->json(['success' => 'Xóa thành công bài thi']);
    }

    protected function searchStudentByName($examId, $keyword)
    {
        $students = User::where('name', 'like', '%' . $keyword . '%')
            ->join('attempts', 'users.id', '=', 'attempts.user_id')
            ->where('exam_id', $examId)
            ->get('users.*');

        $students =  $students->groupBy('id')->map(function ($group) {
            return $group->last();
        })->values()->all();

        return $students;
    }

    protected function searchStudentByEmail($examId, $keyword)
    {
        $students = User::where('username', 'like', '%' . $keyword . '%')
            ->join('attempts', 'users.id', '=', 'attempts.user_id')
            ->where('exam_id', $examId)
            ->get('users.*');

        $students =  $students->groupBy('id')->map(function ($group) {
            return $group->last();
        })->values()->all();

        return $students;
    }

    protected function searchStudentByStatus($examId, $status)
    {
        $students = User::join('attempts', 'users.id', '=', 'attempts.user_id')
            ->where('exam_id', $examId)
            ->get('users.*');

        $students =  $students->groupBy('id')->map(function ($group) {
            return $group->last();
        })->values()->all();

        $exam = Examination::find($examId);

        $students =  collect($students)->filter(function ($student) use ($exam, $status) {
            $finalGrade = $this->getStudentFinalGrade($student, $exam);
            return ($status && $finalGrade >= $exam->grade_pass) || (!$status && $finalGrade < $exam->grade_pass);
        })->all();

        return $students;
    }

    protected function searchStudentGradeMoreThan($examId, $grade)
    {
        $students = User::join('attempts', 'users.id', '=', 'attempts.user_id')
            ->where('exam_id', $examId)
            ->get('users.*');

        $students =  $students->groupBy('id')->map(function ($group) {
            return $group->last();
        })->values()->all();

        $exam = Examination::find($examId);

        $students =  collect($students)->filter(function ($student) use ($exam, $grade) {
            $finalGrade = $this->getStudentFinalGrade($student, $exam);
            return $finalGrade >= $grade;
        })->all();

        return $students;
    }

    protected function getStudentFinalGrade(User $student, Examination $exam)
    {
        $finalGrade = 0;
        $attempts = $student->attempts()->where('exam_id', $exam->id)->get();

        switch ($exam->grading_method) {
            case '0':
                foreach ($attempts as $attempt) {
                    $finalGrade = $attempt->final_grade > $finalGrade ? $attempt->final_grade : $finalGrade;
                }
                break;

            case '1':
                foreach ($attempts as $i => $attempt) {
                    $finalGrade += $attempt->final_grade;
                }
                $finalGrade /= count($attempts);

                break;

            case '2':
                $finalGrade = $attempts[0];
                break;

            case '3':
                $finalGrade = $attempts[count($attempts) - 1];
                break;
        }

        return round($finalGrade, 2);
    }

    protected function isExamClose(Examination $exam): bool
    {
        if (is_null($exam->time_end)) {
            return false;
        }

        return AppHelper::isTodayGreaterThan($exam->time_end);
    }

    protected function isExamStart(Examination $exam): bool
    {
        if (is_null($exam->time_start)) {
            return true;
        }

        return AppHelper::isTodayGreaterThan($exam->time_start);
    }
}
