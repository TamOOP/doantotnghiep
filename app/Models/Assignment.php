<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use PhpParser\Node\Expr\Assign;

class Assignment extends Model
{
    use HasFactory;
    public $timestamps = false;

    public function activity() : BelongsTo {
        return $this->belongsTo(Activity::class);
    }

    public function submissions() : HasMany 
    {
        return $this->hasMany(Submission::class, 'assign_id');
    }

    public function getAssign($id) : Assignment {
        try {
            $assign = $this->find($id);
        } catch (Exception $e) {
            session()->flash('error', $e->getMessage());
        }

        return $assign;
    }
}
