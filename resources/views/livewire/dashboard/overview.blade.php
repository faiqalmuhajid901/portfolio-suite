<div class="space-y-8 lg:space-y-10">
    {{-- Hero Section --}}
    <section class="grid grid-cols-1 gap-6 rounded-[28px] bg-white p-6 shadow-[0_20px_60px_rgba(15,23,42,0.06)] lg:grid-cols-3 lg:p-8">
        <div class="lg:col-span-2">
            <span class="inline-flex rounded-full bg-[#dff3ec] px-3 py-1 text-xs font-semibold uppercase tracking-wide text-[#2f6f61]">
                System Status: Optimal
            </span>

            <h1 class="mt-6 text-3xl font-bold leading-tight sm:text-4xl">
                Welcome back, {{ auth()->user()->name ?? 'Alex' }}
            </h1>

            <p class="mt-4 max-w-2xl text-sm leading-relaxed text-gray-600 sm:text-base">
                Your portfolio workspace is ready. Manage project progress, client work,
                creative assets, and profile visibility from one clean dashboard.
            </p>

            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('projects') }}"
                   class="rounded-xl bg-[#7fac9f] px-6 py-3 text-center text-sm font-semibold text-white">
                    Manage Projects
                </a>

                <a href="{{ route('portfolio') }}"
                   class="rounded-xl border border-[#7fac9f] px-6 py-3 text-center text-sm font-semibold text-[#2f6f61]">
                    View Portfolio
                </a>
            </div>
        </div>

        <div class="rounded-[24px] bg-[#eef5f2] p-6">
            <p class="text-sm text-gray-500">
                Total Engagement
            </p>

            <h2 class="mt-6 text-4xl font-bold sm:text-5xl">
                {{ number_format($totalLikes) }}
            </h2>

            <p class="mt-2 text-sm text-gray-500">
                Project likes collected
            </p>
        </div>
    </section>

    {{-- Statistic Cards --}}
    <section class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-[24px] bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">Total Projects</p>
            <h2 class="mt-4 text-4xl font-bold">{{ $totalProjects }}</h2>
        </div>

        <div class="rounded-[24px] bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">Completed</p>
            <h2 class="mt-4 text-4xl font-bold">{{ $completedProjects }}</h2>
        </div>

        <div class="rounded-[24px] bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">In Review</p>
            <h2 class="mt-4 text-4xl font-bold">{{ $reviewProjects }}</h2>
        </div>

        <div class="rounded-[24px] bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">In Progress</p>
            <h2 class="mt-4 text-4xl font-bold">{{ $inProgressProjects }}</h2>
        </div>
    </section>

    {{-- Recent Projects --}}
    <section class="rounded-[28px] bg-white p-6 shadow-[0_20px_60px_rgba(15,23,42,0.06)] lg:p-8">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold">
                    Recent Projects
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Latest work added to your portfolio.
                </p>
            </div>

            <a href="{{ route('projects') }}"
               class="text-sm font-semibold text-[#2f6f61]">
                View all
            </a>
        </div>

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($recentProjects as $project)
                <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-sm text-gray-500">
                                {{ $project->category ?: 'Uncategorized' }}
                            </p>

                            <h3 class="mt-2 break-words font-semibold leading-snug">
                                {{ $project->name }}
                            </h3>

                            <p class="mt-3 break-words text-sm text-gray-500">
                                {{ $project->client ?: 'No client' }}
                            </p>
                        </div>

                        <span class="shrink-0 rounded-full bg-[#eef5f2] px-3 py-1 text-xs text-[#2f6f61]">
                            {{ str_replace('_', ' ', $project->status) }}
                        </span>
                    </div>

                    <p class="mt-4 line-clamp-2 text-sm leading-relaxed text-gray-600">
                        {{ $project->description }}
                    </p>
                </div>
            @empty
                <div class="rounded-2xl border border-gray-100 p-6 text-center text-gray-500 md:col-span-2 xl:col-span-3">
                    Belum ada project. Tambahkan project baru dari halaman Project Manager.
                </div>
            @endforelse
        </div>
    </section>
</div>
