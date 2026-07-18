<div class="text-slate-950 dark:text-slate-100">
    <div class="mx-auto max-w-7xl">
        @if (session('success'))
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 font-semibold text-emerald-900">{{ session('success') }}</div>
        @endif

        <div class="grid gap-7 xl:grid-cols-[.8fr_1.2fr]">
            <section class="rounded-[2rem] border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900 p-6 shadow-sm">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.18em] text-emerald-700">Project selection</p>
                    <h1 class="mt-2 text-3xl font-black">Case-study editor</h1>
                    <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-300">Basic project data remains managed in the existing Projects module. Select one project here to add professional narrative.</p>
                </div>

                <input wire:model.live.debounce.300ms="search" type="search" placeholder="Search project, category, or client…" class="mt-6 w-full rounded-2xl border-slate-300 bg-slate-50 px-4 py-3 focus:border-emerald-600 focus:ring-emerald-600">

                <div class="mt-5 space-y-3">
                    @forelse ($projects as $project)
                        <button type="button" wire:click="edit({{ $project->id }})" class="w-full rounded-2xl border p-4 text-left transition {{ $projectId === $project->id ? 'border-emerald-500 bg-emerald-50' : 'border-slate-200 hover:border-slate-400' }}">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <h2 class="font-black">{{ $project->name }}</h2>
                                    <p class="mt-1 text-xs font-semibold text-slate-500 dark:text-slate-400">{{ $project->category ?: 'No category' }} · {{ str_replace('_', ' ', $project->status) }}</p>
                                </div>
                                <div class="flex flex-col items-end gap-1 text-[11px] font-black uppercase tracking-wider">
                                    <span class="{{ $project->is_published ? 'text-emerald-700' : 'text-slate-400' }}">{{ $project->is_published ? 'Public' : 'Private' }}</span>
                                    <span class="{{ $project->case_study_published ? 'text-blue-700' : 'text-slate-400' }}">{{ $project->case_study_published ? 'Case live' : 'No case' }}</span>
                                </div>
                            </div>
                        </button>
                    @empty
                        <p class="rounded-2xl border border-dashed border-slate-300 p-6 text-sm text-slate-600 dark:text-slate-300">No projects found. Create a project first.</p>
                    @endforelse
                </div>

                <div class="mt-5">{{ $projects->links() }}</div>
            </section>

            <section class="rounded-[2rem] border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900 p-6 shadow-sm sm:p-8">
                @if ($projectId)
                    <div class="flex flex-col gap-3 border-b border-slate-200 pb-6 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <p class="text-xs font-black uppercase tracking-[0.18em] text-emerald-700">Editing</p>
                            <h2 class="mt-2 text-3xl font-black">{{ $projectName }}</h2>
                        </div>
                        <button type="button" wire:click="clearSelection" class="text-sm font-bold text-slate-500 dark:text-slate-400 hover:text-slate-950">Close editor</button>
                    </div>

                    <form wire:submit="save" class="mt-7 space-y-6">
                        <div class="grid gap-5 md:grid-cols-2">
                            <label class="block">
                                <span class="text-sm font-bold">Public slug</span>
                                <input wire:model="slug" type="text" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-950 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
                                @error('slug') <span class="mt-2 block text-sm font-semibold text-red-600">{{ $message }}</span> @enderror
                            </label>
                            <label class="block">
                                <span class="text-sm font-bold">Your role</span>
                                <input wire:model="role" type="text" placeholder="Lead Developer, Full-stack Engineer…" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-950 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
                                @error('role') <span class="mt-2 block text-sm font-semibold text-red-600">{{ $message }}</span> @enderror
                            </label>
                        </div>

                        <label class="block">
                            <span class="text-sm font-bold">Executive summary</span>
                            <textarea wire:model="summary" rows="3" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-950 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100" placeholder="One concise paragraph explaining the product and its value."></textarea>
                            @error('summary') <span class="mt-2 block text-sm font-semibold text-red-600">{{ $message }}</span> @enderror
                        </label>

                        <label class="block">
                            <span class="text-sm font-bold">Problem</span>
                            <textarea wire:model="problem" rows="6" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-950 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100" placeholder="What was broken, inefficient, risky, or missing? Who was affected?"></textarea>
                            @error('problem') <span class="mt-2 block text-sm font-semibold text-red-600">{{ $message }}</span> @enderror
                        </label>

                        <label class="block">
                            <span class="text-sm font-bold">Solution</span>
                            <textarea wire:model="solution" rows="7" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-950 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100" placeholder="Explain architecture, product decisions, constraints, implementation, and verification."></textarea>
                            @error('solution') <span class="mt-2 block text-sm font-semibold text-red-600">{{ $message }}</span> @enderror
                        </label>

                        <label class="block">
                            <span class="text-sm font-bold">Outcome</span>
                            <textarea wire:model="outcome" rows="6" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-950 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100" placeholder="State verified results, measurable improvements, adoption, or lessons. Do not fabricate metrics."></textarea>
                            @error('outcome') <span class="mt-2 block text-sm font-semibold text-red-600">{{ $message }}</span> @enderror
                        </label>

                        <div class="grid gap-5 md:grid-cols-[1fr_.35fr]">
                            <label class="block">
                                <span class="text-sm font-bold">Source-code URL</span>
                                <input wire:model="sourceCodeUrl" type="text" placeholder="github.com/…" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-950 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
                                @error('sourceCodeUrl') <span class="mt-2 block text-sm font-semibold text-red-600">{{ $message }}</span> @enderror
                            </label>
                            <label class="block">
                                <span class="text-sm font-bold">Sort order</span>
                                <input wire:model="sortOrder" type="number" min="0" max="999" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-950 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
                                @error('sortOrder') <span class="mt-2 block text-sm font-semibold text-red-600">{{ $message }}</span> @enderror
                            </label>
                        </div>

                        <div class="grid gap-3 rounded-2xl bg-slate-100 p-5 sm:grid-cols-3">
                            <label class="flex items-start gap-3">
                                <input wire:model="isPublished" type="checkbox" class="mt-1 rounded border-slate-300 text-emerald-700 focus:ring-emerald-600">
                                <span><strong class="block text-sm">Public project</strong><small class="text-slate-500 dark:text-slate-400">Show on homepage</small></span>
                            </label>
                            <label class="flex items-start gap-3">
                                <input wire:model="isFeatured" type="checkbox" class="mt-1 rounded border-slate-300 text-emerald-700 focus:ring-emerald-600">
                                <span><strong class="block text-sm">Featured</strong><small class="text-slate-500 dark:text-slate-400">Prioritize ordering</small></span>
                            </label>
                            <label class="flex items-start gap-3">
                                <input wire:model="caseStudyPublished" type="checkbox" class="mt-1 rounded border-slate-300 text-emerald-700 focus:ring-emerald-600">
                                <span><strong class="block text-sm">Publish case study</strong><small class="text-slate-500 dark:text-slate-400">Enable detail URL</small></span>
                            </label>
                        </div>
                        @error('caseStudyPublished') <span class="block text-sm font-semibold text-red-600">{{ $message }}</span> @enderror

                        <div class="flex flex-wrap items-center justify-between gap-4 border-t border-slate-200 pt-6">
                            <p class="text-xs leading-5 text-slate-500 dark:text-slate-400">Publication requires completed status plus role, summary, problem, solution, and outcome.</p>
                            <button type="submit" wire:loading.attr="disabled" class="rounded-full bg-slate-950 px-7 py-3 text-sm font-black text-white hover:bg-emerald-700 disabled:opacity-60">Save professional content</button>
                        </div>
                    </form>
                @else
                    <div class="grid min-h-[520px] place-items-center text-center">
                        <div class="max-w-md">
                            <p class="text-sm font-black uppercase tracking-[0.18em] text-emerald-700">No project selected</p>
                            <h2 class="mt-3 text-3xl font-black">Choose a project from the left panel.</h2>
                            <p class="mt-4 leading-7 text-slate-600 dark:text-slate-300">Do not publish an empty case study. The public page is intentionally blocked until the substantive fields are complete.</p>
                        </div>
                    </div>
                @endif
            </section>
        </div>
    </div>
</div>
