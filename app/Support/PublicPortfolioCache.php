<?php

namespace App\Support;

use App\Models\Certificate;
use App\Models\Education;
use App\Models\Profile;
use App\Models\Project;
use Closure;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Cache;
use Throwable;

final class PublicPortfolioCache
{
    private const PROFILE_KEY = 'public_portfolio:profile:v1';

    private const PROJECTS_KEY = 'public_portfolio:projects:v1';

    private const PROJECT_COUNT_KEY = 'public_portfolio:project_count:v1';

    private const TOTAL_LIKES_KEY = 'public_portfolio:total_likes:v1';

    private const CERTIFICATES_KEY = 'public_portfolio:certificates:v1';

    private const CONTENT_TTL_SECONDS = 600;

    private const METRIC_TTL_SECONDS = 60;

    public static function profile(): ?Profile
    {
        try {
            $payload = self::remember(
                self::PROFILE_KEY,
                self::CONTENT_TTL_SECONDS,
                function (): ?array {
                    $profile = Profile::query()
                        ->published()
                        ->with([
                            'educations' => function ($query): void {
                                $query->where('is_visible', true);
                            },
                        ])
                        ->latest()
                        ->first();

                    if ($profile === null) {
                        return null;
                    }

                    return [
                        'profile' => $profile->getAttributes(),

                        'educations' => $profile->educations
                            ->map(
                                static fn (Education $education): array =>
                                    $education->getAttributes()
                            )
                            ->all(),
                    ];
                }
            );

            if (
                ! is_array($payload)
                || ! isset($payload['profile'])
                || ! is_array($payload['profile'])
            ) {
                return null;
            }

            $profile = (new Profile())->newFromBuilder(
                $payload['profile']
            );

            $educationModels = array_map(
                static fn (array $attributes): Education =>
                    (new Education())->newFromBuilder($attributes),
                is_array($payload['educations'] ?? null)
                    ? $payload['educations']
                    : []
            );

            $profile->setRelation(
                'educations',
                (new Education())->newCollection($educationModels)
            );

            return $profile;
        } catch (Throwable $exception) {
            /*
             * Kegagalan database tidak boleh menjatuhkan homepage.
             * Blade akan menggunakan konten fallback.
             */
            report($exception);

            return null;
        }
    }

    public static function projects(
        string $search = ''
    ): EloquentCollection {
        $search = trim($search);

        try {
            /*
             * Search bersifat dinamis dan tidak dimasukkan ke cache
             * untuk menghindari terlalu banyak cache key.
             */
            if ($search !== '') {
                return self::queryProjects($search);
            }

            $rows = self::remember(
                self::PROJECTS_KEY,
                self::CONTENT_TTL_SECONDS,
                function (): array {
                    return self::queryProjects()
                        ->map(
                            static fn (Project $project): array =>
                                $project->getAttributes()
                        )
                        ->all();
                }
            );

            return self::hydrateProjects(
                is_array($rows) ? $rows : []
            );
        } catch (Throwable $exception) {
            report($exception);

            return (new Project())->newCollection();
        }
    }

    public static function projectCount(): int
    {
        try {
            return (int) self::remember(
                self::PROJECT_COUNT_KEY,
                self::CONTENT_TTL_SECONDS,
                static fn (): int => Project::query()
                    ->published()
                    ->count('*')
            );
        } catch (Throwable $exception) {
            report($exception);

            return 0;
        }
    }

    public static function totalLikes(): int
    {
        try {
            return (int) self::remember(
                self::TOTAL_LIKES_KEY,
                self::METRIC_TTL_SECONDS,
                static fn (): int => (int) Project::query()
                    ->published()
                    ->sum('likes')
            );
        } catch (Throwable $exception) {
            report($exception);

            return 0;
        }
    }

    public static function certificates(): EloquentCollection
    {
        try {
            $rows = self::remember(
                self::CERTIFICATES_KEY,
                self::CONTENT_TTL_SECONDS,
                static function (): array {
                    return Certificate::query()
                        ->visible()
                        ->latest()
                        ->take(6)
                        ->get()
                        ->map(
                            static fn (Certificate $certificate): array =>
                                $certificate->getAttributes()
                        )
                        ->all();
                }
            );

            $models = array_map(
                static fn (array $attributes): Certificate =>
                    (new Certificate())->newFromBuilder($attributes),
                is_array($rows) ? $rows : []
            );

            return (new Certificate())->newCollection($models);
        } catch (Throwable $exception) {
            report($exception);

            return (new Certificate())->newCollection();
        }
    }

    public static function forgetProfile(): void
    {
        self::forgetKeys([
            self::PROFILE_KEY,
        ]);
    }

    public static function forgetProjects(): void
    {
        self::forgetKeys([
            self::PROJECTS_KEY,
            self::PROJECT_COUNT_KEY,
            self::TOTAL_LIKES_KEY,
        ]);
    }

    public static function forgetCertificates(): void
    {
        self::forgetKeys([
            self::CERTIFICATES_KEY,
        ]);
    }

    public static function forgetAll(): void
    {
        self::forgetKeys([
            self::PROFILE_KEY,
            self::PROJECTS_KEY,
            self::PROJECT_COUNT_KEY,
            self::TOTAL_LIKES_KEY,
            self::CERTIFICATES_KEY,
        ]);
    }

    private static function queryProjects(
        string $search = ''
    ): EloquentCollection {
        return Project::query()
            ->published()
            ->when(
                $search !== '',
                function ($query) use ($search): void {
                    $keyword = '%' . $search . '%';

                    $query->where(
                        function ($subQuery) use ($keyword): void {
                            $subQuery
                                ->where('name', 'like', $keyword)
                                ->orWhere(
                                    'category',
                                    'like',
                                    $keyword
                                )
                                ->orWhere(
                                    'client',
                                    'like',
                                    $keyword
                                )
                                ->orWhere(
                                    'description',
                                    'like',
                                    $keyword
                                );
                        }
                    );
                }
            )
            ->latest()
            ->take(7)
            ->get();
    }

    private static function hydrateProjects(
        array $rows
    ): EloquentCollection {
        $models = array_map(
            static fn (array $attributes): Project =>
                (new Project())->newFromBuilder($attributes),
            $rows
        );

        return (new Project())->newCollection($models);
    }

    private static function remember(
        string $key,
        int $seconds,
        Closure $callback
    ): mixed {
        try {
            return Cache::remember(
                $key,
                now()->addSeconds($seconds),
                $callback
            );
        } catch (Throwable $exception) {
            /*
             * Jika cache gagal tetapi database masih tersedia,
             * data tetap diambil langsung.
             */
            report($exception);

            return $callback();
        }
    }

    private static function forgetKeys(array $keys): void
    {
        foreach ($keys as $key) {
            try {
                Cache::forget($key);
            } catch (Throwable $exception) {
                /*
                 * Gagal menghapus cache tidak boleh menggagalkan
                 * proses simpan data admin.
                 */
                report($exception);
            }
        }
    }
}
