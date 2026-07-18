<div class="rounded-[2rem] border border-slate-200 bg-white p-7 shadow-sm sm:p-9">
    @if ($sent)
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 text-emerald-900" role="status">
            <p class="font-black">Message received.</p>
            <p class="mt-1 text-sm leading-6">Your message has been stored securely. I will review the context before responding.</p>
        </div>
    @endif

    <form wire:submit="submit" class="space-y-5 {{ $sent ? 'mt-6' : '' }}" novalidate>
        <div class="grid gap-5 sm:grid-cols-2">
            <label class="block">
                <span class="text-sm font-bold">Name</span>
                <input wire:model.blur="name" type="text" autocomplete="name" class="mt-2 w-full rounded-2xl border-slate-300 bg-slate-50 px-4 py-3 focus:border-emerald-600 focus:ring-emerald-600">
                @error('name') <span class="mt-2 block text-sm font-semibold text-red-600">{{ $message }}</span> @enderror
            </label>
            <label class="block">
                <span class="text-sm font-bold">Email</span>
                <input wire:model.blur="email" type="email" autocomplete="email" class="mt-2 w-full rounded-2xl border-slate-300 bg-slate-50 px-4 py-3 focus:border-emerald-600 focus:ring-emerald-600">
                @error('email') <span class="mt-2 block text-sm font-semibold text-red-600">{{ $message }}</span> @enderror
            </label>
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <label class="block">
                <span class="text-sm font-bold">Company <span class="font-normal text-slate-400">(optional)</span></span>
                <input wire:model.blur="company" type="text" autocomplete="organization" class="mt-2 w-full rounded-2xl border-slate-300 bg-slate-50 px-4 py-3 focus:border-emerald-600 focus:ring-emerald-600">
                @error('company') <span class="mt-2 block text-sm font-semibold text-red-600">{{ $message }}</span> @enderror
            </label>
            <label class="block">
                <span class="text-sm font-bold">Subject</span>
                <input wire:model.blur="subject" type="text" class="mt-2 w-full rounded-2xl border-slate-300 bg-slate-50 px-4 py-3 focus:border-emerald-600 focus:ring-emerald-600">
                @error('subject') <span class="mt-2 block text-sm font-semibold text-red-600">{{ $message }}</span> @enderror
            </label>
        </div>

        <label class="block">
            <span class="text-sm font-bold">Message</span>
            <textarea wire:model.blur="message" rows="7" class="mt-2 w-full rounded-2xl border-slate-300 bg-slate-50 px-4 py-3 focus:border-emerald-600 focus:ring-emerald-600" placeholder="What are you building, what problem needs to be solved, and what outcome do you expect?"></textarea>
            @error('message') <span class="mt-2 block text-sm font-semibold text-red-600">{{ $message }}</span> @enderror
        </label>

        <div class="absolute -left-[10000px] top-auto h-px w-px overflow-hidden" aria-hidden="true">
            <label>Website <input wire:model="website" type="text" tabindex="-1" autocomplete="off"></label>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-xs leading-5 text-slate-500">Protected by a honeypot and rate limiting. Raw IP addresses are not stored.</p>
            <button type="submit" wire:loading.attr="disabled" wire:target="submit" class="rounded-full bg-slate-950 px-7 py-3.5 text-sm font-black text-white transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-60">
                <span wire:loading.remove wire:target="submit">Send message</span>
                <span wire:loading wire:target="submit">Sending…</span>
            </button>
        </div>
    </form>
</div>
