<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PortfolioAiReview extends Model
{
    protected $fillable = [
        'user_id',
        'score',
        'summary',
        'strengths',
        'weaknesses',
        'recommendations',
        'model',
        'source_snapshot',
        'generated_at',
    ];

    protected $casts = [
        'strengths' => 'array',
        'weaknesses' => 'array',
        'recommendations' => 'array',
        'source_snapshot' => 'array',
        'generated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
