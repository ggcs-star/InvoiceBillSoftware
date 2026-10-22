<?php

namespace App\Models;

use App\Enums\SequenceType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NumberSequence extends Model
{
    protected $fillable = [
        'branch_id',
        'type',
        'year',
        'last_number',
    ];

    protected $casts = [
        'type' => SequenceType::class,
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
