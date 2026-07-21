<?php

namespace App\Providers;

use App\Support\SeoManager;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        /*
         * Satu instance per request.
         * Data SEO tidak bocor ke request lain.
         */
        $this->app->scoped(
            SeoManager::class,
            fn (): SeoManager => new SeoManager()
        );
    }

    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
