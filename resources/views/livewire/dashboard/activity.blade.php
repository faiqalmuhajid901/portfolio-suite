<div class="space-y-8" wire:poll.10s>
        <section class="rounded-[28px] bg-white p-8 shadow-sm">
        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-[#2f6f61]">
            Activity Monitor
        </p>

        <h1 class="mt-4 text-4xl font-bold">
            Portfolio Activity
        </h1>

        <p class="mt-4 max-w-2xl text-gray-600">
            Track recent project movement, completion status, review progress,
            and portfolio engagement from one activity center.
        </p>
    </section>

    <section class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-4">
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

    <section class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="rounded-[28px] bg-[#2f6f61] p-8 text-white shadow-sm">
            <p class="text-sm text-white/70">Total Likes</p>
            <h2 class="mt-6 text-5xl font-bold">{{ number_format($totalLikes) }}</h2>
            <p class="mt-3 text-white/80">
                Engagement collected across all portfolio projects.
            </p>
        </div>

        <div class="rounded-[28px] bg-white p-8 shadow-sm lg:col-span-2">
            <h2 class="text-2xl font-bold">Recent Timeline</h2>

            <div class="mt-6 space-y-5">
                @forelse ($projects as $project)
                    <div class="flex gap-4 rounded-2xl border border-gray-100 p-5">
                        <div class="mt-1 h-3 w-3 rounded-full bg-[#2f6f61]"></div>

                        <div class="flex-1">
                            <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                                <div>
                                    <h3 class="font-semibold">{{ $project->name }}</h3>
                                    <p class="mt-1 text-sm text-gray-500">
                                        {{ $project->client }} · {{ $project->category }}
                                    </p>
                                </div>

                                <span class="w-fit rounded-full bg-[#eef5f2] px-3 py-1 text-xs text-[#2f6f61]">
                                    {{ str_replace('_', ' ', $project->status) }}
                                </span>
                            </div>

                            <p class="mt-3 text-sm text-gray-600">
                                {{ $project->description }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl border border-gray-100 p-5 text-gray-500">
                        Belum ada project. Jalankan seeder atau tambahkan data project terlebih dahulu.
                    </div>
                @endforelse
            </div>
        </div>
    </section>
</div>
