<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    {{-- Back Button --}}
    <div class="mb-6 flex justify-center">
        @auth
            <a
                href="{{ route('dashboard') }}"
                wire:navigate
                class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 dark:border-gray-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800"
            >
                ← Back to Dashboard
            </a>
        @else
            <a
                href="{{ url('/') }}"
                wire:navigate
                class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 dark:border-gray-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800"
            >
                ← Back to Home
            </a>
        @endauth
    </div>

    {{-- Session Status --}}
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login.post') }}">
    @csrf
        {{-- Email Address --}}
        <div>
            <x-input-label for="email" :value="__('Email')" />

            <input
                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm mt-1 block w-full"
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="username"
            />

            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>

        {{-- Password --}}
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <input
                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm mt-1 block w-full"
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
            />

            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        {{-- Remember Me --}}
        <div class="mt-4 block">
            <label for="remember" class="inline-flex items-center">
                <input
                id="remember"
                type="checkbox"
                class="rounded border-gray-300 text-[#7fac9f] shadow-sm focus:ring-[#7fac9f]"
                name="remember"
            />

                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Remember me') }}
                </span>
            </label>
        </div>

        {{-- Login Button --}}
        <div class="mt-6 flex items-center justify-end">
            <x-primary-button>
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</div>