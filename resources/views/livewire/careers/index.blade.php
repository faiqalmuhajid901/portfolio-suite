<div class="min-h-screen bg-slate-100 px-4 py-8 text-slate-950 sm:px-8">
    <div class="mx-auto max-w-7xl">
        <x-phase-three-admin-nav />

        @if (session('success'))
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 font-semibold text-emerald-900">{{ session('success') }}</div>
        @endif

        <div class="grid gap-7 xl:grid-cols-[.9fr_1.1fr]">
            <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                <p class="text-xs font-black uppercase tracking-[0.18em] text-emerald-700">Career editor</p>
                <h1 class="mt-2 text-3xl font-black">{{ $careerId ? 'Edit experience' : 'Add experience' }}</h1>
                <p class="mt-2 text-sm leading-6 text-slate-600">Write responsibilities and verified achievements. Empty corporate language does not strengthen a portfolio.</p>

                <form wire:submit="save" class="mt-7 space-y-5">
                    <div class="grid gap-5 sm:grid-cols-2">
                        <label class="block">
                            <span class="text-sm font-bold">Role / title</span>
                            <input wire:model="title" type="text" class="mt-2 w-full rounded-2xl border-slate-300 px-4 py-3">
                            @error('title') <span class="mt-2 block text-sm font-semibold text-red-600">{{ $message }}</span> @enderror
                        </label>
                        <label class="block">
                            <span class="text-sm font-bold">Company / organization</span>
                            <input wire:model="company" type="text" class="mt-2 w-full rounded-2xl border-slate-300 px-4 py-3">
                            @error('company') <span class="mt-2 block text-sm font-semibold text-red-600">{{ $message }}</span> @enderror
                        </label>
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <label class="block">
                            <span class="text-sm font-bold">Employment type</span>
                            <input wire:model="employmentType" type="text" placeholder="Full-time, Contract, Internship…" class="mt-2 w-full rounded-2xl border-slate-300 px-4 py-3">
                        </label>
                        <label class="block">
                            <span class="text-sm font-bold">Location</span>
                            <input wire:model="location" type="text" placeholder="Jakarta / Remote" class="mt-2 w-full rounded-2xl border-slate-300 px-4 py-3">
                        </label>
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <label class="block">
                            <span class="text-sm font-bold">Start date</span>
                            <input wire:model="startDate" type="date" class="mt-2 w-full rounded-2xl border-slate-300 px-4 py-3">
                            @error('startDate') <span class="mt-2 block text-sm font-semibold text-red-600">{{ $message }}</span> @enderror
                        </label>
                        <label class="block">
                            <span class="text-sm font-bold">End date</span>
                            <input wire:model="endDate" type="date" @disabled($isCurrent) class="mt-2 w-full rounded-2xl border-slate-300 px-4 py-3 disabled:bg-slate-100">
                            @error('endDate') <span class="mt-2 block text-sm font-semibold text-red-600">{{ $message }}</span> @enderror
                        </label>
                    </div>

                    <label class="flex items-center gap-3 rounded-2xl bg-slate-100 p-4">
                        <input wire:model.live="isCurrent" type="checkbox" class="rounded border-slate-300 text-emerald-700 focus:ring-emerald-600">
                        <span class="text-sm font-bold">I currently hold this role</span>
                    </label>

                    <label class="block">
                        <span class="text-sm font-bold">Responsibility summary</span>
                        <textarea wire:model="description" rows="5" class="mt-2 w-full rounded-2xl border-slate-300 px-4 py-3" placeholder="Explain the scope of responsibility and the problems you owned."></textarea>
                        @error('description') <span class="mt-2 block text-sm font-semibold text-red-600">{{ $message }}</span> @enderror
                    </label>

                    <label class="block">
                        <span class="text-sm font-bold">Achievements — one item per line</span>
                        <textarea wire:model="achievementsInput" rows="6" class="mt-2 w-full rounded-2xl border-slate-300 px-4 py-3" placeholder="Reduced deployment errors by…&#10;Led delivery of…&#10;Automated…"></textarea>
                    </label>

                    <label class="block">
                        <span class="text-sm font-bold">Technologies — comma separated</span>
                        <input wire:model="technologiesInput" type="text" placeholder="Laravel, Livewire, PostgreSQL, Vercel" class="mt-2 w-full rounded-2xl border-slate-300 px-4 py-3">
                    </label>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="flex items-center gap-3 rounded-2xl bg-slate-100 p-4">
                            <input wire:model="isPublic" type="checkbox" class="rounded border-slate-300 text-emerald-700 focus:ring-emerald-600">
                            <span class="text-sm font-bold">Show publicly</span>
                        </label>
                        <label class="block">
                            <span class="text-sm font-bold">Sort order</span>
                            <input wire:model="sortOrder" type="number" min="0" max="999" class="mt-2 w-full rounded-2xl border-slate-300 px-4 py-3">
                        </label>
                    </div>

                    <div class="flex flex-wrap justify-end gap-3 border-t border-slate-200 pt-6">
                        @if ($careerId)
                            <button type="button" wire:click="resetForm" class="rounded-full border border-slate-300 px-6 py-3 text-sm font-black">Cancel</button>
                        @endif
                        <button type="submit" class="rounded-full bg-slate-950 px-7 py-3 text-sm font-black text-white hover:bg-emerald-700">{{ $careerId ? 'Update experience' : 'Add experience' }}</button>
                    </div>
                </form>
            </section>

            <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                <p class="text-xs font-black uppercase tracking-[0.18em] text-emerald-700">Published sequence</p>
                <h2 class="mt-2 text-3xl font-black">Career timeline</h2>

                <div class="mt-7 space-y-4">
                    @forelse ($careers as $career)
                        <article class="rounded-3xl border border-slate-200 p-6">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="text-xs font-black uppercase tracking-wider text-emerald-700">{{ $career->display_period }}</span>
                                        <span class="rounded-full px-2.5 py-1 text-[11px] font-black uppercase {{ $career->is_public ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-500' }}">{{ $career->is_public ? 'Public' : 'Private' }}</span>
                                    </div>
                                    <h3 class="mt-3 text-xl font-black">{{ $career->title }}</h3>
                                    <p class="mt-1 text-sm font-semibold text-slate-600">{{ $career->company }}@if($career->employment_type) · {{ $career->employment_type }}@endif</p>
                                </div>
                                <div class="flex gap-3 text-sm font-black">
                                    <button type="button" wire:click="edit({{ $career->id }})" class="text-emerald-700 hover:text-emerald-900">Edit</button>
                                    <button type="button" wire:click="delete({{ $career->id }})" wire:confirm="Delete this experience permanently?" class="text-red-600 hover:text-red-800">Delete</button>
                                </div>
                            </div>
                            <p class="mt-4 text-sm leading-6 text-slate-600">{{ $career->description }}</p>
                            @if (filled($career->technologies))
                                <div class="mt-4 flex flex-wrap gap-2">
                                    @foreach ($career->technologies as $technology)
                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold">{{ $technology }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </article>
                    @empty
                        <p class="rounded-3xl border border-dashed border-slate-300 p-8 text-slate-600">No career entries yet.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</div>
