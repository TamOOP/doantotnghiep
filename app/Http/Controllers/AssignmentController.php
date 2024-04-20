<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Http\Requests\SearchStudentRequest;
use App\Http\Requests\SearchSubmissionRequest;
use App\Models\Assignment;
use App\Http\Requests\StoreAssignmentRequest;
use App\Http\Requests\UpdateAssignmentRequest;
use App\Models\Submission;
use App\Models\Topic;
use DateTime;
use DateTimeZone;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Throwable;

class AssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \App\Http\Requests\StoreAssignmentRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAssignmentRequest $request)
    {
        $topicId = $request->id;
        $activityId = (new ActivityController())->store(new Request([
            'topicId' => $request->id,
            'name' => $request->name,
            'description' => $request->description,
            'type' => 'assign'
        ]));

        if (is_null($activityId)) {
            return response()->json(['error' => 'Có lỗi khi lưu hoạt động']);
        }

        $assign = new Assignment();
        $assign->activity_id = $activityId;
        $assign->grade_pass = $request->input('grade-pass');
        $assign->max_grade = $request->input('max-grade');

        $assign->file_path = $request->has('file') ? AppHelper::storeFileOnServer($request->file, 'file/assign/' . $topicId . '/') : null;
        $assign->time_start = $request->has('cb-start') ? $request->input('date-start') . ' ' . $request->input('start-hour') . ':' . $request->input('start-minute') . ':' . '00' : null;
        $assign->time_end = $request->has('cb-end') ? $request->input('date-end') . ' ' . $request->input('end-hour') . ':' . $request->input('end-minute') . ':' . '00' : null;

        try {
            $assign->save();
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
        return response()->json(['success' => 'Thêm thành công bài tập']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Assignment  $assignment
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $assign = Assignment::find($request->query('id'));

        $assign->allowSubmit = !$this->isAssignClose($assign) && $this->isAssignStart($assign);
        $assign->timeRemain = $assign->allowSubmit ? AppHelper::calcTimeDiff($assign->time_end) : null;
        
        $assign->time_start = AppHelper::formatDateTime($assign->time_start, 'd/m/Y, h:i A');
        $assign->time_end = AppHelper::formatDateTime($assign->time_end, 'd/m/Y, h:i A');
        $assign->notGradeSubmissions = $assign->submissions()->where('grade', '-1')->get();

        if (auth()->user()->role == 'student') {
            try {
                $submission = Submission::where('assign_id', $request->query('id'))
                    ->where('user_id', auth()->user()->id)
                    ->first();

                if (!is_null($submission)) {
                    $submission->last_modified = AppHelper::formatDateTime($submission->last_modified, 'd/m/Y, h:i A');
                    $submission->last_grade = AppHelper::formatDateTime($submission->last_grade, 'd/m/Y, h:i A');
                }
            } catch (\Throwable $th) {
            }
        }

        return view('course.activity.assign-overview', [
            'assign' => $assign,
            'submission' => isset($submission) ? $submission : null
        ]);
    }

    public function showSubmissions(Request $request)
    {
        $assign = Assignment::find($request->query('id'));
        foreach ($assign->submissions as $submission) {
            $submission->last_modified = AppHelper::formatDateTime($submission->last_modified, 'd/m/Y, h:i A');
            $submission->last_grade = AppHelper::formatDateTime($submission->last_grade, 'd/m/Y, h:i A');
        }


        return view('course.activity.assign-submission', [
            'assign' => $assign,
        ]);
    }

    public function searchSubmissions(SearchSubmissionRequest $request)
    {
        $assignId = $request->query('id');

        switch ($request->input('search-condition')) {
            case 'name':
                if (!is_null($request->input('search-keyword'))) {
                    $submissions = (new SubmissionController)->searchSubmissionByName($assignId, $request->input('search-keyword'));
                }
                break;

            case 'email':
                if (!is_null($request->input('search-keyword'))) {
                    $submissions = (new SubmissionController)->searchSubmissionByEmail($assignId, $request->input('search-keyword'));
                }
                break;

            case 'grade-status':
                if (!is_null($request->input('search-option'))) {
                    $submissions = (new SubmissionController)->searchSubmissionByGradeStatus($assignId, $request->input('search-option'));
                }
                break;

            default:
                return response()->json(['error' => 'Error search']);
                break;
        }

        if (!isset($submissions)) {
            $submissions = Submission::where('assign_id', $assignId)
                ->join('users', 'users.id', '=', 'submissions.user_id')
                ->where('status', '1')
                ->get();
        }

        foreach ($submissions as $submission) {
            $submission->last_modified = AppHelper::formatDateTime($submission->last_modified, 'd/m/Y, h:i A');
            $submission->last_grade = AppHelper::formatDateTime($submission->last_grade, 'd/m/Y, h:i A');
        }

        return response()->json([
            'submissions' => $submissions
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Assignment  $assignment
     * @return \Illuminate\Http\Response
     */
    public function edit(Assignment $assignment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateAssignmentRequest  $request
     * @param  \App\Models\Assignment  $assignment
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAssignmentRequest $request)
    {
        $id = $request->id;
        $assign = (new Assignment)->getAssign($id);

        if (!is_null($assign)) {
            $assign->grade_pass = $request->input('grade-pass');
            $assign->max_grade = $request->input('max-grade');

            $assign->file_path = $request->has('file') ? AppHelper::storeFileOnServer($request->file, 'file/assign/' . $assign->activity->topic->id . '/') : ($request->isDeleted == 'true' ? null : $assign->file_path);
            $assign->time_start = $request->has('cb-start') ? $request->input('date-start') . ' ' . $request->input('start-hour') . ':' . $request->input('start-minute') . ':' . '00' : null;
            $assign->time_end = $request->has('cb-end') ? $request->input('date-end') . ' ' . $request->input('end-hour') . ':' . $request->input('end-minute') . ':' . '00' : null;

            try {
                $assign->save();
            } catch (Exception $e) {
                return response()->json(['error' => $e->getMessage()], 400);
            }
        }
        return response()->json(['success' => 'Sửa thành công']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Assignment  $assignment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $assign = Assignment::find($request->id);

        $activity = (new ActivityController)->destroy($assign->activity->id);

        if ($activity instanceof Throwable) {
            return response()->json(['error' => $activity->getMessage()]);
        }

        return response()->json(['success' => 'Xóa thành công bài tập']);
    }

    protected function isAssignClose(Assignment $assign): bool
    {
        if (is_null($assign->time_end)) {
            return false;
        }

        return AppHelper::isTodayGreaterThan($assign->time_end);
    }

    protected function isAssignStart(Assignment $assign): bool
    {
        if (is_null($assign->time_start)) {
            return true;
        }

        return AppHelper::isTodayGreaterThan($assign->time_start);
    }
}
