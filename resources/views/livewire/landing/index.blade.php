<div
    class="min-h-screen overflow-hidden"
    x-data="{
        scrollY: window.scrollY,
        mouseX: 0,
        mouseY: 0,
        centerX: 0,
        centerY: 0,

        initScene() {
            this.updateScroll();

            window.addEventListener('scroll', () => {
                this.updateScroll();
            });

            window.addEventListener('resize', () => {
                this.centerX = window.innerWidth / 2;
                this.centerY = window.innerHeight / 2;
            });

            this.centerX = window.innerWidth / 2;
            this.centerY = window.innerHeight / 2;
        },

        updateScroll() {
            this.scrollY = window.scrollY;
        },

        updateMouse(event) {
            this.mouseX = (event.clientX - this.centerX) / this.centerX;
            this.mouseY = (event.clientY - this.centerY) / this.centerY;
        },

        resetMouse() {
            this.mouseX = 0;
            this.mouseY = 0;
        },

        clamp(value, min, max) {
            return Math.min(Math.max(value, min), max);
        },

        lowerLift() {
            return Math.min(Math.max(this.scrollY - 360, 0) / 5.5, 70);
        },

        lowerTilt() {
            return Math.min(Math.max(this.scrollY - 360, 0) / 230, 4);
        },

        lowerScale() {
            return 1 + Math.min(Math.max(this.scrollY - 360, 0) / 30000, 0.012);
        }
    }"
    x-init="initScene()"
>
    @php
        $heroBackground = $publicProfile?->hero_background
            ? asset($publicProfile->hero_background)
            : null;
    @endphp

    {{-- CINEMATIC HERO --}}
    <section
        id="overview"
        class="relative min-h-[calc(100vh-72px)] overflow-hidden px-5 py-12 lg:px-8 lg:py-16"
        style="perspective: 1800px;"
        x-on:mousemove="updateMouse($event)"
        x-on:mouseleave="resetMouse()"
    >
        {{-- Background --}}
        @if ($heroBackground)
            <div
                class="absolute inset-0 z-0"
                :style="`
                    background-image: url('{{ $heroBackground }}');
                    background-size: cover;
                    background-position:
                        calc(50% + ${mouseX * 24}px)
                        calc(50% + ${Math.min(scrollY * 0.15, 90)}px);
                    transform:
                        scale(${1.06 + Math.min(scrollY / 10000, 0.06)})
                        translate3d(${mouseX * -14}px, ${mouseY * -10}px, 0);
                    transition: transform 0.18s ease-out, background-position 0.18s ease-out;
                `"
            ></div>
        @else
            <div class="absolute inset-0 z-0 bg-gradient-to-br from-[#eef5f2] via-white to-[#dcebe6] dark:from-slate-900 dark:via-slate-800 dark:to-[#1f3b34]"></div>
        @endif

        {{-- Balanced overlay --}}
        <div class="absolute inset-0 z-10 bg-white/50 backdrop-blur-[1px] dark:bg-slate-950/38"></div>

        {{-- Readability gradient --}}
        <div class="pointer-events-none absolute inset-0 z-20 bg-[linear-gradient(90deg,rgba(255,255,255,0.62)_0%,rgba(255,255,255,0.38)_45%,rgba(255,255,255,0.22)_100%)] dark:bg-[linear-gradient(90deg,rgba(15,23,42,0.40)_0%,rgba(15,23,42,0.30)_48%,rgba(15,23,42,0.18)_100%)]"></div>

        {{-- Vignette --}}
        <div class="pointer-events-none absolute inset-0 z-20 bg-[radial-gradient(circle_at_center,transparent_0%,transparent_48%,rgba(15,23,42,0.18)_100%)] dark:bg-[radial-gradient(circle_at_center,transparent_0%,transparent_52%,rgba(2,6,23,0.38)_100%)]"></div>

        {{-- Glow --}}
        <div
            class="pointer-events-none absolute left-[8%] top-[18%] z-20 h-56 w-56 rounded-full bg-[#7fac9f]/25 blur-3xl dark:bg-emerald-400/12"
            :style="`
                transform:
                    translate3d(${mouseX * 42}px, ${mouseY * 34}px, 0)
                    scale(${1 + Math.min(scrollY / 6000, 0.18)});
                transition: transform 0.18s ease-out;
            `"
        ></div>

        <div
            class="pointer-events-none absolute bottom-[10%] right-[10%] z-20 h-72 w-72 rounded-full bg-emerald-200/20 blur-3xl dark:bg-emerald-300/10"
            :style="`
                transform:
                    translate3d(${mouseX * -55}px, ${mouseY * -38}px, 0)
                    scale(${1.05 + Math.min(scrollY / 7000, 0.16)});
                transition: transform 0.18s ease-out;
            `"
        ></div>

        {{-- Hero Content --}}
        <div
            class="relative z-30 mx-auto max-w-7xl"
            :style="`
                transform-style: preserve-3d;
                transform:
                    rotateX(${clamp(mouseY * -4 + scrollY / 140, -6, 9)}deg)
                    rotateY(${clamp(mouseX * 5, -7, 7)}deg)
                    translateY(${Math.min(scrollY / 18, 34)}px)
                    scale(${1 - Math.min(scrollY / 7500, 0.035)});
                transition: transform 0.16s ease-out;
            `"
        >
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                {{-- Left hero card --}}
                <div
                    class="relative overflow-hidden rounded-[36px] bg-white/92 p-8 shadow-[0_35px_100px_rgba(15,23,42,0.22)] ring-1 ring-white/90 backdrop-blur-2xl dark:bg-white/86 dark:ring-white/40 lg:col-span-2 lg:p-10"
                    :style="`
                        transform:
                            translateZ(95px)
                            rotateY(${clamp(mouseX * 4 + scrollY / 180, -6, 6)}deg)
                            rotateX(${clamp(mouseY * -3, -4, 4)}deg);
                        transition: transform 0.16s ease-out;
                    `"
                >
                    <div class="pointer-events-none absolute -right-20 -top-20 h-52 w-52 rounded-full bg-[#7fac9f]/18 blur-3xl"></div>
                    <div class="pointer-events-none absolute -bottom-24 left-10 h-44 w-44 rounded-full bg-emerald-300/16 blur-3xl"></div>
                    <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-white/80 via-white/45 to-transparent"></div>

                    <div class="relative">
                        <p class="text-sm font-bold uppercase tracking-[0.28em] text-[#2f6f61]">
                            {{ $publicProfile?->hero_badge ?? 'MY PORTFOLIO' }}
                        </p>

                        <h1 class="mt-6 max-w-4xl text-4xl font-black leading-[0.95] tracking-[-0.05em] text-slate-950 drop-shadow-sm sm:text-5xl lg:text-6xl">
                            {{ $publicProfile?->hero_title ?? 'Creative portfolio system for refined digital projects.' }}
                        </h1>

                        <p class="mt-6 max-w-2xl text-base font-medium leading-relaxed text-slate-700">
                            {{ $publicProfile?->hero_description ?? 'Explore selected works, project progress, design systems, and portfolio activity from one clean public page.' }}
                        </p>

                        <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                            <a
                                href="#portfolio"
                                class="group inline-flex items-center justify-center gap-2 rounded-2xl bg-[#7fac9f] px-6 py-3 text-center text-sm font-semibold text-white shadow-[0_16px_40px_rgba(47,111,97,0.35)] transition hover:-translate-y-1 hover:bg-[#6d9b8f]"
                            >
                                View Portfolio
                                <span class="transition group-hover:translate-x-1">→</span>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Profile card --}}
                <div
                    class="relative overflow-hidden rounded-[36px] bg-white/90 p-8 text-center shadow-[0_35px_100px_rgba(15,23,42,0.22)] ring-1 ring-white/90 backdrop-blur-2xl dark:bg-white/84 dark:ring-white/40 lg:p-10"
                    :style="`
                        transform:
                            translateZ(135px)
                            rotateY(${clamp(mouseX * -6 - scrollY / 200, -8, 8)}deg)
                            rotateX(${clamp(mouseY * -3, -5, 5)}deg)
                            translateY(${Math.min(scrollY / 24, 20)}px);
                        transition: transform 0.16s ease-out;
                    `"
                >
                    <div class="pointer-events-none absolute -left-16 -top-16 h-44 w-44 rounded-full bg-white/70 blur-3xl"></div>
                    <div class="pointer-events-none absolute inset-0 bg-gradient-to-b from-white/70 via-white/35 to-transparent"></div>

                    <div
                        class="relative mx-auto flex h-36 w-36 items-center justify-center overflow-hidden rounded-full bg-[#2f6f61] text-4xl font-bold text-white ring-4 ring-white shadow-[0_20px_60px_rgba(15,23,42,0.26)]"
                        :style="`
                            transform:
                                translateZ(65px)
                                rotateX(${clamp(mouseY * -7, -8, 8)}deg)
                                rotateY(${clamp(mouseX * 7, -8, 8)}deg);
                            transition: transform 0.16s ease-out;
                        `"
                    >
                        @if ($publicProfile?->avatar)
                            <img
                                src="{{ asset($publicProfile->avatar) }}"
                                alt="{{ $publicProfile->name }}"
                                class="h-full w-full object-cover"
                            >
                        @else
                            {{ strtoupper(substr($publicProfile?->name ?? 'A', 0, 1)) }}
                        @endif
                    </div>

                    <h2 class="relative mt-7 text-2xl font-extrabold tracking-[-0.03em] text-slate-950 drop-shadow-sm">
                        {{ $publicProfile?->name ?? 'Alex Rivera' }}
                    </h2>

                    <p class="relative mt-1 text-sm font-bold text-[#2f6f61]">
                        {{ $publicProfile?->role ?? 'Portfolio Manager' }}
                    </p>

                    <p class="relative mx-auto mt-5 max-w-sm text-sm font-medium leading-relaxed text-slate-700">
                        {{ $publicProfile?->bio ?? 'Creative portfolio manager focused on refined digital projects and clean design systems.' }}
                    </p>

                    <div
                        class="relative mt-7 rounded-3xl bg-white/88 p-5 shadow-sm backdrop-blur-md"
                        :style="`
                            transform: translateZ(55px);
                            transition: transform 0.16s ease-out;
                        `"
                    >
                        <p class="text-sm font-medium text-gray-500">
                            Project likes collected
                        </p>

                        <p class="mt-2 text-4xl font-black text-slate-950">
                            {{ number_format($totalLikes) }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Summary card --}}
            <div
                class="mt-12 flex justify-center"
                :style="`
                    transform-style: preserve-3d;
                    transform:
                        translateY(${Math.min(scrollY / 22, 28)}px)
                        rotateX(${clamp(scrollY / 180, 0, 6)}deg);
                    transition: transform 0.16s ease-out;
                `"
            >
                <div
                    class="relative w-full max-w-md overflow-hidden rounded-[36px] bg-white/92 p-8 text-center shadow-[0_35px_100px_rgba(15,23,42,0.22)] ring-1 ring-white/90 backdrop-blur-2xl dark:bg-white/86 dark:ring-white/40"
                    :style="`
                        transform:
                            translateZ(170px)
                            rotateY(${clamp(mouseX * 4, -6, 6)}deg)
                            rotateX(${clamp(mouseY * -4, -6, 6)}deg)
                            scale(${1 - Math.min(scrollY / 9000, 0.03)});
                        transition: transform 0.16s ease-out;
                    `"
                >
                    <div class="pointer-events-none absolute inset-x-10 -top-16 h-28 rounded-full bg-[#7fac9f]/20 blur-3xl"></div>

                    <span class="relative inline-flex rounded-full bg-[#eef5f2] px-4 py-2 text-xs font-bold uppercase tracking-[0.18em] text-[#2f6f61]">
                        Portfolio Summary
                    </span>

                    <h2 class="relative mt-6 text-7xl font-black tracking-[-0.06em] text-slate-950">
                        {{ $totalProjects }}
                    </h2>

                    <p class="relative mt-3 text-sm font-semibold text-gray-500">
                        Published portfolio projects
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- LOWER 3D OVERLAY SECTION --}}
    <section
        id="portfolio"
        class="relative z-40 -mt-12 px-6 pb-12 pt-16 sm:px-8 lg:px-14"
        style="perspective: 1700px;"
    >
        <div
            class="pointer-events-none absolute inset-x-0 top-0 h-40 bg-gradient-to-b from-transparent via-[#f5f8f8]/60 to-[#f5f8f8] dark:via-slate-950/70 dark:to-slate-950"
        ></div>

        <div
            class="relative mx-auto max-w-6xl rounded-[42px] bg-white/78 p-5 shadow-[0_-24px_80px_rgba(15,23,42,0.10)] ring-1 ring-white/70 backdrop-blur-2xl dark:bg-slate-950/78 dark:ring-white/10 sm:p-7 lg:p-8"
            :style="`
                transform-style: preserve-3d;
                transform:
                    translateY(-${lowerLift()}px)
                    translateZ(${Math.min(Math.max(scrollY - 360, 0) / 8, 45)}px)
                    rotateX(${lowerTilt()}deg)
                    scale(${lowerScale()});
                transform-origin: top center;
                transition: transform 0.16s ease-out;
            `"
        >
            <div class="pointer-events-none absolute -top-20 left-1/2 h-56 w-[70%] -translate-x-1/2 rounded-full bg-[#7fac9f]/20 blur-3xl dark:bg-emerald-400/10"></div>

            {{-- FEATURED PORTFOLIO --}}
            <div
                class="relative"
                :style="`
                    transform:
                        translateZ(70px)
                        rotateX(${clamp((scrollY - 420) / 320, 0, 3)}deg);
                    transition: transform 0.16s ease-out;
                `"
            >
                <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-[#2f6f61] dark:text-emerald-300">
                            Curated Selection
                        </p>

                        <h2 class="mt-3 text-3xl font-bold text-slate-950 dark:text-white sm:text-4xl">
                            My Projects
                        </h2>
                    </div>

                    <input
                        wire:model.live.debounce.400ms="search"
                        type="text"
                        placeholder="Search portfolio..."
                        class="rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:border-[#7fac9f] dark:border-slate-700 dark:bg-slate-900 dark:text-white"
                    >
                </div>

                @if ($featured)
                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                        <div
                            class="overflow-hidden rounded-[32px] bg-white shadow-[0_25px_70px_rgba(15,23,42,0.14)] dark:bg-slate-900"
                            :style="`
                                transform:
                                    translateZ(70px)
                                    rotateY(${clamp(mouseX * 2, -3, 3)}deg);
                                transition: transform 0.16s ease-out;
                            `"
                        >
                            <img
                                src="{{ asset($featured->image) }}"
                                alt="{{ $featured->name }}"
                                class="h-72 w-full object-cover sm:h-80 lg:h-[360px]"
                            >
                        </div>

                        <div
                            class="rounded-[32px] bg-white p-6 shadow-[0_25px_70px_rgba(15,23,42,0.12)] dark:bg-slate-900 sm:p-8"
                            :style="`
                                transform:
                                    translateZ(90px)
                                    rotateY(${clamp(mouseX * -2, -3, 3)}deg);
                                transition: transform 0.16s ease-out;
                            `"
                        >
                            <span class="rounded-full bg-[#eef5f2] px-3 py-1 text-xs font-semibold text-[#2f6f61] dark:bg-emerald-950 dark:text-emerald-200">
                                Featured Project
                            </span>

                            <h3 class="mt-6 text-2xl font-bold text-slate-950 dark:text-white sm:text-3xl">
                                {{ $featured->name }}
                            </h3>

                            <p class="mt-2 text-sm text-gray-500 dark:text-slate-400">
                                {{ $featured->category }} · {{ $featured->client }} · {{ $featured->created_at->format('Y') }}
                            </p>

                            <p class="mt-6 leading-relaxed text-gray-600 dark:text-slate-300">
                                {{ $featured->description }}
                            </p>

                            <div class="mt-6 flex flex-wrap gap-2">
                                @foreach ($featured->tags ?? [] as $tag)
                                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs dark:bg-slate-800 dark:text-slate-200">
                                        {{ $tag }}
                                    </span>
                                @endforeach
                            </div>

                            <div class="mt-8 flex flex-col gap-4 border-t border-gray-100 pt-6 dark:border-slate-800 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-slate-400">
                                        Project Likes
                                    </p>

                                    <p class="text-2xl font-bold text-slate-950 dark:text-white">
                                        {{ number_format($featured->likes) }}
                                    </p>
                                </div>

                                <div class="flex flex-col gap-3 sm:flex-row">
                                    @if ($featured->website_url)
                                        <a
                                            href="{{ $featured->website_url }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="rounded-xl bg-[#7fac9f] px-5 py-3 text-center text-sm font-semibold text-white"
                                        >
                                            Visit Website
                                        </a>
                                    @endif

                                    @php
                                        $featuredLiked = in_array(
                                            (int) $featured->id,
                                            $likedProjectIds,
                                            true
                                        );
                                    @endphp

                                    <button
                                        type="button"
                                        wire:click="like({{ $featured->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="like({{ $featured->id }})"
                                        @disabled($featuredLiked)
                                        @class([
                                            'rounded-xl border px-5 py-3 text-sm font-semibold transition',
                                            'border-[#7fac9f] text-[#2f6f61] hover:bg-[#eef5f2] dark:text-emerald-300 dark:hover:bg-slate-800' =>
                                                ! $featuredLiked,
                                            'cursor-not-allowed border-emerald-200 bg-emerald-50 text-emerald-700 opacity-75 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-300' =>
                                                $featuredLiked,
                                        ])
                                    >
                                        {{ $featuredLiked ? '♥ Liked' : '♡ Like Project' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="rounded-[24px] bg-white p-8 text-gray-600 shadow-sm dark:bg-slate-900 dark:text-slate-300">
                        Belum ada data project.
                    </div>
                @endif
            </div>

            {{-- PROJECT CARDS --}}
            <div
                id="projects"
                class="relative mt-12"
                :style="`
                    transform:
                        translateZ(85px)
                        translateY(-${Math.min(Math.max(scrollY - 760, 0) / 12, 26)}px);
                    transition: transform 0.16s ease-out;
                `"
            >
                <div class="mb-6">
                    <h2 class="text-3xl font-bold text-slate-950 dark:text-white">
                        Recent Works
                    </h2>

                    <p class="mt-2 text-sm text-gray-500 dark:text-slate-400">
                        Latest public portfolio entries.
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
                    @forelse ($projects as $project)
                        <div
                            wire:key="public-project-{{ $project->id }}"
                            class="overflow-hidden rounded-[24px] bg-white shadow-[0_20px_60px_rgba(15,23,42,0.10)] transition dark:bg-slate-900"
                            :style="`
                                transform:
                                    translateZ(${45 + Math.min(Math.max(scrollY - 780, 0) / 16, 28)}px)
                                    rotateX(${clamp((scrollY - 780) / 420, 0, 2)}deg);
                                transition: transform 0.16s ease-out;
                            `"
                        >
                            <img
                                src="{{ asset($project->image) }}"
                                alt="{{ $project->name }}"
                                class="h-56 w-full object-cover"
                            >

                            <div class="p-6">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <h3 class="text-xl font-semibold text-slate-950 dark:text-white">
                                            {{ $project->name }}
                                        </h3>

                                        <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">
                                            {{ $project->category }}
                                        </p>
                                    </div>

                                    <span class="text-xs text-gray-500 dark:text-slate-400">
                                        {{ $project->created_at->format('Y') }}
                                    </span>
                                </div>

                                <p class="mt-4 text-sm leading-relaxed text-gray-600 dark:text-slate-300">
                                    {{ $project->description }}
                                </p>

                                <div class="mt-6 flex flex-wrap gap-2">
                                    @foreach ($project->tags ?? [] as $tag)
                                        <span class="rounded-full bg-gray-100 px-3 py-1 text-xs dark:bg-slate-800 dark:text-slate-200">
                                            {{ $tag }}
                                        </span>
                                    @endforeach
                                </div>

                                <div class="mt-6 flex flex-col gap-3 border-t border-gray-100 pt-5 dark:border-slate-800 sm:flex-row sm:items-center sm:justify-between">
                                    @php
                                        $projectLiked = in_array(
                                            (int) $project->id,
                                            $likedProjectIds,
                                            true
                                        );
                                    @endphp

                                    <button
                                        type="button"
                                        wire:click="like({{ $project->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="like({{ $project->id }})"
                                        @disabled($projectLiked)
                                        @class([
                                            'text-left text-sm font-semibold transition',
                                            'text-[#2f6f61] hover:underline dark:text-emerald-300' =>
                                                ! $projectLiked,
                                            'cursor-not-allowed text-emerald-700 opacity-75 dark:text-emerald-300' =>
                                                $projectLiked,
                                        ])
                                    >
                                        {{ $projectLiked ? '♥' : '♡' }}
                                        {{ number_format($project->likes) }}
                                        likes
                                    </button>

                                    <div class="flex items-center gap-3">
                                        @if ($project->website_url)
                                            <a
                                                href="{{ $project->website_url }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="text-sm font-semibold text-[#2f6f61] hover:underline dark:text-emerald-300"
                                            >
                                                Website
                                            </a>
                                        @endif

                                        <span class="rounded-full bg-[#eef5f2] px-3 py-1 text-xs text-[#2f6f61] dark:bg-emerald-950 dark:text-emerald-200">
                                            {{ str_replace('_', ' ', $project->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-[24px] bg-white p-8 text-center text-gray-500 shadow-sm dark:bg-slate-900 dark:text-slate-300 md:col-span-2 xl:col-span-3">
                            Tidak ada project yang cocok.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- CERTIFICATES --}}
            <div
                id="certificates"
                class="relative mt-12"
                :style="`
                    transform:
                        translateZ(125px)
                        translateY(-${Math.min(Math.max(scrollY - 900, 0) / 11, 34)}px)
                        rotateX(${clamp((scrollY - 900) / 380, 0, 3)}deg);
                    transition: transform 0.16s ease-out;
                `"
            >
                <div class="mb-6">
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-[#2f6f61] dark:text-emerald-300">
                        Certificates
                    </p>

                    <h2 class="mt-3 text-3xl font-bold text-slate-950 dark:text-white">
                        My Certificates
                    </h2>

                    <p class="mt-2 text-sm text-gray-500 dark:text-slate-400">
                        Selected certificates and professional learning documents.
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
                    @forelse ($certificates as $certificate)
                        <div
                            wire:key="public-certificate-{{ $certificate->id }}"
                            class="overflow-hidden rounded-[28px] bg-white shadow-[0_20px_60px_rgba(15,23,42,0.12)] dark:bg-slate-900"
                            :style="`
                                transform:
                                    translateZ(${55 + Math.min(Math.max(scrollY - 900, 0) / 18, 35)}px)
                                    rotateX(${clamp((scrollY - 900) / 480, 0, 2)}deg);
                                transition: transform 0.16s ease-out;
                            `"
                        >
                            <div class="bg-[#eef5f2] p-4 dark:bg-slate-800">
                                <div
                                    class="certificate-preview overflow-hidden rounded-2xl bg-white shadow-inner dark:bg-slate-950"
                                    wire:ignore
                                >
                                    <canvas
                                        class="certificate-canvas block w-full"
                                        data-pdf-url="{{ asset($certificate->pdf_path) }}"
                                    ></canvas>
                                </div>
                            </div>

                            <div class="p-6">
                                <h3 class="text-xl font-bold text-slate-950 dark:text-white">
                                    {{ $certificate->title }}
                                </h3>

                                <p class="mt-1 text-sm font-medium text-[#2f6f61] dark:text-emerald-300">
                                    {{ $certificate->issuer ?: 'Certificate' }}
                                </p>

                                <p class="mt-3 text-sm leading-relaxed text-gray-600 dark:text-slate-300">
                                    {{ $certificate->description ?: 'Certificate document uploaded as PDF.' }}
                                </p>

                                <div class="mt-5 flex items-center justify-between gap-3 border-t border-gray-100 pt-5 dark:border-slate-800">
                                    <span class="text-xs text-gray-500 dark:text-slate-400">
                                        {{ $certificate->issued_at ? $certificate->issued_at->format('Y') : 'PDF' }}
                                    </span>

                                    <a
                                        href="{{ asset($certificate->pdf_path) }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="rounded-xl border border-[#7fac9f] px-4 py-2 text-sm font-semibold text-[#2f6f61] hover:bg-[#eef5f2] dark:text-emerald-300 dark:hover:bg-slate-800"
                                    >
                                        Open PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-[28px] border border-dashed border-gray-300 p-8 text-center text-gray-500 dark:border-slate-700 dark:text-slate-400 md:col-span-2 xl:col-span-3">
                            Belum ada sertifikat yang ditampilkan.
                        </div>
                    @endforelse
                </div>
            </div>

        @push('scripts')
            <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>

            <script>
                function renderCertificatePreviews() {
                    if (!window.pdfjsLib) {
                        return;
                    }

                    window.pdfjsLib.GlobalWorkerOptions.workerSrc =
                        'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

                    const canvases = document.querySelectorAll('.certificate-canvas');

                    canvases.forEach((canvas) => {
                        if (canvas.dataset.rendered === 'true') {
                            return;
                        }

                        const pdfUrl = canvas.dataset.pdfUrl;

                        if (!pdfUrl) {
                            return;
                        }

                        canvas.dataset.rendered = 'true';

                        window.pdfjsLib.getDocument(pdfUrl).promise
                            .then((pdf) => pdf.getPage(1))
                            .then((page) => {
                                const container = canvas.closest('.certificate-preview');
                                const containerWidth = container ? container.clientWidth : 360;

                                const viewport = page.getViewport({ scale: 1 });
                                const scale = containerWidth / viewport.width;
                                const scaledViewport = page.getViewport({ scale: scale });

                                const context = canvas.getContext('2d');

                                canvas.width = scaledViewport.width;
                                canvas.height = scaledViewport.height;

                                page.render({
                                    canvasContext: context,
                                    viewport: scaledViewport
                                });
                            })
                            .catch(() => {
                                const wrapper = canvas.closest('.certificate-preview');

                                if (wrapper) {
                                    wrapper.innerHTML = `
                                        <div class="flex h-64 items-center justify-center p-6 text-center text-sm text-gray-500 dark:text-slate-400">
                                            Preview PDF belum bisa ditampilkan. Klik Open PDF untuk melihat file.
                                        </div>
                                    `;
                                }
                            });
                    });
                }

                document.addEventListener('DOMContentLoaded', renderCertificatePreviews);
                document.addEventListener('livewire:navigated', renderCertificatePreviews);
                document.addEventListener('livewire:updated', renderCertificatePreviews);

                setTimeout(renderCertificatePreviews, 500);
            </script>
        @endpush

            {{-- CONTACT --}}
            <div
                id="contact"
                class="relative mt-12"
                :style="`
                    transform:
                        translateZ(95px)
                        translateY(-${Math.min(Math.max(scrollY - 980, 0) / 10, 38)}px)
                        rotateX(${clamp((scrollY - 980) / 360, 0, 3)}deg);
                    transition: transform 0.16s ease-out;
                `"
            >
                <div class="rounded-[32px] bg-[#eef5f2] p-8 text-center shadow-[0_20px_60px_rgba(15,23,42,0.10)] dark:bg-slate-900 lg:p-10">
                    <h2 class="text-3xl font-bold text-slate-950 dark:text-white">
                        Interested in collaborating?
                    </h2>

                    <p class="mx-auto mt-4 max-w-2xl text-gray-600 dark:text-slate-300">
                        Connect with me through professional and social platforms below.
                    </p>

                    <div class="mt-8 flex flex-wrap justify-center gap-4">
                        <a
                            href="https://github.com/faiqalmuhajid901"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-slate-950 shadow-sm transition hover:-translate-y-1 hover:shadow-md dark:bg-slate-800 dark:text-white"
                            title="GitHub"
                        >
                            <svg class="h-7 w-7" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M12 .5C5.65.5.5 5.65.5 12c0 5.08 3.29 9.39 7.86 10.92.58.1.79-.25.79-.56v-2.15c-3.2.7-3.87-1.37-3.87-1.37-.52-1.33-1.28-1.69-1.28-1.69-1.04-.71.08-.7.08-.7 1.15.08 1.76 1.19 1.76 1.19 1.03 1.75 2.7 1.25 3.36.95.1-.74.4-1.25.73-1.54-2.55-.29-5.23-1.28-5.23-5.68 0-1.25.45-2.28 1.18-3.08-.12-.29-.51-1.46.11-3.04 0 0 .96-.31 3.16 1.18A10.9 10.9 0 0 1 12 6.05c.98 0 1.96.13 2.88.38 2.19-1.49 3.15-1.18 3.15-1.18.63 1.58.24 2.75.12 3.04.74.8 1.18 1.83 1.18 3.08 0 4.41-2.69 5.38-5.25 5.67.41.36.78 1.06.78 2.14v3.18c0 .31.21.67.8.56A11.51 11.51 0 0 0 23.5 12C23.5 5.65 18.35.5 12 .5Z"/>
                            </svg>
                        </a>

                        <a
                            href="https://www.linkedin.com/in/muhammad-faiq-almuhajid201/"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-[#0A66C2] shadow-sm transition hover:-translate-y-1 hover:shadow-md dark:bg-slate-800"
                            title="LinkedIn"
                        >
                            <svg class="h-7 w-7" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M4.98 3.5C4.98 4.88 3.87 6 2.5 6S.02 4.88.02 3.5 1.13 1 2.5 1s2.48 1.12 2.48 2.5ZM.31 8.01h4.38V23H.31V8.01ZM8.09 8.01h4.2v2.05h.06c.58-1.1 2.01-2.26 4.14-2.26 4.43 0 5.25 2.92 5.25 6.71V23h-4.38v-7.53c0-1.8-.03-4.1-2.5-4.1-2.5 0-2.88 1.95-2.88 3.97V23H8.09V8.01Z"/>
                            </svg>
                        </a>

                        <a
                            href="https://www.instagram.com/wfaiq._/"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-pink-600 shadow-sm transition hover:-translate-y-1 hover:shadow-md dark:bg-slate-800"
                            title="Instagram"
                        >
                            <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <rect x="3" y="3" width="18" height="18" rx="5" stroke="currentColor" stroke-width="2"/>
                                <circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="2"/>
                                <circle cx="17.5" cy="6.5" r="1.2" fill="currentColor"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- SCROLL TO TOP --}}
    <button
        type="button"
        onclick="window.scrollTo({ top: 0, behavior: 'smooth' })"
        class="fixed bottom-6 right-6 z-[90] flex h-12 w-12 items-center justify-center rounded-full bg-[#7fac9f] text-white shadow-[0_12px_30px_rgba(15,23,42,0.18)] transition hover:-translate-y-1 hover:bg-[#6c9a8e] dark:bg-emerald-700 dark:hover:bg-emerald-600"
        title="Back to top"
    >
        ↑
    </button>
</div>
