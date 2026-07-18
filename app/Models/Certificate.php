<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Certificate extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'issuer',
        'issued_at',
        'pdf_path',
        'description',
        'is_visible',
    ];

    protected $casts = [
        'issued_at' => 'date',
        'is_visible' => 'boolean',
    ];

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_visible', true);
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('phase3:home:certificates'));
        static::deleted(fn () => Cache::forget('phase3:home:certificates'));
    }
}
