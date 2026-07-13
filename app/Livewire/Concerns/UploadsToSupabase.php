<?php

namespace App\Livewire\Concerns;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

trait UploadsToSupabase
{
    protected function uploadLocalFileToSupabase(
        string $localPath,
        string $folder,
        string $mimeType,
        string $extension,
        string $errorKey = 'file'
    ): string {
        if (! is_file($localPath)) {
            throw ValidationException::withMessages([
                $errorKey => 'File sementara tidak ditemukan.',
            ]);
        }

        $supabaseUrl = rtrim((string) config('services.supabase.url'), '/');
        $bucket = (string) config('services.supabase.storage_bucket');
        $serviceKey = (string) config('services.supabase.service_role_key');

        if ($supabaseUrl === '' || $bucket === '' || $serviceKey === '') {
            throw ValidationException::withMessages([
                $errorKey => 'Konfigurasi Supabase belum lengkap.',
            ]);
        }

        $extension = ltrim(strtolower($extension), '.');
        $path = trim($folder, '/') . '/' . Str::uuid() . '.' . $extension;

        $encodedPath = collect(explode('/', $path))
            ->map(fn ($segment) => rawurlencode($segment))
            ->join('/');

        $uploadUrl = "{$supabaseUrl}/storage/v1/object/{$bucket}/{$encodedPath}";

        $response = Http::withToken($serviceKey)
            ->withHeaders([
                'apikey' => $serviceKey,
                'Content-Type' => $mimeType,
                'x-upsert' => 'false',
            ])
            ->withBody(file_get_contents($localPath), $mimeType)
            ->post($uploadUrl);

        if (! $response->successful()) {
            throw ValidationException::withMessages([
                $errorKey => 'Upload ke Supabase gagal: ' . $response->body(),
            ]);
        }

        return "{$supabaseUrl}/storage/v1/object/public/{$bucket}/{$encodedPath}";
    }

    protected function uploadUploadedFileToSupabase(
        $uploadedFile,
        string $folder,
        string $errorKey = 'file'
    ): string {
        $extension = $uploadedFile->getClientOriginalExtension()
            ?: $uploadedFile->extension()
            ?: 'bin';

        $mimeType = $uploadedFile->getMimeType()
            ?: 'application/octet-stream';

        return $this->uploadLocalFileToSupabase(
            $uploadedFile->getRealPath(),
            $folder,
            $mimeType,
            $extension,
            $errorKey
        );
    }

    protected function storeImageAsWebpToSupabase(
        $uploadedFile,
        string $folder,
        string $errorKey = 'image'
    ): string {
        if (! function_exists('imagewebp')) {
            throw ValidationException::withMessages([
                $errorKey => 'Ekstensi GD dengan dukungan WebP belum aktif di PHP.',
            ]);
        }

        $sourcePath = $uploadedFile->getRealPath();
        $mimeType = $uploadedFile->getMimeType();

        $image = match ($mimeType) {
            'image/jpeg', 'image/jpg' => imagecreatefromjpeg($sourcePath),
            'image/png' => imagecreatefrompng($sourcePath),
            'image/gif' => imagecreatefromgif($sourcePath),
            'image/webp' => imagecreatefromwebp($sourcePath),
            'image/bmp', 'image/x-ms-bmp' => function_exists('imagecreatefrombmp')
                ? imagecreatefrombmp($sourcePath)
                : false,
            default => false,
        };

        if (! $image) {
            throw ValidationException::withMessages([
                $errorKey => 'Format gambar tidak dapat dikonversi ke WebP.',
            ]);
        }

        if (function_exists('imagepalettetotruecolor')) {
            imagepalettetotruecolor($image);
        }

        imagealphablending($image, true);
        imagesavealpha($image, true);

        $tmpPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . Str::uuid() . '.webp';

        try {
            $converted = imagewebp($image, $tmpPath, 85);
            imagedestroy($image);

            if (! $converted || ! is_file($tmpPath)) {
                throw ValidationException::withMessages([
                    $errorKey => 'Gambar gagal dikonversi ke WebP.',
                ]);
            }

            return $this->uploadLocalFileToSupabase(
                $tmpPath,
                $folder,
                'image/webp',
                'webp',
                $errorKey
            );
        } finally {
                imagedestroy($image);
            

            if (is_file($tmpPath)) {
                @unlink($tmpPath);
            }
        }
    }
}