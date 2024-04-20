<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Topic extends Model
{
    use HasFactory;
    public $timestamps = false;

    public function course() : BelongsTo 
    {
        return $this->belongsTo(Course::class);
    }

    public function activities() : HasMany 
    {
        return $this->hasMany(Activity::class)->where('status', '1');    
    }
}
