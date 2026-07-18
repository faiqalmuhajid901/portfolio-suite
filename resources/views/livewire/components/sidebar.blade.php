<div>
    {{-- Overlay mobile --}}
    <div
        x-cloak
        x-show="sidebarOpen"
        x-transition.opacity
        @click="sidebarOpen = false"
        class="fixed inset-0 z-40 bg-black/40 lg:hidden"
    ></div>

    {{-- Sidebar --}}
    <aside
        x-cloak
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        class="fixed left-0 top-0 z-50 flex h-screen w-72 flex-col overflow-y-auto bg-[#e9eeee] px-6 py-8 shadow-[20px_0_60px_rgba(15,23,42,0.08)] transition duration-300 dark:bg-slate-900 lg:w-64"
    >
        <div class="flex min-h-full flex-col">
            <div class="flex items-start justify-between">
                <h1 class="text-2xl font-bold leading-tight text-slate-950 dark:text-white">
                    My<br>Portfolio
                </h1>

                <button
                    type="button"
                    @click="sidebarOpen = false"
                    class="rounded-xl bg-white px-3 py-2 text-sm dark:bg-slate-800 dark:text-white lg:hidden"
                    aria-label="Close sidebar"
                >
                    ✕
                </button>
            </div>

            {{-- User Card --}}
            <div class="mt-10 flex items-center gap-3 rounded-2xl bg-white/70 p-4 dark:bg-slate-800">
                @if (auth()->user()?->profile?->avatar)
                    <img
                        src="{{ asset(auth()->user()->profile->avatar) }}"
                        alt="{{ auth()->user()->name }}"
                        class="h-11 w-11 rounded-full object-cover"
                    >
                @else
                    <div class="flex h-11 w-11 items-center justify-center rounded-full bg-[#2f6f61] text-sm font-bold text-white">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </div>
                @endif

                <div class="min-w-0">
                    <p class="truncate font-semibold text-slate-950 dark:text-white">
                        {{ auth()->user()->name ?? 'Alex Rivera' }}
                    </p>
                    <p class="truncate text-sm text-gray-500 dark:text-slate-400">
                        {{ auth()->user()?->profile?->role ?? 'Portfolio Manager' }}
                    </p>
                </div>
            </div>

            {{-- Navigation --}}
            <nav class="mt-8 flex-1 space-y-1 text-sm" aria-label="Dashboard navigation">
                <p class="px-4 pb-2 pt-1 text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">
                    Overview
                </p>

                <a
                    href="{{ route('portfolio') }}"
                    class="block rounded-xl px-4 py-3 text-slate-800 hover:bg-white/70 dark:text-slate-200 dark:hover:bg-slate-800 {{ request()->routeIs('portfolio') ? 'bg-white font-semibold dark:bg-slate-800' : '' }}"
                >
                    Portfolio
                </a>

                <a
                    href="{{ route('dashboard') }}"
                    class="block rounded-xl px-4 py-3 text-slate-800 hover:bg-white/70 dark:text-slate-200 dark:hover:bg-slate-800 {{ request()->routeIs('dashboard') ? 'bg-white font-semibold dark:bg-slate-800' : '' }}"
                >
                    Analytics
                </a>

                <a
                    href="{{ route('activity') }}"
                    class="block rounded-xl px-4 py-3 text-slate-800 hover:bg-white/70 dark:text-slate-200 dark:hover:bg-slate-800 {{ request()->routeIs('activity') ? 'bg-white font-semibold dark:bg-slate-800' : '' }}"
                >
                    Activity
                </a>

                <p class="px-4 pb-2 pt-6 text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">
                    Content
                </p>

                @if (Route::has('content.index'))
                    <a
                        href="{{ route('content.index') }}"
                        class="block rounded-xl px-4 py-3 text-slate-800 hover:bg-white/70 dark:text-slate-200 dark:hover:bg-slate-800 {{ request()->routeIs('content.*') ? 'bg-white font-semibold dark:bg-slate-800' : '' }}"
                    >
                        Content Hub
                    </a>
                @endif

                <a
                    href="{{ route('projects') }}"
                    class="block rounded-xl px-4 py-3 text-slate-800 hover:bg-white/70 dark:text-slate-200 dark:hover:bg-slate-800 {{ request()->routeIs('projects') && ! request()->routeIs('projects.show') ? 'bg-white font-semibold dark:bg-slate-800' : '' }}"
                >
                    Projects
                </a>

                @if (Route::has('project-case-studies.index'))
                    <a
                        href="{{ route('project-case-studies.index') }}"
                        class="block rounded-xl px-4 py-3 text-slate-800 hover:bg-white/70 dark:text-slate-200 dark:hover:bg-slate-800 {{ request()->routeIs('project-case-studies.*') ? 'bg-white font-semibold dark:bg-slate-800' : '' }}"
                    >
                        Case Studies
                    </a>
                @endif

                <a
                    href="{{ route('certificates') }}"
                    class="block rounded-xl px-4 py-3 text-slate-800 hover:bg-white/70 dark:text-slate-200 dark:hover:bg-slate-800 {{ request()->routeIs('certificates') ? 'bg-white font-semibold dark:bg-slate-800' : '' }}"
                >
                    Certificates
                </a>

                @if (Route::has('careers.index'))
                    <a
                        href="{{ route('careers.index') }}"
                        class="block rounded-xl px-4 py-3 text-slate-800 hover:bg-white/70 dark:text-slate-200 dark:hover:bg-slate-800 {{ request()->routeIs('careers.*') ? 'bg-white font-semibold dark:bg-slate-800' : '' }}"
                    >
                        Career Timeline
                    </a>
                @endif

                @if (Route::has('messages.index'))
                    <a
                        href="{{ route('messages.index') }}"
                        class="block rounded-xl px-4 py-3 text-slate-800 hover:bg-white/70 dark:text-slate-200 dark:hover:bg-slate-800 {{ request()->routeIs('messages.*') ? 'bg-white font-semibold dark:bg-slate-800' : '' }}"
                    >
                        Contact Inbox
                    </a>
                @endif

                <p class="px-4 pb-2 pt-6 text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">
                    Account
                </p>

                <a
                    href="{{ route('profile.show') }}"
                    class="block rounded-xl px-4 py-3 text-slate-800 hover:bg-white/70 dark:text-slate-200 dark:hover:bg-slate-800 {{ request()->routeIs('profile.*') ? 'bg-white font-semibold dark:bg-slate-800' : '' }}"
                >
                    Profile
                </a>

                <a
                    href="{{ route('settings') }}"
                    class="block rounded-xl px-4 py-3 text-slate-800 hover:bg-white/70 dark:text-slate-200 dark:hover:bg-slate-800 {{ request()->routeIs('settings') ? 'bg-white font-semibold dark:bg-slate-800' : '' }}"
                >
                    Settings
                </a>
            </nav>

            <a
                href="{{ route('home') }}"
                target="_blank"
                rel="noopener noreferrer"
                class="mt-8 block rounded-xl border border-slate-300 px-4 py-3 text-center text-sm font-semibold text-slate-800 transition hover:bg-white/70 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
            >
                Open Public Site ↗
            </a>
        </div>
    </aside>
</div>
