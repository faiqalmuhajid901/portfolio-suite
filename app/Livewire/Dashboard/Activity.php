<?php

namespace App\Livewire\Dashboard;

use App\Models\Project;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dashboard')]
class Activity extends Component
{
    public function render()
    {
        return view('livewire.dashboard.activity', [
            'totalProjects' => Project::query()->count(),

            'completedProjects' => Project::query()
                ->where('status', '=', 'completed')
                ->count(),

            'reviewProjects' => Project::query()
                ->where('status', '=', 'review')
                ->count(),

            'inProgressProjects' => Project::query()
                ->where('status', '=', 'in_progress')
                ->count(),

            'activeProjects' => Project::query()
                ->where('status', '=', 'in_progress')
                ->count(),

            'totalLikes' => Project::query()->sum('likes'),

            'projects' => Project::query()
                ->latest()
                ->take(5)
                ->get(),
        ]);
    }
}