<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\PortfolioAiReview;
use App\Models\Profile;
use App\Models\Project;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use JsonException;
use RuntimeException;

class PortfolioReviewService
{
    public function __construct(
        private readonly PortfolioAnalyticsService $analyticsService,
        private readonly PortfolioQualityService $qualityService,
    ) {
    }

    /** @throws JsonException */
    public function review(int $userId): PortfolioAiReview
    {
        $baseUrl = rtrim((string) config('services.ollama.base_url'), '/');
        $model = (string) config('services.ollama.model', 'qwen3:4b');
        $timeout = max(30, (int) config('services.ollama.timeout', 120));

        if (blank($baseUrl)) {
            throw new RuntimeException('OLLAMA_BASE_URL belum dikonfigurasi pada environment server.');
        }

        if (blank($model)) {
            throw new RuntimeException('OLLAMA_MODEL belum dikonfigurasi pada environment server.');
        }

        $snapshot = $this->snapshot($userId);

        try {
            $response = $this->httpClient($timeout)
                ->post($baseUrl.'/api/chat', [
                    'model' => $model,
                    'stream' => false,
                    'think' => false,
                    'format' => $this->responseSchema(),
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => implode("\n", [
                                'Anda adalah reviewer portfolio software developer yang kritis dan berbasis bukti.',
                                'Nilai daya tarik untuk recruiter, kedalaman teknis, kredibilitas bukti, kejelasan case study, dan kualitas positioning.',
                                'Jangan mengarang fakta yang tidak tersedia dalam data. Nyatakan kekurangan data sebagai kelemahan.',
                                'Berikan score 0 sampai 100.',
                                'Berikan minimal satu strength, weakness, dan recommendation.',
                                'Tulis seluruh hasil dalam Bahasa Indonesia yang langsung, spesifik, dan dapat ditindaklanjuti.',
                                'Kembalikan JSON murni yang mengikuti schema tanpa markdown atau komentar tambahan.',
                            ]),
                        ],
                        [
                            'role' => 'user',
                            'content' => 'Analisis portfolio berdasarkan snapshot JSON berikut:' . "\n" .
                                json_encode(
                                    $snapshot,
                                    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR,
                                ),
                        ],
                    ],
                    'options' => [
                        'temperature' => 0.2,
                        'num_predict' => 1800,
                    ],
                    'keep_alive' => '5m',
                ])
                ->throw();
        } catch (RequestException $exception) {
            $status = $exception->response?->status();
            $details = app()->isLocal()
                ? Str::limit((string) $exception->response?->body(), 1000)
                : null;

            throw new RuntimeException(
                trim(sprintf(
                    'Ollama tidak dapat memproses permintaan%s%s',
                    $status ? " (HTTP {$status})" : '',
                    $details ? ": {$details}" : '.',
                )),
                previous: $exception,
            );
        }

        $outputText = data_get($response->json(), 'message.content');

        if (blank($outputText)) {
            throw new RuntimeException('Ollama tidak mengembalikan message.content yang dapat diproses.');
        }

        $review = json_decode(
            $this->normalizeJson((string) $outputText),
            true,
            flags: JSON_THROW_ON_ERROR,
        );

        $validated = validator($review, [
            'score' => ['required', 'integer', 'between:0,100'],
            'summary' => ['required', 'string', 'max:3000'],
            'strengths' => ['required', 'array', 'min:1', 'max:6'],
            'strengths.*' => ['required', 'string', 'max:1000'],
            'weaknesses' => ['required', 'array', 'min:1', 'max:6'],
            'weaknesses.*' => ['required', 'string', 'max:1000'],
            'recommendations' => ['required', 'array', 'min:1', 'max:8'],
            'recommendations.*' => ['required', 'string', 'max:1000'],
        ])->validate();

        return PortfolioAiReview::create([
            'user_id' => $userId,
            'score' => $validated['score'],
            'summary' => $validated['summary'],
            'strengths' => array_values($validated['strengths']),
            'weaknesses' => array_values($validated['weaknesses']),
            'recommendations' => array_values($validated['recommendations']),
            'model' => $model,
            'source_snapshot' => $snapshot,
            'generated_at' => now(),
        ]);
    }

    private function httpClient(int $timeout): PendingRequest
    {
        $headers = [];
        $clientId = (string) config('services.ollama.cf_access_client_id');
        $clientSecret = (string) config('services.ollama.cf_access_client_secret');
        $bearerToken = (string) config('services.ollama.bearer_token');

        if (filled($clientId) && filled($clientSecret)) {
            $headers['CF-Access-Client-Id'] = $clientId;
            $headers['CF-Access-Client-Secret'] = $clientSecret;
        }

        $request = Http::acceptJson()
            ->asJson()
            ->withHeaders($headers)
            ->connectTimeout(10)
            ->timeout($timeout)
            ->retry(1, 750);

        return filled($bearerToken)
            ? $request->withToken($bearerToken)
            : $request;
    }

    private function responseSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'score' => [
                    'type' => 'integer',
                    'minimum' => 0,
                    'maximum' => 100,
                ],
                'summary' => ['type' => 'string'],
                'strengths' => [
                    'type' => 'array',
                    'minItems' => 1,
                    'maxItems' => 6,
                    'items' => ['type' => 'string'],
                ],
                'weaknesses' => [
                    'type' => 'array',
                    'minItems' => 1,
                    'maxItems' => 6,
                    'items' => ['type' => 'string'],
                ],
                'recommendations' => [
                    'type' => 'array',
                    'minItems' => 1,
                    'maxItems' => 8,
                    'items' => ['type' => 'string'],
                ],
            ],
            'required' => [
                'score',
                'summary',
                'strengths',
                'weaknesses',
                'recommendations',
            ],
            'additionalProperties' => false,
        ];
    }

    private function normalizeJson(string $value): string
    {
        $value = trim($value);

        if (str_starts_with($value, '```')) {
            $value = preg_replace('/^```(?:json)?\s*/i', '', $value) ?? $value;
            $value = preg_replace('/\s*```$/', '', $value) ?? $value;
        }

        return trim($value);
    }

    private function snapshot(int $userId): array
    {
        $profile = Profile::query()->where('user_id', $userId)->first();

        $projects = Project::query()
            ->where('user_id', $userId)
            ->latest()
            ->get()
            ->map(fn (Project $project): array => [
                'name' => $project->name,
                'category' => $project->category,
                'client' => $project->client,
                'status' => $project->status,
                'start_date' => $project->start_date?->toDateString(),
                'end_date' => $project->end_date?->toDateString(),
                'description' => Str::limit(strip_tags((string) $project->description), 1500),
                'tags' => $project->tags ?: [],
                'has_image' => filled($project->image),
                'has_live_url' => filled($project->website_url),
                'likes' => (int) $project->likes,
            ])
            ->all();

        $certificates = Certificate::query()
            ->where('user_id', $userId)
            ->get()
            ->map(fn (Certificate $certificate): array => [
                'title' => $certificate->title,
                'issuer' => $certificate->issuer,
                'issued_at' => $certificate->issued_at?->toDateString(),
                'description' => Str::limit(strip_tags((string) $certificate->description), 500),
            ])
            ->all();

        $analytics = $this->analyticsService->dashboard();

        return [
            'profile' => [
                'name' => $profile?->name,
                'role' => $profile?->role,
                'bio' => Str::limit(strip_tags((string) $profile?->bio), 1200),
                'hero_title' => $profile?->hero_title,
                'hero_description' => Str::limit(strip_tags((string) $profile?->hero_description), 1200),
                'has_avatar' => filled($profile?->avatar),
            ],
            'projects' => $projects,
            'certificates' => $certificates,
            'deterministic_quality' => $this->qualityService->evaluate($userId),
            'traffic_summary' => $analytics['summary'],
            'top_pages' => $analytics['top_pages'],
        ];
    }
}
