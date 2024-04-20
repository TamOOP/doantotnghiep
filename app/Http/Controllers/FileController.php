<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Models\File;
use App\Http\Requests\StoreFileRequest;
use App\Http\Requests\UpdateFileRequest;
use Illuminate\Http\Request;
use Throwable;

class FileController extends Controller
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
     * @param  \App\Http\Requests\StoreFileRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreFileRequest $request)
    {
        $topicId = $request->id;
        $activityId = (new ActivityController())->store(new Request([
            'topicId' => $topicId,
            'name' => $request->name,
            'description' => $request->description,
            'type' => 'file'
        ]));

        if ($activityId instanceof Throwable) {
            return response()->json(['error' => $activityId->getMessage()]);
        }

        $file = new File();
        $file->activity_id = $activityId;
        $file->file_path = AppHelper::storeFileOnServer($request->file, 'media/' . $topicId . '/');

        $fileName = $request->file('file')->getClientOriginalName();
        $fileType = AppHelper::discoverFileType($fileName);

        if ($fileType !== 'unknown') {
            $file->type = $fileType;
        } else {
            session()->flash('error', 'Tập tin không được hỗ trợ');
            return response()->json(['error' => 'Tập tin không được hỗ trợ']);
        }

        try {
            $file->save();
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }

        return response()->json(['success' => 'Thêm thành công']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\File  $file
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $file = File::find($request->id);

        return view('course.activity.file-overview', [
            'file' => $file
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\File  $file
     * @return \Illuminate\Http\Response
     */
    public function edit(File $file)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateFileRequest  $request
     * @param  \App\Models\File  $file
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateFileRequest $request)
    {
        $file = File::find($request->id);

        $activity = (new ActivityController())->updateReal(new Request([
            'id' => $file->activity->id,
            'name' => $request->name,
            'description' => $request->description,
        ]));

        if ($activity instanceof Throwable) {
            return response()->json(['error' => $activity->getMessage()]);
        }

        $file->file_path = $request->isDeleted == 'false'
            ? $file->file_path
            : AppHelper::storeFileOnServer($request->file, 'media/' . $file->activity->topic->id . '/');

        if ($request->has('file')) {
            $fileName = $request->file('file')->getClientOriginalName();
            $fileType = AppHelper::discoverFileType($fileName);

            if ($fileType !== 'unknown') {
                $file->type = $fileType;
            } else {
                session()->flash('error', 'Tập tin không được hỗ trợ');
                return response()->json(['error' => 'Tập tin không được hỗ trợ']);
            }
        }

        try {
            $file->save();
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }

        return response()->json(['success' => 'Sửa thành công']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\File  $file
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $file = File::find($request->id);

        $activity = (new ActivityController)->destroy($file->activity->id);

        if ($activity instanceof Throwable) {
            return response()->json(['error' => $activity->getMessage()]);
        }

        return response()->json(['success' => 'Xóa thành công tệp tin']);
    }
}
