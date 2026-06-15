<div class="space-y-10">
    {{-- Header Portfolio --}}
    <section>
        <div class="flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-[#2f6f61]">
                    Portfolio Gallery
                </p>

                <h1 class="mt-3 text-3xl font-bold sm:text-4xl">
                    Aesthetic Projects
                </h1>

                <p class="mt-3 max-w-2xl text-gray-600">
                    A curated collection of visual systems, digital products,
                    and experimental interface concepts.
                </p>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row">
                <input
                    wire:model.live.debounce.400ms="search"
                    type="text"
                    placeholder="Search portfolio..."
                    class="rounded-xl border border-gray-200 px-4 py-3 text-sm outline-none focus:border-[#7fac9f]"
                >

                <a href="{{ route('projects') }}"
                   class="rounded-xl bg-[#7fac9f] px-5 py-3 text-center text-sm font-semibold text-white">
                    Manage Projects
                </a>
            </div>
        </div>
    </section>

    {{-- Featured Project --}}
    @if ($featured)
        <section class="grid grid-cols-1 gap-6 lg:grid-cols-5">
            <div class="overflow-hidden rounded-[32px] bg-white shadow-[0_20px_60px_rgba(15,23,42,0.06)] lg:col-span-3">
                <img
                    src="{{ asset($featured->image) }}"
                    alt="{{ $featured->name }}"
                    class="h-72 w-full object-cover sm:h-96 lg:h-[420px]"
                >
            </div>

            <div class="rounded-[32px] bg-white p-6 shadow-[0_20px_60px_rgba(15,23,42,0.06)] sm:p-8 lg:col-span-2">
                <span class="rounded-full bg-[#eef5f2] px-3 py-1 text-xs font-semibold text-[#2f6f61]">
                    Featured Project
                </span>

                <h2 class="mt-6 text-2xl font-bold sm:text-3xl">
                    {{ $featured->name }}
                </h2>

                <p class="mt-2 text-sm text-gray-500">
                    {{ $featured->category }}
                    ·
                    {{ $featured->client }}
                    ·
                    {{ $featured->created_at->format('Y') }}
                </p>

                <p class="mt-6 leading-relaxed text-gray-600">
                    {{ $featured->description }}
                </p>

                <div class="mt-6 flex flex-wrap gap-2">
                    @foreach ($featured->tags ?? [] as $tag)
                        <span class="rounded-full bg-gray-100 px-3 py-1 text-xs">
                            {{ $tag }}
                        </span>
                    @endforeach
                </div>

                <div class="mt-8 flex flex-col gap-4 border-t border-gray-100 pt-6 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Project Likes</p>
                        <p class="text-2xl font-bold">
                            {{ number_format($featured->likes) }}
                        </p>
                    </div>

                    <button
                        wire:click="like({{ $featured->id }})"
                        class="rounded-xl border border-[#7fac9f] px-5 py-3 text-sm font-semibold text-[#2f6f61] hover:bg-[#eef5f2]"
                    >
                        ♥ Like Project
                    </button>
                </div>
            </div>
        </section>
    @else
        <div class="rounded-[24px] bg-white p-8 text-gray-600 shadow-sm">
            Belum ada data project. Jalankan seeder dulu atau tambahkan project baru.
        </div>
    @endif

    {{-- Recent Works --}}
    <section>
        <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold">
                    Recent Works
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Showing latest portfolio entries
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($projects as $project)
                <div class="overflow-hidden rounded-[24px] bg-white shadow-[0_20px_60px_rgba(15,23,42,0.06)]">
                    <img
                        src="{{ asset($project->image) }}"
                        alt="{{ $project->name }}"
                        class="h-56 w-full object-cover"
                    >

                    <div class="p-6">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h3 class="text-xl font-semibold">
                                    {{ $project->name }}
                                </h3>

                                <p class="mt-1 text-sm text-gray-500">
                                    {{ $project->category }}
                                </p>
                            </div>

                            <span class="text-xs text-gray-500">
                                {{ $project->created_at->format('Y') }}
                            </span>
                        </div>

                        <p class="mt-2 text-sm text-gray-500">
                            Client: {{ $project->client }}
                        </p>

                        <p class="mt-4 text-sm leading-relaxed text-gray-600">
                            {{ $project->description }}
                        </p>

                        <div class="mt-6 flex flex-wrap gap-2">
                            @foreach ($project->tags ?? [] as $tag)
                                <span class="rounded-full bg-gray-100 px-3 py-1 text-xs">
                                    {{ $tag }}
                                </span>
                            @endforeach
                        </div>

                        <div class="mt-6 flex items-center justify-between border-t border-gray-100 pt-5">
                            <button
                                wire:click="like({{ $project->id }})"
                                class="text-sm font-semibold text-[#2f6f61] hover:underline"
                            >
                                ♥ {{ number_format($project->likes) }} likes
                            </button>

                            <span class="rounded-full bg-[#eef5f2] px-3 py-1 text-xs text-[#2f6f61]">
                                {{ str_replace('_', ' ', $project->status) }}
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="rounded-[24px] bg-white p-8 text-center text-gray-500 shadow-sm md:col-span-2 xl:col-span-3">
                    Tidak ada project yang cocok dengan pencarian.
                </div>
            @endforelse
        </div>
    </section>

    {{-- Experimental Lab + CTA --}}
    <section class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="rounded-[28px] bg-[#2f6f61] p-8 text-white">
            <p class="text-sm uppercase tracking-[0.2em] text-white/70">
                Experimental Lab
            </p>

            <h2 class="mt-4 text-2xl font-bold sm:text-3xl">
                Exploring new visual systems
            </h2>

            <p class="mt-4 text-white/80">
                This section can be used for prototypes, unfinished ideas,
                animation tests, or interface experiments.
            </p>
        </div>

        <div class="rounded-[28px] bg-white p-8 shadow-sm">
            <p class="text-sm uppercase tracking-[0.2em] text-[#2f6f61]">
                Collaboration
            </p>

            <h2 class="mt-4 text-2xl font-bold sm:text-3xl">
                Have a project in mind?
            </h2>

            <p class="mt-4 text-gray-600">
                Start a new project entry, organize client work,
                and manage every creative milestone from one dashboard.
            </p>

            <a href="{{ route('projects') }}"
               class="mt-6 inline-block rounded-xl bg-[#7fac9f] px-6 py-3 text-sm font-semibold text-white">
                Create New Project
            </a>
        </div>
    </section>
</div>
