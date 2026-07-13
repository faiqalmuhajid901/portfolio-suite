<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use RuntimeException;
use Throwable;

#[Layout('layouts.dashboard')]
class Index extends Component
{
    use WithPagination;

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

    /** Path object yang sudah diunggah langsung dari browser ke Supabase. */
    public ?string $uploadedImagePath = null;

    /** Hanya untuk menampilkan gambar lama ketika modal edit dibuka. */
    public ?string $existingImageUrl = null;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'category' => ['nullable', 'string', 'max:120'],
            'client' => ['nullable', 'string', 'max:120'],
            'projectStatus' => ['required', 'in:in_progress,review,completed'],
            'startDate' => ['nullable', 'date'],
            'endDate' => ['nullable', 'date', 'after_or_equal:startDate'],
            'websiteUrl' => ['nullable', 'url', 'max:255'],
            'uploadedImagePath' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'tagsInput' => ['nullable', 'string', 'max:255'],
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'Nama project wajib diisi.',
            'name.max' => 'Nama project maksimal 120 karakter.',
            'category.max' => 'Kategori maksimal 120 karakter.',
            'client.max' => 'Nama client maksimal 120 karakter.',
            'projectStatus.required' => 'Status project wajib dipilih.',
            'projectStatus.in' => 'Status project tidak valid.',
            'startDate.date' => 'Tanggal mulai tidak valid.',
            'endDate.date' => 'Tanggal selesai tidak valid.',
            'endDate.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            'websiteUrl.url' => 'URL website tidak valid.',
            'websiteUrl.max' => 'URL website maksimal 255 karakter.',
            'description.max' => 'Deskripsi maksimal 500 karakter.',
            'tagsInput.max' => 'Tags maksimal 255 karakter.',
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
        $this->discardPendingUpload();
        $this->resetForm();

        $this->showCreateModal = true;
    }

    public function openEditModal(int $projectId): void
    {
        $this->discardPendingUpload();
        $this->resetForm();

        $project = $this->projectQuery()->find($projectId);

        if (! $project) {
            session()->flash('error', 'Project tidak ditemukan.');
            return;
        }

        $this->isEditing = true;
        $this->editingProjectId = $project->id;
        $this->name = $project->name ?? '';
        $this->category = $project->category ?? '';
        $this->client = $project->client ?? '';
        $this->projectStatus = $project->status ?? 'review';
        $this->startDate = $project->start_date?->format('Y-m-d');
        $this->endDate = $project->end_date?->format('Y-m-d');
        $this->websiteUrl = $project->website_url ?? '';
        $this->description = $project->description ?? '';
        $this->tagsInput = is_array($project->tags)
            ? implode(', ', $project->tags)
            : '';
        $this->existingImageUrl = $project->image;
        $this->showCreateModal = true;
    }

    public function closeCreateModal(): void
    {
        $this->discardPendingUpload();
        $this->showCreateModal = false;
        $this->resetForm();
    }

    public function setUploadedImage(string $path): void
    {
        $path = trim($path);
        $this->assertValidUploadedImagePath($path);

        if ($this->uploadedImagePath && $this->uploadedImagePath !== $path) {
            $this->deleteSupabaseObject($this->uploadedImagePath);
        }

        $this->uploadedImagePath = $path;
        $this->resetErrorBag('uploadedImagePath');
    }

    public function removeUploadedImage(): void
    {
        $this->discardPendingUpload();
        $this->dispatch('project-image-reset');
    }

    public function saveProject(): void
    {
        $this->websiteUrl = $this->normalizeWebsiteUrl($this->websiteUrl);
        $pendingUploadPath = $this->uploadedImagePath;

        try {
            $this->validate();

            if ($pendingUploadPath !== null) {
                $this->assertValidUploadedImagePath($pendingUploadPath);
            }

            $project = $this->findEditingProject();

            if ($this->isEditing && ! $project) {
                session()->flash('error', 'Project yang akan diperbarui tidak ditemukan.');
                return;
            }

            $oldImageUrl = $project?->image;
            $imageUrl = $this->resolveProjectImage($project);

            $payload = [
                'user_id' => Auth::id(),
                'name' => trim($this->name),
                'category' => $this->nullableTrim($this->category),
                'client' => $this->nullableTrim($this->client),
                'status' => $this->projectStatus,
                'start_date' => $this->startDate ?: null,
                'end_date' => $this->endDate ?: null,
                'image' => $imageUrl,
                'website_url' => $this->websiteUrl ?: null,
                'description' => $this->nullableTrim($this->description),
                'tags' => $this->prepareTags(),
            ];

            DB::transaction(function () use ($project, $payload): void {
                if ($project) {
                    $project->update($payload);
                    return;
                }

                Project::create([
                    ...$payload,
                    'likes' => 0,
                ]);
            });

            if ($pendingUploadPath !== null && $oldImageUrl && $oldImageUrl !== $imageUrl) {
                $this->deleteSupabaseImageByUrl($oldImageUrl);
            }

            session()->flash(
                'success',
                $project ? 'Project berhasil diperbarui.' : 'Project berhasil ditambahkan.'
            );

            $this->uploadedImagePath = null;
            $this->showCreateModal = false;
            $this->resetForm();
            $this->dispatch('project-image-reset');
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            report($exception);

            if ($pendingUploadPath !== null) {
                $this->deleteSupabaseObject($pendingUploadPath);
                $this->uploadedImagePath = null;
            }

            $message = app()->isLocal()
                ? 'Penyimpanan project gagal: '.$exception->getMessage()
                : 'Penyimpanan project gagal. Silakan coba kembali.';

            $this->addError('uploadedImagePath', $message);
            session()->flash('error', $message);
        }
    }

    public function deleteProject(int $projectId): void
    {
        try {
            $project = $this->projectQuery()->find($projectId);

            if (! $project) {
                session()->flash('error', 'Project tidak ditemukan.');
                return;
            }

            $imageUrl = $project->image;
            $project->delete();
            $this->deleteSupabaseImageByUrl($imageUrl);

            session()->flash('success', 'Project berhasil dihapus.');
        } catch (Throwable $exception) {
            report($exception);

            session()->flash(
                'error',
                app()->isLocal()
                    ? 'Project gagal dihapus: '.$exception->getMessage()
                    : 'Project gagal dihapus.'
            );
        }
    }

    public function updateStatus(int $projectId, string $status): void
    {
        if (! in_array($status, ['in_progress', 'review', 'completed'], true)) {
            session()->flash('error', 'Status project tidak valid.');
            return;
        }

        $updated = $this->projectQuery()
            ->whereKey($projectId)
            ->update(['status' => $status]);

        session()->flash(
            $updated ? 'success' : 'error',
            $updated ? 'Status project berhasil diperbarui.' : 'Project tidak ditemukan.'
        );
    }

    public function exportCsv()
    {
        $projects = $this->projectQuery()->latest()->get();

        return response()->streamDownload(function () use ($projects): void {
            $handle = fopen('php://output', 'w');

            if ($handle === false) {
                throw new RuntimeException('Gagal membuat file CSV.');
            }

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

    private function projectQuery()
    {
        return Project::query()->where('user_id', Auth::id());
    }

    private function findEditingProject(): ?Project
    {
        if (! $this->isEditing || ! $this->editingProjectId) {
            return null;
        }

        return $this->projectQuery()->find($this->editingProjectId);
    }

    private function prepareTags(): array
    {
        return collect(explode(',', $this->tagsInput))
            ->map(fn (string $tag): string => trim($tag))
            ->filter(fn (string $tag): bool => $tag !== '')
            ->unique()
            ->values()
            ->toArray();
    }

    private function resolveProjectImage(?Project $project): string
    {
        if ($this->uploadedImagePath !== null) {
            return $this->publicSupabaseUrl($this->uploadedImagePath);
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
        $oldImage = (string) ($project->image ?? '');
        $oldUrl = (string) ($project->website_url ?? '');

        return $oldUrl !== $this->websiteUrl
            && Str::startsWith($oldImage, [
                'https://s.wordpress.com/mshots/v1/',
                'https://placehold.co/',
            ]);
    }

    private function publicSupabaseUrl(string $path): string
    {
        $supabaseUrl = rtrim((string) config('services.supabase.url'), '/');
        $bucket = trim((string) config('services.supabase.storage_bucket'), '/');

        if ($supabaseUrl === '' || $bucket === '') {
            throw new RuntimeException('Konfigurasi Supabase belum lengkap.');
        }

        return $supabaseUrl.'/storage/v1/object/public/'.$bucket.'/'.$this->encodeStoragePath($path);
    }

    private function deleteSupabaseImageByUrl(?string $imageUrl): void
    {
        $path = $this->extractSupabasePath($imageUrl);

        if ($path !== null) {
            $this->deleteSupabaseObject($path);
        }
    }

    private function deleteSupabaseObject(string $path): void
    {
        try {
            $supabaseUrl = rtrim((string) config('services.supabase.url'), '/');
            $bucket = trim((string) config('services.supabase.storage_bucket'), '/');
            $serviceKey = (string) config('services.supabase.service_role_key');

            if ($supabaseUrl === '' || $bucket === '' || $serviceKey === '') {
                return;
            }

            $response = Http::withToken($serviceKey)
                ->withHeaders([
                    'apikey' => $serviceKey,
                    'Accept' => 'application/json',
                ])
                ->timeout(15)
                ->delete(
                    $supabaseUrl.'/storage/v1/object/'.$bucket,
                    ['prefixes' => [$path]]
                );

            if (! $response->successful()) {
                report(new RuntimeException('Gagal menghapus file Supabase: '.$response->body()));
            }
        } catch (Throwable $exception) {
            report($exception);
        }
    }

    private function extractSupabasePath(?string $imageUrl): ?string
    {
        if (! $imageUrl) {
            return null;
        }

        $supabaseUrl = rtrim((string) config('services.supabase.url'), '/');
        $bucket = trim((string) config('services.supabase.storage_bucket'), '/');
        $prefix = $supabaseUrl.'/storage/v1/object/public/'.$bucket.'/';

        if ($supabaseUrl === '' || $bucket === '' || ! Str::startsWith($imageUrl, $prefix)) {
            return null;
        }

        $path = rawurldecode(Str::after($imageUrl, $prefix));

        return Str::startsWith($path, 'projects/') ? $path : null;
    }

    private function assertValidUploadedImagePath(string $path): void
    {
        if (! preg_match(
            '/^projects\/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}\.(jpg|jpeg|png|gif|webp|bmp)$/i',
            $path
        )) {
            throw ValidationException::withMessages([
                'uploadedImagePath' => 'Path gambar hasil upload tidak valid.',
            ]);
        }
    }

    private function encodeStoragePath(string $path): string
    {
        return collect(explode('/', $path))
            ->map(fn (string $segment): string => rawurlencode($segment))
            ->implode('/');
    }

    private function discardPendingUpload(): void
    {
        if ($this->uploadedImagePath !== null) {
            $this->deleteSupabaseObject($this->uploadedImagePath);
            $this->uploadedImagePath = null;
        }
    }

    private function generateWebsiteScreenshotUrl(string $url): string
    {
        return 'https://s.wordpress.com/mshots/v1/'.rawurlencode($url).'?w=1200';
    }

    private function generatePlaceholderImageUrl(): string
    {
        return 'https://placehold.co/1200x800/eef5f2/2f6f61?text=Portfolio+Project';
    }

    private function normalizeWebsiteUrl(?string $url): string
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
            'uploadedImagePath',
            'existingImageUrl',
            'editingProjectId',
        ]);

        $this->projectStatus = 'review';
        $this->isEditing = false;
        $this->resetValidation();
    }

    public function render()
    {
        $projects = $this->projectQuery()
            ->when($this->search !== '', function ($query): void {
                $query->where(function ($subQuery): void {
                    $subQuery
                        ->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('client', 'like', '%'.$this->search.'%')
                        ->orWhere('category', 'like', '%'.$this->search.'%')
                        ->orWhere('website_url', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->status !== 'all', function ($query): void {
                $query->where('status', $this->status);
            })
            ->latest()
            ->paginate(6);

        return view('livewire.projects.index', compact('projects'));
    }
}
