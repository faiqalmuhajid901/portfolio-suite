<article>
    <header class="border-b border-slate-200">
        <div class="mx-auto max-w-5xl px-5 py-16 sm:px-8 sm:py-24 lg:px-10">
            <a href="{{ route('home') }}#work" class="text-sm font-black text-emerald-700 hover:text-emerald-900">← Back to selected work</a>
            <div class="mt-8 flex flex-wrap items-center gap-3 text-xs font-black uppercase tracking-wider text-slate-500">
                <span>{{ $project->category ?: 'Case study' }}</span>
                @if ($project->client)<span>· {{ $project->client }}</span>@endif
                @if ($project->end_date)<span>· {{ $project->end_date->format('Y') }}</span>@endif
            </div>
            <h1 class="mt-5 text-5xl font-black leading-[1.03] tracking-[-0.045em] sm:text-6xl">{{ $project->name }}</h1>
            <p class="mt-7 max-w-3xl text-xl leading-8 text-slate-600">{{ $project->summary ?: $project->description }}</p>

            <div class="mt-9 flex flex-wrap gap-4">
                @if ($project->website_url)
                    <a href="{{ $project->website_url }}" target="_blank" rel="noopener noreferrer" class="rounded-full bg-slate-950 px-6 py-3 text-sm font-black text-white hover:bg-emerald-700">Open live product ↗</a>
                @endif
                @if ($project->source_code_url)
                    <a href="{{ $project->source_code_url }}" target="_blank" rel="noopener noreferrer" class="rounded-full border border-slate-300 bg-white px-6 py-3 text-sm font-black hover:border-slate-950">View source code ↗</a>
                @endif
            </div>
        </div>
    </header>

    @if ($project->image)
        <div class="mx-auto max-w-7xl px-5 py-10 sm:px-8 lg:px-10">
            <img src="{{ $project->image }}" alt="{{ $project->name }} project preview" class="w-full rounded-[2rem] border border-slate-200 bg-white object-cover shadow-sm">
        </div>
    @endif

    <div class="mx-auto grid max-w-5xl gap-10 px-5 py-12 sm:px-8 lg:grid-cols-[.35fr_.65fr] lg:px-10 lg:py-20">
        <aside>
            <div class="sticky top-28 rounded-3xl border border-slate-200 bg-white p-6">
                <dl class="space-y-5">
                    <div>
                        <dt class="text-xs font-black uppercase tracking-wider text-slate-500">Role</dt>
                        <dd class="mt-2 font-black">{{ $project->role ?: 'Not specified' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-black uppercase tracking-wider text-slate-500">Duration</dt>
                        <dd class="mt-2 font-semibold text-slate-700">
                            {{ $project->start_date?->format('M Y') ?: '—' }} — {{ $project->end_date?->format('M Y') ?: 'Present' }}
                        </dd>
                    </div>
                    @if (filled($project->tags))
                        <div>
                            <dt class="text-xs font-black uppercase tracking-wider text-slate-500">Stack</dt>
                            <dd class="mt-3 flex flex-wrap gap-2">
                                @foreach ($project->tags as $tag)
                                    <span class="rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold">{{ $tag }}</span>
                                @endforeach
                            </dd>
                        </div>
                    @endif
                </dl>
            </div>
        </aside>

        <div class="space-y-12">
            <section>
                <p class="text-sm font-black uppercase tracking-[0.2em] text-emerald-700">01 · Problem</p>
                <div class="mt-4 text-lg leading-8 text-slate-700">{!! nl2br(e($project->problem)) !!}</div>
            </section>
            <section>
                <p class="text-sm font-black uppercase tracking-[0.2em] text-emerald-700">02 · Solution</p>
                <div class="mt-4 text-lg leading-8 text-slate-700">{!! nl2br(e($project->solution)) !!}</div>
            </section>
            <section class="rounded-[2rem] bg-slate-950 p-7 text-white sm:p-10">
                <p class="text-sm font-black uppercase tracking-[0.2em] text-emerald-400">03 · Outcome</p>
                <div class="mt-4 text-lg leading-8 text-slate-200">{!! nl2br(e($project->outcome)) !!}</div>
            </section>
        </div>
    </div>

    @if ($relatedProjects->isNotEmpty())
        <section class="border-t border-slate-200 bg-white">
            <div class="mx-auto max-w-7xl px-5 py-16 sm:px-8 lg:px-10">
                <h2 class="text-3xl font-black tracking-tight">More case studies</h2>
                <div class="mt-8 grid gap-5 md:grid-cols-3">
                    @foreach ($relatedProjects as $related)
                        <a href="{{ route('projects.show', $related->slug) }}" class="rounded-3xl border border-slate-200 p-6 transition hover:-translate-y-1 hover:border-emerald-500">
                            <p class="text-xs font-black uppercase tracking-wider text-slate-500">{{ $related->category }}</p>
                            <h3 class="mt-3 text-xl font-black">{{ $related->name }}</h3>
                            <p class="mt-3 text-sm leading-6 text-slate-600">{{ \Illuminate\Support\Str::limit($related->summary ?: $related->description, 120) }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</article>
