<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Dashboard\Overview;
use App\Livewire\Dashboard\Activity;
use App\Livewire\Portfolio\Index as PortfolioIndex;
use App\Livewire\Projects\Index as ProjectIndex;
use App\Livewire\Profile\Show as ProfileShow;
use App\Livewire\Settings\Index as SettingsIndex;
use App\Livewire\Landing\Index as LandingIndex;
use App\Livewire\Certificates\Index as CertificateIndex;
use App\Http\Controllers\ProjectImageUploadController;

Route::get('/', LandingIndex::class)->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', Overview::class)->name('dashboard');
    Route::get('/activity', Activity::class)->name('activity');
    Route::get('/portfolio', PortfolioIndex::class)->name('portfolio');
    Route::get('/projects', ProjectIndex::class)->name('projects');
    Route::get('/certificates', CertificateIndex::class)->name('certificates');
    Route::get('/profile', ProfileShow::class)->name('profile.show');
    Route::get('/settings', SettingsIndex::class)->name('settings');
    Route::post(
    '/projects/image-upload-url',
    [
        ProjectImageUploadController::class,
        'createSignedUploadUrl',
    ]
    )->name('projects.image-upload-url');
});

/*
|--------------------------------------------------------------------------
| Logout Route
|--------------------------------------------------------------------------
| Dibutuhkan karena sidebar memakai route('logout').
| Logout harus memakai POST, bukan GET.
*/
Route::post('/logout', function (Request $request) {
    Auth::guard('web')->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/login');
})->middleware('auth')->name('logout');

require __DIR__.'/auth.php';
