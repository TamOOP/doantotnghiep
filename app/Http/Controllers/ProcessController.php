<?php

namespace App\Http\Controllers;

use App\Models\Process;
use App\Http\Requests\StoreProcessRequest;
use App\Http\Requests\UpdateProcessRequest;
use App\Models\Activity;
use Illuminate\Http\Request;
use Throwable;

class ProcessController extends Controller
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
     * @param  \App\Http\Requests\StoreProcessRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $activityId = $request->id;
        $userId = auth()->user()->id;

        $process = new Process();
        $process->activity_id = $activityId;
        $process->user_id = $userId;
        $process->marked = '0';

        try {
            $process->save();
        } catch (\Throwable $th) {
            return $th;
        }

        return $process;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Process  $process
     * @return \Illuminate\Http\Response
     */
    public function show(Process $process)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Process  $process
     * @return \Illuminate\Http\Response
     */
    public function edit(Process $process)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateProcessRequest  $request
     * @param  \App\Models\Process  $process
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $activityId = $request->id;
        $userId = auth()->user()->id;

        try {
            $process = Process::where('activity_id', $activityId)
                ->where('user_id', $userId)
                ->first();
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }


        if (is_null($process)) {
            $process = $this->store(new Request([
                'id' => $activityId
            ]));
        }

        if ($process instanceof Throwable) {
            return response()->json(['error' => $process->getMessage()]);
        }

        $process->marked = !$process->marked;
        try {
            $process->save();
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }

        return response()->json(['success' => 'success']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Process  $process
     * @return \Illuminate\Http\Response
     */
    public function destroy(Process $process)
    {
        //
    }
}
