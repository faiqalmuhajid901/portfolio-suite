<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-data="{
        darkMode:
            document.documentElement.classList.contains(
                'dark'
            ),
        mobileMenu: false
    }"
    x-init="
        $watch('darkMode', (value) => {
            document.documentElement.classList.toggle(
                'dark',
                value
            );

            localStorage.setItem(
                'portfolio-dark-mode',
                value ? 'true' : 'false'
            );
        })
    "
    :class="{ 'dark': darkMode }"
>
<head>
    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <meta
        name="color-scheme"
        content="light dark"
    >

    <title>
        {{ $title ?? 'Portfolio Suite' }}
    </title>

    <script>
        (() => {
            const savedPreference =
                localStorage.getItem(
                    'portfolio-dark-mode'
                );

            const systemPreference =
                window.matchMedia(
                    '(prefers-color-scheme: dark)'
                ).matches;

            const shouldUseDarkMode =
                savedPreference === 'true' ||
                (
                    savedPreference === null &&
                    systemPreference
                );

            document.documentElement.classList.toggle(
                'dark',
                shouldUseDarkMode
            );
        })();
    </script>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js',
    ])

    @livewireStyles
</head>

<body
    class="bg-[#f5f8f8] text-slate-950
        antialiased transition-colors
        dark:bg-slate-950 dark:text-slate-100"
>
    <a
        href="#main-content"
        class="public-focus-ring fixed left-4 top-4
            z-[120] -translate-y-24 rounded-xl
            bg-slate-950 px-4 py-3 text-sm
            font-semibold text-white transition
            focus:translate-y-0 dark:bg-white
            dark:text-slate-950"
    >
        Skip to content
    </a>

    <header
        class="sticky top-0 z-[100] border-b
            border-black/5 bg-white/90
            backdrop-blur-xl dark:border-white/10
            dark:bg-slate-950/90"
        @keydown.escape.window="mobileMenu = false"
    >
        <div
            class="mx-auto flex h-[72px] max-w-7xl
                items-center justify-between px-5
                sm:px-6 lg:px-8"
        >
            <a
                href="#overview"
                class="public-focus-ring rounded-lg
                    text-lg font-black tracking-[-0.03em]
                    text-slate-950 dark:text-white"
                @click="mobileMenu = false"
            >
                Portfolio Suite
            </a>

            <nav
                aria-label="Primary navigation"
                class="hidden items-center gap-7
                    text-sm font-semibold text-slate-600
                    dark:text-slate-300 lg:flex"
            >
                <a
                    href="#overview"
                    class="rounded-lg transition
                        hover:text-[#2f6f61]
                        focus-visible:outline-none
                        focus-visible:ring-2
                        focus-visible:ring-[#2f6f61]"
                >
                    Home
                </a>

                <a
                    href="#about"
                    class="rounded-lg transition
                        hover:text-[#2f6f61]
                        focus-visible:outline-none
                        focus-visible:ring-2
                        focus-visible:ring-[#2f6f61]"
                >
                    About
                </a>

                <a
                    href="#portfolio"
                    class="rounded-lg transition
                        hover:text-[#2f6f61]
                        focus-visible:outline-none
                        focus-visible:ring-2
                        focus-visible:ring-[#2f6f61]"
                >
                    Work
                </a>

                <a
                    href="#certificates"
                    class="rounded-lg transition
                        hover:text-[#2f6f61]
                        focus-visible:outline-none
                        focus-visible:ring-2
                        focus-visible:ring-[#2f6f61]"
                >
                    Certificates
                </a>

                <a
                    href="#contact"
                    class="rounded-lg transition
                        hover:text-[#2f6f61]
                        focus-visible:outline-none
                        focus-visible:ring-2
                        focus-visible:ring-[#2f6f61]"
                >
                    Contact
                </a>
            </nav>

            <div class="flex items-center gap-2">
                @guest
                    <a
                        href="{{ route('login') }}"
                        class="public-focus-ring hidden rounded-xl bg-[#2f6f61]
                            px-5 py-2.5 text-sm font-bold text-white
                            transition hover:bg-[#24584d]
                            dark:bg-emerald-700
                            dark:hover:bg-emerald-600
                            lg:inline-flex"
                    >
                        Log in
                    </a>
                @else
                    <a
                        href="{{ route('dashboard') }}"
                        class="public-focus-ring hidden rounded-xl bg-[#2f6f61]
                            px-5 py-2.5 text-sm font-bold text-white
                            transition hover:bg-[#24584d]
                            dark:bg-emerald-700
                            dark:hover:bg-emerald-600
                            lg:inline-flex"
                    >
                        Dashboard
                    </a>
                @endguest
                <button
                    type="button"
                    class="public-focus-ring flex h-10 w-10
                        items-center justify-center rounded-full
                        bg-[#eef5f2] text-lg
                        text-slate-700 transition
                        hover:bg-[#dfece8]
                        dark:bg-slate-800
                        dark:text-slate-100
                        dark:hover:bg-slate-700"
                    aria-label="Toggle dark mode"
                    :aria-pressed="darkMode.toString()"
                    @click="darkMode = !darkMode"
                >
                    <span
                        x-show="!darkMode"
                        aria-hidden="true"
                    >
                        ☾
                    </span>

                    <span
                        x-show="darkMode"
                        x-cloak
                        aria-hidden="true"
                    >
                        ☀
                    </span>
                </button>

                <button
                    type="button"
                    class="public-focus-ring flex h-10 w-10
                        items-center justify-center rounded-full
                        bg-[#eef5f2] text-slate-700
                        transition hover:bg-[#dfece8]
                        dark:bg-slate-800
                        dark:text-slate-100
                        dark:hover:bg-slate-700 lg:hidden"
                    aria-label="Toggle navigation menu"
                    aria-controls="public-mobile-navigation"
                    aria-label="Back to top"
                    :aria-expanded="mobileMenu.toString()"
                    @click="mobileMenu = !mobileMenu"
                >
                    <span
                        x-show="!mobileMenu"
                        aria-hidden="true"
                    >
                        ☰
                    </span>

                    <span
                        x-show="mobileMenu"
                        x-cloak
                        aria-hidden="true"
                    >
                        ✕
                    </span>
                </button>
            </div>
        </div>

        <nav
            id="public-mobile-navigation"
            x-cloak
            x-show="mobileMenu"
            x-transition.opacity.duration.150ms
            @click.outside="mobileMenu = false"
            aria-label="Mobile navigation"
            class="border-t border-black/5 bg-white
                px-5 py-4 dark:border-white/10
                dark:bg-slate-950 lg:hidden"
        >
            <div
                class="mx-auto grid max-w-7xl gap-1
                    text-sm font-semibold"
            >
                <a
                    href="#overview"
                    class="rounded-xl px-4 py-3
                        text-slate-700 hover:bg-[#eef5f2]
                        dark:text-slate-200
                        dark:hover:bg-slate-800"
                    @click="mobileMenu = false"
                >
                    Home
                </a>

                <a
                    href="#about"
                    class="rounded-xl px-4 py-3
                        text-slate-700 hover:bg-[#eef5f2]
                        dark:text-slate-200
                        dark:hover:bg-slate-800"
                    @click="mobileMenu = false"
                >
                    About
                </a>

                <a
                    href="#portfolio"
                    class="rounded-xl px-4 py-3
                        text-slate-700 hover:bg-[#eef5f2]
                        dark:text-slate-200
                        dark:hover:bg-slate-800"
                    @click="mobileMenu = false"
                >
                    Work
                </a>

                <a
                    href="#certificates"
                    class="rounded-xl px-4 py-3
                        text-slate-700 hover:bg-[#eef5f2]
                        dark:text-slate-200
                        dark:hover:bg-slate-800"
                    @click="mobileMenu = false"
                >
                    Certificates
                </a>

                <a
                    href="#contact"
                    class="rounded-xl px-4 py-3
                        text-slate-700 hover:bg-[#eef5f2]
                        dark:text-slate-200
                        dark:hover:bg-slate-800"
                    @click="mobileMenu = false"
                >
                    Contact
                </a>

                @guest
                    <a
                        href="{{ route('login') }}"
                        class="mt-2 rounded-xl bg-[#2f6f61] px-4 py-3
                            text-center font-bold text-white
                            hover:bg-[#24584d]
                            dark:bg-emerald-700
                            dark:hover:bg-emerald-600"
                        @click="mobileMenu = false"
                    >
                        Log in
                    </a>
                @else
                    <a
                        href="{{ route('dashboard') }}"
                        class="mt-2 rounded-xl bg-[#2f6f61] px-4 py-3
                            text-center font-bold text-white
                            hover:bg-[#24584d]
                            dark:bg-emerald-700
                            dark:hover:bg-emerald-600"
                        @click="mobileMenu = false"
                    >
                        Dashboard
                    </a>
                @endguest

            </div>
        </nav>
    </header>

    <main id="main-content">
        {{ $slot }}
    </main>

    <script>
        document.addEventListener(
            'DOMContentLoaded',
            () => {
                const storageKey =
                    'portfolio-heartbeat-sent';

                if (
                    sessionStorage.getItem(
                        storageKey
                    ) === '1'
                ) {
                    return;
                }

                const csrfToken =
                    document.querySelector(
                        'meta[name="csrf-token"]'
                    )?.content;

                const heartbeatUrl =
                    @json(route('analytics.heartbeat'));

                const sendHeartbeat = () => {
                    if (
                        document.visibilityState !==
                            'visible' ||
                        !csrfToken
                    ) {
                        return;
                    }

                    fetch(
                        heartbeatUrl,
                        {
                            method: 'POST',
                            credentials: 'same-origin',
                            keepalive: true,
                            headers: {
                                'X-CSRF-TOKEN':
                                    csrfToken,

                                'X-Requested-With':
                                    'XMLHttpRequest',

                                Accept:
                                    'application/json',
                            },
                        }
                    )
                        .then(() => {
                            sessionStorage.setItem(
                                storageKey,
                                '1'
                            );
                        })
                        .catch(() => {
                            /*
                             * Analytics tidak boleh
                             * mengganggu halaman publik.
                             */
                        });
                };

                window.setTimeout(
                    sendHeartbeat,
                    15000
                );
            }
        );
    </script>

    @livewireScripts
    @stack('scripts')
</body>
</html>
