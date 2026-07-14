<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use RuntimeException;

class ProjectImageUploadController extends Controller
{
    public function createSignedUploadUrl(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => [
                'required',
                'string',
                Rule::in([
                    'image/jpeg',
                    'image/png',
                    'image/gif',
                    'image/webp',
                    'image/bmp',
                    'image/x-ms-bmp',
                ]),
            ],
            'size' => ['required', 'integer', 'min:1', 'max:4194304'],
        ], [
            'type.in' => 'Format gambar harus JPG, JPEG, PNG, GIF, WEBP, atau BMP.',
            'size.max' => 'Ukuran gambar maksimal 4 MB.',
        ]);

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

        $extension = match ($data['type']) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'image/bmp',
            'image/x-ms-bmp' => 'bmp',
            default => throw new RuntimeException(
                'Format gambar tidak didukung.'
            ),
        };

        $path = 'projects/' . Str::uuid() . '.' . $extension;

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
                'Supabase signed upload gagal. Status '
                . $response->status()
                . ': '
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

        if (
            ! is_string($relativeUrl)
            || trim($relativeUrl) === ''
        ) {
            return response()->json([
                'message' => 'Supabase tidak mengembalikan signed upload URL.',
            ], 502);
        }

        if (Str::startsWith($relativeUrl, ['http://', 'https://'])) {
            $signedUrl = $relativeUrl;
        } elseif (Str::startsWith($relativeUrl, '/storage/v1/')) {
            $signedUrl = $supabaseUrl . $relativeUrl;
        } else {
            $signedUrl = $supabaseUrl
                . '/storage/v1'
                . (
                    Str::startsWith($relativeUrl, '/')
                        ? $relativeUrl
                        : '/' . $relativeUrl
                );
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
