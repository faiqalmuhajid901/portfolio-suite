@php
    $name = $profile?->name ?: 'Muhammad Faiq';
    $role = $profile?->role ?: 'Software Engineer';
    $heroTitle = $profile?->hero_title ?: 'I build dependable digital products from idea to production.';
    $heroDescription = $profile?->hero_description ?: ($profile?->bio ?: 'I design and develop web applications with a focus on clear product thinking, maintainable engineering, and measurable outcomes.');
    $aboutTitle = $profile?->about_title ?: 'Engineering with product context, not code in isolation.';
    $aboutDescription = $profile?->about_description ?: ($profile?->bio ?: 'My work connects user needs, technical constraints, delivery quality, and long-term maintainability.');
@endphp

<div>
    <section class="relative overflow-hidden border-b border-slate-200">
        <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_top_right,rgba(16,185,129,0.14),transparent_38%),radial-gradient(circle_at_bottom_left,rgba(15,23,42,0.08),transparent_35%)]"></div>
        <div class="mx-auto grid max-w-7xl gap-12 px-5 py-20 sm:px-8 sm:py-28 lg:grid-cols-[1.25fr_.75fr] lg:items-center lg:px-10 lg:py-32">
            <div>
                <div class="mb-7 inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2 text-xs font-black uppercase tracking-[0.18em] text-emerald-800">
                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                    {{ $profile?->hero_badge ?: ($profile?->professional_status ?: 'Available for meaningful work') }}
                </div>
                <p class="text-sm font-black uppercase tracking-[0.24em] text-slate-500">{{ $role }}</p>
                <h1 class="mt-5 max-w-5xl text-5xl font-black leading-[1.02] tracking-[-0.045em] text-slate-950 sm:text-6xl lg:text-7xl">
                    {{ $heroTitle }}
                </h1>
                <p class="mt-7 max-w-3xl text-lg leading-8 text-slate-600 sm:text-xl">
                    {{ $heroDescription }}
                </p>
                <div class="mt-10 flex flex-wrap gap-4">
                    <a href="#work" class="rounded-full bg-slate-950 px-7 py-3.5 text-sm font-bold text-white transition hover:-translate-y-0.5 hover:bg-emerald-700">View selected work</a>
                    <a href="#contact" class="rounded-full border border-slate-300 bg-white px-7 py-3.5 text-sm font-bold text-slate-950 transition hover:border-slate-950">Discuss a project</a>
                    @if ($profile?->cv_url)
                        <a href="{{ $profile->cv_url }}" target="_blank" rel="noopener noreferrer" class="px-3 py-3.5 text-sm font-bold text-slate-600 hover:text-slate-950">View résumé ↗</a>
                    @endif
                </div>
            </div>

            <aside class="rounded-[2rem] border border-slate-200 bg-white/90 p-7 shadow-[0_30px_80px_rgba(15,23,42,.08)] sm:p-9">
                <div class="flex items-center gap-5">
                    @if ($profile?->avatar)
                        <img src="{{ $profile->avatar }}" alt="Portrait of {{ $name }}" class="h-20 w-20 rounded-3xl object-cover" loading="eager">
                    @else
                        <div class="grid h-20 w-20 place-items-center rounded-3xl bg-slate-950 text-2xl font-black text-white">MF</div>
                    @endif
                    <div>
                        <p class="text-xl font-black">{{ $name }}</p>
                        <p class="mt-1 text-sm text-slate-500">{{ $profile?->domicile ?: 'Indonesia' }}</p>
                        <p class="mt-1 text-sm font-semibold text-emerald-700">{{ $profile?->work_preference ?: 'Open to remote and collaborative work' }}</p>
                    </div>
                </div>

                <dl class="mt-8 grid grid-cols-2 gap-4">
                    <div class="rounded-2xl bg-slate-100 p-5">
                        <dt class="text-xs font-bold uppercase tracking-wider text-slate-500">Published work</dt>
                        <dd class="mt-2 text-3xl font-black">{{ $stats['projects'] }}</dd>
                    </div>
                    <div class="rounded-2xl bg-slate-100 p-5">
                        <dt class="text-xs font-bold uppercase tracking-wider text-slate-500">Case studies</dt>
                        <dd class="mt-2 text-3xl font-black">{{ $stats['caseStudies'] }}</dd>
                    </div>
                </dl>

                @if (filled($profile?->current_focus))
                    <div class="mt-7">
                        <p class="text-xs font-black uppercase tracking-[0.18em] text-slate-500">Current focus</p>
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach ($profile->current_focus as $focus)
                                <span class="rounded-full border border-slate-200 px-3 py-1.5 text-xs font-semibold">{{ $focus }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </aside>
        </div>
    </section>

    <section id="about" class="scroll-mt-24 border-b border-slate-200 bg-white">
        <div class="mx-auto grid max-w-7xl gap-12 px-5 py-20 sm:px-8 lg:grid-cols-[.75fr_1.25fr] lg:px-10 lg:py-28">
            <div>
                <p class="text-sm font-black uppercase tracking-[0.22em] text-emerald-700">About</p>
                <h2 class="mt-4 text-4xl font-black tracking-[-0.035em] sm:text-5xl">{{ $aboutTitle }}</h2>
            </div>
            <div>
                <p class="text-lg leading-8 text-slate-600">{{ $aboutDescription }}</p>

                @if ($profile?->educations?->isNotEmpty())
                    <div class="mt-10 grid gap-4 sm:grid-cols-2">
                        @foreach ($profile->educations as $education)
                            <article class="rounded-2xl border border-slate-200 p-5">
                                <p class="text-xs font-black uppercase tracking-wider text-slate-500">{{ $education->level }}</p>
                                <h3 class="mt-2 font-black">{{ $education->institution }}</h3>
                                <p class="mt-1 text-sm text-slate-600">{{ $education->major }}</p>
                                <p class="mt-3 text-xs font-semibold text-slate-500">{{ $education->start_year }} — {{ $education->end_year ?: 'Present' }}</p>
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </section>

    <section id="work" class="scroll-mt-24">
        <div class="mx-auto max-w-7xl px-5 py-20 sm:px-8 lg:px-10 lg:py-28">
            <div class="max-w-3xl">
                <p class="text-sm font-black uppercase tracking-[0.22em] text-emerald-700">Selected work</p>
                <h2 class="mt-4 text-4xl font-black tracking-[-0.035em] sm:text-5xl">Projects explained through decisions and outcomes.</h2>
                <p class="mt-5 text-lg leading-8 text-slate-600">The emphasis is no longer popularity metrics. Each selected project shows the role, problem, solution, stack, and result.</p>
            </div>

            <div class="mt-12 grid gap-7 lg:grid-cols-2">
                @forelse ($projects as $project)
                    <article class="group overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
                        <div class="aspect-[16/10] overflow-hidden bg-slate-100">
                            <img src="{{ $project->image }}" alt="Preview of {{ $project->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.025]" loading="lazy">
                        </div>
                        <div class="p-7 sm:p-8">
                            <div class="flex flex-wrap items-center gap-2 text-xs font-black uppercase tracking-wider text-slate-500">
                                <span>{{ $project->category ?: 'Digital product' }}</span>
                                @if ($project->is_featured)
                                    <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-emerald-800">Featured</span>
                                @endif
                            </div>
                            <h3 class="mt-4 text-2xl font-black tracking-tight">{{ $project->name }}</h3>
                            @if ($project->role)
                                <p class="mt-2 text-sm font-bold text-emerald-700">Role: {{ $project->role }}</p>
                            @endif
                            <p class="mt-4 leading-7 text-slate-600">{{ $project->summary ?: $project->description }}</p>

                            @if (filled($project->tags))
                                <div class="mt-5 flex flex-wrap gap-2">
                                    @foreach (array_slice($project->tags, 0, 6) as $tag)
                                        <span class="rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-700">{{ $tag }}</span>
                                    @endforeach
                                </div>
                            @endif

                            <div class="mt-7 flex flex-wrap items-center gap-5 text-sm font-black">
                                @if ($project->case_study_published)
                                    <a class="text-slate-950 hover:text-emerald-700" href="{{ route('projects.show', $project->slug) }}">Read case study →</a>
                                @endif
                                @if ($project->website_url)
                                    <a class="text-slate-500 hover:text-slate-950" href="{{ $project->website_url }}" target="_blank" rel="noopener noreferrer">Live product ↗</a>
                                @endif
                                @if ($project->source_code_url)
                                    <a class="text-slate-500 hover:text-slate-950" href="{{ $project->source_code_url }}" target="_blank" rel="noopener noreferrer">Source code ↗</a>
                                @endif
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-[2rem] border border-dashed border-slate-300 bg-white p-10 text-slate-600 lg:col-span-2">
                        No public projects are available yet. Publish completed projects from the case-study editor.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <section id="experience" class="scroll-mt-24 border-y border-slate-200 bg-slate-950 text-white">
        <div class="mx-auto grid max-w-7xl gap-12 px-5 py-20 sm:px-8 lg:grid-cols-[.7fr_1.3fr] lg:px-10 lg:py-28">
            <div>
                <p class="text-sm font-black uppercase tracking-[0.22em] text-emerald-400">Experience</p>
                <h2 class="mt-4 text-4xl font-black tracking-[-0.035em] sm:text-5xl">A timeline of responsibility, not a list of job titles.</h2>
            </div>
            <div class="space-y-6">
                @forelse ($careers as $career)
                    <article class="relative rounded-[1.75rem] border border-white/15 bg-white/5 p-7 sm:p-8">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <p class="text-xs font-black uppercase tracking-wider text-emerald-400">{{ $career->display_period }}</p>
                                <h3 class="mt-3 text-2xl font-black">{{ $career->title }}</h3>
                                <p class="mt-1 text-slate-300">{{ $career->company }}@if($career->employment_type) · {{ $career->employment_type }}@endif</p>
                            </div>
                            @if ($career->location)
                                <span class="text-sm font-semibold text-slate-400">{{ $career->location }}</span>
                            @endif
                        </div>
                        <p class="mt-5 leading-7 text-slate-300">{{ $career->description }}</p>

                        @if (filled($career->achievements))
                            <ul class="mt-5 space-y-2 text-sm leading-6 text-slate-300">
                                @foreach ($career->achievements as $achievement)
                                    <li class="flex gap-3"><span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-emerald-400"></span><span>{{ $achievement }}</span></li>
                                @endforeach
                            </ul>
                        @endif

                        @if (filled($career->technologies))
                            <div class="mt-6 flex flex-wrap gap-2">
                                @foreach ($career->technologies as $technology)
                                    <span class="rounded-full border border-white/15 px-3 py-1.5 text-xs font-semibold text-slate-300">{{ $technology }}</span>
                                @endforeach
                            </div>
                        @endif
                    </article>
                @empty
                    <p class="rounded-3xl border border-white/15 bg-white/5 p-8 text-slate-300">The professional timeline is being prepared.</p>
                @endforelse
            </div>
        </div>
    </section>

    <section id="certificates" class="scroll-mt-24 border-b border-slate-200 bg-white">
        <div class="mx-auto max-w-7xl px-5 py-20 sm:px-8 lg:px-10 lg:py-28">
            <div class="max-w-3xl">
                <p class="text-sm font-black uppercase tracking-[0.22em] text-emerald-700">Certificates</p>
                <h2 class="mt-4 text-4xl font-black tracking-[-0.035em] sm:text-5xl">Evidence of structured learning.</h2>
            </div>

            <div class="mt-12 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                @forelse ($certificates as $certificate)
                    @php
                        $certificateUrl = null;
                        if ($certificate->pdf_path) {
                            $certificateUrl = \Illuminate\Support\Str::startsWith($certificate->pdf_path, ['http://', 'https://'])
                                ? $certificate->pdf_path
                                : \Illuminate\Support\Facades\Storage::url($certificate->pdf_path);
                        }
                    @endphp
                    <article class="rounded-3xl border border-slate-200 p-6">
                        <p class="text-xs font-black uppercase tracking-wider text-slate-500">{{ $certificate->issued_at?->format('Y') ?: 'Credential' }}</p>
                        <h3 class="mt-3 text-lg font-black">{{ $certificate->title }}</h3>
                        <p class="mt-2 text-sm font-semibold text-emerald-700">{{ $certificate->issuer }}</p>
                        @if ($certificate->description)
                            <p class="mt-4 text-sm leading-6 text-slate-600">{{ $certificate->description }}</p>
                        @endif
                        @if ($certificateUrl)
                            <a class="mt-6 inline-block text-sm font-black hover:text-emerald-700" href="{{ $certificateUrl }}" target="_blank" rel="noopener noreferrer">View credential ↗</a>
                        @endif
                    </article>
                @empty
                    <p class="rounded-3xl border border-dashed border-slate-300 p-8 text-slate-600 md:col-span-2 lg:col-span-3">No public certificates are available.</p>
                @endforelse
            </div>
        </div>
    </section>

    <section id="contact" class="scroll-mt-24">
        <div class="mx-auto grid max-w-7xl gap-12 px-5 py-20 sm:px-8 lg:grid-cols-[.75fr_1.25fr] lg:px-10 lg:py-28">
            <div>
                <p class="text-sm font-black uppercase tracking-[0.22em] text-emerald-700">Contact</p>
                <h2 class="mt-4 text-4xl font-black tracking-[-0.035em] sm:text-5xl">Bring a concrete problem. I will respond with concrete questions.</h2>
                <p class="mt-5 text-lg leading-8 text-slate-600">Use the form for project work, collaboration, employment opportunities, or technical discussions.</p>
                <div class="mt-8 flex flex-wrap gap-5 text-sm font-black">
                    @if ($profile?->github_url)
                        <a href="{{ $profile->github_url }}" target="_blank" rel="noopener noreferrer" class="hover:text-emerald-700">GitHub ↗</a>
                    @endif
                    @if ($profile?->linkedin_url)
                        <a href="{{ $profile->linkedin_url }}" target="_blank" rel="noopener noreferrer" class="hover:text-emerald-700">LinkedIn ↗</a>
                    @endif
                    @if ($profile?->public_email)
                        <a href="mailto:{{ $profile->public_email }}" class="hover:text-emerald-700">{{ $profile->public_email }}</a>
                    @endif
                </div>
            </div>

            <livewire:landing.contact-form />
        </div>
    </section>
</div>
