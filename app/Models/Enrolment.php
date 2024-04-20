<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrolment extends Model
{
    use HasFactory;
    public $timestamps = false;

    public function student() : BelongsTo 
    {
        return $this->belongsTo(User::class);
    }

    public function course() : BelongsTo 
    {
        return $this->belongsTo(Course::class);
    }

    public function getEnrolment($course_id, $student_id)
    {
        try {
            $enrolment = Enrolment::where('course_id', $course_id)
                ->where('user_id', $student_id)
                ->first();

            return $enrolment;
        } catch (\Throwable $th) {
            return null;
        }
    }
}
