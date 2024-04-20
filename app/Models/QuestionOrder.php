<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class QuestionOrder extends Model
{
    use HasFactory;
    public $timestamps = false;

    public function choices() : BelongsToMany {
        return $this->belongsToMany(Choice::class, 'choice_orders', 'qorder_id', 'choice_id')
            ->withPivot('id', 'index', 'selected');
    }
}
