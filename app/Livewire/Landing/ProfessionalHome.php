<?php

namespace App\Livewire\Landing;

use App\Support\SeoManager;
use App\Models\Career;
use App\Models\Certificate;
use App\Models\Education;
use App\Models\Profile;
use App\Models\Project;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class ProfessionalHome extends Component
{
    private const CACHE_TTL_MINUTES = 30;

    public function render(): View
    {
        $profilePayload = $this->rememberArray(
            'phase3:home:profile',
            function (): array {
                $profile = Profile::query()
                    ->published()
                    ->with([
                        'educations' => fn ($query) => $query->where('is_visible', true),
                    ])
                    ->first();

                if ($profile === null) {
                    return [];
                }

                return [
                    'attributes' => $profile->getAttributes(),
                    'educations' => $profile->educations
                        ->map(fn (Education $education): array => $education->getAttributes())
                        ->all(),
                ];
            }
        );

        $projectRows = $this->rememberArray(
            'phase3:home:projects',
            fn (): array => Project::query()
                ->published()
                ->orderByDesc('is_featured')
                ->orderBy('sort_order')
                ->orderByDesc('end_date')
                ->orderByDesc('id')
                ->limit(6)
                ->get()
                ->map(fn (Project $project): array => $project->getAttributes())
                ->all()
        );

        $careerRows = $this->rememberArray(
            'phase3:home:careers',
            fn (): array => Career::query()
                ->public()
                ->orderBy('sort_order')
                ->orderByDesc('is_current')
                ->orderByDesc('start_date')
                ->orderByDesc('id')
                ->get()
                ->map(fn (Career $career): array => $career->getAttributes())
                ->all()
        );

        $certificateRows = $this->rememberArray(
            'phase3:home:certificates',
            fn (): array => Certificate::query()
                ->visible()
                ->orderByDesc('issued_at')
                ->orderByDesc('id')
                ->limit(6)
                ->get()
                ->map(fn (Certificate $certificate): array => $certificate->getAttributes())
                ->all()
        );

        $stats = $this->rememberArray(
            'phase3:home:stats',
            fn (): array => [
                'projects' => Project::query()->published()->count(),
                'caseStudies' => Project::query()->caseStudyPublished()->count(),
            ]
        );

        $profile = $this->hydrateProfile($profilePayload);
        $projects = $this->hydrateCollection(Project::class, $projectRows);
        $careers = $this->hydrateCollection(Career::class, $careerRows);
        $certificates = $this->hydrateCollection(Certificate::class, $certificateRows);

        return view('livewire.landing.professional-home', compact(
            'profile',
            'projects',
            'careers',
            'certificates',
            'stats'
        ))
            ->layout('layouts.professional-public')
            ->title(($profile?->name ?? 'Muhammad Faiq').' — Portfolio');
    }

    /**
     * Laravel 13 restricts which PHP objects may be unserialized from cache.
     * Store only scalar/array payloads and discard legacy object-based entries.
     *
     * @param  Closure(): array  $resolver
     * @return array<mixed>
     */
    private function rememberArray(string $key, Closure $resolver): array
    {
        $cached = Cache::get($key);

        if ($cached !== null && ! is_array($cached)) {
            Cache::forget($key);
        }

        /** @var array<mixed> $value */
        $value = Cache::remember(
            $key,
            now()->addMinutes(self::CACHE_TTL_MINUTES),
            $resolver
        );

        return $value;
    }

    /**
     * @param  array<mixed>  $payload
     */
    private function hydrateProfile(array $payload): ?Profile
    {
        if (! isset($payload['attributes']) || ! is_array($payload['attributes'])) {
            return null;
        }

        $profile = (new Profile())->newFromBuilder($payload['attributes']);

        $educationRows = isset($payload['educations']) && is_array($payload['educations'])
            ? $payload['educations']
            : [];

        $profile->setRelation(
            'educations',
            $this->hydrateCollection(Education::class, $educationRows)
        );

        return $profile;
    }

    /**
     * @template TModel of Model
     *
     * @param  class-string<TModel>  $modelClass
     * @param  array<int, array<string, mixed>>  $rows
     * @return EloquentCollection<int, TModel>
     */
    private function hydrateCollection(string $modelClass, array $rows): EloquentCollection
    {
        /** @var TModel $prototype */
        $prototype = new $modelClass();

        $models = array_map(
            fn (array $attributes): Model => $prototype->newFromBuilder($attributes),
            $rows
        );

        /** @var EloquentCollection<int, TModel> $collection */
        $collection = $prototype->newCollection($models);

        return $collection;
    }
}
