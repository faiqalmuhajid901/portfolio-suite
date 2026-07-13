<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-data="{ darkMode: localStorage.getItem('darkMode') === 'true', sidebarOpen: false }"
    x-init="$watch('darkMode', value => localStorage.setItem('darkMode', value))"
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

<body class="bg-[#f5f8f8] text-[#111827] antialiased transition-colors duration-300 dark:bg-slate-950 dark:text-slate-100">
    <div class="min-h-screen">
        <livewire:components.sidebar />

        <main class="min-h-screen px-4 py-5 sm:px-6 lg:ml-64 lg:px-10 lg:py-6">
            <livewire:components.topbar />

            <div class="mt-6 lg:mt-8">
                {{ $slot }}
            </div>
        </main>
    </div>

    @livewireScripts
</body>
</html>
