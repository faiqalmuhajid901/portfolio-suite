<?php

namespace App\Http\Middleware;

use App\Models\PortfolioVisit;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class TrackPortfolioVisit
{
    private const COOKIE_NAME = 'portfolio_visitor_id';

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $this->shouldTrack($request, $response)) {
            return $response;
        }

        $visitorId = $request->cookie(self::COOKIE_NAME) ?: (string) Str::uuid();

        try {
            PortfolioVisit::create([
                'visitor_id' => $visitorId,
                'session_id' => $request->hasSession() ? $request->session()->getId() : null,
                'ip_hash' => $this->hashIp($request->ip()),
                'path' => '/'.ltrim($request->path(), '/'),
                'route_name' => $request->route()?->getName(),
                'referrer_host' => $this->externalReferrerHost($request),
                'device_type' => $this->detectDevice($request->userAgent()),
                'browser' => $this->detectBrowser($request->userAgent()),
                'operating_system' => $this->detectOperatingSystem($request->userAgent()),
                'country_code' => $this->header($request, 'x-vercel-ip-country', 2),
                'region' => $this->header($request, 'x-vercel-ip-country-region', 100),
                'city' => $this->header($request, 'x-vercel-ip-city', 150, decode: true),
                'last_seen_at' => now(),
            ]);

            $response->headers->setCookie(cookie(
                name: self::COOKIE_NAME,
                value: $visitorId,
                minutes: 60 * 24 * 365,
                path: '/',
                domain: null,
                secure: app()->isProduction(),
                httpOnly: true,
                raw: false,
                sameSite: 'Lax',
            ));
        } catch (Throwable $exception) {
            // Analytics must never make the public portfolio unavailable.
            report($exception);
        }

        return $response;
    }

    private function shouldTrack(Request $request, Response $response): bool
    {
        if (! $request->isMethod('GET')) {
            return false;
        }

        if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 400) {
            return false;
        }

        $contentType = (string) $response->headers->get('Content-Type');

        if ($contentType !== '' && ! str_contains(strtolower($contentType), 'text/html')) {
            return false;
        }

        // Do not count the administrator's own authenticated visits.
        if (Auth::check()) {
            return false;
        }

        if ($request->is(
            'dashboard',
            'activity',
            'portfolio',
            'projects',
            'certificates',
            'profile',
            'settings',
            'login',
            'register',
            'forgot-password',
            'reset-password/*',
            'verify-email/*',
            'analytics/heartbeat',
            'livewire/*',
            'build/*',
            'storage/*',
            'up',
            'health',
            'favicon.ico',
            'robots.txt'
        )) {
            return false;
        }

        return ! $this->isBot($request->userAgent());
    }

    private function hashIp(?string $ip): ?string
    {
        if (blank($ip)) {
            return null;
        }

        return hash_hmac('sha256', $ip, (string) config('app.key'));
    }

    private function externalReferrerHost(Request $request): ?string
    {
        $referrer = $request->headers->get('referer');

        if (blank($referrer)) {
            return null;
        }

        $host = parse_url($referrer, PHP_URL_HOST);

        if (! is_string($host) || $host === '') {
            return null;
        }

        if (strcasecmp($host, $request->getHost()) === 0) {
            return null;
        }

        return Str::limit(strtolower($host), 191, '');
    }

    private function header(
        Request $request,
        string $name,
        int $maximumLength,
        bool $decode = false
    ): ?string {
        $value = trim((string) $request->headers->get($name));

        if ($value === '') {
            return null;
        }

        if ($decode) {
            $value = rawurldecode($value);
        }

        return Str::limit($value, $maximumLength, '');
    }

    private function detectDevice(?string $userAgent): string
    {
        $userAgent = strtolower((string) $userAgent);

        if (preg_match('/ipad|tablet|kindle|silk/', $userAgent) === 1) {
            return 'tablet';
        }

        if (preg_match('/mobile|iphone|ipod|android/', $userAgent) === 1) {
            return 'mobile';
        }

        return 'desktop';
    }

    private function detectBrowser(?string $userAgent): string
    {
        $userAgent = strtolower((string) $userAgent);

        return match (true) {
            str_contains($userAgent, 'edg/') => 'Edge',
            str_contains($userAgent, 'opr/'), str_contains($userAgent, 'opera') => 'Opera',
            str_contains($userAgent, 'firefox/') => 'Firefox',
            str_contains($userAgent, 'chrome/'), str_contains($userAgent, 'crios/') => 'Chrome',
            str_contains($userAgent, 'safari/') => 'Safari',
            default => 'Other',
        };
    }

    private function detectOperatingSystem(?string $userAgent): string
    {
        $userAgent = strtolower((string) $userAgent);

        return match (true) {
            str_contains($userAgent, 'windows') => 'Windows',
            str_contains($userAgent, 'android') => 'Android',
            str_contains($userAgent, 'iphone'), str_contains($userAgent, 'ipad') => 'iOS',
            str_contains($userAgent, 'mac os'), str_contains($userAgent, 'macintosh') => 'macOS',
            str_contains($userAgent, 'linux') => 'Linux',
            default => 'Other',
        };
    }

    private function isBot(?string $userAgent): bool
    {
        if (blank($userAgent)) {
            return true;
        }

        return preg_match(
            '/bot|crawler|spider|slurp|bingpreview|facebookexternalhit|whatsapp|telegrambot|headless|lighthouse|monitoring/i',
            $userAgent
        ) === 1;
    }
}
