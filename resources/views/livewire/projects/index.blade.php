<div class="space-y-6">
    {{-- NOTIFIKASI GLOBAL --}}
    @if (session()->has('success'))
        <div
            class="rounded-2xl border border-emerald-200 bg-[#eef5f2]
                   px-5 py-4 text-sm font-medium text-[#2f6f61]
                   dark:border-emerald-900 dark:bg-emerald-950
                   dark:text-emerald-200"
        >
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div
            class="rounded-2xl border border-red-200 bg-red-50
                   px-5 py-4 text-sm font-medium text-red-700
                   dark:border-red-900 dark:bg-red-950/40
                   dark:text-red-300"
        >
            {{ session('error') }}
        </div>
    @endif

    {{-- PROJECT MANAGER --}}
    <div
        class="rounded-[28px] bg-white
               shadow-[0_20px_60px_rgba(15,23,42,0.06)]
               dark:bg-slate-900 dark:shadow-none"
    >
        {{-- HEADER --}}
        <div
            class="flex flex-col gap-4 p-6 sm:p-8
                   lg:flex-row lg:items-center lg:justify-between"
        >
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
                    class="rounded-xl border border-gray-200 px-4 py-2
                           text-sm outline-none focus:border-[#7fac9f]
                           dark:border-slate-700 dark:bg-slate-950
                           dark:text-white"
                >

                <select
                    wire:model.live="status"
                    class="rounded-xl border border-gray-200 px-4 py-2
                           text-sm outline-none focus:border-[#7fac9f]
                           dark:border-slate-700 dark:bg-slate-950
                           dark:text-white"
                >
                    <option value="all">All Status</option>
                    <option value="in_progress">In Progress</option>
                    <option value="review">Review</option>
                    <option value="completed">Completed</option>
                </select>

                <button
                    type="button"
                    wire:click="exportCsv"
                    wire:loading.attr="disabled"
                    wire:target="exportCsv"
                    class="rounded-xl border border-[#7fac9f] px-4 py-2
                           text-sm font-semibold text-[#2f6f61]
                           disabled:cursor-not-allowed disabled:opacity-60
                           dark:text-emerald-300"
                >
                    <span wire:loading.remove wire:target="exportCsv">
                        Export CSV
                    </span>

                    <span wire:loading wire:target="exportCsv">
                        Exporting...
                    </span>
                </button>

                <button
                    type="button"
                    wire:click="openCreateModal"
                    class="rounded-xl bg-[#7fac9f] px-4 py-2
                           text-sm font-semibold text-white"
                >
                    + New Project
                </button>
            </div>
        </div>

        {{-- DESKTOP TABLE --}}
        <div class="hidden overflow-x-auto md:block">
            <table class="w-full text-left">
                <thead
                    class="bg-[#f2f6f6] text-sm uppercase tracking-wider
                           text-gray-500 dark:bg-slate-800
                           dark:text-slate-300"
                >
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
                        <tr
                            wire:key="project-row-{{ $project->id }}"
                            class="border-t border-gray-100
                                   dark:border-slate-800"
                        >
                            <td class="px-8 py-5">
                                <div
                                    class="font-semibold text-slate-950
                                           dark:text-white"
                                >
                                    {{ $project->name }}
                                </div>

                                <div
                                    class="text-sm text-gray-500
                                           dark:text-slate-400"
                                >
                                    {{ $project->category ?: '-' }}
                                </div>
                            </td>

                            <td
                                class="px-8 py-5 text-slate-700
                                       dark:text-slate-200"
                            >
                                {{ $project->client ?: '-' }}
                            </td>

                            <td class="px-8 py-5">
                                <select
                                    wire:change="updateStatus(
                                        {{ $project->id }},
                                        $event.target.value
                                    )"
                                    wire:loading.attr="disabled"
                                    wire:target="updateStatus"
                                    class="min-w-[150px] rounded-full
                                           border border-[#7fac9f]/50
                                           bg-[#eef5f2] px-4 py-2
                                           text-sm font-medium
                                           text-[#2f6f61] outline-none
                                           focus:border-[#2f6f61]
                                           disabled:opacity-60
                                           dark:border-emerald-800
                                           dark:bg-emerald-950
                                           dark:text-emerald-200"
                                >
                                    <option
                                        value="in_progress"
                                        @selected(
                                            $project->status === 'in_progress'
                                        )
                                    >
                                        In Progress
                                    </option>

                                    <option
                                        value="review"
                                        @selected(
                                            $project->status === 'review'
                                        )
                                    >
                                        Review
                                    </option>

                                    <option
                                        value="completed"
                                        @selected(
                                            $project->status === 'completed'
                                        )
                                    >
                                        Completed
                                    </option>
                                </select>
                            </td>

                            <td
                                class="px-8 py-5 text-sm text-gray-500
                                       dark:text-slate-400"
                            >
                                @if ($project->start_date)
                                    {{ $project->start_date->format('M d, Y') }}
                                @else
                                    -
                                @endif

                                <span class="mx-1">—</span>

                                @if ($project->end_date)
                                    {{ $project->end_date->format('M d, Y') }}
                                @else
                                    -
                                @endif
                            </td>

                            <td class="px-8 py-5">
                                <div
                                    class="flex flex-wrap items-center gap-3"
                                >
                                    @if ($project->website_url)
                                        <a
                                            href="{{ $project->website_url }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="rounded-lg px-3 py-1
                                                   text-sm font-semibold
                                                   text-[#2f6f61]
                                                   hover:bg-[#eef5f2]
                                                   dark:text-emerald-300
                                                   dark:hover:bg-slate-800"
                                        >
                                            Website
                                        </a>
                                    @endif

                                    <button
                                        type="button"
                                        wire:click="openEditModal(
                                            {{ $project->id }}
                                        )"
                                        class="rounded-lg px-3 py-1
                                               text-sm font-semibold
                                               text-blue-600
                                               hover:bg-blue-50
                                               dark:text-blue-300
                                               dark:hover:bg-blue-950/40"
                                    >
                                        Edit
                                    </button>

                                    <button
                                        type="button"
                                        wire:click="deleteProject(
                                            {{ $project->id }}
                                        )"
                                        wire:confirm="Hapus project ini?"
                                        wire:loading.attr="disabled"
                                        wire:target="deleteProject(
                                            {{ $project->id }}
                                        )"
                                        class="rounded-lg px-3 py-1
                                               text-sm font-semibold
                                               text-red-500
                                               hover:bg-red-50
                                               disabled:cursor-not-allowed
                                               disabled:opacity-60
                                               dark:hover:bg-red-950/40"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td
                                colspan="5"
                                class="px-8 py-10 text-center
                                       text-gray-500 dark:text-slate-400"
                            >
                                No projects found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- MOBILE CARDS --}}
        <div class="space-y-4 p-6 md:hidden">
            @forelse ($projects as $project)
                <div
                    wire:key="project-card-{{ $project->id }}"
                    class="rounded-2xl border border-gray-100 p-5
                           dark:border-slate-800"
                >
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3
                                class="font-semibold text-slate-950
                                       dark:text-white"
                            >
                                {{ $project->name }}
                            </h3>

                            <p
                                class="mt-1 text-sm text-gray-500
                                       dark:text-slate-400"
                            >
                                {{ $project->client ?: '-' }}
                            </p>
                        </div>

                        <button
                            type="button"
                            wire:click="deleteProject(
                                {{ $project->id }}
                            )"
                            wire:confirm="Hapus project ini?"
                            wire:loading.attr="disabled"
                            wire:target="deleteProject(
                                {{ $project->id }}
                            )"
                            class="text-sm text-red-500
                                   disabled:opacity-60"
                        >
                            Delete
                        </button>
                    </div>

                    <p
                        class="mt-3 text-sm text-gray-500
                               dark:text-slate-400"
                    >
                        {{ $project->category ?: '-' }}
                    </p>

                    <div
                        class="mt-4 flex flex-wrap items-center
                               justify-between gap-3"
                    >
                        <div class="relative inline-block min-w-[150px]">
                            <select
                                wire:change="updateStatus(
                                    {{ $project->id }},
                                    $event.target.value
                                )"
                                class="w-full appearance-none rounded-full
                                       border border-[#7fac9f]/50
                                       bg-[#eef5f2] px-4 py-2 pr-10
                                       text-xs font-medium text-[#2f6f61]
                                       outline-none
                                       dark:border-emerald-800
                                       dark:bg-emerald-950
                                       dark:text-emerald-200"
                            >
                                <option
                                    value="in_progress"
                                    @selected(
                                        $project->status === 'in_progress'
                                    )
                                >
                                    In Progress
                                </option>

                                <option
                                    value="review"
                                    @selected(
                                        $project->status === 'review'
                                    )
                                >
                                    Review
                                </option>

                                <option
                                    value="completed"
                                    @selected(
                                        $project->status === 'completed'
                                    )
                                >
                                    Completed
                                </option>
                            </select>

                            <svg
                                class="pointer-events-none absolute right-4
                                       top-1/2 h-4 w-4 -translate-y-1/2
                                       text-[#2f6f61]
                                       dark:text-emerald-300"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 9l-7 7-7-7"
                                />
                            </svg>
                        </div>

                        <div class="flex items-center gap-3">
                            @if ($project->website_url)
                                <a
                                    href="{{ $project->website_url }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="text-sm font-semibold
                                           text-[#2f6f61]
                                           dark:text-emerald-300"
                                >
                                    Website
                                </a>
                            @endif

                            <button
                                type="button"
                                wire:click="openEditModal(
                                    {{ $project->id }}
                                )"
                                class="text-sm font-semibold text-blue-600
                                       dark:text-blue-300"
                            >
                                Edit
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div
                    class="rounded-2xl border border-gray-100 p-5
                           text-center text-gray-500
                           dark:border-slate-800 dark:text-slate-400"
                >
                    No projects found.
                </div>
            @endforelse
        </div>

        {{-- PAGINATION --}}
        <div
            class="border-t border-gray-100 p-6
                   dark:border-slate-800"
        >
            {{ $projects->links() }}
        </div>
    </div>

    {{-- CREATE / EDIT MODAL --}}
    @if ($showCreateModal)
        <div
            class="fixed inset-0 z-[80] flex items-center justify-center
                   bg-black/40 p-4"
            wire:keydown.escape.window="closeCreateModal"
        >
            <div
                class="max-h-[90vh] w-full max-w-2xl overflow-y-auto
                       rounded-[28px] bg-white p-6 shadow-2xl
                       dark:bg-slate-900"
            >
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2
                            class="text-2xl font-bold text-slate-950
                                   dark:text-white"
                        >
                            {{ $isEditing
                                ? 'Edit Project'
                                : 'Create New Project'
                            }}
                        </h2>

                        <p
                            class="mt-1 text-sm text-gray-500
                                   dark:text-slate-400"
                        >
                            {{ $isEditing
                                ? 'Update existing portfolio project entry.'
                                : 'Add a new portfolio project entry.'
                            }}
                        </p>
                    </div>

                    <button
                        type="button"
                        wire:click="closeCreateModal"
                        class="rounded-xl bg-gray-100 px-3 py-2
                               dark:bg-slate-800 dark:text-white"
                    >
                        ✕
                    </button>
                </div>

                {{-- ERROR DI DALAM MODAL --}}
                @if (session()->has('error'))
                    <div
                        class="mt-5 rounded-xl border border-red-200
                               bg-red-50 px-4 py-3 text-sm
                               font-medium text-red-700
                               dark:border-red-900
                               dark:bg-red-950/40
                               dark:text-red-300"
                    >
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div
                        class="mt-5 rounded-xl border border-red-200
                               bg-red-50 px-4 py-3 text-sm text-red-700
                               dark:border-red-900
                               dark:bg-red-950/40
                               dark:text-red-300"
                    >
                        Periksa kembali data yang belum valid.
                    </div>
                @endif

                <form wire:submit="saveProject">
                    <div
                        class="mt-6 grid grid-cols-1 gap-4
                               md:grid-cols-2"
                    >
                        {{-- PROJECT NAME --}}
                        <div>
                            <label
                                for="project-name"
                                class="text-sm font-medium text-slate-700
                                       dark:text-slate-200"
                            >
                                Project Name
                            </label>

                            <input
                                id="project-name"
                                wire:model="name"
                                type="text"
                                maxlength="120"
                                class="mt-2 w-full rounded-xl border px-4
                                       py-2 outline-none
                                       {{ $errors->has('name')
                                           ? 'border-red-400'
                                           : 'border-gray-200'
                                       }}
                                       dark:bg-slate-950 dark:text-white
                                       dark:border-slate-700"
                            >

                            @error('name')
                                <p class="mt-1 text-xs text-red-500">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- CATEGORY --}}
                        <div>
                            <label
                                for="project-category"
                                class="text-sm font-medium text-slate-700
                                       dark:text-slate-200"
                            >
                                Category
                            </label>

                            <input
                                id="project-category"
                                wire:model="category"
                                type="text"
                                maxlength="120"
                                class="mt-2 w-full rounded-xl border px-4
                                       py-2 outline-none
                                       {{ $errors->has('category')
                                           ? 'border-red-400'
                                           : 'border-gray-200'
                                       }}
                                       dark:bg-slate-950 dark:text-white
                                       dark:border-slate-700"
                            >

                            @error('category')
                                <p class="mt-1 text-xs text-red-500">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- CLIENT --}}
                        <div>
                            <label
                                for="project-client"
                                class="text-sm font-medium text-slate-700
                                       dark:text-slate-200"
                            >
                                Client
                            </label>

                            <input
                                id="project-client"
                                wire:model="client"
                                type="text"
                                maxlength="120"
                                class="mt-2 w-full rounded-xl border px-4
                                       py-2 outline-none
                                       {{ $errors->has('client')
                                           ? 'border-red-400'
                                           : 'border-gray-200'
                                       }}
                                       dark:bg-slate-950 dark:text-white
                                       dark:border-slate-700"
                            >

                            @error('client')
                                <p class="mt-1 text-xs text-red-500">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- STATUS --}}
                        <div>
                            <label
                                for="project-status"
                                class="text-sm font-medium text-slate-700
                                       dark:text-slate-200"
                            >
                                Status
                            </label>

                            <div class="relative mt-2">
                                <select
                                    id="project-status"
                                    wire:model="projectStatus"
                                    class="w-full appearance-none
                                           rounded-xl border px-4 py-2 pr-10
                                           outline-none
                                           focus:border-[#7fac9f]
                                           {{ $errors->has('projectStatus')
                                               ? 'border-red-400'
                                               : 'border-gray-200'
                                           }}
                                           dark:bg-slate-950
                                           dark:text-white
                                           dark:border-slate-700"
                                >
                                    <option value="in_progress">
                                        In Progress
                                    </option>

                                    <option value="review">
                                        Review
                                    </option>

                                    <option value="completed">
                                        Completed
                                    </option>
                                </select>

                                <svg
                                    class="pointer-events-none absolute
                                           right-4 top-1/2 h-4 w-4
                                           -translate-y-1/2 text-gray-500
                                           dark:text-slate-400"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 9l-7 7-7-7"
                                    />
                                </svg>
                            </div>

                            @error('projectStatus')
                                <p class="mt-1 text-xs text-red-500">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- START DATE --}}
                        <div>
                            <label
                                for="project-start-date"
                                class="text-sm font-medium text-slate-700
                                       dark:text-slate-200"
                            >
                                Start Date
                            </label>

                            <input
                                id="project-start-date"
                                wire:model="startDate"
                                type="date"
                                class="mt-2 w-full rounded-xl border px-4
                                       py-2 outline-none
                                       {{ $errors->has('startDate')
                                           ? 'border-red-400'
                                           : 'border-gray-200'
                                       }}
                                       dark:bg-slate-950 dark:text-white
                                       dark:border-slate-700"
                            >

                            @error('startDate')
                                <p class="mt-1 text-xs text-red-500">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- END DATE --}}
                        <div>
                            <label
                                for="project-end-date"
                                class="text-sm font-medium text-slate-700
                                       dark:text-slate-200"
                            >
                                End Date
                            </label>

                            <input
                                id="project-end-date"
                                wire:model="endDate"
                                type="date"
                                class="mt-2 w-full rounded-xl border px-4
                                       py-2 outline-none
                                       {{ $errors->has('endDate')
                                           ? 'border-red-400'
                                           : 'border-gray-200'
                                       }}
                                       dark:bg-slate-950 dark:text-white
                                       dark:border-slate-700"
                            >

                            @error('endDate')
                                <p class="mt-1 text-xs text-red-500">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- WEBSITE URL --}}
                        <div class="md:col-span-2">
                            <label
                                for="project-website"
                                class="text-sm font-medium text-slate-700
                                       dark:text-slate-200"
                            >
                                Website URL
                            </label>

                            <input
                                id="project-website"
                                wire:model.live.debounce.500ms="websiteUrl"
                                type="text"
                                maxlength="255"
                                placeholder="https://example.com"
                                class="mt-2 w-full rounded-xl border px-4
                                       py-2 outline-none
                                       {{ $errors->has('websiteUrl')
                                           ? 'border-red-400'
                                           : 'border-gray-200'
                                       }}
                                       dark:bg-slate-950 dark:text-white
                                       dark:border-slate-700"
                            >

                            <p
                                class="mt-1 text-xs text-gray-500
                                       dark:text-slate-400"
                            >
                                Jika gambar tidak diupload, sistem akan
                                membuat preview otomatis dari Website URL.
                            </p>

                            @error('websiteUrl')
                                <p class="mt-1 text-xs text-red-500">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- IMAGE UPLOAD --}}
                        <div class="md:col-span-2">
                            <label
                                for="project-image"
                                class="text-sm font-medium text-slate-700
                                       dark:text-slate-200"
                            >
                                Upload Project Image

                                <span
                                    class="text-xs font-normal
                                           text-gray-500
                                           dark:text-slate-400"
                                >
                                    (Opsional)
                                </span>
                            </label>

                            <div
                                class="mt-2 rounded-2xl border
                                       border-dashed p-5
                                       {{ $errors->has('imageUpload')
                                           ? 'border-red-400 bg-red-50/40'
                                           : 'border-gray-300'
                                       }}
                                       dark:border-slate-700"
                            >
                                <input
                                    id="project-image"
                                    wire:model="imageUpload"
                                    type="file"
                                    accept=".jpg,.jpeg,.png,.gif,.webp,.bmp"
                                    class="w-full text-sm text-slate-700
                                           file:mr-4 file:rounded-xl
                                           file:border-0
                                           file:bg-[#7fac9f]
                                           file:px-4 file:py-2
                                           file:text-sm
                                           file:font-semibold
                                           file:text-white
                                           dark:text-slate-200"
                                >

                                <p
                                    class="mt-3 text-xs text-gray-500
                                           dark:text-slate-400"
                                >
                                    Format yang diterima: JPG, JPEG, PNG,
                                    GIF, WEBP, atau BMP. Ukuran maksimal
                                    4 MB. File akan disimpan sebagai WebP.
                                </p>

                                <div
                                    wire:loading
                                    wire:target="imageUpload"
                                    class="mt-3 rounded-xl bg-[#eef5f2]
                                           px-4 py-3 text-sm
                                           text-[#2f6f61]
                                           dark:bg-emerald-950
                                           dark:text-emerald-300"
                                >
                                    Mengunggah dan memvalidasi gambar...
                                </div>

                                @if (
                                    $imageUpload
                                    && ! $errors->has('imageUpload')
                                )
                                    <div
                                        wire:loading.remove
                                        wire:target="imageUpload"
                                        class="mt-4 overflow-hidden
                                               rounded-2xl border
                                               border-gray-100
                                               dark:border-slate-800"
                                    >
                                        <img
                                            src="{{ $imageUpload->temporaryUrl() }}"
                                            class="h-48 w-full object-cover"
                                            alt="Preview project image"
                                        >
                                    </div>
                                @elseif (
                                    ! $imageUpload
                                    && filled($websiteUrl)
                                )
                                    <div
                                        class="mt-4 rounded-2xl
                                               bg-[#eef5f2] p-4
                                               dark:bg-slate-800"
                                    >
                                        <p
                                            class="text-xs font-medium
                                                   text-[#2f6f61]
                                                   dark:text-emerald-300"
                                        >
                                            Jika tidak ada file yang
                                            diupload, preview akan dibuat
                                            dari Website URL.
                                        </p>
                                    </div>
                                @endif

                                @error('imageUpload')
                                    <p
                                        class="mt-3 rounded-lg bg-red-50
                                               px-3 py-2 text-xs
                                               font-medium text-red-600
                                               dark:bg-red-950/40
                                               dark:text-red-300"
                                    >
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        {{-- TAGS --}}
                        <div class="md:col-span-2">
                            <label
                                for="project-tags"
                                class="text-sm font-medium text-slate-700
                                       dark:text-slate-200"
                            >
                                Tags
                            </label>

                            <input
                                id="project-tags"
                                wire:model="tagsInput"
                                type="text"
                                maxlength="255"
                                placeholder="UI, Dashboard, Branding"
                                class="mt-2 w-full rounded-xl border px-4
                                       py-2 outline-none
                                       {{ $errors->has('tagsInput')
                                           ? 'border-red-400'
                                           : 'border-gray-200'
                                       }}
                                       dark:bg-slate-950 dark:text-white
                                       dark:border-slate-700"
                            >

                            @error('tagsInput')
                                <p class="mt-1 text-xs text-red-500">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- DESCRIPTION --}}
                        <div class="md:col-span-2">
                            <label
                                for="project-description"
                                class="text-sm font-medium text-slate-700
                                       dark:text-slate-200"
                            >
                                Description
                            </label>

                            <textarea
                                id="project-description"
                                wire:model="description"
                                rows="4"
                                maxlength="500"
                                class="mt-2 w-full rounded-xl border px-4
                                       py-2 outline-none
                                       {{ $errors->has('description')
                                           ? 'border-red-400'
                                           : 'border-gray-200'
                                       }}
                                       dark:bg-slate-950 dark:text-white
                                       dark:border-slate-700"
                            ></textarea>

                            @error('description')
                                <p class="mt-1 text-xs text-red-500">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    {{-- ACTION BUTTONS --}}
                    <div class="mt-6 flex justify-end gap-3">
                        <button
                            type="button"
                            wire:click="closeCreateModal"
                            wire:loading.attr="disabled"
                            wire:target="saveProject,imageUpload"
                            class="rounded-xl border border-gray-200
                                   px-5 py-3 text-sm font-semibold
                                   disabled:cursor-not-allowed
                                   disabled:opacity-60
                                   dark:border-slate-700 dark:text-white"
                        >
                            Cancel
                        </button>

                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="saveProject,imageUpload"
                            class="rounded-xl bg-[#7fac9f] px-5 py-3
                                   text-sm font-semibold text-white
                                   disabled:cursor-not-allowed
                                   disabled:opacity-60"
                        >
                            <span
                                wire:loading.remove
                                wire:target="saveProject,imageUpload"
                            >
                                {{ $isEditing
                                    ? 'Update Project'
                                    : 'Save Project'
                                }}
                            </span>

                            <span
                                wire:loading
                                wire:target="imageUpload"
                            >
                                Uploading Image...
                            </span>

                            <span
                                wire:loading
                                wire:target="saveProject"
                            >
                                Saving...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
