<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Muhammad Faiq — Portfolio' }}</title>
    <meta name="description" content="Portfolio of Muhammad Faiq: selected software projects, case studies, professional experience, and contact information.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('head')
</head>
<body class="min-h-screen bg-[#f7f8f6] text-slate-950 antialiased selection:bg-emerald-200">
    <header class="sticky top-0 z-50 border-b border-slate-200/80 bg-[#f7f8f6]/90 backdrop-blur-xl">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-5 py-4 sm:px-8 lg:px-10">
            <a href="{{ route('home') }}" class="text-sm font-black uppercase tracking-[0.22em] text-slate-950">
                Muhammad Faiq
            </a>

            <nav class="hidden items-center gap-7 text-sm font-semibold text-slate-600 md:flex" aria-label="Primary navigation">
                <a class="transition hover:text-slate-950" href="{{ route('home') }}#about">About</a>
                <a class="transition hover:text-slate-950" href="{{ route('home') }}#work">Work</a>
                <a class="transition hover:text-slate-950" href="{{ route('home') }}#experience">Experience</a>
                <a class="transition hover:text-slate-950" href="{{ route('home') }}#certificates">Certificates</a>

                @auth
                    <a class="rounded-full border border-slate-300 px-5 py-2.5 text-slate-800 transition hover:border-slate-950 hover:text-slate-950" href="{{ route('dashboard') }}">Dashboard</a>
                @else
                    <a class="rounded-full border border-slate-300 px-5 py-2.5 text-slate-800 transition hover:border-slate-950 hover:text-slate-950" href="{{ route('login') }}">Login</a>
                @endauth

                <a class="rounded-full bg-slate-950 px-5 py-2.5 text-white transition hover:bg-emerald-700" href="{{ route('home') }}#contact">Contact</a>
            </nav>

            <details class="relative md:hidden">
                <summary class="cursor-pointer list-none rounded-full border border-slate-300 px-4 py-2 text-sm font-bold">Menu</summary>
                <nav class="absolute right-0 mt-3 w-56 rounded-2xl border border-slate-200 bg-white p-3 shadow-xl" aria-label="Mobile navigation">
                    <a class="block rounded-xl px-4 py-3 text-sm font-semibold hover:bg-slate-100" href="{{ route('home') }}#about">About</a>
                    <a class="block rounded-xl px-4 py-3 text-sm font-semibold hover:bg-slate-100" href="{{ route('home') }}#work">Work</a>
                    <a class="block rounded-xl px-4 py-3 text-sm font-semibold hover:bg-slate-100" href="{{ route('home') }}#experience">Experience</a>
                    <a class="block rounded-xl px-4 py-3 text-sm font-semibold hover:bg-slate-100" href="{{ route('home') }}#certificates">Certificates</a>

                    @auth
                        <a class="block rounded-xl border border-slate-300 px-4 py-3 text-sm font-semibold hover:border-slate-950" href="{{ route('dashboard') }}">Dashboard</a>
                    @else
                        <a class="block rounded-xl border border-slate-300 px-4 py-3 text-sm font-semibold hover:border-slate-950" href="{{ route('login') }}">Login</a>
                    @endauth

                    <a class="block rounded-xl bg-slate-950 px-4 py-3 text-sm font-semibold text-white" href="{{ route('home') }}#contact">Contact</a>
                </nav>
            </details>
        </div>
    </header>

    <main>
        {{ $slot }}
    </main>

    <footer class="border-t border-slate-200 bg-white">
        <div class="mx-auto flex max-w-7xl flex-col gap-3 px-5 py-8 text-sm text-slate-500 sm:px-8 md:flex-row md:items-center md:justify-between lg:px-10">
            <p>© {{ now()->year }} Muhammad Faiq. Built with Laravel and Livewire.</p>
            <div class="flex gap-5">
                <a class="font-semibold hover:text-slate-950" href="{{ route('home') }}#work">Selected work</a>
                <a class="font-semibold hover:text-slate-950" href="{{ route('home') }}#contact">Start a conversation</a>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
