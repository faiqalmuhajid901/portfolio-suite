$app = @'
<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (app()->environment('production')) {
            URL::forceRootUrl(config('app.url'));
            URL::forceScheme('https');
        }
    }
}
'@

[System.IO.File]::WriteAllText(
    "app\Providers\AppServiceProvider.php",
    $app,
    (New-Object System.Text.UTF8Encoding $false)
)