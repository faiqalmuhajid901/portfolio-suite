<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(
    basePath: dirname(__DIR__)
)
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request): bool => $request->is('api/*')
        );

        /*
         * Log ringkas untuk Vercel.
         * Tidak mencetak seluruh stack trace agar pesan utama terlihat.
         */
        $exceptions->report(function (\Throwable $exception): void {
            $request = app()->bound('request')
                ? app('request')
                : null;

            error_log(
                '[APP_EXCEPTION] ' . json_encode(
                    [
                        'class' => $exception::class,
                        'message' => $exception->getMessage(),
                        'file' => $exception->getFile(),
                        'line' => $exception->getLine(),
                        'method' => $request?->method(),
                        'uri' => $request?->getRequestUri(),
                    ],
                    JSON_UNESCAPED_SLASHES
                    | JSON_UNESCAPED_UNICODE
                )
            );
        });
    })
    ->create();
