<?php

namespace App\Livewire\Dashboard;

use App\Models\PortfolioAiReview;
use App\Services\PortfolioAnalyticsService;
use App\Services\PortfolioQualityService;
use App\Services\PortfolioReviewService;
use Livewire\Component;
use Throwable;

class Overview extends Component
{
    public array $summary = [];

    public array $trend = [];

    public array $devices = [];

    public array $sources = [];

    public array $topPages = [];

    public array $locations = [];

    public array $realtimeVisitors = [];

    public array $chartPayload = [];

    public array $quality = [];

    public ?array $aiReview = null;

    public ?string $aiError = null;

    public function mount(): void
    {
        $this->loadDashboard();
    }

    public function refreshAnalytics(): void
    {
        $this->loadAnalytics();

        $this->dispatch('analytics-refreshed', payload: $this->chartPayload);
    }

    public function generateAiReview(): void
    {
        $this->aiError = null;
        $userId = (int) auth()->id();

        $latestReview = PortfolioAiReview::query()
            ->where('user_id', $userId)
            ->latest('generated_at')
            ->first();

        if ($latestReview?->generated_at?->greaterThan(now()->subMinute())) {
            $this->aiError = 'Analisis baru saja dibuat. Tunggu satu menit sebelum menjalankan ulang.';

            return;
        }

        try {
            $review = app(PortfolioReviewService::class)->review($userId);
            $this->aiReview = $this->formatReview($review);
        } catch (Throwable $exception) {
            report($exception);
            $this->aiError = app()->isLocal()
                ? $exception->getMessage()
                : 'Analisis AI gagal dijalankan. Periksa endpoint Ollama, model, koneksi server, dan log aplikasi.';
        }
    }

    public function render()
    {
        return view('livewire.dashboard.overview')
            ->layout('layouts.dashboard', [
                'title' => 'Portfolio Analytics',
            ]);
    }

    private function loadDashboard(): void
    {
        $this->loadAnalytics();
        $this->quality = app(PortfolioQualityService::class)->evaluate((int) auth()->id());
        $this->loadLatestAiReview();
    }

    private function loadAnalytics(): void
    {
        $analytics = app(PortfolioAnalyticsService::class)->dashboard();

        $this->summary = $analytics['summary'];
        $this->trend = $analytics['trend'];
        $this->devices = $analytics['devices'];
        $this->sources = $analytics['sources'];
        $this->topPages = $analytics['top_pages'];
        $this->locations = $analytics['locations'];
        $this->realtimeVisitors = $analytics['realtime_visitors'];
        $this->chartPayload = $analytics['chart_payload'];
    }

    private function loadLatestAiReview(): void
    {
        $review = PortfolioAiReview::query()
            ->where('user_id', auth()->id())
            ->latest('generated_at')
            ->first();

        $this->aiReview = $review ? $this->formatReview($review) : null;
    }

    private function formatReview(PortfolioAiReview $review): array
    {
        return [
            'score' => $review->score,
            'summary' => $review->summary,
            'strengths' => $review->strengths ?: [],
            'weaknesses' => $review->weaknesses ?: [],
            'recommendations' => $review->recommendations ?: [],
            'model' => $review->model,
            'generated_at' => $review->generated_at?->format('d M Y, H:i'),
        ];
    }
}
