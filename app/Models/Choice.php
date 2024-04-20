<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Choice extends Model
{
    use HasFactory;
    public $timestamps = false;

    public function questionOrders(): BelongsToMany
    {
        return $this->belongsToMany(QuestionOrder::class, 'choice_orders', 'choice_id', 'qorder_id')
            ->withPivot('index', 'selected');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
