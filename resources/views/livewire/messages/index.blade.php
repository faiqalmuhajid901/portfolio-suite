<div class="text-slate-950 dark:text-slate-100">
    <div class="mx-auto max-w-7xl">
        @if (session('success'))
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 font-semibold text-emerald-900">{{ session('success') }}</div>
        @endif

        <header class="rounded-[2rem] bg-slate-950 p-7 text-white sm:p-9">
            <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.18em] text-emerald-400">Contact inbox</p>
                    <h1 class="mt-3 text-4xl font-black">{{ $newCount }} new message{{ $newCount === 1 ? '' : 's' }}</h1>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300">Messages are stored in the database. Reply through your email client, then mark the record as read or archived.</p>
                </div>
            </div>
        </header>

        <div class="mt-7 grid gap-4 rounded-3xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900 p-5 sm:grid-cols-[1fr_220px]">
            <input wire:model.live.debounce.300ms="search" type="search" placeholder="Search name, email, company, subject, or message…" class="rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-950 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
            <select wire:model.live="status" class="rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-950 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
                <option value="all">All statuses</option>
                <option value="new">New</option>
                <option value="read">Read</option>
                <option value="archived">Archived</option>
            </select>
        </div>

        <div class="mt-6 space-y-4">
            @forelse ($messages as $message)
                <article class="rounded-[1.75rem] border bg-white p-6 shadow-sm dark:bg-slate-900 {{ $message->status === 'new' ? 'border-emerald-400' : 'border-slate-200 dark:border-slate-800' }}">
                    <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-3">
                                <span class="rounded-full px-3 py-1 text-[11px] font-black uppercase tracking-wider {{ $message->status === 'new' ? 'bg-emerald-100 text-emerald-800' : ($message->status === 'archived' ? 'bg-slate-200 text-slate-600 dark:text-slate-300' : 'bg-blue-100 text-blue-800') }}">{{ $message->status }}</span>
                                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">{{ $message->created_at->format('d M Y, H:i') }}</span>
                            </div>
                            <h2 class="mt-4 text-xl font-black">{{ $message->subject }}</h2>
                            <p class="mt-2 text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $message->name }} · <a class="text-emerald-700 hover:underline" href="mailto:{{ $message->email }}?subject={{ rawurlencode('Re: '.$message->subject) }}">{{ $message->email }}</a>@if($message->company) · {{ $message->company }}@endif</p>
                            <p class="mt-5 whitespace-pre-line leading-7 text-slate-600 dark:text-slate-300">{{ $message->message }}</p>
                        </div>
                        <div class="flex shrink-0 flex-wrap gap-3 text-sm font-black">
                            <a href="mailto:{{ $message->email }}?subject={{ rawurlencode('Re: '.$message->subject) }}" class="rounded-full bg-slate-950 px-5 py-2.5 text-white hover:bg-emerald-700">Reply</a>
                            @if ($message->status === 'new')
                                <button type="button" wire:click="markRead({{ $message->id }})" class="rounded-full border border-slate-300 px-5 py-2.5">Mark read</button>
                            @endif
                            @if ($message->status !== 'archived')
                                <button type="button" wire:click="archive({{ $message->id }})" class="rounded-full border border-slate-300 px-5 py-2.5">Archive</button>
                            @endif
                            <button type="button" wire:click="delete({{ $message->id }})" wire:confirm="Delete this message permanently?" class="px-2 py-2.5 text-red-600">Delete</button>
                        </div>
                    </div>
                </article>
            @empty
                <p class="rounded-3xl border border-dashed border-slate-300 bg-white dark:border-slate-700 dark:bg-slate-900 p-10 text-center text-slate-600 dark:text-slate-300">No messages match the current filters.</p>
            @endforelse
        </div>

        <div class="mt-6">{{ $messages->links() }}</div>
    </div>
</div>
