<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transfer extends Model
{
    use HasFactory;
    public $timestamps = false;

    public function bank() : BelongsTo {
        return $this->belongsTo(Bank::class);
    }

    public function user() : BelongsTo {
        return $this->belongsTo(User::class);
    }
}
