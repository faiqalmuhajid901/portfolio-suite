<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Livewire::setScriptRoute(function ($handle) {
            return Route::get('/build/livewire/livewire.min.js', $handle);
        });

        Livewire::setUpdateRoute(function ($handle) {
            return Route::post('/livewire/update', $handle);
        });
    }
}