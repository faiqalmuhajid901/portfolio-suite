<?php

namespace App\Livewire\Certificates;

use App\Livewire\Concerns\UploadsToSupabase;
use App\Models\Certificate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.dashboard')]
class Index extends Component
{
    use WithFileUploads;
    use WithPagination;
    use UploadsToSupabase;

    public string $search = '';

    public string $title = '';
    public string $issuer = '';
    public ?string $issuedAt = null;
    public string $description = '';
    public bool $isVisible = true;

    public $pdfUpload = null;

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:180'],
            'issuer' => ['nullable', 'string', 'max:180'],
            'issuedAt' => ['nullable', 'date'],
            'description' => ['nullable', 'string', 'max:500'],
            'isVisible' => ['boolean'],
            'pdfUpload' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function saveCertificate(): void
{
    $validated = $this->validate();

    $pdfUrl = $this->uploadUploadedFileToSupabase(
        $this->pdfUpload,
        'certificates',
        'application/pdf',
        'pdf',
        'pdfUpload'
    );

    Certificate::create([
        'user_id' => Auth::id(),
        'title' => $validated['title'],
        'issuer' => $validated['issuer'] ?: null,
        'issued_at' => $validated['issuedAt'] ?: null,
        'description' => $validated['description'] ?: null,
        'is_visible' => $validated['isVisible'],
        'pdf_path' => $pdfUrl,
    ]);

    $this->resetForm();

    session()->flash('success', 'Sertifikat berhasil diupload.');
}

    public function toggleVisibility(int $certificateId): void
    {
        $certificate = Certificate::find($certificateId);

        if (! $certificate) {
            return;
        }

        $certificate->update([
            'is_visible' => ! $certificate->is_visible,
        ]);

        session()->flash('success', 'Status sertifikat berhasil diperbarui.');
    }

    public function deleteCertificate(int $certificateId): void
    {
        $certificate = Certificate::find($certificateId);

        if (! $certificate) {
            return;
        }

        if ($certificate->pdf_path && Str::startsWith($certificate->pdf_path, 'storage/certificates/')) {
        }

        $certificate->delete();

        session()->flash('success', 'Sertifikat berhasil dihapus.');
    }

    private function resetForm(): void
    {
        $this->reset([
            'title',
            'issuer',
            'issuedAt',
            'description',
            'pdfUpload',
        ]);

        $this->isVisible = true;
        $this->resetValidation();
    }

    public function render()
    {
        $certificates = Certificate::query()
            ->when($this->search !== '', function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('issuer', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(8);

        return view('livewire.certificates.index', [
            'certificates' => $certificates,
        ]);
    }
}