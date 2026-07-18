<?php

namespace App\Livewire\Landing;

use App\Models\Project;
use App\Support\PublicPortfolioCache;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.public')]
class Index extends Component
{
    public string $search = '';

    /**
     * @var array<int, int>
     */
    public array $likedProjectIds = [];

    public bool $likesLoaded = false;

    /**
     * Dipanggil melalui wire:init setelah HTML utama selesai
     * dirender. Query status like tidak lagi menghambat homepage.
     */
    public function loadLikedProjects(): void
    {
        if ($this->likesLoaded) {
            return;
        }

        $this->likesLoaded = true;

        try {
            $visitorHash = $this->visitorHash();

            $this->likedProjectIds = DB::table('project_likes')
                ->where('visitor_hash', $visitorHash)
                ->pluck('project_id')
                ->map(
                    static fn (mixed $projectId): int =>
                        (int) $projectId
                )
                ->values()
                ->all();
        } catch (\Throwable $exception) {
            /*
             * Gagal memuat status like tidak boleh
             * menjatuhkan halaman publik.
             */
            report($exception);

            $this->likedProjectIds = [];
        }
    }

    public function like(int $projectId): void
    {
        if (! $this->likesLoaded) {
            $this->loadLikedProjects();
        }

        if (
            in_array(
                $projectId,
                $this->likedProjectIds,
                true
            )
        ) {
            return;
        }

        $projectExists = Project::query()
            ->published()
            ->whereKey($projectId)
            ->exists();

        if (! $projectExists) {
            return;
        }

        $visitorHash = $this->visitorHash();

        $wasInserted = DB::transaction(
            function () use (
                $projectId,
                $visitorHash
            ): bool {
                $inserted = DB::table('project_likes')
                    ->insertOrIgnore([
                        'project_id' => $projectId,
                        'visitor_hash' => $visitorHash,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                if ($inserted !== 1) {
                    return false;
                }

                Project::query()
                    ->whereKey($projectId)
                    ->increment('likes');

                return true;
            }
        );

        $likeExists = $wasInserted
            || DB::table('project_likes')
                ->where('project_id', $projectId)
                ->where('visitor_hash', $visitorHash)
                ->exists();

        if (! $likeExists) {
            return;
        }

        $this->likedProjectIds[] = $projectId;

        $this->likedProjectIds = array_values(
            array_unique($this->likedProjectIds)
        );

        /*
         * Project cache menyimpan angka like.
         * Cache harus dibuang setelah increment berhasil.
         */
        PublicPortfolioCache::forgetProjects();
    }

    public function render(): View
        {
            $publicProfile =
                PublicPortfolioCache::profile();

            $educations =
                $publicProfile?->getRelation(
                    'educations'
                ) ?? collect([]);

            /*
            * Seluruh project publik diambil satu kali dari
            * cache. Pencarian selanjutnya dilakukan di browser.
            */
            $portfolioProjects =
                PublicPortfolioCache::projects();

            $featured =
                $portfolioProjects->first();

            $projects =
                $portfolioProjects
                    ->skip(1)
                    ->take(6)
                    ->values();

            /*
            * Hanya index teks ringan yang dikirim ke browser.
            * Tidak ada deskripsi HTML atau data sensitif.
            */
            $projectSearchIndex =
                $portfolioProjects
                    ->map(
                        function (
                            Project $project
                        ): array {
                            $tags = is_array(
                                $project->tags
                            )
                                ? implode(
                                    ' ',
                                    $project->tags
                                )
                                : '';

                            $searchableContent =
                                implode(
                                    ' ',
                                    array_filter([
                                        $project->name,
                                        $project->category,
                                        $project->client,
                                        $project->description,
                                        $tags,
                                    ])
                                );

                            return [
                                'id' => (int) $project->id,

                                'search' => Str::of(
                                    $searchableContent
                                )
                                    ->lower()
                                    ->squish()
                                    ->toString(),
                            ];
                        }
                    )
                    ->values()
                    ->all();

            return view(
                'livewire.landing.index',
                [
                    'publicProfile' =>
                        $publicProfile,

                    'educations' =>
                        $educations,

                    'totalProjects' =>
                        PublicPortfolioCache::projectCount(),

                    'totalLikes' =>
                        PublicPortfolioCache::totalLikes(),

                    'featured' =>
                        $featured,

                    'projects' =>
                        $projects,

                    'projectSearchIndex' =>
                        $projectSearchIndex,

                    'certificates' =>
                        PublicPortfolioCache::certificates(),
                ]
            );
        }

    private function visitorHash(): string
    {
        $visitorToken = request()->cookie(
            'portfolio_visitor'
        );

        if (
            ! is_string($visitorToken)
            || ! Str::isUuid($visitorToken)
        ) {
            $visitorToken = (string) Str::uuid();

            Cookie::queue(
                'portfolio_visitor',
                $visitorToken,
                60 * 24 * 365 * 2,
                '/',
                null,
                app()->environment('production'),
                true,
                false,
                'lax'
            );
        }

        return hash_hmac(
            'sha256',
            $visitorToken,
            (string) config('app.key')
        );
    }
}
