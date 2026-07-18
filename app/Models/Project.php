<?php

namespace App\Models;

use App\Support\PublicPortfolioCache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Project extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'category',
        'client',
        'status',
        'is_published',
        'is_featured',
        'case_study_published',
        'case_study_published_at',
        'sort_order',
        'start_date',
        'end_date',
        'image',
        'website_url',
        'source_code_url',
        'description',
        'role',
        'summary',
        'problem',
        'solution',
        'outcome',
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
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'case_study_published' => 'boolean',
            'case_study_published_at' => 'datetime',
            'sort_order' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', 'completed')
            ->where('is_published', true);
    }

    public function scopeCaseStudyPublished(Builder $query): Builder
    {
        return $query
            ->published()
            ->where('case_study_published', true);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Compatibility helper for the existing Projects Livewire component.
     */
    public function deleteProject(?int $projectId = null): bool
    {
        $project = $projectId !== null && $projectId !== $this->getKey()
            ? self::query()->find($projectId)
            : $this;

        return $project?->delete() ?? false;
    }

    protected static function booted(): void
    {
        static::saving(function (Project $project): void {
            if (blank($project->slug)) {
                $project->slug = self::makeUniqueSlug($project->name, $project->getKey());
            }

            if ($project->case_study_published && $project->case_study_published_at === null) {
                $project->case_study_published_at = now();
            }

            if (! $project->case_study_published) {
                $project->case_study_published_at = null;
            }
        });

        static::saved(function (): void {
            self::forgetPublicCache();
        });

        static::deleted(function (): void {
            self::forgetPublicCache();
        });
    }

    private static function makeUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'project';
        $slug = $base;
        $suffix = 2;

        while (self::query()
            ->when($ignoreId, fn (Builder $query) => $query->where($query->getModel()->getQualifiedKeyName(), '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    private static function forgetPublicCache(): void
    {
        PublicPortfolioCache::forgetProjects();
        Cache::forget('phase3:home:projects');
        Cache::forget('phase3:home:stats');
    }
}
