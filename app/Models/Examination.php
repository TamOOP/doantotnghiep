<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Examination extends Model
{
    use HasFactory;
    public $timestamps = false;

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'exam_id')
            ->where('status', '1');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(Attempt::class, 'exam_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'attempts', 'exam_id', 'user_id')
            ->withPivot('time_start', 'time_end', 'total_mark', 'final_grade');
    }

    public function getExam($id): Examination
    {
        try {
            $exam = $this->find($id);
        } catch (Exception $e) {
            session()->flash('error', $e->getMessage());
        }

        return $exam;
    }
}
