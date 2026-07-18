<div
    class="min-h-screen overflow-hidden"
    wire:init="loadLikedProjects"
    x-data="publicPortfolio({
        projectSearchIndex: @js($projectSearchIndex ?? [])
    })"
>
    @php
        $heroBackground = $publicProfile?->hero_background
            ? asset($publicProfile->hero_background)
            : null;

        $githubUrl = $publicProfile?->github_url
            ?: 'https://github.com/faiqalmuhajid901';

        $linkedinUrl = $publicProfile?->linkedin_url
            ?: 'https://www.linkedin.com/in/muhammad-faiq-almuhajid201/';
    @endphp

    {{-- HERO --}}
    <section
        id="overview"
        class="public-anchor relative min-h-[calc(100vh-72px)] overflow-hidden px-5 py-12 lg:px-8 lg:py-16"
        x-on:mousemove="updateMouse($event)"
        x-on:mouseleave="resetMouse()"
    >
        @if ($heroBackground)
            <img
                src="{{ $heroBackground }}"
                alt=""
                width="1920"
                height="1080"
                loading="eager"
                decoding="async"
                fetchpriority="high"
                aria-hidden="true"
                class="hero-motion-surface absolute inset-0 z-0 h-full w-full object-cover"
                :style="heroBackgroundStyle()"
            >
        @else
            <div
                class="absolute inset-0 z-0 bg-gradient-to-br from-[#eef5f2] via-white to-[#dcebe6] dark:from-slate-900 dark:via-slate-800 dark:to-[#1f3b34]"
            ></div>
        @endif

        <div class="mobile-reduce-blur absolute inset-0 z-10 bg-white/50 backdrop-blur-[1px] dark:bg-slate-950/38"></div>

        <div
            class="pointer-events-none absolute inset-0 z-20 bg-[linear-gradient(90deg,rgba(255,255,255,0.62)_0%,rgba(255,255,255,0.38)_45%,rgba(255,255,255,0.22)_100%)] dark:bg-[linear-gradient(90deg,rgba(15,23,42,0.40)_0%,rgba(15,23,42,0.30)_48%,rgba(15,23,42,0.18)_100%)]"
        ></div>

        <div
            class="pointer-events-none absolute inset-0 z-20 bg-[radial-gradient(circle_at_center,transparent_0%,transparent_48%,rgba(15,23,42,0.18)_100%)] dark:bg-[radial-gradient(circle_at_center,transparent_0%,transparent_52%,rgba(2,6,23,0.38)_100%)]"
        ></div>

        <div
            class="hero-motion-surface pointer-events-none absolute left-[8%] top-[18%] z-20 h-56 w-56 rounded-full bg-[#7fac9f]/25 blur-3xl dark:bg-emerald-400/12"
            :style="heroGlowStyle(1)"
        ></div>

        <div
            class="hero-motion-surface pointer-events-none absolute bottom-[10%] right-[10%] z-20 h-72 w-72 rounded-full bg-emerald-200/20 blur-3xl dark:bg-emerald-300/10"
            :style="heroGlowStyle(-1)"
        ></div>

        <div
            class="hero-motion-surface relative z-30 mx-auto max-w-7xl"
            :style="heroContentStyle()"
        >
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <article
                    class="hero-motion-surface relative overflow-hidden rounded-[36px] bg-white/92 p-8 shadow-[0_35px_100px_rgba(15,23,42,0.22)] ring-1 ring-white/90 backdrop-blur-2xl dark:bg-slate-900/88 dark:ring-white/10 lg:col-span-2 lg:p-10"
                    :style="heroCardStyle(1)"
                >
                    <div class="pointer-events-none absolute -right-20 -top-20 h-52 w-52 rounded-full bg-[#7fac9f]/18 blur-3xl"></div>
                    <div class="pointer-events-none absolute -bottom-24 left-10 h-44 w-44 rounded-full bg-emerald-300/16 blur-3xl"></div>
                    <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-white/80 via-white/45 to-transparent dark:from-white/5 dark:via-transparent"></div>

                    <div class="relative">
                        <p class="text-sm font-bold uppercase tracking-[0.28em] text-[#2f6f61] dark:text-emerald-300">
                            {{ $publicProfile?->hero_badge ?? 'MY PORTFOLIO' }}
                        </p>

                        <h1 class="mt-6 max-w-4xl text-4xl font-black leading-[0.95] tracking-[-0.05em] text-slate-950 drop-shadow-sm dark:text-white sm:text-5xl lg:text-6xl">
                            {{ $publicProfile?->hero_title ?? 'Creative portfolio system for refined digital projects.' }}
                        </h1>

                        <p class="mt-6 max-w-2xl text-base font-medium leading-relaxed text-slate-700 dark:text-slate-300">
                            {{ $publicProfile?->hero_description ?? 'Explore selected works, project progress, design systems, and portfolio activity from one clean public page.' }}
                        </p>

                        <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                            <a
                                href="#portfolio"
                                class="public-focus-ring group inline-flex items-center justify-center gap-2 rounded-2xl bg-[#7fac9f] px-6 py-3 text-center text-sm font-semibold text-white shadow-[0_16px_40px_rgba(47,111,97,0.35)] transition hover:-translate-y-1 hover:bg-[#6d9b8f]"
                            >
                                View Portfolio
                                <span class="transition group-hover:translate-x-1" aria-hidden="true">→</span>
                            </a>

                            @if ($publicProfile?->public_email)
                                <a
                                    href="mailto:{{ $publicProfile->public_email }}"
                                    class="public-focus-ring inline-flex items-center justify-center rounded-2xl border border-[#7fac9f] bg-white/70 px-6 py-3 text-sm font-semibold text-[#2f6f61] transition hover:bg-white dark:bg-slate-900/70 dark:text-emerald-300"
                                >
                                    Contact Me
                                </a>
                            @endif
                        </div>
                    </div>
                </article>

                <article
                    class="hero-motion-surface relative overflow-hidden rounded-[36px] bg-white/90 p-8 text-center shadow-[0_35px_100px_rgba(15,23,42,0.22)] ring-1 ring-white/90 backdrop-blur-2xl dark:bg-slate-900/88 dark:ring-white/10 lg:p-10"
                    :style="heroCardStyle(-1)"
                >
                    <div class="pointer-events-none absolute -left-16 -top-16 h-44 w-44 rounded-full bg-white/70 blur-3xl dark:bg-emerald-400/10"></div>
                    <div class="pointer-events-none absolute inset-0 bg-gradient-to-b from-white/70 via-white/35 to-transparent dark:from-white/5 dark:via-transparent"></div>

                    <div
                        class="hero-motion-surface relative mx-auto flex h-36 w-36 items-center justify-center overflow-hidden rounded-full bg-[#2f6f61] text-4xl font-bold text-white ring-4 ring-white shadow-[0_20px_60px_rgba(15,23,42,0.26)] dark:ring-slate-800"
                        :style="avatarStyle()"
                    >
                        @if ($publicProfile?->avatar)
                            <img
                                src="{{ asset($publicProfile->avatar) }}"
                                alt="{{ $publicProfile->name }}"
                                width="144"
                                height="144"
                                loading="eager"
                                decoding="async"
                                class="h-full w-full object-cover"
                            >
                        @else
                            {{ strtoupper(substr($publicProfile?->name ?? 'A', 0, 1)) }}
                        @endif
                    </div>

                    <h2 class="relative mt-7 text-2xl font-extrabold tracking-[-0.03em] text-slate-950 drop-shadow-sm dark:text-white">
                        {{ $publicProfile?->name ?? 'Alex Rivera' }}
                    </h2>

                    <p class="relative mt-1 text-sm font-bold text-[#2f6f61] dark:text-emerald-300">
                        {{ $publicProfile?->role ?? 'Portfolio Manager' }}
                    </p>

                    <p class="relative mx-auto mt-5 max-w-sm text-sm font-medium leading-relaxed text-slate-700 dark:text-slate-300">
                        {{ $publicProfile?->bio ?? 'Creative portfolio manager focused on refined digital projects and clean design systems.' }}
                    </p>

                    <div class="relative mt-7 rounded-3xl bg-white/88 p-5 shadow-sm backdrop-blur-md dark:bg-slate-800/90">
                        <p class="text-sm font-medium text-gray-500 dark:text-slate-400">
                            Project likes collected
                        </p>

                        <p class="mt-2 text-4xl font-black text-slate-950 dark:text-white">
                            {{ number_format($totalLikes) }}
                        </p>
                    </div>
                </article>
            </div>

            <div class="mt-12 flex justify-center">
                <article
                    class="hero-motion-surface relative w-full max-w-md overflow-hidden rounded-[36px] bg-white/92 p-8 text-center shadow-[0_35px_100px_rgba(15,23,42,0.22)] ring-1 ring-white/90 backdrop-blur-2xl dark:bg-slate-900/88 dark:ring-white/10"
                    :style="heroSummaryStyle()"
                >
                    <div class="pointer-events-none absolute inset-x-10 -top-16 h-28 rounded-full bg-[#7fac9f]/20 blur-3xl"></div>

                    <span class="relative inline-flex rounded-full bg-[#eef5f2] px-4 py-2 text-xs font-bold uppercase tracking-[0.18em] text-[#2f6f61] dark:bg-emerald-950 dark:text-emerald-300">
                        Portfolio Summary
                    </span>

                    <h2 class="relative mt-6 text-7xl font-black tracking-[-0.06em] text-slate-950 dark:text-white">
                        {{ $totalProjects }}
                    </h2>

                    <p class="relative mt-3 text-sm font-semibold text-gray-500 dark:text-slate-400">
                        Published portfolio projects
                    </p>
                </article>
            </div>
        </div>
    </section>

    {{-- ABOUT --}}
    @if ($publicProfile)
        @php
            $age = $publicProfile->birth_date?->age;

            $languages = is_array($publicProfile->languages)
                ? $publicProfile->languages
                : [];

            $currentFocus = is_array($publicProfile->current_focus)
                ? $publicProfile->current_focus
                : [];

            $primaryEducation = $educations->first();

            $aboutFacts = collect([
                [
                    'label' => 'Age',
                    'value' => $age !== null ? $age . ' years old' : null,
                ],
                [
                    'label' => 'Domicile',
                    'value' => $publicProfile->domicile,
                ],
                [
                    'label' => 'Education',
                    'value' => $primaryEducation?->level,
                ],
                [
                    'label' => 'Major',
                    'value' => $primaryEducation?->major,
                ],
                [
                    'label' => 'Institution',
                    'value' => $primaryEducation?->institution,
                ],
                [
                    'label' => 'GPA',
                    'value' => $primaryEducation?->gpa,
                ],
            ])->filter(
                fn (array $fact): bool => filled($fact['value'])
            );
        @endphp

        <section
            id="about"
            class="public-anchor content-auto-section relative z-40 px-6 py-16 sm:px-8 lg:px-14 lg:py-20"
        >
            <div
                class="pointer-events-none absolute left-1/2 top-1/2 h-96 w-[70%] -translate-x-1/2 -translate-y-1/2 rounded-full bg-[#7fac9f]/15 blur-3xl dark:bg-emerald-400/10"
            ></div>

            <div
                class="relative mx-auto max-w-6xl overflow-hidden rounded-[42px] border border-white/70 bg-white/88 p-6 shadow-[0_30px_100px_rgba(15,23,42,0.13)] backdrop-blur-2xl dark:border-white/10 dark:bg-slate-900/88 sm:p-8 lg:p-10"
            >
                <div class="pointer-events-none absolute -right-24 -top-24 h-72 w-72 rounded-full bg-[#7fac9f]/20 blur-3xl dark:bg-emerald-400/10"></div>

                <div class="relative grid gap-10 lg:grid-cols-[300px_1fr] lg:items-start">
                    <aside class="rounded-[30px] bg-[#eef5f2] p-6 text-center shadow-sm dark:bg-slate-800">
                        <div
                            class="mx-auto flex h-40 w-40 items-center justify-center overflow-hidden rounded-full bg-[#2f6f61] text-5xl font-black text-white ring-8 ring-white shadow-[0_20px_60px_rgba(15,23,42,0.20)] dark:ring-slate-900"
                        >
                            @if ($publicProfile->avatar)
                                <img
                                    src="{{ asset($publicProfile->avatar) }}"
                                    alt="{{ $publicProfile->name }}"
                                    width="160"
                                    height="160"
                                    loading="lazy"
                                    decoding="async"
                                    class="h-full w-full object-cover"
                                >
                            @else
                                {{ strtoupper(substr($publicProfile->name, 0, 1)) }}
                            @endif
                        </div>

                        <h2 class="mt-7 text-2xl font-black tracking-[-0.03em] text-slate-950 dark:text-white">
                            {{ $publicProfile->name }}
                        </h2>

                        @if ($publicProfile->role)
                            <p class="mt-2 text-sm font-bold text-[#2f6f61] dark:text-emerald-300">
                                {{ $publicProfile->role }}
                            </p>
                        @endif

                        @if ($publicProfile->professional_status)
                            <div class="mt-6 inline-flex items-center gap-2 rounded-full bg-white px-4 py-2 shadow-sm dark:bg-slate-900">
                                <span class="relative flex h-2.5 w-2.5" aria-hidden="true">
                                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                                </span>

                                <span class="text-xs font-bold text-slate-600 dark:text-slate-300">
                                    {{ $publicProfile->professional_status }}
                                </span>
                            </div>
                        @endif

                        <div class="mt-7 flex flex-wrap justify-center gap-2">
                            @if ($publicProfile->github_url)
                                <a
                                    href="{{ $publicProfile->github_url }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="public-focus-ring rounded-xl border border-[#7fac9f] px-4 py-2 text-xs font-semibold text-[#2f6f61] transition hover:bg-white dark:text-emerald-300 dark:hover:bg-slate-900"
                                >
                                    GitHub
                                </a>
                            @endif

                            @if ($publicProfile->linkedin_url)
                                <a
                                    href="{{ $publicProfile->linkedin_url }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="public-focus-ring rounded-xl border border-[#7fac9f] px-4 py-2 text-xs font-semibold text-[#2f6f61] transition hover:bg-white dark:text-emerald-300 dark:hover:bg-slate-900"
                                >
                                    LinkedIn
                                </a>
                            @endif
                        </div>
                    </aside>

                    <div>
                        <p class="text-sm font-bold uppercase tracking-[0.22em] text-[#2f6f61] dark:text-emerald-300">
                            About Me
                        </p>

                        <h2 class="mt-4 max-w-3xl text-3xl font-black tracking-[-0.04em] text-slate-950 dark:text-white sm:text-4xl">
                            {{ $publicProfile->about_title ?: 'Personal background and professional focus.' }}
                        </h2>

                        @if ($publicProfile->about_description || $publicProfile->bio)
                            <p class="mt-6 max-w-3xl whitespace-pre-line leading-8 text-slate-600 dark:text-slate-300">
                                {{ $publicProfile->about_description ?: $publicProfile->bio }}
                            </p>
                        @endif

                        @if ($aboutFacts->isNotEmpty())
                            <div class="mt-8 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                                @foreach ($aboutFacts as $fact)
                                    <div class="rounded-2xl border border-black/5 bg-[#f5f8f8] p-4 dark:border-white/5 dark:bg-slate-800">
                                        <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-slate-400">
                                            {{ $fact['label'] }}
                                        </p>

                                        <p class="mt-2 text-sm font-bold text-slate-950 dark:text-white">
                                            {{ $fact['value'] }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <div class="mt-7 grid gap-5 border-t border-slate-100 pt-7 dark:border-slate-800 sm:grid-cols-2">
                            @if ($publicProfile->work_preference)
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-[0.14em] text-slate-400">
                                        Work Preference
                                    </p>

                                    <p class="mt-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                                        {{ $publicProfile->work_preference }}
                                    </p>
                                </div>
                            @endif

                            @if ($languages !== [])
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-[0.14em] text-slate-400">
                                        Languages
                                    </p>

                                    <p class="mt-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                                        {{ implode(', ', $languages) }}
                                    </p>
                                </div>
                            @endif
                        </div>

                        <div class="mt-8 flex flex-wrap gap-3">
                            @if ($publicProfile->public_email)
                                <a
                                    href="mailto:{{ $publicProfile->public_email }}"
                                    class="public-focus-ring rounded-xl bg-[#2f6f61] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#265c51]"
                                >
                                    Contact Me
                                </a>
                            @endif

                            @if ($publicProfile->cv_url)
                                <a
                                    href="{{ $publicProfile->cv_url }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="public-focus-ring rounded-xl border border-[#7fac9f] px-5 py-3 text-sm font-semibold text-[#2f6f61] transition hover:bg-[#eef5f2] dark:text-emerald-300 dark:hover:bg-slate-800"
                                >
                                    View CV
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                @if ($currentFocus !== [])
                    <div class="relative mt-10 border-t border-slate-100 pt-8 dark:border-slate-800">
                        <p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-400">
                            Current Focus
                        </p>

                        <div class="mt-4 grid gap-3 md:grid-cols-3">
                            @foreach ($currentFocus as $focus)
                                <div class="flex items-center gap-3 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-100 dark:bg-slate-800 dark:ring-white/5">
                                    <span class="h-2.5 w-2.5 shrink-0 rounded-full bg-[#7fac9f]" aria-hidden="true"></span>

                                    <span class="text-sm font-bold text-slate-700 dark:text-slate-200">
                                        {{ $focus }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($educations->isNotEmpty())
                    <div class="relative mt-10 border-t border-slate-100 pt-8 dark:border-slate-800">
                        <p class="text-sm font-bold uppercase tracking-[0.2em] text-[#2f6f61] dark:text-emerald-300">
                            Education Journey
                        </p>

                        <h3 class="mt-3 text-2xl font-black text-slate-950 dark:text-white">
                            Academic background
                        </h3>

                        <div class="mt-6 space-y-4">
                            @foreach ($educations as $education)
                                <article class="grid gap-5 rounded-2xl border border-slate-100 p-5 dark:border-slate-800 md:grid-cols-[150px_1fr]">
                                    <div>
                                        <p class="text-sm font-bold text-[#2f6f61] dark:text-emerald-300">
                                            {{ $education->start_year ?: '?' }}
                                            —
                                            {{ $education->end_year ?: 'Present' }}
                                        </p>

                                        <p class="mt-2 text-xs font-semibold uppercase tracking-[0.12em] text-slate-400">
                                            {{ $education->level }}
                                        </p>
                                    </div>

                                    <div>
                                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                            <div>
                                                <h4 class="text-lg font-bold text-slate-950 dark:text-white">
                                                    {{ $education->institution }}
                                                </h4>

                                                @if ($education->major)
                                                    <p class="mt-1 text-sm font-semibold text-[#2f6f61] dark:text-emerald-300">
                                                        {{ $education->major }}
                                                    </p>
                                                @endif
                                            </div>

                                            @if ($education->gpa)
                                                <span class="shrink-0 rounded-full bg-[#eef5f2] px-3 py-1 text-xs font-bold text-[#2f6f61] dark:bg-emerald-950 dark:text-emerald-300">
                                                    GPA {{ $education->gpa }}
                                                </span>
                                            @endif
                                        </div>

                                        @if ($education->description)
                                            <p class="mt-4 text-sm leading-7 text-slate-600 dark:text-slate-300">
                                                {{ $education->description }}
                                            </p>
                                        @endif
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </section>
    @endif

    {{-- WORK --}}
    <section
        id="portfolio"
        class="public-anchor content-auto-section relative z-40 -mt-12 px-6 pb-12 pt-16 sm:px-8 lg:px-14"
    >
        <div class="pointer-events-none absolute inset-x-0 top-0 h-40 bg-gradient-to-b from-transparent via-[#f5f8f8]/60 to-[#f5f8f8] dark:via-slate-950/70 dark:to-slate-950"></div>

        <div class="relative mx-auto max-w-6xl rounded-[42px] bg-white/78 p-5 shadow-[0_-24px_80px_rgba(15,23,42,0.10)] ring-1 ring-white/70 backdrop-blur-2xl dark:bg-slate-950/78 dark:ring-white/10 sm:p-7 lg:p-8">
            <div class="pointer-events-none absolute -top-20 left-1/2 h-56 w-[70%] -translate-x-1/2 rounded-full bg-[#7fac9f]/20 blur-3xl dark:bg-emerald-400/10"></div>

            <div class="relative">
                <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-[#2f6f61] dark:text-emerald-300">
                            Curated Selection
                        </p>

                        <h2 class="mt-3 text-3xl font-bold text-slate-950 dark:text-white sm:text-4xl">
                            My Projects
                        </h2>
                    </div>

                    <div class="flex w-full gap-2 md:w-auto">
                        <input
                            x-model.debounce.150ms="projectSearch"
                            type="search"
                            placeholder="Search portfolio..."
                            aria-label="Search portfolio projects"
                            class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:border-[#7fac9f] focus:ring-2 focus:ring-[#7fac9f]/20 dark:border-slate-700 dark:bg-slate-900 dark:text-white md:w-72"
                        >

                        <button
                            type="button"
                            x-cloak
                            x-show="projectSearch !== ''"
                            x-on:click="clearProjectSearch()"
                            class="public-focus-ring rounded-xl border border-gray-200 px-4 py-3 text-sm font-semibold text-slate-600 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
                        >
                            Clear
                        </button>
                    </div>
                </div>

                @if ($featured)
                    <div
                        x-cloak
                        x-show="matchesProject({{ (int) $featured->id }})"
                        x-transition.opacity.duration.150ms
                        class="grid grid-cols-1 gap-6 lg:grid-cols-2"
                    >
                        <div class="overflow-hidden rounded-[32px] bg-white shadow-[0_25px_70px_rgba(15,23,42,0.14)] dark:bg-slate-900">
                            @if ($featured->image)
                                <img
                                    src="{{ asset($featured->image) }}"
                                    alt="{{ $featured->name }}"
                                    width="1280"
                                    height="720"
                                    loading="lazy"
                                    decoding="async"
                                    class="h-72 w-full object-cover sm:h-80 lg:h-[360px]"
                                >
                            @else
                                <div class="flex h-72 w-full items-center justify-center bg-[#eef5f2] text-sm font-semibold text-slate-500 dark:bg-slate-800 dark:text-slate-400 sm:h-80 lg:h-[360px]">
                                    Project image unavailable
                                </div>
                            @endif
                        </div>

                        <article class="rounded-[32px] bg-white p-6 shadow-[0_25px_70px_rgba(15,23,42,0.12)] dark:bg-slate-900 sm:p-8">
                            <span class="rounded-full bg-[#eef5f2] px-3 py-1 text-xs font-semibold text-[#2f6f61] dark:bg-emerald-950 dark:text-emerald-200">
                                Featured Project
                            </span>

                            <h3 class="mt-6 text-2xl font-bold text-slate-950 dark:text-white sm:text-3xl">
                                {{ $featured->name }}
                            </h3>

                            <p class="mt-2 text-sm text-gray-500 dark:text-slate-400">
                                {{ $featured->category }}
                                @if ($featured->client)
                                    · {{ $featured->client }}
                                @endif
                                · {{ $featured->created_at->format('Y') }}
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
                                            class="public-focus-ring rounded-xl bg-[#7fac9f] px-5 py-3 text-center text-sm font-semibold text-white transition hover:bg-[#6d9b8f]"
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
                                            'public-focus-ring rounded-xl border px-5 py-3 text-sm font-semibold transition',
                                            'border-[#7fac9f] text-[#2f6f61] hover:bg-[#eef5f2] dark:text-emerald-300 dark:hover:bg-slate-800' => ! $featuredLiked,
                                            'cursor-not-allowed border-emerald-200 bg-emerald-50 text-emerald-700 opacity-75 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-300' => $featuredLiked,
                                        ])
                                    >
                                        {{ $featuredLiked ? '♥ Liked' : '♡ Like Project' }}
                                    </button>
                                </div>
                            </div>
                        </article>
                    </div>
                @else
                    <div class="rounded-[24px] bg-white p-8 text-gray-600 shadow-sm dark:bg-slate-900 dark:text-slate-300">
                        No published project is available yet.
                    </div>
                @endif
            </div>

            <div id="projects" class="relative mt-12">
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
                        <article
                            wire:key="public-project-{{ $project->id }}"
                            x-cloak
                            x-show="matchesProject({{ (int) $project->id }})"
                            x-transition.opacity.duration.150ms
                            data-public-project-card
                            class="overflow-hidden rounded-[24px] bg-white shadow-[0_20px_60px_rgba(15,23,42,0.10)] transition hover:-translate-y-1 hover:shadow-[0_24px_70px_rgba(15,23,42,0.14)] dark:bg-slate-900"
                        >
                            @if ($project->image)
                                <img
                                    src="{{ asset($project->image) }}"
                                    alt="{{ $project->name }}"
                                    width="960"
                                    height="540"
                                    loading="lazy"
                                    decoding="async"
                                    class="h-56 w-full object-cover"
                                >
                            @else
                                <div class="flex h-56 w-full items-center justify-center bg-[#eef5f2] text-sm font-semibold text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                                    Project image unavailable
                                </div>
                            @endif

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
                                            'public-focus-ring text-left text-sm font-semibold transition',
                                            'text-[#2f6f61] hover:underline dark:text-emerald-300' => ! $projectLiked,
                                            'cursor-not-allowed text-emerald-700 opacity-75 dark:text-emerald-300' => $projectLiked,
                                        ])
                                    >
                                        {{ $projectLiked ? '♥' : '♡' }}
                                        {{ number_format($project->likes) }} likes
                                    </button>

                                    <div class="flex items-center gap-3">
                                        @if ($project->website_url)
                                            <a
                                                href="{{ $project->website_url }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="public-focus-ring rounded-md text-sm font-semibold text-[#2f6f61] hover:underline dark:text-emerald-300"
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
                        </article>
                    @empty
                        <div
                            x-show="projectSearch.trim() === ''"
                            class="rounded-[24px] bg-white p-8 text-center text-gray-500 shadow-sm dark:bg-slate-900 dark:text-slate-300 md:col-span-2 xl:col-span-3"
                        >
                            No additional project is available yet.
                        </div>
                    @endforelse
                </div>

                <div
                    x-cloak
                    x-show="projectSearch.trim() !== '' && !hasProjectResults"
                    class="mt-6 rounded-[24px] border border-dashed border-gray-300 p-8 text-center text-gray-500 dark:border-slate-700 dark:text-slate-400"
                >
                    No projects match your search.
                </div>
            </div>

            {{-- CERTIFICATES --}}
            <div
                id="certificates"
                class="public-anchor content-auto-section relative mt-12"
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
                        <article
                            wire:key="public-certificate-{{ $certificate->id }}"
                            class="overflow-hidden rounded-[28px] bg-white shadow-[0_20px_60px_rgba(15,23,42,0.12)] dark:bg-slate-900"
                        >
                            <div class="bg-[#eef5f2] p-4 dark:bg-slate-800">
                                <div
                                    class="certificate-preview overflow-hidden rounded-2xl bg-white shadow-inner dark:bg-slate-950"
                                    wire:ignore
                                >
                                    <canvas
                                        class="certificate-canvas block aspect-[4/3] w-full"
                                        width="720"
                                        height="540"
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
                                        class="public-focus-ring rounded-xl border border-[#7fac9f] px-4 py-2 text-sm font-semibold text-[#2f6f61] hover:bg-[#eef5f2] dark:text-emerald-300 dark:hover:bg-slate-800"
                                    >
                                        Open PDF
                                    </a>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-[28px] border border-dashed border-gray-300 p-8 text-center text-gray-500 dark:border-slate-700 dark:text-slate-400 md:col-span-2 xl:col-span-3">
                            No certificate is currently published.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- CONTACT --}}
            <div
                id="contact"
                class="public-anchor content-auto-section relative mt-12"
            >
                <div class="rounded-[32px] bg-[#eef5f2] p-8 text-center shadow-[0_20px_60px_rgba(15,23,42,0.10)] dark:bg-slate-900 lg:p-10">
                    <h2 class="text-3xl font-bold text-slate-950 dark:text-white">
                        Interested in collaborating?
                    </h2>

                    <p class="mx-auto mt-4 max-w-2xl text-gray-600 dark:text-slate-300">
                        Connect with me through professional and social platforms below.
                    </p>

                    @if ($publicProfile?->public_email)
                        <a
                            href="mailto:{{ $publicProfile->public_email }}"
                            class="public-focus-ring mt-7 inline-flex rounded-xl bg-[#2f6f61] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#265c51]"
                        >
                            Send Email
                        </a>
                    @endif

                    <div class="mt-8 flex flex-wrap justify-center gap-4">
                        <a
                            href="{{ $githubUrl }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            aria-label="Open GitHub profile"
                            class="public-focus-ring flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-slate-950 shadow-sm transition hover:-translate-y-1 hover:shadow-md dark:bg-slate-800 dark:text-white"
                        >
                            <svg class="h-7 w-7" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M12 .5C5.65.5.5 5.65.5 12c0 5.08 3.29 9.39 7.86 10.92.58.1.79-.25.79-.56v-2.15c-3.2.7-3.87-1.37-3.87-1.37-.52-1.33-1.28-1.69-1.28-1.69-1.04-.71.08-.7.08-.7 1.15.08 1.76 1.19 1.76 1.19 1.03 1.75 2.7 1.25 3.36.95.1-.74.4-1.25.73-1.54-2.55-.29-5.23-1.28-5.23-5.68 0-1.25.45-2.28 1.18-3.08-.12-.29-.51-1.46.11-3.04 0 0 .96-.31 3.16 1.18A10.9 10.9 0 0 1 12 6.05c.98 0 1.96.13 2.88.38 2.19-1.49 3.15-1.18 3.15-1.18.63 1.58.24 2.75.12 3.04.74.8 1.18 1.83 1.18 3.08 0 4.41-2.69 5.38-5.25 5.67.41.36.78 1.06.78 2.14v3.18c0 .31.21.67.8.56A11.51 11.51 0 0 0 23.5 12C23.5 5.65 18.35.5 12 .5Z"/>
                            </svg>
                        </a>

                        <a
                            href="{{ $linkedinUrl }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            aria-label="Open LinkedIn profile"
                            class="public-focus-ring flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-[#0A66C2] shadow-sm transition hover:-translate-y-1 hover:shadow-md dark:bg-slate-800"
                        >
                            <svg class="h-7 w-7" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M4.98 3.5C4.98 4.88 3.87 6 2.5 6S.02 4.88.02 3.5 1.13 1 2.5 1s2.48 1.12 2.48 2.5ZM.31 8.01h4.38V23H.31V8.01ZM8.09 8.01h4.2v2.05h.06c.58-1.1 2.01-2.26 4.14-2.26 4.43 0 5.25 2.92 5.25 6.71V23h-4.38v-7.53c0-1.8-.03-4.1-2.5-4.1-2.5 0-2.88 1.95-2.88 3.97V23H8.09V8.01Z"/>
                            </svg>
                        </a>

                        <a
                            href="https://www.instagram.com/wfaiq._/"
                            target="_blank"
                            rel="noopener noreferrer"
                            aria-label="Open Instagram profile"
                            class="public-focus-ring flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-pink-600 shadow-sm transition hover:-translate-y-1 hover:shadow-md dark:bg-slate-800"
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

    @if ($certificates->isNotEmpty())
        @push('scripts')
            <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>

            <script>
                (() => {
                    const certificatePreviewState = {
                        observer: null,
                    };

                    function showCertificateFallback(canvas) {
                        const wrapper = canvas.closest('.certificate-preview');

                        if (!wrapper) {
                            return;
                        }

                        wrapper.innerHTML = `
                            <div class="flex min-h-64 items-center justify-center p-6 text-center text-sm text-gray-500 dark:text-slate-400">
                                PDF preview is unavailable. Use Open PDF to view the document.
                            </div>
                        `;
                    }

                    async function renderCertificate(canvas) {
                        if (canvas.dataset.rendered === 'true') {
                            return;
                        }

                        const pdfUrl = canvas.dataset.pdfUrl;

                        if (!pdfUrl || !window.pdfjsLib) {
                            return;
                        }

                        canvas.dataset.rendered = 'true';

                        try {
                            const pdf = await window.pdfjsLib
                                .getDocument(pdfUrl)
                                .promise;

                            const page = await pdf.getPage(1);
                            const container = canvas.closest('.certificate-preview');
                            const containerWidth = Math.max(
                                container?.clientWidth ?? 360,
                                240
                            );

                            const initialViewport = page.getViewport({
                                scale: 1,
                            });

                            const scale = containerWidth / initialViewport.width;
                            const viewport = page.getViewport({ scale });
                            const context = canvas.getContext('2d');

                            if (!context) {
                                throw new Error('Canvas context unavailable');
                            }

                            canvas.width = Math.ceil(viewport.width);
                            canvas.height = Math.ceil(viewport.height);

                            await page.render({
                                canvasContext: context,
                                viewport,
                            }).promise;
                        } catch (error) {
                            canvas.dataset.rendered = 'failed';
                            showCertificateFallback(canvas);
                        }
                    }

                    function initializeCertificatePreviews() {
                        if (!window.pdfjsLib) {
                            return;
                        }

                        window.pdfjsLib.GlobalWorkerOptions.workerSrc =
                            'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

                        certificatePreviewState.observer?.disconnect();

                        const canvases = document.querySelectorAll(
                            '.certificate-canvas'
                        );

                        if (!('IntersectionObserver' in window)) {
                            canvases.forEach(renderCertificate);
                            return;
                        }

                        certificatePreviewState.observer =
                            new IntersectionObserver(
                                (entries, observer) => {
                                    entries.forEach((entry) => {
                                        if (!entry.isIntersecting) {
                                            return;
                                        }

                                        observer.unobserve(entry.target);
                                        renderCertificate(entry.target);
                                    });
                                },
                                {
                                    rootMargin: '250px 0px',
                                    threshold: 0.01,
                                }
                            );

                        canvases.forEach((canvas) => {
                            if (canvas.dataset.rendered === 'true') {
                                return;
                            }

                            certificatePreviewState.observer.observe(canvas);
                        });
                    }

                    document.addEventListener(
                        'DOMContentLoaded',
                        initializeCertificatePreviews
                    );

                    document.addEventListener(
                        'livewire:navigated',
                        initializeCertificatePreviews
                    );

                    document.addEventListener(
                        'livewire:updated',
                        initializeCertificatePreviews
                    );
                })();
            </script>
        @endpush
    @endif

    <button
        type="button"
        x-cloak
        x-show="scrollY > 700"
        x-transition.opacity.duration.150ms
        x-on:click="scrollToTop()"
        aria-label="Back to top"
        class="public-focus-ring fixed bottom-6 right-6 z-[90] flex h-12 w-12 items-center justify-center rounded-full bg-[#7fac9f] text-white shadow-[0_12px_30px_rgba(15,23,42,0.18)] transition hover:-translate-y-1 hover:bg-[#6c9a8e] dark:bg-emerald-700 dark:hover:bg-emerald-600"
    >
        <span aria-hidden="true">↑</span>
    </button>
</div>
