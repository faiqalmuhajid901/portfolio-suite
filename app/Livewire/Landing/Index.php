<?php

namespace App\Livewire\Landing;

use App\Models\Certificate;
use App\Models\Profile;
use App\Models\Project;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.public')]
class Index extends Component
{
    public string $search = '';

    public function like(int $projectId): void
    {
        Project::query()
            ->whereKey($projectId)
            ->increment('likes');
    }

    public function render()
    {
        $baseQuery = Project::query()
            ->when($this->search !== '', function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('category', 'like', '%' . $this->search . '%')
                        ->orWhere('client', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->latest();

        return view('livewire.landing.index', [
            'publicProfile' => Profile::query()->latest()->first(),
            'totalProjects' => Project::query()->count(),
            'totalLikes' => Project::query()->sum('likes'),
            'featured' => (clone $baseQuery)->first(),
            'projects' => (clone $baseQuery)->take(6)->get(),
            'certificates' => Certificate::query()
                ->visible()
                ->latest()
                ->take(6)
                ->get(),
        ]);
    }
}