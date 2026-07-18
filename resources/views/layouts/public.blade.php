<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    class="scroll-smooth"
    x-data="{
        darkMode: localStorage.getItem('darkMode') === 'true',
        mobileMenu: false
    }"
    x-init="
        $watch('darkMode', value => {
            localStorage.setItem('darkMode', value ? 'true' : 'false')
        })
    "
    :class="{ 'dark': darkMode }"
>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Portfolio Suite' }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="bg-[#f5f8f8] text-slate-950 antialiased transition-colors duration-300 dark:bg-slate-950 dark:text-white">
    <div class="min-h-screen">
        {{-- Public Header --}}
        <header class="sticky top-0 z-50 border-b border-black/5 bg-[#f5f8f8]/80 backdrop-blur-xl dark:border-white/10 dark:bg-slate-950/80">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-5 py-4 lg:px-8">
                <a href="{{ route('home') }}" class="text-xl font-bold leading-tight text-slate-950 dark:text-white">
                    Portfolio Suite
                </a>

                <nav class="hidden items-center gap-8 text-sm md:flex">
                    <a href="#overview" class="text-gray-600 hover:text-[#2f6f61] dark:text-slate-300 dark:hover:text-emerald-300">
                        Overview
                    </a>

                    <a
                        href="#about"
                        class="text-gray-600 hover:text-[#2f6f61]
                            dark:text-slate-300 dark:hover:text-emerald-300"
                    >
                        About Me
                    </a>

                    <a href="#portfolio" class="text-gray-600 hover:text-[#2f6f61] dark:text-slate-300 dark:hover:text-emerald-300">
                        Portfolio
                    </a>

                    <a href="#projects" class="text-gray-600 hover:text-[#2f6f61] dark:text-slate-300 dark:hover:text-emerald-300">
                        Projects
                    </a>

                    <a href="#certificates" class="text-gray-600 hover:text-[#2f6f61] dark:text-slate-300 dark:hover:text-emerald-300">
                        Certificates
                    </a>

                    <a href="#contact" class="text-gray-600 hover:text-[#2f6f61] dark:text-slate-300 dark:hover:text-emerald-300">
                        Contact
                    </a>
                </nav>

                <div class="hidden items-center gap-3 md:flex">
                    <button
                        type="button"
                        @click="darkMode = !darkMode"
                        class="rounded-full bg-white p-2 shadow-sm transition dark:bg-slate-900"
                    >
                        <span x-show="!darkMode" x-cloak>🌙</span>
                        <span x-show="darkMode" x-cloak>☀️</span>
                    </button>

                    <a href="{{ route('login') }}" class="rounded-xl border border-[#7fac9f] px-4 py-2 text-sm font-semibold text-[#2f6f61] dark:text-emerald-300">
                        Log in
                    </a>

                </div>

                <button
                    type="button"
                    @click="mobileMenu = !mobileMenu"
                    class="rounded-xl bg-white px-3 py-2 shadow-sm dark:bg-slate-900 md:hidden"
                >
                    ☰
                </button>
            </div>

            {{-- Mobile Menu --}}
            <div
                x-cloak
                x-show="mobileMenu"
                x-transition
                class="border-t border-black/5 bg-white px-5 py-4 dark:border-white/10 dark:bg-slate-900 md:hidden"
            >
                <nav class="space-y-3 text-sm">
                    <a href="#overview" @click="mobileMenu = false" class="block text-slate-700 dark:text-slate-200">
                        Overview
                    </a>

                    <a
                        href="#about"
                        class="text-gray-600 hover:text-[#2f6f61]
                            dark:text-slate-300 dark:hover:text-emerald-300"
                    >
                        About Me
                    </a>

                    <a href="#portfolio" @click="mobileMenu = false" class="block text-slate-700 dark:text-slate-200">
                        Portfolio
                    </a>

                    <a href="#projects" @click="mobileMenu = false" class="block text-slate-700 dark:text-slate-200">
                        Projects
                    </a>

                    <a href="#certificates" @click="mobileMenu = false" class="block text-slate-700 dark:text-slate-200">
                        Certificates
                    </a>

                    <a href="#contact" @click="mobileMenu = false" class="block text-slate-700 dark:text-slate-200">
                        Contact
                    </a>

                    <div class="flex gap-3 border-t border-gray-100 pt-4 dark:border-slate-800">
                        <a href="{{ route('login') }}" class="flex-1 rounded-xl border border-[#7fac9f] px-4 py-2 text-center text-sm font-semibold text-[#2f6f61] dark:text-emerald-300">
                            Log in
                        </a>

                    </div>
                </nav>
            </div>
        </header>

        <main>
            {{ $slot }}
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const storageKey = 'portfolio-heartbeat-sent';

            /*
            * Hanya satu heartbeat per browser tab/session.
            */
            if (sessionStorage.getItem(storageKey) === '1') {
                return;
            }

            const csrfToken = document.querySelector(
                'meta[name="csrf-token"]'
            )?.content;

            const heartbeatUrl = @json(
                route('analytics.heartbeat')
            );

            const sendHeartbeat = () => {
                if (
                    document.visibilityState !== 'visible'
                    || !csrfToken
                ) {
                    return;
                }

                fetch(heartbeatUrl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    keepalive: true,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                })
                    .then(() => {
                        sessionStorage.setItem(
                            storageKey,
                            '1'
                        );
                    })
                    .catch(() => {
                        /*
                        * Analytics tidak boleh mengganggu
                        * halaman publik.
                        */
                    });
            };

            window.setTimeout(sendHeartbeat, 15000);
        });
    </script>


    @livewireScripts

    @stack('scripts')
</body>
</html>
