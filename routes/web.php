<?php

use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use App\Http\Controllers\DirectUploadController;
use App\Http\Controllers\ProjectImageUploadController;
use App\Livewire\Certificates\Index as CertificateIndex;
use App\Livewire\Dashboard\Activity;
use App\Livewire\Dashboard\Overview;
use App\Livewire\Landing\Index as LandingIndex;
use App\Livewire\Portfolio\Index as PortfolioIndex;
use App\Livewire\Profile\AboutEditor;
use App\Livewire\Profile\Show as ProfileShow;
use App\Livewire\Projects\Index as ProjectIndex;
use App\Livewire\Settings\Index as SettingsIndex;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HealthController;
use App\Http\Middleware\TrackPortfolioVisit;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

Route::get('/', LandingIndex::class)
    ->name('home');

Route::get('/health', HealthController::class)
    ->withoutMiddleware([
        EncryptCookies::class,
        AddQueuedCookiesToResponse::class,
        StartSession::class,
        ShareErrorsFromSession::class,
        PreventRequestForgery::class,
        TrackPortfolioVisit::class,
    ])
    ->name('health');

Route::post(
    '/analytics/heartbeat',
    App\Http\Controllers\AnalyticsHeartbeatController::class
)
    ->middleware('throttle:30,1')
    ->name('analytics.heartbeat');

Route::middleware([
    'auth',
    'verified',
])->group(function (): void {
    Route::get('/dashboard', Overview::class)
        ->name('dashboard');

    Route::get('/activity', Activity::class)
        ->name('activity');

    Route::get('/portfolio', PortfolioIndex::class)
        ->name('portfolio');

    Route::get('/projects', ProjectIndex::class)
        ->name('projects');

    Route::get('/certificates', CertificateIndex::class)
        ->name('certificates');

    Route::get('/profile/about', AboutEditor::class)
        ->name('profile.about');

    Route::get('/profile', ProfileShow::class)
        ->name('profile.show');

    Route::get('/settings', SettingsIndex::class)
        ->name('settings');

    Route::post(
        '/projects/image-upload-url',
        [
            ProjectImageUploadController::class,
            'createSignedUploadUrl',
        ]
    )->name('projects.image-upload-url');

    Route::post(
        '/direct-upload-url',
        [
            DirectUploadController::class,
            'createSignedUrl',
        ]
    )->name('direct-upload-url');
});

/*
|--------------------------------------------------------------------------
| Logout Route
|--------------------------------------------------------------------------
*/

Route::post('/logout', function (Request $request) {
    Auth::guard('web')->logout();

    $request->session()->invalidate();

    $request->session()->regenerateToken();

    return redirect('/');
})
    ->middleware('auth')
    ->name('logout');

require __DIR__ . '/auth.php';
