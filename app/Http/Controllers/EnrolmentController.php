<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Models\Enrolment;
use App\Http\Requests\StoreEnrolmentRequest;
use App\Http\Requests\UpdateEnrolmentRequest;
use Illuminate\Support\Facades\Log;

class EnrolmentController extends Controller
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
     * @param  \App\Http\Requests\StoreEnrolmentRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store($courseId, $userId, $enrolMethod)
    {
        $enrolment = Enrolment::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->first();
            
        if (!is_null($enrolment)) {
            return $enrolment->id;
        }

        $newEnrol = new Enrolment();

        try {
            $newEnrol->course_id = $courseId;
            $newEnrol->user_id = $userId;
            $newEnrol->enrol_date = AppHelper::getCurrentTime();
            $newEnrol->enrolment_method = $enrolMethod;
            $newEnrol->save();
        } catch (\Throwable $th) {
            Log::info('Xảy ra lỗi khi thêm');
            return $th;
        }

        return $newEnrol->id;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Enrolment  $enrolment
     * @return \Illuminate\Http\Response
     */
    public function show(Enrolment $enrolment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Enrolment  $enrolment
     * @return \Illuminate\Http\Response
     */
    public function edit(Enrolment $enrolment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateEnrolmentRequest  $request
     * @param  \App\Models\Enrolment  $enrolment
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateEnrolmentRequest $request, Enrolment $enrolment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Enrolment  $enrolment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Enrolment $enrolment)
    {
        //
    }
}
