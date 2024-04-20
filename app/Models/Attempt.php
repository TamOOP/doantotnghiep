<?php

namespace App\Models;

use App\Helpers\AppHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Attempt extends Model
{
    use HasFactory;
    public $timestamps = false;

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'question_orders', 'attempt_id', 'question_id')
            ->withPivot('id', 'index', 'grade');
    }

    public function getAttemptNotFinished($examId, $userId)
    {
        $now = AppHelper::getCurrentTime();

        try {
            $attemptNotFinished = Attempt::where('user_id', $userId)
                ->where('exam_id', $examId)
                ->where('time_end', '>=', $now)
                ->first();
        } catch (\Throwable $th) {
            return null;
        }

        return $attemptNotFinished;
    }
}
