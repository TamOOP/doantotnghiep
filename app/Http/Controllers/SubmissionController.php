<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Models\Submission;
use App\Http\Requests\StoreSubmissionRequest;
use App\Http\Requests\UpdateSubmissionRequest;
use App\Models\Assignment;
use App\Models\Enrolment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SubmissionController extends Controller
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
     * @param  \App\Http\Requests\StoreSubmissionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSubmissionRequest $request)
    {

        $userId = auth()->user()->id;
        $assignId = $request->query('id');
        $folderPath = 'file/assign/submission/' . $userId . '/';

        // xoa file da co
        if (File::isDirectory(public_path($folderPath))) {
            $files = File::allFiles(public_path($folderPath));

            foreach ($files as $file) {
                File::delete($file);
            }
        }

        // kiem tra ton tai bai nop
        try {
            $submission = Submission::where('assign_id', $assignId)
                ->where('user_id', $userId)
                ->first();
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Error when access data']);
        }

        //cap nhat hoac them moi bai nop
        if (is_null($submission)) {
            $submission = new Submission();
            $submission->assign_id = $assignId;
            $submission->user_id = $userId;
        }

        $submission->last_modified = AppHelper::getCurrentTime();
        $submission->file_path = AppHelper::storeFileOnServer($request->file, $folderPath);

        try {
            $submission->save();
            return response()->json(['success' => 'Lưu thành công']);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Error when store submission']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Submission  $submission
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $assignId = $request->query('id');
        $userId = $request->query('userId');

        $assign = (new Assignment)->getAssign($assignId);

        if (count($assign->submissions) == 0) {
            return view('course.activity.assign-grading', [
                'submission' => null
            ]);
        }
        
        if (is_null($userId)) {
            return redirect('/course/assign/grading?id=' . $assignId . '&userId=' . $assign->submissions[0]->user_id);
        } else {
            $submission = $assign->submissions()
                ->where('user_id', $userId)
                ->first();
        }

        return view('course.activity.assign-grading', [
            'submission' => $submission
        ]);
    }

    public function change(Request $request)
    {
        $request->validate([
            'action' => 'required|string|in:next,previous',
        ]);

        try {
            $submissions = Submission::where('assign_id', $request->query('id'))
                ->join('users', 'users.id', '=', 'submissions.user_id')
                ->where('status', '1')
                ->get();
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Error']);
        }

        foreach ($submissions as $index => $submission) {
            if ($submission->user_id == $request->query('userId')) {
                if ($request->action == 'next') {
                    $returnIndex = ($index + 1) % count($submissions);
                } else {
                    $returnIndex = ($index - 1 + count($submissions)) % count($submissions);
                }

                $submission = $submissions[$returnIndex];

                return response()->json(['submission' => $submission]);
                break;
            }
        }

        return response()->json(['error' => 'No submission found']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Submission  $submission
     * @return \Illuminate\Http\Response
     */
    public function edit(Submission $submission)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSubmissionRequest  $request
     * @param  \App\Models\Submission  $submission
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSubmissionRequest $request)
    {
        try {
            $submission = Submission::where('assign_id', $request->query('id'))
                ->where('user_id', $request->query('userId'))
                ->first();
        } catch (\Throwable $th) {
            return response()->json(['error' => 'No submission found']);
        }


        if (!is_null($submission)) {
            if (!is_null($request->grade)) {
                $submission->grade = $request->grade;
                $submission->last_grade = AppHelper::getCurrentTime();
            } else {
                $submission->grade = -1;
                $submission->last_grade = null;
            }

            try {
                $submission->save();
            } catch (\Throwable $th) {
                return response()->json(['error' => 'Error update submission']);
            }
        }

        return response()->json(['success' => 'Lưu điểm thành công']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Submission  $submission
     * @return \Illuminate\Http\Response
     */
    public function destroy(Submission $submission)
    {
        //
    }

    public function searchSubmissionByName($assignId, $keyword)
    {
        try {
            $submissions = Submission::where('assign_id', $assignId)
                ->join('users', 'users.id', '=', 'submissions.user_id')
                ->where('name', 'like', '%' . $keyword . '%')
                ->where('status', '1')
                ->get();
        } catch (\Throwable $th) {
            return null;
        }

        return $submissions;
    }

    public function searchSubmissionByEmail($assignId, $keyword)
    {
        try {
            $submissions = Submission::where('assign_id', $assignId)
                ->join('users', 'users.id', '=', 'submissions.user_id')
                ->where('username', 'like', '%' . $keyword . '%')
                ->where('status', '1')
                ->get();
        } catch (\Throwable $th) {
            return null;
        }

        return $submissions;
    }

    public function searchSubmissionByGradeStatus($assignId, $status)
    {
        try {
            $submissions = Submission::where('assign_id', $assignId)
                ->where('grade', ($status == 'grade' ? '>' : '='), '-1')
                ->join('users', 'users.id', '=', 'submissions.user_id')
                ->where('status', '1')
                ->get();
        } catch (\Throwable $th) {
            return null;
        }

        return $submissions;
    }
}
