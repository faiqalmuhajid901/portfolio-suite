<div class="min-h-screen bg-slate-100 px-4 py-8 text-slate-950 sm:px-8">
    <div class="mx-auto max-w-7xl">
        <x-phase-three-admin-nav />

        <div class="rounded-[2rem] bg-slate-950 p-8 text-white sm:p-10">
            <p class="text-sm font-black uppercase tracking-[0.2em] text-emerald-400">Phase 3</p>
            <h1 class="mt-4 text-4xl font-black tracking-tight">Professional content control center</h1>
            <p class="mt-4 max-w-3xl leading-7 text-slate-300">Use this sequence: complete the basic project record, write its case study, publish experience, then process incoming contact messages.</p>
        </div>

        <div class="mt-7 grid gap-5 md:grid-cols-2 xl:grid-cols-4">
            @foreach ([
                ['route' => 'projects', 'step' => '01', 'title' => 'Basic Projects', 'text' => 'Maintain title, category, client, dates, image, live URL, description, and stack tags.'],
                ['route' => 'project-case-studies.index', 'step' => '02', 'title' => 'Case Studies', 'text' => 'Add role, problem, solution, outcome, source code, publishing, featured status, and ordering.'],
                ['route' => 'careers.index', 'step' => '03', 'title' => 'Career Timeline', 'text' => 'Publish roles, responsibilities, achievements, technologies, and chronological ordering.'],
                ['route' => 'messages.index', 'step' => '04', 'title' => 'Contact Inbox', 'text' => 'Review, reply to, archive, or delete messages submitted from the public portfolio.'],
            ] as $card)
                <a href="{{ route($card['route']) }}" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-emerald-500">
                    <p class="text-xs font-black uppercase tracking-wider text-emerald-700">Step {{ $card['step'] }}</p>
                    <h2 class="mt-3 text-xl font-black">{{ $card['title'] }}</h2>
                    <p class="mt-3 text-sm leading-6 text-slate-600">{{ $card['text'] }}</p>
                </a>
            @endforeach
        </div>
    </div>
</div>
