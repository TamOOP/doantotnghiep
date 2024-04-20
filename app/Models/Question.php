<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;
    public $timestamps = false;

    

    public function choices() : HasMany {
        return $this->hasMany(Choice::class);
    }

    public function attempts() : BelongsToMany {
        return $this->belongsToMany(Attempt::class, 'question_orders', 'question_id', 'attempt_id')
            ->withPivot('index', 'grade');
    }

    public function exam() : BelongsTo {
        return $this->belongsTo(Examination::class);
    }
}
