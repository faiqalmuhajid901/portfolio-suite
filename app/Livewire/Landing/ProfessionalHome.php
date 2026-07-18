<?php

namespace App\Livewire\Landing;

use App\Models\Career;
use App\Models\Certificate;
use App\Models\Profile;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class ProfessionalHome extends Component
{
    public function render(): View
    {
        $profile = Cache::remember(
            'phase3:home:profile',
            now()->addMinutes(30),
            fn () => Profile::query()
                ->published()
                ->with(['educations' => fn ($query) => $query->where('is_visible', true)])
                ->first()
        );

        $projects = Cache::remember(
            'phase3:home:projects',
            now()->addMinutes(30),
            fn () => Project::query()
                ->published()
                ->orderByDesc('is_featured')
                ->orderBy('sort_order')
                ->orderByDesc('end_date')
                ->orderByDesc('id')
                ->limit(6)
                ->get()
        );

        $careers = Cache::remember(
            'phase3:home:careers',
            now()->addMinutes(30),
            fn () => Career::query()
                ->public()
                ->orderBy('sort_order')
                ->orderByDesc('is_current')
                ->orderByDesc('start_date')
                ->orderByDesc('id')
                ->get()
        );

        $certificates = Cache::remember(
            'phase3:home:certificates',
            now()->addMinutes(30),
            fn () => Certificate::query()
                ->visible()
                ->orderByDesc('issued_at')
                ->orderByDesc('id')
                ->limit(6)
                ->get()
        );

        $stats = Cache::remember(
            'phase3:home:stats',
            now()->addMinutes(30),
            fn () => [
                'projects' => Project::query()->published()->count(),
                'caseStudies' => Project::query()->caseStudyPublished()->count(),
            ]
        );

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
}
