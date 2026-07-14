<?php

namespace App\Livewire\Settings;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;
use RuntimeException;

#[Layout('layouts.dashboard')]
class Index extends Component
{
    private const IMAGE_MIME_EXTENSIONS = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
        'image/bmp' => 'bmp',
        'image/x-ms-bmp' => 'bmp',
    ];

    public string $name = '';

    public string $email = '';

    public string $currentPassword = '';

    public string $newPassword = '';

    public string $newPasswordConfirmation = '';

    public string $heroBadge = '';

    public string $heroTitle = '';

    public string $heroDescription = '';

    public function mount(): void
    {
        $user = Auth::user();

        $this->name = $user?->name ?? '';
        $this->email = $user?->email ?? '';

        $profile = $user?->profile;

        $this->heroBadge = $profile?->hero_badge ?? 'MY PORTFOLIO';
        $this->heroTitle = $profile?->hero_title
            ?? 'Creative portfolio system for refined digital projects.';
        $this->heroDescription = $profile?->hero_description
            ?? 'Explore selected works, project progress, design systems, and portfolio activity from one clean public page.';
    }

    /**
     * Membuat signed upload URL Supabase.
     *
     * File gambar tidak dikirim ke Laravel/Livewire.
     * Laravel hanya menerima metadata file yang kecil.
     */
    public function createSettingsImageUpload(
        string $target,
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
                'target' => $target,
                'name' => $name,
                'type' => $type,
                'size' => $size,
            ],
            [
                'target' => [
                    'required',
                    Rule::in(['profile-photo', 'hero-background']),
                ],
                'name' => ['required', 'string', 'max:255'],
                'type' => [
                    'required',
                    'string',
                    Rule::in(array_keys(self::IMAGE_MIME_EXTENSIONS)),
                ],
                // Batas global. Batas khusus target diperiksa lagi di bawah.
                'size' => ['required', 'integer', 'min:1', 'max:8388608'],
            ],
            [
                'target.in' => 'Target upload gambar tidak valid.',
                'type.in' => 'Format gambar harus JPG, PNG, GIF, WEBP, atau BMP.',
                'size.max' => 'Ukuran gambar maksimal 8 MB.',
            ]
        );

        if ($validator->fails()) {
            return $this->failedUploadResponse(
                $validator->errors()->first()
            );
        }

        $data = $validator->validated();

        $definition = $this->uploadDefinition(
            $data['target'],
            (int) $user->id
        );

        if ($definition === null) {
            return $this->failedUploadResponse(
                'Target upload gambar tidak ditemukan.'
            );
        }

        if ((int) $data['size'] > $definition['max_bytes']) {
            return $this->failedUploadResponse(
                $definition['max_message']
            );
        }

        $extension = self::IMAGE_MIME_EXTENSIONS[$data['type']] ?? null;

        if ($extension === null) {
            return $this->failedUploadResponse(
                'Format gambar tidak didukung.'
            );
        }

        $supabase = $this->supabaseConfiguration();

        if ($supabase === null) {
            return $this->failedUploadResponse(
                'Konfigurasi Supabase belum lengkap.'
            );
        }

        $path = $definition['folder']
            . '/'
            . Str::uuid()
            . '.'
            . $extension;

        $encodedPath = $this->encodeStoragePath($path);

        $endpoint = $supabase['url']
            . '/storage/v1/object/upload/sign/'
            . rawurlencode($supabase['bucket'])
            . '/'
            . $encodedPath;

        try {
            $response = Http::withToken($supabase['service_role_key'])
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
                'Supabase signed upload gagal. Status '
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

        if (Str::startsWith($relativeUrl, ['http://', 'https://'])) {
            $signedUrl = $relativeUrl;
        } elseif (Str::startsWith($relativeUrl, '/storage/v1/')) {
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

        $publicUrl = $this->buildPublicUrl(
            $path,
            $supabase
        );

        return [
            'ok' => true,
            'path' => $path,
            'signed_url' => $signedUrl,
            'public_url' => $publicUrl,
        ];
    }

    /**
     * Menyimpan path gambar yang sudah berhasil di-upload langsung
     * dari browser ke Supabase.
     */
    public function saveSettingsImage(
        string $target,
        string $path
    ): array {
        $user = Auth::user();

        if (! $user) {
            return $this->failedUploadResponse(
                'Sesi login sudah berakhir. Silakan login kembali.'
            );
        }

        $definition = $this->uploadDefinition(
            $target,
            (int) $user->id
        );

        if ($definition === null) {
            return $this->failedUploadResponse(
                'Target penyimpanan gambar tidak valid.'
            );
        }

        if (! $this->isValidOwnedImagePath($path, $definition['folder'])) {
            return $this->failedUploadResponse(
                'Path gambar tidak valid.'
            );
        }

        $supabase = $this->supabaseConfiguration();

        if ($supabase === null) {
            return $this->failedUploadResponse(
                'Konfigurasi Supabase belum lengkap.'
            );
        }

        $publicUrl = $this->buildPublicUrl(
            $path,
            $supabase
        );

        $currentProfile = $user->profile;

        $profileData = [
            'name' => $user->name,
            'role' => $currentProfile?->role ?? 'Portfolio Manager',
            'bio' => $currentProfile?->bio,
            'avatar' => $currentProfile?->avatar,
            'hero_badge' => $this->heroBadge,
            'hero_title' => $this->heroTitle,
            'hero_description' => $this->heroDescription,
            'hero_background' => $currentProfile?->hero_background,
        ];

        $profileData[$definition['database_field']] = $publicUrl;

        $savedProfile = $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $profileData
        );

        // Memastikan tampilan Livewire langsung memakai data terbaru.
        $user->setRelation('profile', $savedProfile);

        session()->flash(
            $definition['flash_key'],
            $definition['success_message']
        );

        return [
            'ok' => true,
            'public_url' => $publicUrl,
            'message' => $definition['success_message'],
        ];
    }

    public function updateAccount(): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        $currentProfile = $user->profile;

        $savedProfile = $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'name' => $validated['name'],
                'role' => $currentProfile?->role ?? 'Portfolio Manager',
                'bio' => $currentProfile?->bio,
                'avatar' => $currentProfile?->avatar,
                'hero_badge' => $this->heroBadge,
                'hero_title' => $this->heroTitle,
                'hero_description' => $this->heroDescription,
                'hero_background' => $currentProfile?->hero_background,
            ]
        );

        $user->setRelation('profile', $savedProfile);

        session()->flash(
            'account_success',
            'Nama dan email berhasil diperbarui.'
        );
    }

    public function updateHeroContent(): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $validated = $this->validate([
            'heroBadge' => ['required', 'string', 'max:80'],
            'heroTitle' => ['required', 'string', 'max:180'],
            'heroDescription' => ['required', 'string', 'max:500'],
        ]);

        $currentProfile = $user->profile;

        $savedProfile = $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'name' => $user->name,
                'role' => $currentProfile?->role ?? 'Portfolio Manager',
                'bio' => $currentProfile?->bio,
                'avatar' => $currentProfile?->avatar,
                'hero_badge' => $validated['heroBadge'],
                'hero_title' => $validated['heroTitle'],
                'hero_description' => $validated['heroDescription'],
                'hero_background' => $currentProfile?->hero_background,
            ]
        );

        $user->setRelation('profile', $savedProfile);

        session()->flash(
            'hero_success',
            'Konten halaman public berhasil diperbarui.'
        );
    }

    public function updatePassword(): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $this->validate(
            [
                'currentPassword' => ['required'],
                'newPassword' => ['required', Password::defaults()],
                'newPasswordConfirmation' => [
                    'required',
                    'same:newPassword',
                ],
            ],
            [
                'newPasswordConfirmation.same' =>
                    'Konfirmasi password baru tidak sama.',
            ]
        );

        if (! Hash::check($this->currentPassword, $user->password)) {
            $this->addError(
                'currentPassword',
                'Password lama tidak sesuai.'
            );

            return;
        }

        $user->update([
            'password' => Hash::make($this->newPassword),
        ]);

        $this->reset([
            'currentPassword',
            'newPassword',
            'newPasswordConfirmation',
        ]);

        session()->flash(
            'password_success',
            'Password berhasil diperbarui.'
        );
    }

    public function render(): View
    {
        return view('livewire.settings.index');
    }

    private function uploadDefinition(
        string $target,
        int $userId
    ): ?array {
        return match ($target) {
            'profile-photo' => [
                'folder' => 'profile-photos/' . $userId,
                'max_bytes' => 4 * 1024 * 1024,
                'max_message' => 'Ukuran foto profil maksimal 4 MB.',
                'database_field' => 'avatar',
                'flash_key' => 'photo_success',
                'success_message' =>
                    'Foto profil berhasil diperbarui.',
            ],

            'hero-background' => [
                'folder' => 'hero-backgrounds/' . $userId,
                'max_bytes' => 8 * 1024 * 1024,
                'max_message' => 'Ukuran background maksimal 8 MB.',
                'database_field' => 'hero_background',
                'flash_key' => 'hero_background_success',
                'success_message' =>
                    'Background halaman public berhasil diperbarui.',
            ],

            default => null,
        };
    }

    private function supabaseConfiguration(): ?array
    {
        $url = rtrim(
            (string) config('services.supabase.url'),
            '/'
        );

        $bucket = trim(
            (string) config('services.supabase.storage_bucket'),
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

    private function encodeStoragePath(string $path): string
    {
        return implode(
            '/',
            array_map(
                static fn (string $segment): string =>
                    rawurlencode($segment),
                explode('/', $path)
            )
        );
    }

    private function isValidOwnedImagePath(
        string $path,
        string $expectedFolder
    ): bool {
        if (
            $path === ''
            || Str::contains($path, ['..', '\\'])
            || ! Str::startsWith($path, $expectedFolder . '/')
        ) {
            return false;
        }

        return preg_match(
            '/\.(jpg|jpeg|png|gif|webp|bmp)$/i',
            $path
        ) === 1;
    }

    private function failedUploadResponse(string $message): array
    {
        return [
            'ok' => false,
            'message' => $message,
        ];
    }
}
