<?php

namespace App\Livewire\Projects;

use App\Livewire\Concerns\UploadsToSupabase;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.dashboard')]
class Index extends Component
{
    use WithPagination;
    use WithFileUploads;
    use UploadsToSupabase;

    public string $search = '';
    public string $status = 'all';

    public bool $showCreateModal = false;
    public bool $isEditing = false;
    public ?int $editingProjectId = null;

    public string $name = '';
    public string $category = '';
    public string $client = '';
    public string $projectStatus = 'review';
    public ?string $startDate = null;
    public ?string $endDate = null;
    public string $websiteUrl = '';
    public string $description = '';
    public string $tagsInput = '';

    public $imageUpload = null;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'category' => ['nullable', 'string', 'max:120'],
            'client' => ['nullable', 'string', 'max:120'],
            'projectStatus' => ['required', 'in:in_progress,review,completed'],
            'startDate' => ['nullable', 'date'],
            'endDate' => ['nullable', 'date'],
            'websiteUrl' => ['nullable', 'url', 'max:255'],
            'imageUpload' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp,bmp', 'max:4096'],
            'description' => ['nullable', 'string', 'max:500'],
            'tagsInput' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();

        $this->isEditing = false;
        $this->editingProjectId = null;
        $this->showCreateModal = true;
    }

    public function openEditModal(int $projectId): void
    {
        $this->resetForm();

        $project = Project::find($projectId);

        if (! $project) {
            session()->flash('success', 'Project tidak ditemukan.');
            return;
        }

        $this->isEditing = true;
        $this->editingProjectId = $project->id;

        $this->name = $project->name ?? '';
        $this->category = $project->category ?? '';
        $this->client = $project->client ?? '';
        $this->projectStatus = $project->status ?? 'review';
        $this->startDate = $project->start_date ? $project->start_date->format('Y-m-d') : null;
        $this->endDate = $project->end_date ? $project->end_date->format('Y-m-d') : null;
        $this->websiteUrl = $project->website_url ?? '';
        $this->description = $project->description ?? '';
        $this->tagsInput = is_array($project->tags) ? implode(', ', $project->tags) : '';

        $this->showCreateModal = true;
    }

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
        $this->resetForm();
    }

    public function saveProject(): void
    {
        $this->websiteUrl = $this->normalizeWebsiteUrl($this->websiteUrl);

        $this->validate();

        $project = null;

        if ($this->isEditing && $this->editingProjectId) {
            $project = Project::find($this->editingProjectId);

            if (! $project) {
                session()->flash('success', 'Project tidak ditemukan.');
                return;
            }
        }

        $tags = collect(explode(',', $this->tagsInput))
            ->map(fn ($tag) => trim($tag))
            ->filter()
            ->values()
            ->toArray();

        $imagePath = $this->resolveProjectImage($project);

        $payload = [
            'user_id' => Auth::id(),
            'name' => $this->name,
            'category' => $this->category,
            'client' => $this->client,
            'status' => $this->projectStatus,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'image' => $imagePath,
            'website_url' => $this->websiteUrl ?: null,
            'description' => $this->description,
            'tags' => $tags,
        ];

        if ($project) {
            $project->update($payload);
            session()->flash('success', 'Project berhasil diperbarui.');
        } else {
            $payload['likes'] = 0;
            Project::create($payload);
            session()->flash('success', 'Project berhasil ditambahkan.');
        }

        $this->showCreateModal = false;
        $this->resetForm();
    }

    public function deleteProject(int $projectId): void
    {
        $project = Project::find($projectId);

        if (! $project) {
            return;
        }

        $this->deleteLocalProjectImage($project->image);

        $project->delete();

        session()->flash('success', 'Project berhasil dihapus.');
    }

    public function updateStatus(int $projectId, string $status): void
    {
        if (! in_array($status, ['in_progress', 'review', 'completed'], true)) {
            return;
        }

        Project::whereKey($projectId)->update([
            'status' => $status,
        ]);

        session()->flash('success', 'Status project berhasil diperbarui.');
    }

    public function exportCsv()
    {
        $projects = Project::latest()->get();

        return response()->streamDownload(function () use ($projects) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Name',
                'Category',
                'Client',
                'Status',
                'Start Date',
                'End Date',
                'Website URL',
                'Likes',
            ]);

            foreach ($projects as $project) {
                fputcsv($handle, [
                    $project->name,
                    $project->category,
                    $project->client,
                    $project->status,
                    optional($project->start_date)->format('Y-m-d'),
                    optional($project->end_date)->format('Y-m-d'),
                    $project->website_url,
                    $project->likes,
                ]);
            }

            fclose($handle);
        }, 'portfolio-projects.csv');
    }

    private function resetForm(): void
    {
        $this->reset([
            'name',
            'category',
            'client',
            'startDate',
            'endDate',
            'websiteUrl',
            'description',
            'tagsInput',
            'imageUpload',
            'editingProjectId',
        ]);

        $this->projectStatus = 'review';
        $this->isEditing = false;
        $this->resetValidation();
    }

    private function normalizeWebsiteUrl(?string $url): string
    {
        $url = trim((string) $url);

        if ($url === '') {
            return '';
        }

        if (! preg_match('/^https?:\/\//i', $url)) {
            return 'https://' . $url;
        }

        return $url;
    }

    private function resolveProjectImage(?Project $project): string
    {
        if ($this->imageUpload) {
            $newImage = $this->storeImageAsWebpToSupabase(
            $this->imageUpload,
            'projects',
            'imageUpload'
        );;

            if ($project) {
                $this->deleteLocalProjectImage($project->image);
            }

            return $newImage;
        }

        if ($project) {
            if ($this->shouldRegenerateAutoImage($project)) {
                return $this->websiteUrl !== ''
                    ? $this->generateWebsiteScreenshotUrl($this->websiteUrl)
                    : $this->generatePlaceholderImageUrl();
            }

            return $project->image ?: $this->generatePlaceholderImageUrl();
        }

        if ($this->websiteUrl !== '') {
            return $this->generateWebsiteScreenshotUrl($this->websiteUrl);
        }

        return $this->generatePlaceholderImageUrl();
    }

    private function shouldRegenerateAutoImage(Project $project): bool
    {
        $oldImage = $project->image ?? '';
        $oldUrl = $project->website_url ?? '';

        $websiteUrlChanged = $oldUrl !== $this->websiteUrl;

        $oldImageWasAuto = Str::startsWith($oldImage, [
            'https://s.wordpress.com/mshots/v1/',
            'https://placehold.co/',
        ]);

        return $websiteUrlChanged && $oldImageWasAuto;
    }

    private function generateWebsiteScreenshotUrl(string $url): string
    {
        return 'https://s.wordpress.com/mshots/v1/' . rawurlencode($url) . '?w=1200';
    }

    private function generatePlaceholderImageUrl(): string
    {
        return 'https://placehold.co/1200x800/eef5f2/2f6f61?text=Portfolio+Project';
    }

    private function deleteLocalProjectImage(?string $imagePath): void
    {
    }

    public function render()
    {
        $projects = Project::query()
            ->when($this->search !== '', function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('client', 'like', '%' . $this->search . '%')
                        ->orWhere('category', 'like', '%' . $this->search . '%')
                        ->orWhere('website_url', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->status !== 'all', function ($query) {
                $query->where('status', '=', $this->status);
            })
            ->latest()
            ->paginate(6);

        return view('livewire.projects.index', [
            'projects' => $projects,
        ]);
    }
}