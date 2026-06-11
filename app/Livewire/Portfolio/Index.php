<?php

namespace App\Livewire\Portfolio;

use App\Models\Project;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dashboard')]
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
                        ->orWhere('client', 'like', '%' . $this->search . '%');
                });
            })
            ->latest();

        return view('livewire.portfolio.index', [
            'featured' => (clone $baseQuery)->first(),
            'projects' => (clone $baseQuery)->skip(1)->take(6)->get(),
        ]);
    }
}