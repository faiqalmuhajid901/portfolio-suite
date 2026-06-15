<div class="rounded-[28px] bg-white p-8 shadow-sm">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold">Quick Bio Update</h2>
            <p class="mt-1 text-sm text-gray-500">
                Update your short professional introduction.
            </p>
        </div>

        <span class="text-sm text-gray-500">
            {{ strlen($bio) }} characters
        </span>
    </div>

    @if (session('bio_success'))
        <div class="mt-5 rounded-2xl bg-[#eef5f2] px-5 py-4 text-sm font-medium text-[#2f6f61]">
            {{ session('bio_success') }}
        </div>
    @endif

    <textarea
        wire:model.live="bio"
        rows="5"
        maxlength="280"
        class="mt-6 w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm outline-none focus:border-[#7fac9f]"
    ></textarea>

    <div class="mt-5 flex justify-end">
        <button
            wire:click="save"
            class="rounded-xl bg-[#7fac9f] px-5 py-3 text-sm font-semibold text-white"
        >
            Update Bio
        </button>
    </div>
</div>
