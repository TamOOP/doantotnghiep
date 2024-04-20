<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Submission extends Model
{
    use HasFactory;
    public $timestamps = false;

    public function user() : BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function assign(): BelongsTo {
        return $this->belongsTo(Assignment::class, 'assign_id');
    }
}
