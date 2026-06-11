<?php

namespace App\Livewire\Dashboard;

use App\Models\Project;
use Livewire\Component;

class Overview extends Component
{
    public function render()
    {
        return view('livewire.dashboard.overview', [
            'totalProjects' => Project::count(),
            'completedProjects' => Project::where('status', 'completed')->count(),
            'reviewProjects' => Project::where('status', 'review')->count(),
            'inProgressProjects' => Project::where('status', 'in_progress')->count(),
            'recentProjects' => Project::latest()->take(3)->get(),
            'totalLikes' => Project::sum('likes'),
        ])->layout('layouts.dashboard');
    }
}