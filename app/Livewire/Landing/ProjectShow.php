<?php

namespace App\Livewire\Landing;

use App\Models\Project;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ProjectShow extends Component
{
    public Project $project;

    public function mount(string $slug): void
    {
        $this->project = Project::query()
            ->caseStudyPublished()
            ->where('slug', $slug)
            ->firstOrFail();
    }

    public function render(): View
    {
        $relatedProjects = Project::query()
            ->caseStudyPublished()
            ->whereKeyNot($this->project->getKey())
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->limit(3)
            ->get();

        return view('livewire.landing.project-show', compact('relatedProjects'))
            ->layout('layouts.professional-public')
            ->title($this->project->name.' — Case Study');
    }
}
