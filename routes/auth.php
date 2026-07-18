<?php

use App\Http\Controllers\Auth\VerifyEmailController;
use App\Livewire\Actions\Logout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Volt;

/*
|--------------------------------------------------------------------------
| Guest Authentication Routes
|--------------------------------------------------------------------------
|
| Registrasi publik sengaja tidak disediakan karena website ini hanya
| dikelola oleh pemilik. Login dan pemulihan password tetap tersedia.
|
*/

Route::middleware('guest')->group(function (): void {
    /*
    |--------------------------------------------------------------------------
    | Login Page
    |--------------------------------------------------------------------------
    */

    Volt::route('login', 'pages.auth.login')
        ->name('login');

    /*
    |--------------------------------------------------------------------------
    | Login Process
    |--------------------------------------------------------------------------
    */

    Route::post('login', function (Request $request) {
        $credentials = $request->validate([
            'email' => [
                'required',
                'email',
            ],
            'password' => [
                'required',
                'string',
            ],
        ]);

        if (! Auth::attempt(
            $credentials,
            $request->boolean('remember')
        )) {
            throw ValidationException::withMessages([
                'email' => 'Email atau password salah.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(
            route('dashboard', absolute: false)
        );
    })->name('login.post');

    /*
    |--------------------------------------------------------------------------
    | Password Recovery
    |--------------------------------------------------------------------------
    */

    Volt::route(
        'forgot-password',
        'pages.auth.forgot-password'
    )->name('password.request');

    Volt::route(
        'reset-password/{token}',
        'pages.auth.reset-password'
    )->name('password.reset');
});

/*
|--------------------------------------------------------------------------
| Authenticated Account Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function (): void {
    /*
    |--------------------------------------------------------------------------
    | Email Verification
    |--------------------------------------------------------------------------
    */

    Volt::route(
        'verify-email',
        'pages.auth.verify-email'
    )->name('verification.notice');

    Route::get(
        'verify-email/{id}/{hash}',
        VerifyEmailController::class
    )
        ->middleware([
            'signed',
            'throttle:6,1',
        ])
        ->name('verification.verify');

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation
    |--------------------------------------------------------------------------
    */

    Volt::route(
        'confirm-password',
        'pages.auth.confirm-password'
    )->name('password.confirm');

    /*
    |--------------------------------------------------------------------------
    | Logout
    |--------------------------------------------------------------------------
    */

    Route::post('logout', function (Logout $logout) {
        $logout();

        return redirect('/');
    })->name('logout');
});
