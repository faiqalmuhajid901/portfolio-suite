<?php

namespace App\Models;

use App\Support\PublicPortfolioCache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'category',
        'client',
        'status',
        'start_date',
        'end_date',
        'image',
        'website_url',
        'description',
        'tags',
        'likes',
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'start_date' => 'date',
            'end_date' => 'date',
            'likes' => 'integer',
        ];
    }

    /**
     * Pada struktur status yang ada saat ini,
     * hanya proyek completed yang ditampilkan publik.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }

    protected static function booted(): void
    {
        static::saved(
            static function (Project $project): void {
                PublicPortfolioCache::forgetProjects();
            }
        );

        static::deleted(
            static function (Project $project): void {
                PublicPortfolioCache::forgetProjects();
            }
        );
    }
}
