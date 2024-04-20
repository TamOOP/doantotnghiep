<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;
    public $timestamps = false;

    public function enrolment() : BelongsTo {
        return $this->belongsTo(Enrolment::class);
    }
}
