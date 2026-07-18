<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

final class HealthController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $startedAt = microtime(true);

        try {
            DB::select('select 1');

            $cacheTable = (string) config(
                'cache.stores.database.table',
                'cache'
            );

            $cacheTableAvailable = Schema::hasTable(
                $cacheTable
            );

            if (! $cacheTableAvailable) {
                return response()->json([
                    'status' => 'degraded',
                    'application' => 'ok',
                    'database' => 'ok',
                    'cache' => 'missing',
                    'duration_ms' => round(
                        (microtime(true) - $startedAt) * 1000,
                        2
                    ),
                    'timestamp' => now()->toIso8601String(),
                ], 503);
            }

            return response()->json([
                'status' => 'ok',
                'application' => 'ok',
                'database' => 'ok',
                'cache' => 'ok',

                'revision' => env(
                    'VERCEL_GIT_COMMIT_SHA'
                ),

                'duration_ms' => round(
                    (microtime(true) - $startedAt) * 1000,
                    2
                ),

                'timestamp' => now()->toIso8601String(),
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'status' => 'unavailable',
                'application' => 'ok',
                'database' => 'error',
                'cache' => 'unknown',

                'duration_ms' => round(
                    (microtime(true) - $startedAt) * 1000,
                    2
                ),

                'timestamp' => now()->toIso8601String(),
            ], 503);
        }
    }
}
