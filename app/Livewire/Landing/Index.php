<?php

namespace App\Livewire\Landing;

use App\Models\Certificate;
use App\Models\Profile;
use App\Models\Project;
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
     * Daftar project yang sudah diberi like oleh browser ini.
     *
     * Digunakan untuk menonaktifkan tombol setelah berhasil like.
     *
     * @var array<int, int>
     */
    public array $likedProjectIds = [];

    public function mount(): void
    {
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
    }

    public function like(int $projectId): void
    {
        /*
         * Hentikan request lebih awal apabila browser ini
         * sudah tercatat memberikan like.
         */
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
                /*
                 * insertOrIgnore mengembalikan 1 apabila record baru
                 * berhasil dibuat, dan 0 apabila unique constraint
                 * menemukan bahwa visitor sudah pernah like.
                 */
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

        /*
         * Tetap tandai tombol sebagai sudah liked.
         *
         * Kondisi kedua menangani kasus user menekan like
         * dari dua tab secara hampir bersamaan.
         */
        $likeExists = $wasInserted
            || DB::table('project_likes')
                ->where('project_id', $projectId)
                ->where('visitor_hash', $visitorHash)
                ->exists();

        if ($likeExists) {
            $this->likedProjectIds[] = $projectId;

            $this->likedProjectIds = array_values(
                array_unique($this->likedProjectIds)
            );
        }
    }

    public function render(): View
    {
        $baseQuery = Project::query()
            ->when(
                $this->search !== '',
                function ($query): void {
                    $query->where(
                        function ($subQuery): void {
                            $keyword = '%'
                                . $this->search
                                . '%';

                            $subQuery
                                ->where(
                                    'name',
                                    'like',
                                    $keyword
                                )
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
            ->latest();

        return view('livewire.landing.index', [
            'publicProfile' => Profile::query()
                ->latest()
                ->first(),

            'totalProjects' => Project::query()->count('*'),

            'totalLikes' => Project::query()->sum('likes'),

            'featured' => (clone $baseQuery)->first(),

            'projects' => (clone $baseQuery)
                ->take(6)
                ->get(),

            'certificates' => Certificate::query()
                ->visible()
                ->latest()
                ->take(6)
                ->get(),
        ]);
    }

    /**
     * Menghasilkan hash stabil untuk browser pengunjung.
     *
     * UUID asli disimpan di encrypted cookie Laravel,
     * sedangkan database hanya menyimpan hasil hash.
     */
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

            /*
             * Cookie berlaku selama dua tahun.
             */
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
