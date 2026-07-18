<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class Education extends Model
{
    protected $table = 'educations';

    protected $fillable = [
        'profile_id',
        'level',
        'institution',
        'major',
        'gpa',
        'start_year',
        'end_year',
        'status',
        'description',
        'sort_order',
        'is_visible',
    ];

    protected function casts(): array
    {
        return [
            'gpa' => 'decimal:2',
            'start_year' => 'integer',
            'end_year' => 'integer',
            'sort_order' => 'integer',
            'is_visible' => 'boolean',
        ];
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('phase3:home:profile'));
        static::deleted(fn () => Cache::forget('phase3:home:profile'));
    }
}
