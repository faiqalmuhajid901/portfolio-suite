<header class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <button
                type="button"
                @click="sidebarOpen = true"
                class="rounded-xl bg-white px-3 py-2 shadow-sm transition dark:bg-slate-900 lg:hidden"
            >
                ☰
            </button>

            <h2 class="text-xl font-bold text-slate-950 dark:text-white">
                Portfolio Suite
            </h2>
        </div>

        {{-- Mobile action buttons --}}
        <div class="flex items-center gap-3 lg:hidden">
            <button class="rounded-full bg-white p-2 shadow-sm transition dark:bg-slate-900">
                🔔
            </button>

            <button
                type="button"
                @click="darkMode = !darkMode"
                class="rounded-full bg-white p-2 shadow-sm transition hover:bg-gray-50 dark:bg-slate-900 dark:hover:bg-slate-800"
                title="Toggle dark mode"
            >
                <span x-show="!darkMode" x-cloak>🌙</span>
                <span x-show="darkMode" x-cloak>☀️</span>
            </button>

            {{-- Mobile Profile Dropdown --}}
            <div class="relative" x-data="{ profileOpen: false }">
                <button
                    type="button"
                    @click="profileOpen = !profileOpen"
                    class="flex h-9 w-9 items-center justify-center overflow-hidden rounded-full bg-[#2f6f61] text-sm font-bold text-white shadow-sm transition hover:scale-105"
                    title="Account menu"
                >
                    @if (auth()->user()?->profile?->avatar)
                        <img
                            src="{{ asset(auth()->user()->profile->avatar) }}"
                            alt="{{ auth()->user()->name }}"
                            class="h-full w-full object-cover"
                        >
                    @else
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    @endif
                </button>

                <div
                    x-cloak
                    x-show="profileOpen"
                    x-transition
                    @click.outside="profileOpen = false"
                    class="absolute right-0 z-[90] mt-3 w-64 overflow-hidden rounded-2xl bg-white shadow-[0_20px_60px_rgba(15,23,42,0.15)] ring-1 ring-black/5 dark:bg-slate-900 dark:ring-white/10"
                >
                    <div class="border-b border-gray-100 px-5 py-4 dark:border-slate-800">
                        <p class="font-semibold text-slate-950 dark:text-white">
                            {{ auth()->user()->name ?? 'User' }}
                        </p>

                        <p class="mt-1 truncate text-sm text-gray-500 dark:text-slate-400">
                            {{ auth()->user()->email ?? 'user@email.com' }}
                        </p>
                    </div>

                    <div class="p-2">
                        <a
                            href="{{ route('profile.show') }}"
                            class="block rounded-xl px-4 py-3 text-sm text-slate-700 hover:bg-gray-100 dark:text-slate-200 dark:hover:bg-slate-800"
                        >
                            Profile
                        </a>

                        <a
                            href="{{ route('settings') }}"
                            class="block rounded-xl px-4 py-3 text-sm text-slate-700 hover:bg-gray-100 dark:text-slate-200 dark:hover:bg-slate-800"
                        >
                            Settings
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <button
                                type="submit"
                                class="w-full rounded-xl px-4 py-3 text-left text-sm font-semibold text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/40"
                            >
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between lg:flex-1">
        <nav class="flex gap-6 overflow-x-auto text-sm scrollbar-hide lg:ml-8">
            <a href="{{ route('dashboard') }}"
               class="whitespace-nowrap {{ request()->routeIs('dashboard') ? 'border-b border-gray-500 pb-1 font-semibold text-slate-950 dark:text-white' : 'text-gray-500 hover:text-gray-900 dark:text-slate-400 dark:hover:text-white' }}">
                Overview
            </a>

            <a href="{{ route('activity') }}"
               class="whitespace-nowrap {{ request()->routeIs('activity') ? 'border-b border-gray-500 pb-1 font-semibold text-slate-950 dark:text-white' : 'text-gray-500 hover:text-gray-900 dark:text-slate-400 dark:hover:text-white' }}">
                Activity
            </a>

            <a href="{{ route('portfolio') }}"
               class="whitespace-nowrap {{ request()->routeIs('portfolio') ? 'border-b border-gray-500 pb-1 font-semibold text-slate-950 dark:text-white' : 'text-gray-500 hover:text-gray-900 dark:text-slate-400 dark:hover:text-white' }}">
                Portfolio
            </a>

            <a href="{{ route('projects') }}"
               class="whitespace-nowrap {{ request()->routeIs('projects') ? 'border-b border-gray-500 pb-1 font-semibold text-slate-950 dark:text-white' : 'text-gray-500 hover:text-gray-900 dark:text-slate-400 dark:hover:text-white' }}">
                Projects
            </a>
        </nav>

        <div class="flex items-center gap-4">
            <input
                type="text"
                placeholder="Search projects..."
                class="w-full rounded-full bg-white/80 px-4 py-2 text-sm outline-none ring-1 ring-gray-100 transition dark:bg-slate-900 dark:text-white dark:ring-slate-800 sm:w-64"
            >

            {{-- Desktop action buttons --}}
            <div class="hidden items-center gap-4 lg:flex">
                <button class="rounded-full bg-white p-2 shadow-sm transition dark:bg-slate-900">
                    🔔
                </button>

                <button
                    type="button"
                    @click="darkMode = !darkMode"
                    class="rounded-full bg-white p-2 shadow-sm transition hover:bg-gray-50 dark:bg-slate-900 dark:hover:bg-slate-800"
                    title="Toggle dark mode"
                >
                    <span x-show="!darkMode" x-cloak>🌙</span>
                    <span x-show="darkMode" x-cloak>☀️</span>
                </button>

                {{-- Desktop Profile Dropdown --}}
                <div class="relative" x-data="{ profileOpen: false }">
                    <button
                        type="button"
                        @click="profileOpen = !profileOpen"
                        class="flex h-9 w-9 items-center justify-center overflow-hidden rounded-full bg-[#2f6f61] text-sm font-bold text-white shadow-sm transition hover:scale-105"
                        title="Account menu"
                    >
                        @if (auth()->user()?->profile?->avatar)
                            <img
                                src="{{ asset(auth()->user()->profile->avatar) }}"
                                alt="{{ auth()->user()->name }}"
                                class="h-full w-full object-cover"
                            >
                        @else
                            {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                        @endif
                    </button>

                    <div
                        x-cloak
                        x-show="profileOpen"
                        x-transition
                        @click.outside="profileOpen = false"
                        class="absolute right-0 z-[90] mt-3 w-64 overflow-hidden rounded-2xl bg-white shadow-[0_20px_60px_rgba(15,23,42,0.15)] ring-1 ring-black/5 dark:bg-slate-900 dark:ring-white/10"
                    >
                        <div class="border-b border-gray-100 px-5 py-4 dark:border-slate-800">
                            <p class="font-semibold text-slate-950 dark:text-white">
                                {{ auth()->user()->name ?? 'User' }}
                            </p>

                            <p class="mt-1 truncate text-sm text-gray-500 dark:text-slate-400">
                                {{ auth()->user()->email ?? 'user@email.com' }}
                            </p>
                        </div>

                        <div class="p-2">
                            <a
                                href="{{ route('profile.show') }}"
                                class="block rounded-xl px-4 py-3 text-sm text-slate-700 hover:bg-gray-100 dark:text-slate-200 dark:hover:bg-slate-800"
                            >
                                Profile
                            </a>

                            <a
                                href="{{ route('settings') }}"
                                class="block rounded-xl px-4 py-3 text-sm text-slate-700 hover:bg-gray-100 dark:text-slate-200 dark:hover:bg-slate-800"
                            >
                                Settings
                            </a>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <button
                                    type="submit"
                                    class="w-full rounded-xl px-4 py-3 text-left text-sm font-semibold text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/40"
                                >
                                    Log Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>