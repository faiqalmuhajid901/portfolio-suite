<nav class="mb-8 overflow-x-auto rounded-2xl border border-slate-200 bg-white p-2 shadow-sm" aria-label="Professional content navigation">
    <div class="flex min-w-max gap-1">
        @foreach ([
            ['route' => 'content.index', 'label' => 'Content Hub'],
            ['route' => 'projects', 'label' => 'Projects'],
            ['route' => 'project-case-studies.index', 'label' => 'Case Studies'],
            ['route' => 'careers.index', 'label' => 'Career Timeline'],
            ['route' => 'messages.index', 'label' => 'Contact Inbox'],
            ['route' => 'profile.show', 'label' => 'Profile'],
            ['route' => 'home', 'label' => 'Public Site ↗'],
        ] as $item)
            <a href="{{ route($item['route']) }}"
               @if($item['route'] === 'home') target="_blank" rel="noopener noreferrer" @endif
               class="rounded-xl px-4 py-2.5 text-sm font-bold transition {{ request()->routeIs($item['route']) ? 'bg-slate-950 text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950' }}">
                {{ $item['label'] }}
            </a>
        @endforeach
    </div>
</nav>
