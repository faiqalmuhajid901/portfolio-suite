<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class Career extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'company',
        'employment_type',
        'period',
        'start_date',
        'end_date',
        'is_current',
        'location',
        'description',
        'achievements',
        'technologies',
        'is_public',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_current' => 'boolean',
            'achievements' => 'array',
            'technologies' => 'array',
            'is_public' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopePublic(Builder $query): Builder
    {
        return $query->where('is_public', true);
    }

    public function getDisplayPeriodAttribute(): string
    {
        if ($this->start_date) {
            $start = $this->start_date->format('M Y');
            $end = $this->is_current
                ? 'Present'
                : ($this->end_date?->format('M Y') ?? 'Present');

            return $start.' — '.$end;
        }

        return $this->period ?: 'Period not specified';
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('phase3:home:careers'));
        static::deleted(fn () => Cache::forget('phase3:home:careers'));
    }
}
