<?php

namespace App\Livewire\Certificates;

use App\Models\Certificate;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use RuntimeException;

#[Layout('layouts.dashboard')]
class Index extends Component
{
    use WithPagination;

    private const MAX_PDF_SIZE = 10 * 1024 * 1024;

    public string $search = '';

    public string $title = '';

    public string $issuer = '';

    public ?string $issuedAt = null;

    public string $description = '';

    public bool $isVisible = true;

    /**
     * Path object pada bucket Supabase.
     *
     * Contoh:
     * certificates/1/550e8400-e29b-41d4-a716-446655440000.pdf
     */
    public ?string $uploadedPdfPath = null;

    public ?string $uploadedPdfName = null;

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:180'],
            'issuer' => ['nullable', 'string', 'max:180'],
            'issuedAt' => ['nullable', 'date'],
            'description' => ['nullable', 'string', 'max:500'],
            'isVisible' => ['boolean'],
            'uploadedPdfPath' => ['required', 'string', 'max:500'],
            'uploadedPdfName' => ['nullable', 'string', 'max:255'],
        ];
    }

    protected function messages(): array
    {
        return [
            'uploadedPdfPath.required' =>
                'Silakan pilih dan unggah file PDF terlebih dahulu.',
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Membuat signed upload URL untuk PDF.
     *
     * File PDF tidak dikirim ke Laravel atau temporary storage Livewire.
     * Livewire hanya menerima metadata file yang kecil.
     */
    public function createCertificatePdfUpload(
        string $name,
        string $type,
        int $size
    ): array {
        $user = Auth::user();

        if (! $user) {
            return $this->failedUploadResponse(
                'Sesi login sudah berakhir. Silakan login kembali.'
            );
        }

        $validator = Validator::make(
            [
                'name' => $name,
                'type' => $type,
                'size' => $size,
            ],
            [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    function (
                        string $attribute,
                        mixed $value,
                        \Closure $fail
                    ): void {
                        if (
                            ! is_string($value)
                            || ! Str::endsWith(
                                Str::lower($value),
                                '.pdf'
                            )
                        ) {
                            $fail('File harus menggunakan ekstensi .pdf.');
                        }
                    },
                ],
                'type' => [
                    'required',
                    'string',
                    Rule::in([
                        'application/pdf',
                        'application/x-pdf',
                    ]),
                ],
                'size' => [
                    'required',
                    'integer',
                    'min:1',
                    'max:' . self::MAX_PDF_SIZE,
                ],
            ],
            [
                'type.in' => 'Format file harus PDF.',
                'size.max' => 'Ukuran file PDF maksimal 10 MB.',
            ]
        );

        if ($validator->fails()) {
            return $this->failedUploadResponse(
                $validator->errors()->first()
            );
        }

        $supabase = $this->supabaseConfiguration();

        if ($supabase === null) {
            return $this->failedUploadResponse(
                'Konfigurasi Supabase belum lengkap.'
            );
        }

        $path = 'certificates/'
            . $user->id
            . '/'
            . Str::uuid()
            . '.pdf';

        $encodedPath = $this->encodeStoragePath($path);

        $endpoint = $supabase['url']
            . '/storage/v1/object/upload/sign/'
            . rawurlencode($supabase['bucket'])
            . '/'
            . $encodedPath;

        try {
            $response = Http::withToken(
                $supabase['service_role_key']
            )
                ->withHeaders([
                    'apikey' => $supabase['service_role_key'],
                    'Accept' => 'application/json',
                ])
                ->withBody('{}', 'application/json')
                ->timeout(20)
                ->post($endpoint);
        } catch (\Throwable $exception) {
            report($exception);

            return $this->failedUploadResponse(
                'Tidak dapat terhubung ke Supabase.'
            );
        }

        if (! $response->successful()) {
            report(new RuntimeException(
                'Supabase signed PDF upload gagal. Status '
                . $response->status()
                . ': '
                . $response->body()
            ));

            return $this->failedUploadResponse(
                'Gagal membuat signed upload URL.'
            );
        }

        $responseData = $response->json();

        $relativeUrl = $responseData['url']
            ?? $responseData['signedUrl']
            ?? $responseData['signedURL']
            ?? null;

        if (
            ! is_string($relativeUrl)
            || trim($relativeUrl) === ''
        ) {
            return $this->failedUploadResponse(
                'Supabase tidak mengembalikan signed upload URL.'
            );
        }

        if (
            Str::startsWith(
                $relativeUrl,
                ['http://', 'https://']
            )
        ) {
            $signedUrl = $relativeUrl;
        } elseif (
            Str::startsWith(
                $relativeUrl,
                '/storage/v1/'
            )
        ) {
            $signedUrl = $supabase['url'] . $relativeUrl;
        } else {
            $signedUrl = $supabase['url']
                . '/storage/v1'
                . (
                    Str::startsWith($relativeUrl, '/')
                        ? $relativeUrl
                        : '/' . $relativeUrl
                );
        }

        return [
            'ok' => true,
            'path' => $path,
            'signed_url' => $signedUrl,
            'public_url' => $this->buildPublicUrl(
                $path,
                $supabase
            ),
        ];
    }

    /**
     * Dipanggil setelah browser selesai mengirim PDF ke Supabase.
     */
    public function setUploadedPdf(
        string $path,
        string $originalName
    ): array {
        $user = Auth::user();

        if (! $user) {
            return $this->failedUploadResponse(
                'Sesi login sudah berakhir. Silakan login kembali.'
            );
        }

        $expectedFolder = 'certificates/' . $user->id;

        if (
            ! $this->isValidOwnedPdfPath(
                $path,
                $expectedFolder
            )
        ) {
            return $this->failedUploadResponse(
                'Path file PDF tidak valid.'
            );
        }

        $nameValidator = Validator::make(
            ['name' => $originalName],
            ['name' => ['required', 'string', 'max:255']]
        );

        if ($nameValidator->fails()) {
            return $this->failedUploadResponse(
                'Nama file PDF tidak valid.'
            );
        }

        $this->uploadedPdfPath = $path;
        $this->uploadedPdfName = $originalName;

        $this->resetValidation('uploadedPdfPath');

        return [
            'ok' => true,
            'message' => 'File PDF selesai diunggah.',
        ];
    }

    public function clearUploadedPdf(): void
    {
        $this->reset([
            'uploadedPdfPath',
            'uploadedPdfName',
        ]);

        $this->resetValidation('uploadedPdfPath');
    }

    public function saveCertificate(): void
    {
        $validated = $this->validate();

        $user = Auth::user();

        if (! $user) {
            session()->flash(
                'error',
                'Sesi login sudah berakhir.'
            );

            return;
        }

        $expectedFolder = 'certificates/' . $user->id;

        if (
            ! $this->isValidOwnedPdfPath(
                $validated['uploadedPdfPath'],
                $expectedFolder
            )
        ) {
            $this->addError(
                'uploadedPdfPath',
                'Path file PDF tidak valid.'
            );

            return;
        }

        $supabase = $this->supabaseConfiguration();

        if ($supabase === null) {
            $this->addError(
                'uploadedPdfPath',
                'Konfigurasi Supabase belum lengkap.'
            );

            return;
        }

        $pdfUrl = $this->buildPublicUrl(
            $validated['uploadedPdfPath'],
            $supabase
        );

        Certificate::create([
            'user_id' => $user->id,
            'title' => $validated['title'],
            'issuer' => $validated['issuer'] ?: null,
            'issued_at' => $validated['issuedAt'] ?: null,
            'description' =>
                $validated['description'] ?: null,
            'is_visible' => $validated['isVisible'],
            'pdf_path' => $pdfUrl,
        ]);

        $this->resetForm();

        $this->dispatch('certificate-saved');

        session()->flash(
            'success',
            'Sertifikat berhasil diunggah.'
        );
    }

    public function toggleVisibility(
        int $certificateId
    ): void {
        $certificate = Certificate::query()
            ->where('user_id', Auth::id())
            ->find($certificateId);

        if (! $certificate) {
            return;
        }

        $certificate->update([
            'is_visible' => ! $certificate->is_visible,
        ]);

        session()->flash(
            'success',
            'Status sertifikat berhasil diperbarui.'
        );
    }

    public function deleteCertificate(
        int $certificateId
    ): void {
        $certificate = Certificate::query()
            ->where('user_id', Auth::id())
            ->find($certificateId);

        if (! $certificate) {
            return;
        }

        /*
         * Record database dihapus.
         *
         * File Supabase tidak dihapus otomatis karena database saat ini
         * hanya menyimpan public URL, bukan object path terpisah.
         */
        $certificate->delete();

        session()->flash(
            'success',
            'Sertifikat berhasil dihapus.'
        );
    }

    public function render(): View
    {
        $certificates = Certificate::query()
            ->where('user_id', Auth::id())
            ->when(
                $this->search !== '',
                function ($query): void {
                    $query->where(
                        function ($subQuery): void {
                            $term = '%' . $this->search . '%';

                            $subQuery
                                ->where('title', 'like', $term)
                                ->orWhere(
                                    'issuer',
                                    'like',
                                    $term
                                )
                                ->orWhere(
                                    'description',
                                    'like',
                                    $term
                                );
                        }
                    );
                }
            )
            ->latest()
            ->paginate(8);

        return view(
            'livewire.certificates.index',
            [
                'certificates' => $certificates,
            ]
        );
    }

    private function resetForm(): void
    {
        $this->reset([
            'title',
            'issuer',
            'issuedAt',
            'description',
            'uploadedPdfPath',
            'uploadedPdfName',
        ]);

        $this->isVisible = true;

        $this->resetValidation();
    }

    private function supabaseConfiguration(): ?array
    {
        $url = rtrim(
            (string) config('services.supabase.url'),
            '/'
        );

        $bucket = trim(
            (string) config(
                'services.supabase.storage_bucket'
            ),
            '/'
        );

        $serviceRoleKey = (string) config(
            'services.supabase.service_role_key'
        );

        if (
            $url === ''
            || $bucket === ''
            || $serviceRoleKey === ''
        ) {
            return null;
        }

        return [
            'url' => $url,
            'bucket' => $bucket,
            'service_role_key' => $serviceRoleKey,
        ];
    }

    private function buildPublicUrl(
        string $path,
        array $supabase
    ): string {
        return $supabase['url']
            . '/storage/v1/object/public/'
            . rawurlencode($supabase['bucket'])
            . '/'
            . $this->encodeStoragePath($path);
    }

    private function encodeStoragePath(
        string $path
    ): string {
        return implode(
            '/',
            array_map(
                static fn (string $segment): string =>
                    rawurlencode($segment),
                explode('/', $path)
            )
        );
    }

    private function isValidOwnedPdfPath(
        string $path,
        string $expectedFolder
    ): bool {
        if (
            $path === ''
            || Str::contains($path, ['..', '\\'])
            || ! Str::startsWith(
                $path,
                $expectedFolder . '/'
            )
        ) {
            return false;
        }

        return preg_match(
            '/^[a-zA-Z0-9\/_-]+\.pdf$/',
            $path
        ) === 1;
    }

    private function failedUploadResponse(
        string $message
    ): array {
        return [
            'ok' => false,
            'message' => $message,
        ];
    }
}
