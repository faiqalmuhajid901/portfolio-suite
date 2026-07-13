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
use RuntimeException;
use Throwable;

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

    /*
     * Jangan gunakan:
     *
     * public ?string $imageUpload = null;
     *
     * Livewire mengisi properti ini dengan objek TemporaryUploadedFile,
     * bukan string.
     */
    public $imageUpload = null;

    protected function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:120',
            ],

            'category' => [
                'nullable',
                'string',
                'max:120',
            ],

            'client' => [
                'nullable',
                'string',
                'max:120',
            ],

            'projectStatus' => [
                'required',
                'in:in_progress,review,completed',
            ],

            'startDate' => [
                'nullable',
                'date',
            ],

            'endDate' => [
                'nullable',
                'date',
                'after_or_equal:startDate',
            ],

            'websiteUrl' => [
                'nullable',
                'url',
                'max:255',
            ],

            'imageUpload' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,gif,webp,bmp',
                'max:4096',
            ],

            'description' => [
                'nullable',
                'string',
                'max:500',
            ],

            'tagsInput' => [
                'nullable',
                'string',
                'max:255',
            ],
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

            'imageUpload.image' => 'File yang dipilih harus berupa gambar.',
            'imageUpload.mimes' => 'Format gambar harus JPG, JPEG, PNG, GIF, WEBP, atau BMP.',
            'imageUpload.max' => 'Ukuran gambar maksimal 4 MB.',

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

    /**
     * Dipanggil otomatis setelah pengguna memilih gambar.
     * Kesalahan format atau ukuran langsung ditampilkan.
     */
    public function updatedImageUpload(): void
    {
        $this->resetErrorBag('imageUpload');

        $this->validateOnly('imageUpload');
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
            session()->flash('error', 'Project tidak ditemukan.');

            return;
        }

        $this->isEditing = true;
        $this->editingProjectId = $project->id;

        $this->name = $project->name ?? '';
        $this->category = $project->category ?? '';
        $this->client = $project->client ?? '';
        $this->projectStatus = $project->status ?? 'review';

        $this->startDate = $project->start_date
            ? $project->start_date->format('Y-m-d')
            : null;

        $this->endDate = $project->end_date
            ? $project->end_date->format('Y-m-d')
            : null;

        $this->websiteUrl = $project->website_url ?? '';
        $this->description = $project->description ?? '';

        $this->tagsInput = is_array($project->tags)
            ? implode(', ', $project->tags)
            : '';

        $this->imageUpload = null;
        $this->showCreateModal = true;
    }

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;

        $this->resetForm();
    }

    public function saveProject(): void
    {
        $this->websiteUrl = $this->normalizeWebsiteUrl(
            $this->websiteUrl
        );

        try {
            /*
             * Validasi dilakukan sebelum upload dan sebelum penyimpanan database.
             */
            $this->validate();

            $project = $this->findEditingProject();

            if ($this->isEditing && ! $project) {
                session()->flash('error', 'Project yang akan diperbarui tidak ditemukan.');

                return;
            }

            $tags = $this->prepareTags();

            /*
             * Gambar diselesaikan sebelum Project::create() atau update().
             *
             * Jika upload ke Supabase gagal, exception dilempar dan database
             * tidak akan menyimpan placeholder sebagai pengganti upload gagal.
             */
            $imagePath = $this->resolveProjectImage($project);

            $payload = [
                'user_id' => Auth::id(),
                'name' => trim($this->name),
                'category' => $this->nullableTrim($this->category),
                'client' => $this->nullableTrim($this->client),
                'status' => $this->projectStatus,
                'start_date' => $this->startDate ?: null,
                'end_date' => $this->endDate ?: null,
                'image' => $imagePath,
                'website_url' => $this->websiteUrl ?: null,
                'description' => $this->nullableTrim($this->description),
                'tags' => $tags,
            ];

            if ($project) {
                $project->update($payload);

                session()->flash(
                    'success',
                    'Project berhasil diperbarui.'
                );
            } else {
                $payload['likes'] = 0;

                Project::create($payload);

                session()->flash(
                    'success',
                    'Project berhasil ditambahkan.'
                );
            }

            $this->showCreateModal = false;

            $this->resetForm();
        } catch (ValidationException $exception) {
            /*
             * Biarkan Livewire menampilkan pesan validasi ke @error().
             */
            throw $exception;
        } catch (Throwable $exception) {
            report($exception);

            $message = app()->isLocal()
                ? 'Penyimpanan project gagal: ' . $exception->getMessage()
                : 'Penyimpanan project gagal. Periksa gambar dan coba kembali.';

            /*
             * Modal tidak ditutup agar pengguna dapat mengganti file.
             */
            if ($this->imageUpload) {
                $this->addError('imageUpload', $message);
            }

            session()->flash('error', $message);
        }
    }

    public function deleteProject(int $projectId): void
    {
        try {
            $project = Project::find($projectId);

            if (! $project) {
                session()->flash('error', 'Project tidak ditemukan.');

                return;
            }

            /*
             * Aktifkan penghapusan file jika trait UploadsToSupabase
             * menyediakan method untuk menghapus file Supabase.
             */
            $this->deleteOldProjectImage($project->image);

            $project->delete();

            session()->flash(
                'success',
                'Project berhasil dihapus.'
            );
        } catch (Throwable $exception) {
            report($exception);

            session()->flash(
                'error',
                app()->isLocal()
                    ? 'Project gagal dihapus: ' . $exception->getMessage()
                    : 'Project gagal dihapus.'
            );
        }
    }

    public function updateStatus(
        int $projectId,
        string $status
    ): void {
        if (! in_array(
            $status,
            ['in_progress', 'review', 'completed'],
            true
        )) {
            session()->flash('error', 'Status project tidak valid.');

            return;
        }

        try {
            $updated = Project::whereKey($projectId)->update([
                'status' => $status,
            ]);

            if ($updated === 0) {
                session()->flash('error', 'Project tidak ditemukan.');

                return;
            }

            session()->flash(
                'success',
                'Status project berhasil diperbarui.'
            );
        } catch (Throwable $exception) {
            report($exception);

            session()->flash(
                'error',
                'Status project gagal diperbarui.'
            );
        }
    }

    public function exportCsv()
    {
        $projects = Project::latest()->get();

        return response()->streamDownload(
            function () use ($projects): void {
                $handle = fopen('php://output', 'w');

                if ($handle === false) {
                    throw new RuntimeException(
                        'Gagal membuat file CSV.'
                    );
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
            },
            'portfolio-projects.csv'
        );
    }

    private function findEditingProject(): ?Project
    {
        if (! $this->isEditing || ! $this->editingProjectId) {
            return null;
        }

        return Project::find($this->editingProjectId);
    }

    private function prepareTags(): array
    {
        return collect(explode(',', $this->tagsInput))
            ->map(
                fn (string $tag): string => trim($tag)
            )
            ->filter(
                fn (string $tag): bool => $tag !== ''
            )
            ->unique()
            ->values()
            ->toArray();
    }

    private function resolveProjectImage(
        ?Project $project
    ): string {
        /*
         * Jika pengguna memilih file, file tersebut wajib berhasil diupload.
         *
         * Sistem tidak boleh diam-diam mengganti file gagal dengan placeholder.
         */
        if ($this->imageUpload) {
            $newImage = $this->storeImageAsWebpToSupabase(
                $this->imageUpload,
                'projects',
                'imageUpload'
            );

            if (
                ! is_string($newImage)
                || trim($newImage) === ''
            ) {
                throw new RuntimeException(
                    'Supabase tidak mengembalikan URL gambar setelah upload.'
                );
            }

            if ($project && $project->image !== $newImage) {
                $this->deleteOldProjectImage($project->image);
            }

            return $newImage;
        }

        /*
         * Saat edit dan tidak ada gambar baru:
         * pertahankan gambar lama.
         */
        if ($project) {
            if ($this->shouldRegenerateAutoImage($project)) {
                if ($this->websiteUrl !== '') {
                    return $this->generateWebsiteScreenshotUrl(
                        $this->websiteUrl
                    );
                }

                return $this->generatePlaceholderImageUrl();
            }

            if (
                is_string($project->image)
                && trim($project->image) !== ''
            ) {
                return $project->image;
            }

            return $this->generatePlaceholderImageUrl();
        }

        /*
         * Saat membuat project tanpa upload:
         * gunakan screenshot website jika URL diisi.
         */
        if ($this->websiteUrl !== '') {
            return $this->generateWebsiteScreenshotUrl(
                $this->websiteUrl
            );
        }

        /*
         * Placeholder hanya digunakan ketika pengguna memang tidak
         * memilih file dan tidak memberikan website URL.
         */
        return $this->generatePlaceholderImageUrl();
    }

    private function shouldRegenerateAutoImage(
        Project $project
    ): bool {
        $oldImage = (string) ($project->image ?? '');
        $oldUrl = (string) ($project->website_url ?? '');

        $websiteUrlChanged = $oldUrl !== $this->websiteUrl;

        $oldImageWasGeneratedAutomatically = Str::startsWith(
            $oldImage,
            [
                'https://s.wordpress.com/mshots/v1/',
                'https://placehold.co/',
            ]
        );

        return $websiteUrlChanged
            && $oldImageWasGeneratedAutomatically;
    }

    private function generateWebsiteScreenshotUrl(
        string $url
    ): string {
        return 'https://s.wordpress.com/mshots/v1/'
            . rawurlencode($url)
            . '?w=1200';
    }

    private function generatePlaceholderImageUrl(): string
    {
        return 'https://placehold.co/1200x800/eef5f2/2f6f61'
            . '?text=Portfolio+Project';
    }

    /**
     * Method ini sengaja tidak menghapus file secara langsung karena
     * mekanisme penghapusan tergantung implementasi UploadsToSupabase.
     *
     * Jangan gunakan Storage::disk('public')->delete() untuk URL Supabase.
     */
    private function deleteOldProjectImage(
        ?string $imageUrl
    ): void {
        if (! $imageUrl) {
            return;
        }

        /*
         * URL placeholder dan screenshot bukan file milik bucket Supabase.
         */
        if (
            Str::startsWith(
                $imageUrl,
                [
                    'https://placehold.co/',
                    'https://s.wordpress.com/mshots/v1/',
                ]
            )
        ) {
            return;
        }

        /*
         * Contoh jika trait Anda memiliki method:
         *
         * $this->deleteFileFromSupabase($imageUrl);
         *
         * Jangan aktifkan sebelum method tersebut benar-benar tersedia.
         */
    }

    private function normalizeWebsiteUrl(
        ?string $url
    ): string {
        $url = trim((string) $url);

        if ($url === '') {
            return '';
        }

        if (! preg_match('/^https?:\/\//i', $url)) {
            return 'https://' . $url;
        }

        return $url;
    }

    private function nullableTrim(
        ?string $value
    ): ?string {
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
            'imageUpload',
            'editingProjectId',
        ]);

        $this->projectStatus = 'review';
        $this->isEditing = false;

        $this->resetValidation();
    }

    public function render()
    {
        $projects = Project::query()
            ->when(
                $this->search !== '',
                function ($query): void {
                    $query->where(
                        function ($subQuery): void {
                            $subQuery
                                ->where(
                                    'name',
                                    'like',
                                    '%' . $this->search . '%'
                                )
                                ->orWhere(
                                    'client',
                                    'like',
                                    '%' . $this->search . '%'
                                )
                                ->orWhere(
                                    'category',
                                    'like',
                                    '%' . $this->search . '%'
                                )
                                ->orWhere(
                                    'website_url',
                                    'like',
                                    '%' . $this->search . '%'
                                );
                        }
                    );
                }
            )
            ->when(
                $this->status !== 'all',
                function ($query): void {
                    $query->where(
                        'status',
                        $this->status
                    );
                }
            )
            ->latest()
            ->paginate(6);

        return view(
            'livewire.projects.index',
            [
                'projects' => $projects,
            ]
        );
    }
}
