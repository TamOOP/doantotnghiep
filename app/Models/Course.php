<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;
    public $timestamps = false;

    public function teacher() : BelongsTo 
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function topics() : HasMany
    {
        return $this->hasMany(Topic::class)->where('status', '1');    
    }

    public function students() : BelongsToMany 
    {
        return $this->belongsToMany(User::class, 'enrolments', 'course_id', 'user_id')
            ->withPivot('enrol_date', 'last_access')
            ->where('status', '1');
    }
}
