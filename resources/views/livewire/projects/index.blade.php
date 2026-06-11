<div class="space-y-6">
    @if (session('success'))
        <div class="rounded-2xl bg-[#eef5f2] px-5 py-4 text-sm font-medium text-[#2f6f61] dark:bg-emerald-950 dark:text-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="rounded-[28px] bg-white shadow-[0_20px_60px_rgba(15,23,42,0.06)] dark:bg-slate-900 dark:shadow-none">
        <div class="flex flex-col gap-4 p-6 sm:p-8 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-950 dark:text-white">
                    Project Manager
                </h1>

                <p class="mt-1 text-gray-500 dark:text-slate-400">
                    Review and organize your active design projects.
                </p>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row">
                <input
                    wire:model.live.debounce.400ms="search"
                    type="text"
                    placeholder="Search project..."
                    class="rounded-xl border border-gray-200 px-4 py-2 text-sm outline-none focus:border-[#7fac9f] dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                >

                <select
                    wire:model.live="status"
                    class="rounded-xl border border-gray-200 px-4 py-2 text-sm outline-none focus:border-[#7fac9f] dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                >
                    <option value="all">All Status</option>
                    <option value="in_progress">In Progress</option>
                    <option value="review">Review</option>
                    <option value="completed">Completed</option>
                </select>

                <button
                    wire:click="exportCsv"
                    class="rounded-xl border border-[#7fac9f] px-4 py-2 text-sm font-semibold text-[#2f6f61] dark:text-emerald-300"
                >
                    Export CSV
                </button>

                <button
                    wire:click="openCreateModal"
                    class="rounded-xl bg-[#7fac9f] px-4 py-2 text-sm font-semibold text-white"
                >
                    + New Project
                </button>
            </div>
        </div>

        <div class="hidden overflow-x-auto md:block">
            <table class="w-full text-left">
                <thead class="bg-[#f2f6f6] text-sm uppercase tracking-wider text-gray-500 dark:bg-slate-800 dark:text-slate-300">
                    <tr>
                        <th class="px-8 py-4">Project Name</th>
                        <th class="px-8 py-4">Client</th>
                        <th class="px-8 py-4">Status</th>
                        <th class="px-8 py-4">Timeline</th>
                        <th class="px-8 py-4">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($projects as $project)
                        <tr class="border-t border-gray-100 dark:border-slate-800">
                            <td class="px-8 py-5">
                                <div class="font-semibold text-slate-950 dark:text-white">
                                    {{ $project->name }}
                                </div>

                                <div class="text-sm text-gray-500 dark:text-slate-400">
                                    {{ $project->category }}
                                </div>
                            </td>

                            <td class="px-8 py-5 text-slate-700 dark:text-slate-200">
                                {{ $project->client }}
                            </td>

                            <td class="px-8 py-5">
                                <select
                                    wire:change="updateStatus({{ $project->id }}, $event.target.value)"
                                    class="min-w-[150px] rounded-full border border-[#7fac9f]/50 bg-[#eef5f2] px-4 py-2 text-sm font-medium text-[#2f6f61] outline-none focus:border-[#2f6f61] dark:border-emerald-800 dark:bg-emerald-950 dark:text-emerald-200"
                                >
                                    <option value="in_progress" @selected($project->status === 'in_progress')>
                                        In Progress
                                    </option>

                                    <option value="review" @selected($project->status === 'review')>
                                        Review
                                    </option>

                                    <option value="completed" @selected($project->status === 'completed')>
                                        Completed
                                    </option>
                                </select>
                            </td>

                            <td class="px-8 py-5 text-sm text-gray-500 dark:text-slate-400">
                                {{ optional($project->start_date)->format('M d') }}
                                —
                                {{ optional($project->end_date)->format('M d') }}
                            </td>

                            <td class="px-8 py-5">
                                <div class="flex flex-wrap items-center gap-3">
                                    @if ($project->website_url)
                                        <a
                                            href="{{ $project->website_url }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="rounded-lg px-3 py-1 text-sm font-semibold text-[#2f6f61] hover:bg-[#eef5f2] dark:text-emerald-300 dark:hover:bg-slate-800"
                                        >
                                            Website
                                        </a>
                                    @endif

                                    <button
                                        wire:click="openEditModal({{ $project->id }})"
                                        class="rounded-lg px-3 py-1 text-sm font-semibold text-blue-600 hover:bg-blue-50 dark:text-blue-300 dark:hover:bg-blue-950/40"
                                    >
                                        Edit
                                    </button>

                                    <button
                                        wire:click="deleteProject({{ $project->id }})"
                                        wire:confirm="Hapus project ini?"
                                        class="rounded-lg px-3 py-1 text-sm font-semibold text-red-500 hover:bg-red-50 dark:hover:bg-red-950/40"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-10 text-center text-gray-500 dark:text-slate-400">
                                No projects found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="space-y-4 p-6 md:hidden">
            @forelse ($projects as $project)
                <div class="rounded-2xl border border-gray-100 p-5 dark:border-slate-800">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="font-semibold text-slate-950 dark:text-white">
                                {{ $project->name }}
                            </h3>

                            <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">
                                {{ $project->client }}
                            </p>
                        </div>

                        <button
                            wire:click="deleteProject({{ $project->id }})"
                            wire:confirm="Hapus project ini?"
                            class="text-sm text-red-500"
                        >
                            Delete
                        </button>
                    </div>

                    <p class="mt-3 text-sm text-gray-500 dark:text-slate-400">
                        {{ $project->category }}
                    </p>

                    <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                        <div class="relative inline-block min-w-[150px]">
                            <select
                                wire:change="updateStatus({{ $project->id }}, $event.target.value)"
                                class="w-full appearance-none rounded-full border border-[#7fac9f]/50 bg-[#eef5f2] px-4 py-2 pr-10 text-xs font-medium text-[#2f6f61] outline-none dark:border-emerald-800 dark:bg-emerald-950 dark:text-emerald-200"
                            >
                                <option value="in_progress" @selected($project->status === 'in_progress')>
                                    In Progress
                                </option>

                                <option value="review" @selected($project->status === 'review')>
                                    Review
                                </option>

                                <option value="completed" @selected($project->status === 'completed')>
                                    Completed
                                </option>
                            </select>

                            <svg
                                class="pointer-events-none absolute right-4 top-1/2 h-4 w-4 -translate-y-1/2 text-[#2f6f61] dark:text-emerald-300"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>

                        <div class="flex items-center gap-3">
                            @if ($project->website_url)
                                <a
                                    href="{{ $project->website_url }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="text-sm font-semibold text-[#2f6f61] dark:text-emerald-300"
                                >
                                    Website
                                </a>
                            @endif

                            <button
                                wire:click="openEditModal({{ $project->id }})"
                                class="text-sm font-semibold text-blue-600 dark:text-blue-300"
                            >
                                Edit
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="rounded-2xl border border-gray-100 p-5 text-center text-gray-500 dark:border-slate-800 dark:text-slate-400">
                    No projects found.
                </div>
            @endforelse
        </div>

        <div class="border-t border-gray-100 p-6 dark:border-slate-800">
            {{ $projects->links() }}
        </div>
    </div>

    @if ($showCreateModal)
        <div class="fixed inset-0 z-[80] flex items-center justify-center bg-black/40 p-4">
            <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-[28px] bg-white p-6 shadow-2xl dark:bg-slate-900">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-950 dark:text-white">
                            {{ $isEditing ? 'Edit Project' : 'Create New Project' }}
                        </h2>

                        <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">
                            {{ $isEditing ? 'Update existing portfolio project entry.' : 'Add a new portfolio project entry.' }}
                        </p>
                    </div>

                    <button
                        wire:click="closeCreateModal"
                        class="rounded-xl bg-gray-100 px-3 py-2 dark:bg-slate-800 dark:text-white"
                    >
                        ✕
                    </button>
                </div>

                <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-200">
                            Project Name
                        </label>

                        <input
                            wire:model="name"
                            type="text"
                            class="mt-2 w-full rounded-xl border border-gray-200 px-4 py-2 outline-none dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                        >

                        @error('name')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-200">
                            Category
                        </label>

                        <input
                            wire:model="category"
                            type="text"
                            class="mt-2 w-full rounded-xl border border-gray-200 px-4 py-2 outline-none dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                        >
                    </div>

                    <div>
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-200">
                            Client
                        </label>

                        <input
                            wire:model="client"
                            type="text"
                            class="mt-2 w-full rounded-xl border border-gray-200 px-4 py-2 outline-none dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                        >
                    </div>

                    <div>
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-200">
                            Status
                        </label>

                        <div class="relative mt-2">
                            <select
                                wire:model="projectStatus"
                                class="w-full appearance-none rounded-xl border border-gray-200 px-4 py-2 pr-10 outline-none focus:border-[#7fac9f] dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                            >
                                <option value="in_progress">In Progress</option>
                                <option value="review">Review</option>
                                <option value="completed">Completed</option>
                            </select>

                            <svg
                                class="pointer-events-none absolute right-4 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-500 dark:text-slate-400"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-200">
                            Start Date
                        </label>

                        <input
                            wire:model="startDate"
                            type="date"
                            class="mt-2 w-full rounded-xl border border-gray-200 px-4 py-2 outline-none dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                        >
                    </div>

                    <div>
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-200">
                            End Date
                        </label>

                        <input
                            wire:model="endDate"
                            type="date"
                            class="mt-2 w-full rounded-xl border border-gray-200 px-4 py-2 outline-none dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                        >
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-200">
                            Website URL
                        </label>

                        <input
                            wire:model="websiteUrl"
                            type="url"
                            placeholder="https://example.com"
                            class="mt-2 w-full rounded-xl border border-gray-200 px-4 py-2 outline-none dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                        >

                        <p class="mt-1 text-xs text-gray-500 dark:text-slate-400">
                            Jika gambar tidak diupload, sistem otomatis membuat preview gambar landing page dari link website ini.
                        </p>

                        @error('websiteUrl')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-200">
                            Upload Project Image
                            <span class="text-xs font-normal text-gray-500 dark:text-slate-400">
                                (Opsional)
                            </span>
                        </label>

                        <div class="mt-2 rounded-2xl border border-dashed border-gray-300 p-5 dark:border-slate-700">
                            <input
                                wire:model="imageUpload"
                                type="file"
                                accept="image/*"
                                class="w-full text-sm text-slate-700 file:mr-4 file:rounded-xl file:border-0 file:bg-[#7fac9f] file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white dark:text-slate-200"
                            >

                            <p class="mt-3 text-xs text-gray-500 dark:text-slate-400">
                                Opsional. Jika kosong, sistem memakai screenshot otomatis dari Website URL. Jika upload gambar, gambar ini yang akan diprioritaskan dan disimpan sebagai .webp.
                            </p>

                            <div wire:loading.delay.longer wire:target="imageUpload" class="mt-3 text-sm text-[#2f6f61] dark:text-emerald-300">
                                Uploading image preview...
                            </div>

                            @if ($imageUpload)
                                <div class="mt-4 overflow-hidden rounded-2xl border border-gray-100 dark:border-slate-800">
                                    <img
                                        src="{{ $imageUpload->temporaryUrl() }}"
                                        class="h-48 w-full object-cover"
                                        alt="Preview image"
                                    >
                                </div>
                            @elseif ($websiteUrl)
                                <div class="mt-4 rounded-2xl bg-[#eef5f2] p-4 dark:bg-slate-800">
                                    <p class="text-xs font-medium text-[#2f6f61] dark:text-emerald-300">
                                        Preview akan dibuat otomatis dari Website URL setelah project disimpan.
                                    </p>
                                </div>
                            @endif

                            @error('imageUpload')
                                <p class="mt-2 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-200">
                            Tags
                        </label>

                        <input
                            wire:model="tagsInput"
                            type="text"
                            placeholder="UI, Dashboard, Branding"
                            class="mt-2 w-full rounded-xl border border-gray-200 px-4 py-2 outline-none dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                        >
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-200">
                            Description
                        </label>

                        <textarea
                            wire:model="description"
                            rows="4"
                            class="mt-2 w-full rounded-xl border border-gray-200 px-4 py-2 outline-none dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                        ></textarea>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button
                        wire:click="closeCreateModal"
                        class="rounded-xl border border-gray-200 px-5 py-3 text-sm font-semibold dark:border-slate-700 dark:text-white"
                    >
                        Cancel
                    </button>

                    <button
                        wire:click="saveProject"
                        wire:loading.attr="disabled"
                        wire:target="saveProject"
                        class="rounded-xl bg-[#7fac9f] px-5 py-3 text-sm font-semibold text-white disabled:opacity-60"
                    >
                        <span wire:loading.remove wire:target="saveProject">
                            {{ $isEditing ? 'Update Project' : 'Save Project' }}
                        </span>

                        <span wire:loading wire:target="saveProject">
                            Saving...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>