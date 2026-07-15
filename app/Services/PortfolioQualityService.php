<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\Profile;
use App\Models\Project;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class PortfolioQualityService
{
    public function evaluate(int $userId): array
    {
        $profile = Profile::query()->where('user_id', $userId)->first();
        $projects = Project::query()->where('user_id', $userId)->get();
        $certificates = Certificate::query()->where('user_id', $userId)->get();

        $profileScore = $this->profileScore($profile);
        $volumeScore = min($projects->count() * 5, 15);
        $detailScore = $this->projectDetailScore($projects);
        $proofScore = $this->projectProofScore($projects);
        $certificateScore = min($certificates->count() * 5, 10);
        $freshnessScore = $this->freshnessScore($profile, $projects, $certificates);

        $score = (int) round(
            $profileScore
            + $volumeScore
            + $detailScore
            + $proofScore
            + $certificateScore
            + $freshnessScore
        );

        return [
            'score' => min($score, 100),
            'breakdown' => [
                ['label' => 'Profile completeness', 'score' => $profileScore, 'maximum' => 25],
                ['label' => 'Project volume', 'score' => $volumeScore, 'maximum' => 15],
                ['label' => 'Project detail', 'score' => $detailScore, 'maximum' => 25],
                ['label' => 'Evidence and links', 'score' => $proofScore, 'maximum' => 15],
                ['label' => 'Certificates', 'score' => $certificateScore, 'maximum' => 10],
                ['label' => 'Content freshness', 'score' => $freshnessScore, 'maximum' => 10],
            ],
            'quick_wins' => $this->quickWins(
                profile: $profile,
                projects: $projects,
                certificates: $certificates,
            ),
        ];
    }

    private function profileScore(?Profile $profile): int
    {
        if (! $profile) {
            return 0;
        }

        $fields = [
            $profile->name,
            $profile->role,
            $profile->bio,
            $profile->avatar,
            $profile->hero_title,
        ];

        return collect($fields)->filter(fn ($value): bool => filled($value))->count() * 5;
    }

    /** @param Collection<int, Project> $projects */
    private function projectDetailScore(Collection $projects): int
    {
        if ($projects->isEmpty()) {
            return 0;
        }

        $ratios = $projects->map(function (Project $project): float {
            $checks = [
                filled($project->name),
                filled($project->category),
                filled($project->client),
                filled($project->status),
                filled($project->start_date),
                mb_strlen(trim(strip_tags((string) $project->description))) >= 120,
            ];

            return collect($checks)->filter()->count() / count($checks);
        });

        return (int) round($ratios->average() * 25);
    }

    /** @param Collection<int, Project> $projects */
    private function projectProofScore(Collection $projects): int
    {
        if ($projects->isEmpty()) {
            return 0;
        }

        $imageRatio = $projects->filter(fn (Project $project): bool => filled($project->image))->count() / $projects->count();
        $linkRatio = $projects->filter(fn (Project $project): bool => filled($project->website_url))->count() / $projects->count();
        $tagRatio = $projects->filter(
            fn (Project $project): bool => is_array($project->tags) && count($project->tags) >= 2
        )->count() / $projects->count();

        return (int) round(($imageRatio * 5) + ($linkRatio * 5) + ($tagRatio * 5));
    }

    /**
     * @param Collection<int, Project> $projects
     * @param Collection<int, Certificate> $certificates
     */
    private function freshnessScore(
        ?Profile $profile,
        Collection $projects,
        Collection $certificates
    ): int {
        $latestUpdate = collect([
            $profile?->updated_at,
            $projects->max('updated_at'),
            $certificates->max('updated_at'),
        ])->filter()->sortDesc()->first();

        if (! $latestUpdate instanceof CarbonInterface) {
            return 0;
        }

        $days = $latestUpdate->diffInDays(now());

        return match (true) {
            $days <= 90 => 10,
            $days <= 180 => 5,
            default => 0,
        };
    }

    /**
     * @param Collection<int, Project> $projects
     * @param Collection<int, Certificate> $certificates
     */
    private function quickWins(
        ?Profile $profile,
        Collection $projects,
        Collection $certificates
    ): array {
        $recommendations = [];

        if (! $profile || blank($profile->bio) || blank($profile->hero_title)) {
            $recommendations[] = 'Lengkapi bio dan headline utama agar positioning profesional langsung terbaca.';
        }

        if ($projects->count() < 3) {
            $recommendations[] = 'Tambahkan sedikitnya tiga proyek yang mewakili kemampuan teknis berbeda.';
        }

        if ($projects->contains(fn (Project $project): bool => mb_strlen(trim(strip_tags((string) $project->description))) < 120)) {
            $recommendations[] = 'Ubah deskripsi proyek menjadi case study: masalah, peran, solusi, teknologi, dan hasil.';
        }

        if ($projects->contains(fn (Project $project): bool => blank($project->image) || blank($project->website_url))) {
            $recommendations[] = 'Tambahkan bukti visual dan tautan hasil kerja pada setiap proyek yang memungkinkan.';
        }

        if ($certificates->isEmpty()) {
            $recommendations[] = 'Tambahkan sertifikat yang relevan; jangan memasukkan sertifikat yang tidak memperkuat target posisi.';
        }

        return array_slice($recommendations, 0, 4);
    }
}
