<!DOCTYPE html>
<html
    lang="en"
    class="scroll-smooth"
    x-data="{
        darkMode: false,
        mobileMenu: false,
        showBackToTop: false,
    }"
    x-init="
        const savedTheme = localStorage.getItem('darkMode');
        darkMode = savedTheme === null
            ? window.matchMedia('(prefers-color-scheme: dark)').matches
            : savedTheme === 'true';

        $watch('darkMode', value => {
            localStorage.setItem('darkMode', value ? 'true' : 'false');
        });

        const updateBackToTop = () => {
            showBackToTop = window.scrollY > 520;
        };

        updateBackToTop();
        window.addEventListener('scroll', updateBackToTop, { passive: true });
    "
    :class="{ 'dark': darkMode }"
>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <x-seo-meta />

    <script>
        (() => {
            try {
                const storedTheme = localStorage.getItem('darkMode');
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                if (storedTheme === 'true' || (storedTheme === null && prefersDark)) {
                    document.documentElement.classList.add('dark');
                }
            } catch (error) {
                // Theme persistence failure must never block rendering.
            }
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('head')

    <style>
        [x-cloak] {
            display: none !important;
        }

        .dark .phase3-public [class~="bg-white"],
        .dark .phase3-public [class*="bg-white/"] {
            background-color: rgb(15 23 42) !important;
        }

        .dark .phase3-public [class~="bg-slate-100"] {
            background-color: rgb(30 41 59) !important;
        }

        .dark .phase3-public [class~="text-slate-950"],
        .dark .phase3-public [class~="text-slate-800"],
        .dark .phase3-public [class~="text-slate-700"] {
            color: rgb(248 250 252) !important;
        }

        .dark .phase3-public [class~="text-slate-600"],
        .dark .phase3-public [class~="text-slate-500"] {
            color: rgb(203 213 225) !important;
        }

        .dark .phase3-public [class~="border-slate-200"],
        .dark .phase3-public [class~="border-slate-300"] {
            border-color: rgb(51 65 85) !important;
        }
    </style>
</head>
@php
    $homeAnchorPrefix = request()->routeIs('home') ? '' : route('home');
@endphp
<body
    class="min-h-screen bg-[#f7f8f6] text-slate-950 antialiased selection:bg-emerald-200 transition-colors duration-300 dark:bg-slate-950 dark:text-slate-100 dark:selection:bg-emerald-700"
    @keydown.escape.window="mobileMenu = false"
>
    <a
        href="#main-content"
        class="sr-only z-[100] rounded-lg bg-slate-950 px-4 py-3 text-sm font-bold text-white focus:not-sr-only focus:fixed focus:left-4 focus:top-4 dark:bg-white dark:text-slate-950"
    >
        Skip to content
    </a>

    <header class="sticky top-0 z-50 border-b border-slate-200/80 bg-[#f7f8f6]/90 backdrop-blur-xl transition-colors dark:border-white/10 dark:bg-slate-950/90">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-5 py-4 sm:px-8 lg:px-10">
            <a
                href="{{ route('home') }}"
                class="text-sm font-black uppercase tracking-[0.22em] text-slate-950 transition hover:text-emerald-700 dark:text-white dark:hover:text-emerald-400"
            >
                Muhammad Faiq
            </a>

            <nav
                class="hidden items-center gap-6 text-sm font-semibold text-slate-600 md:flex"
                aria-label="Primary navigation"
            >
                <a class="transition hover:text-slate-950 dark:text-slate-300 dark:hover:text-white" href="{{ $homeAnchorPrefix }}#overview">Overview</a>
                <a class="transition hover:text-slate-950 dark:text-slate-300 dark:hover:text-white" href="{{ $homeAnchorPrefix }}#about">About</a>
                <a class="transition hover:text-slate-950 dark:text-slate-300 dark:hover:text-white" href="{{ $homeAnchorPrefix }}#portfolio">Work</a>
                <a class="transition hover:text-slate-950 dark:text-slate-300 dark:hover:text-white" href="{{ $homeAnchorPrefix }}#experience">Experience</a>
                <a class="transition hover:text-slate-950 dark:text-slate-300 dark:hover:text-white" href="{{ $homeAnchorPrefix }}#certificates">Certificates</a>

                <button
                    type="button"
                    class="grid h-10 w-10 place-items-center rounded-full border border-slate-300 bg-white text-slate-700 transition hover:border-slate-950 hover:text-slate-950 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:border-slate-400 dark:hover:text-white"
                    aria-label="Toggle dark mode"
                    :aria-pressed="darkMode.toString()"
                    @click="darkMode = !darkMode"
                >
                    <span x-show="!darkMode" x-cloak aria-hidden="true">☾</span>
                    <span x-show="darkMode" x-cloak aria-hidden="true">☀</span>
                </button>

                @auth
                    <a
                        class="rounded-full border border-slate-300 px-5 py-2.5 text-slate-800 transition hover:border-slate-950 hover:text-slate-950 dark:border-slate-700 dark:text-slate-200 dark:hover:border-slate-400 dark:hover:text-white"
                        href="{{ route('dashboard') }}"
                    >
                        Dashboard
                    </a>
                @else
                    <a
                        class="rounded-full border border-slate-300 px-5 py-2.5 text-slate-800 transition hover:border-slate-950 hover:text-slate-950 dark:border-slate-700 dark:text-slate-200 dark:hover:border-slate-400 dark:hover:text-white"
                        href="{{ route('login') }}"
                        aria-label="Log in to the administration dashboard"
                    >
                        Login
                    </a>
                @endauth

                <a
                    class="rounded-full bg-slate-950 px-5 py-2.5 text-white transition hover:bg-emerald-700 dark:bg-white dark:text-slate-950 dark:hover:bg-emerald-300"
                    href="{{ $homeAnchorPrefix }}#contact"
                >
                    Contact
                </a>
            </nav>

            <div class="flex items-center gap-2 md:hidden">
                <button
                    type="button"
                    class="grid h-10 w-10 place-items-center rounded-full border border-slate-300 bg-white text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200"
                    aria-label="Toggle dark mode"
                    :aria-pressed="darkMode.toString()"
                    @click="darkMode = !darkMode"
                >
                    <span x-show="!darkMode" x-cloak aria-hidden="true">☾</span>
                    <span x-show="darkMode" x-cloak aria-hidden="true">☀</span>
                </button>

                <button
                    type="button"
                    class="grid h-10 w-10 place-items-center rounded-full border border-slate-300 bg-white text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200"
                    aria-label="Toggle navigation menu"
                    :aria-expanded="mobileMenu.toString()"
                    aria-controls="mobile-navigation"
                    @click="mobileMenu = !mobileMenu"
                >
                    <span x-show="!mobileMenu" x-cloak aria-hidden="true">☰</span>
                    <span x-show="mobileMenu" x-cloak aria-hidden="true">✕</span>
                </button>
            </div>
        </div>

        <div
            id="mobile-navigation"
            x-show="mobileMenu"
            x-transition.origin.top
            x-cloak
            class="border-t border-slate-200 bg-white px-5 py-4 shadow-xl dark:border-white/10 dark:bg-slate-900 md:hidden"
        >
            <nav class="mx-auto max-w-7xl space-y-1" aria-label="Mobile navigation">
                <a class="block rounded-xl px-4 py-3 text-sm font-semibold hover:bg-slate-100 dark:hover:bg-slate-800" href="{{ $homeAnchorPrefix }}#overview" @click="mobileMenu = false">Overview</a>
                <a class="block rounded-xl px-4 py-3 text-sm font-semibold hover:bg-slate-100 dark:hover:bg-slate-800" href="{{ $homeAnchorPrefix }}#about" @click="mobileMenu = false">About</a>
                <a class="block rounded-xl px-4 py-3 text-sm font-semibold hover:bg-slate-100 dark:hover:bg-slate-800" href="{{ $homeAnchorPrefix }}#portfolio" @click="mobileMenu = false">Work</a>
                <a class="block rounded-xl px-4 py-3 text-sm font-semibold hover:bg-slate-100 dark:hover:bg-slate-800" href="{{ $homeAnchorPrefix }}#experience" @click="mobileMenu = false">Experience</a>
                <a class="block rounded-xl px-4 py-3 text-sm font-semibold hover:bg-slate-100 dark:hover:bg-slate-800" href="{{ $homeAnchorPrefix }}#certificates" @click="mobileMenu = false">Certificates</a>
                <a class="block rounded-xl px-4 py-3 text-sm font-semibold hover:bg-slate-100 dark:hover:bg-slate-800" href="{{ $homeAnchorPrefix }}#contact" @click="mobileMenu = false">Contact</a>

                <div class="mt-3 border-t border-slate-200 pt-3 dark:border-slate-700">
                    @auth
                        <a
                            class="block rounded-xl border border-slate-300 px-4 py-3 text-center text-sm font-semibold dark:border-slate-700"
                            href="{{ route('dashboard') }}"
                        >
                            Dashboard
                        </a>
                    @else
                        <a
                            class="block rounded-xl border border-slate-300 px-4 py-3 text-center text-sm font-semibold dark:border-slate-700"
                            href="{{ route('login') }}"
                            aria-label="Log in to the administration dashboard"
                        >
                            Login
                        </a>
                    @endauth
                </div>
            </nav>
        </div>
    </header>

    <main id="main-content">
        {{ $slot }}
    </main>

    <button
        type="button"
        x-show="showBackToTop"
        x-transition.opacity
        x-cloak
        aria-label="Back to top"
        class="fixed bottom-5 right-5 z-40 grid h-12 w-12 place-items-center rounded-full bg-slate-950 text-lg font-black text-white shadow-xl transition hover:-translate-y-0.5 hover:bg-emerald-700 dark:bg-white dark:text-slate-950 dark:hover:bg-emerald-300"
        @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
    >
        ↑
    </button>

    <footer class="border-t border-slate-200 bg-white transition-colors dark:border-white/10 dark:bg-slate-900">
        <div class="mx-auto flex max-w-7xl flex-col gap-3 px-5 py-8 text-sm text-slate-500 sm:px-8 md:flex-row md:items-center md:justify-between lg:px-10 dark:text-slate-400">
            <p>© {{ now()->year }} Muhammad Faiq. Built with Laravel and Livewire.</p>
            <div class="flex flex-wrap gap-5">
                <a class="font-semibold hover:text-slate-950 dark:hover:text-white" href="{{ $homeAnchorPrefix }}#portfolio">Selected work</a>
                <a class="font-semibold hover:text-slate-950 dark:hover:text-white" href="{{ $homeAnchorPrefix }}#contact">Start a conversation</a>
            </div>
        </div>
    </footer>

    @livewireScripts
    @stack('scripts')
</body>
</html>
