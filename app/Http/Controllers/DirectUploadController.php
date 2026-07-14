<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use RuntimeException;
use App\Http\Controllers\Controller;

class DirectUploadController extends Controller
{
    public function createSignedUrl(Request $request): JsonResponse
    {
        $data = $request->validate([
            'target' => [
                'required',
                Rule::in([
                    'profile-photo',
                    'hero-background',
                    'certificate',
                ]),
            ],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string'],
            'size' => ['required', 'integer', 'min:1'],
        ]);

        $configuration = match ($data['target']) {
            'profile-photo' => [
                'folder' => 'profile-photos',
                'max' => 4 * 1024 * 1024,
                'mimes' => [
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'image/gif' => 'gif',
                    'image/webp' => 'webp',
                    'image/bmp' => 'bmp',
                    'image/x-ms-bmp' => 'bmp',
                ],
            ],

            'hero-background' => [
                'folder' => 'hero-backgrounds',
                'max' => 8 * 1024 * 1024,
                'mimes' => [
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'image/gif' => 'gif',
                    'image/webp' => 'webp',
                    'image/bmp' => 'bmp',
                    'image/x-ms-bmp' => 'bmp',
                ],
            ],

            'certificate' => [
                'folder' => 'certificates',
                'max' => 10 * 1024 * 1024,
                'mimes' => [
                    'application/pdf' => 'pdf',
                ],
            ],
        };

        if ($data['size'] > $configuration['max']) {
            return response()->json([
                'message' => 'Ukuran file melebihi batas maksimum.',
            ], 422);
        }

        $extension = $configuration['mimes'][$data['type']] ?? null;

        if ($extension === null) {
            return response()->json([
                'message' => 'Format file tidak didukung.',
            ], 422);
        }

        $supabaseUrl = rtrim(
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
            $supabaseUrl === ''
            || $bucket === ''
            || $serviceRoleKey === ''
        ) {
            return response()->json([
                'message' => 'Konfigurasi Supabase belum lengkap.',
            ], 500);
        }

        $path = $configuration['folder']
            . '/'
            . Str::uuid()
            . '.'
            . $extension;

        $encodedPath = collect(explode('/', $path))
            ->map(
                fn (string $segment): string => rawurlencode($segment)
            )
            ->implode('/');

        $endpoint = $supabaseUrl
            . '/storage/v1/object/upload/sign/'
            . rawurlencode($bucket)
            . '/'
            . $encodedPath;

        $response = Http::withToken($serviceRoleKey)
            ->withHeaders([
                'apikey' => $serviceRoleKey,
                'Accept' => 'application/json',
            ])
            ->withBody('{}', 'application/json')
            ->timeout(20)
            ->post($endpoint);

        if (! $response->successful()) {
            report(new RuntimeException(
                'Supabase signed upload gagal: '
                . $response->status()
                . ' '
                . $response->body()
            ));

            return response()->json([
                'message' => 'Gagal membuat signed upload URL.',
            ], 502);
        }

        $responseData = $response->json();

        $relativeUrl = $responseData['url']
            ?? $responseData['signedUrl']
            ?? $responseData['signedURL']
            ?? null;

        if (! is_string($relativeUrl) || trim($relativeUrl) === '') {
            return response()->json([
                'message' => 'Signed upload URL tidak ditemukan.',
            ], 502);
        }

        if (Str::startsWith($relativeUrl, ['http://', 'https://'])) {
            $signedUrl = $relativeUrl;
        } elseif (Str::startsWith($relativeUrl, '/storage/v1/')) {
            $signedUrl = $supabaseUrl.$relativeUrl;
        } else {
            $signedUrl = $supabaseUrl
                . '/storage/v1'
                . (Str::startsWith($relativeUrl, '/')
                    ? $relativeUrl
                    : '/'.$relativeUrl);
        }

        $publicUrl = $supabaseUrl
            . '/storage/v1/object/public/'
            . rawurlencode($bucket)
            . '/'
            . $encodedPath;

        return response()->json([
            'path' => $path,
            'signed_url' => $signedUrl,
            'public_url' => $publicUrl,
        ]);
    }
}
