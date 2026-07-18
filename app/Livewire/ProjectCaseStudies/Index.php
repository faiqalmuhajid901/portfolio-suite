<?php

namespace App\Livewire\ProjectCaseStudies;

use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public ?int $projectId = null;
    public string $projectName = '';
    public string $slug = '';
    public string $role = '';
    public string $summary = '';
    public string $problem = '';
    public string $solution = '';
    public string $outcome = '';
    public string $sourceCodeUrl = '';
    public bool $isPublished = false;
    public bool $isFeatured = false;
    public bool $caseStudyPublished = false;
    public int $sortOrder = 0;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function edit(int $projectId): void
    {
        $project = $this->projectQuery()->findOrFail($projectId);

        $this->projectId = $project->id;
        $this->projectName = $project->name;
        $this->slug = $project->slug ?: Str::slug($project->name);
        $this->role = (string) $project->role;
        $this->summary = (string) ($project->summary ?: $project->description);
        $this->problem = (string) $project->problem;
        $this->solution = (string) $project->solution;
        $this->outcome = (string) $project->outcome;
        $this->sourceCodeUrl = (string) $project->source_code_url;
        $this->isPublished = (bool) $project->is_published;
        $this->isFeatured = (bool) $project->is_featured;
        $this->caseStudyPublished = (bool) $project->case_study_published;
        $this->sortOrder = (int) $project->sort_order;
        $this->resetValidation();
    }

    public function save(): void
    {
        $project = $this->projectQuery()->findOrFail($this->projectId);
        $this->slug = Str::slug($this->slug ?: $project->name);
        $this->sourceCodeUrl = $this->normalizeUrl($this->sourceCodeUrl);

        $validated = $this->validate([
            'projectId' => ['required', 'integer'],
            'slug' => [
                'required',
                'string',
                'max:180',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('projects', 'slug')->ignore($project->id),
            ],
            'role' => ['nullable', 'string', 'max:160'],
            'summary' => ['nullable', 'string', 'max:500'],
            'problem' => ['nullable', 'string', 'max:5000'],
            'solution' => ['nullable', 'string', 'max:5000'],
            'outcome' => ['nullable', 'string', 'max:5000'],
            'sourceCodeUrl' => ['nullable', 'url', 'max:255'],
            'isPublished' => ['boolean'],
            'isFeatured' => ['boolean'],
            'caseStudyPublished' => ['boolean'],
            'sortOrder' => ['integer', 'min:0', 'max:999'],
        ]);

        if ($this->caseStudyPublished && $project->status !== 'completed') {
            $this->addError('caseStudyPublished', 'Case study hanya dapat dipublikasikan untuk project berstatus completed.');
            return;
        }

        if ($this->caseStudyPublished) {
            $requiredFields = [
                'role' => 'Role',
                'summary' => 'Summary',
                'problem' => 'Problem',
                'solution' => 'Solution',
                'outcome' => 'Outcome',
            ];

            foreach ($requiredFields as $field => $label) {
                if (blank($this->{$field})) {
                    $this->addError($field, $label.' wajib diisi sebelum case study dipublikasikan.');
                }
            }

            if ($this->getErrorBag()->isNotEmpty()) {
                return;
            }
        }

        $isPublished = (bool) $validated['isPublished'];
        $caseStudyPublished = $isPublished && (bool) $validated['caseStudyPublished'];

        $project->update([
            'slug' => $validated['slug'],
            'role' => $this->nullableTrim($validated['role'] ?? null),
            'summary' => $this->nullableTrim($validated['summary'] ?? null),
            'problem' => $this->nullableTrim($validated['problem'] ?? null),
            'solution' => $this->nullableTrim($validated['solution'] ?? null),
            'outcome' => $this->nullableTrim($validated['outcome'] ?? null),
            'source_code_url' => $validated['sourceCodeUrl'] ?: null,
            'is_published' => $isPublished,
            'is_featured' => (bool) $validated['isFeatured'],
            'case_study_published' => $caseStudyPublished,
            'sort_order' => (int) $validated['sortOrder'],
        ]);

        session()->flash('success', 'Konten profesional project berhasil diperbarui.');
        $this->edit($project->id);
    }

    public function clearSelection(): void
    {
        $this->reset([
            'projectId',
            'projectName',
            'slug',
            'role',
            'summary',
            'problem',
            'solution',
            'outcome',
            'sourceCodeUrl',
            'isPublished',
            'isFeatured',
            'caseStudyPublished',
            'sortOrder',
        ]);
        $this->resetValidation();
    }

    public function render(): View
    {
        $projects = $this->projectQuery()
            ->when($this->search !== '', function (Builder $query): void {
                $query->where(function (Builder $subQuery): void {
                    $term = '%'.$this->search.'%';
                    $subQuery
                        ->where('name', 'like', $term)
                        ->orWhere('category', 'like', $term)
                        ->orWhere('client', 'like', $term);
                });
            })
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->latest('id')
            ->paginate(10);

        return view('livewire.project-case-studies.index', compact('projects'))
            ->layout('layouts.dashboard')
            ->title('Case Studies');
    }

    private function projectQuery(): Builder
    {
        return Project::query()->where('user_id', Auth::id());
    }

    private function normalizeUrl(?string $url): string
    {
        $url = trim((string) $url);

        if ($url === '') {
            return '';
        }

        return preg_match('/^https?:\/\//i', $url) ? $url : 'https://'.$url;
    }

    private function nullableTrim(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
