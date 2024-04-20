<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Models\Activity;
use App\Http\Requests\StoreActivityRequest;
use App\Http\Requests\UpdateActivityRequest;
use App\Http\Requests\UpdateAssignmentRequest;
use App\Http\Requests\UpdateExaminationRequest;
use App\Http\Requests\UpdateQuestionRequest;
use App\Models\Assignment;
use App\Models\Examination;
use App\Models\File;
use App\Models\Question;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class ActivityController extends Controller
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
    public function create(Request $request)
    {
        if (!is_null($request->id)) {
            $topic = Topic::find($request->id);
        }

        return view('course.add', [
            'course' => isset($topic) ? $topic->course : null
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreActivityRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $activity = new Activity();
        $activity->topic_id = $request->topicId;
        $activity->name = $request->name;
        $activity->description = $request->description;
        $activity->type = $request->type;

        try {
            $activity->save();
        } catch (\Throwable $th) {
            return $th;
        }

        $process = (new ProcessController)->store(new Request([
            'id' => $activity->id
        ]));

        if ($process instanceof Throwable) {
            return response()->json(['error' => $process->getMessage()]);
        }
        
        return $activity->id;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $returnValue = [];
        switch ($request->query('type')) {
            case 'assign':
                $id = $request->query('id');
                $assign = (new Assignment)->getAssign($id);
                $assign->time_start = AppHelper::splitDateTime($assign->time_start);
                $assign->time_end = AppHelper::splitDateTime($assign->time_end);

                $returnValue['assign'] = $assign;

                break;

            case 'quiz':
                $id = $request->query('id');
                $exam = (new Examination())->getExam($id);
                $exam->time_start = AppHelper::splitDateTime($exam->time_start);
                $exam->time_end = AppHelper::splitDateTime($exam->time_end);

                $returnValue['exam'] = $exam;

                break;

            case 'question':
                $questionId = $request->query('id');

                $question = Question::find($questionId);

                $exam = $question->exam;

                $returnValue['exam'] = $exam;
                $returnValue['question'] = $question;

                break;

            case 'file':
                $file = File::find($request->query('id'));

                $returnValue['file'] = $file;
                break;
            default:
                return response()->json(['error' => 'Loại hoạt động không tồn tại'], 400);
                break;
        }

        return view('course.activity.edit', $returnValue);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateActivityRequest  $request
     * @param  \App\Models\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        switch ($request->query('type')) {
            case 'assign':
                $request->validate([
                    'name' => 'required'
                ]);

                $id = $request->id;
                $assign = (new Assignment)->getAssign($id);

                $assign->activity->name = $request->name;
                $assign->activity->description = $request->has('description') ? $request->description : null;
                try {
                    $assign->activity->save();
                } catch (\Throwable $th) {
                    return response()->json(['error' => 'Error access data'], 400);
                }

                return (new AssignmentController)->update(new UpdateAssignmentRequest($request->toArray()));
                break;

            case 'quiz':
                $request->validate([
                    'name' => 'required'
                ]);

                $id = $request->id;
                $exam = (new Examination())->getExam($id);

                $exam->activity->name = $request->name;
                $exam->activity->description = $request->has('description') ? $request->description : null;
                try {
                    $exam->activity->save();
                } catch (\Throwable $th) {
                    return response()->json(['error' => 'Error access data'], 400);
                }

                return (new ExaminationController)->update(new UpdateExaminationRequest($request->toArray()));
                break;

            case 'question':
                return (new QuestionController)->update(new UpdateQuestionRequest($request->toArray()));
                break;

            default:
                return response()->json(['error' => 'Invalid type parameter'], 400);
                break;
        }
    }

    public function updateReal(Request $request)
    {
        $activity = Activity::find($request->id);

        $activity->name = $request->name;
        $activity->description = $request->description;

        try {
            $activity->save();
        } catch (\Throwable $th) {
            return $th;
        }

        return $activity;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $activity = Activity::find($id);

        $activity->status = '-1';

        try {
            $activity->save();
        } catch (\Throwable $th) {
            return $th;
        }

        return $activity;
    }
}
