<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Activity extends Model
{
    use HasFactory;
    public $timestamps = false;

    public function derived(): HasOne
    {
        if ($this->type == 'assign') {
            return $this->hasOne(Assignment::class, 'activity_id');
        } elseif ($this->type == 'exam') {
            return $this->hasOne(Examination::class, 'activity_id');
        } else {
            return $this->hasOne(File::class, 'activity_id');
        }
    }

    public function process(): HasOne
    {
        return $this->hasOne(Process::class);
    }

    public function students() : BelongsToMany {
        return $this->belongsToMany(User::class, 'processes', 'activity_id', 'user_id')
            ->withPivot('marked');
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }
}
