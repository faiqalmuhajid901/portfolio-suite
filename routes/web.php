<?php

use App\Http\Controllers\DirectUploadController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\ProjectImageUploadController;
use App\Http\Middleware\TrackPortfolioVisit;
use App\Livewire\Careers\Index as CareerIndex;
use App\Livewire\Certificates\Index as CertificateIndex;
use App\Livewire\Content\Index as ContentIndex;
use App\Livewire\Dashboard\Activity;
use App\Livewire\Dashboard\Overview;
use App\Livewire\Landing\ProfessionalHome;
use App\Livewire\Landing\ProjectShow;
use App\Livewire\Messages\Index as MessageIndex;
use App\Livewire\Portfolio\Index as PortfolioIndex;
use App\Livewire\Profile\AboutEditor;
use App\Livewire\Profile\Show as ProfileShow;
use App\Livewire\ProjectCaseStudies\Index as ProjectCaseStudyIndex;
use App\Livewire\Projects\Index as ProjectIndex;
use App\Livewire\Settings\Index as SettingsIndex;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;

/*
|--------------------------------------------------------------------------
| Public Portfolio Routes
|--------------------------------------------------------------------------
*/

Route::get('/', ProfessionalHome::class)
    ->name('home');

Route::get('/projects/{slug}', ProjectShow::class)
    ->name('projects.show');

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

/*
|--------------------------------------------------------------------------
| Authenticated Dashboard Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function (): void {
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

    /*
    |--------------------------------------------------------------------------
    | Phase 3 Professional Content
    |--------------------------------------------------------------------------
    */

    Route::get('/content', ContentIndex::class)
        ->name('content.index');

    Route::get('/project-case-studies', ProjectCaseStudyIndex::class)
        ->name('project-case-studies.index');

    Route::get('/careers', CareerIndex::class)
        ->name('careers.index');

    Route::get('/messages', MessageIndex::class)
        ->name('messages.index');

    /*
    |--------------------------------------------------------------------------
    | Upload Endpoints
    |--------------------------------------------------------------------------
    */

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

require __DIR__.'/auth.php';
