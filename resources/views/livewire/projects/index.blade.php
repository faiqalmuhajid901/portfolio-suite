<div class="space-y-6">
    @if (session()->has('success'))
        <div class="rounded-2xl bg-[#eef5f2] px-5 py-4 text-sm font-medium text-[#2f6f61] dark:bg-emerald-950 dark:text-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-medium text-red-700 dark:border-red-900 dark:bg-red-950/40 dark:text-red-300">
            {{ session('error') }}
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
                    type="button"
                    wire:click="exportCsv"
                    wire:loading.attr="disabled"
                    wire:target="exportCsv"
                    class="rounded-xl border border-[#7fac9f] px-4 py-2 text-sm font-semibold text-[#2f6f61] disabled:opacity-60 dark:text-emerald-300"
                >
                    <span wire:loading.remove wire:target="exportCsv">Export CSV</span>
                    <span wire:loading wire:target="exportCsv">Exporting...</span>
                </button>

                <button
                    type="button"
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
                        <tr wire:key="project-row-{{ $project->id }}" class="border-t border-gray-100 dark:border-slate-800">
                            <td class="px-8 py-5">
                                <div class="font-semibold text-slate-950 dark:text-white">
                                    {{ $project->name }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-slate-400">
                                    {{ $project->category ?: '-' }}
                                </div>
                            </td>

                            <td class="px-8 py-5 text-slate-700 dark:text-slate-200">
                                {{ $project->client ?: '-' }}
                            </td>

                            <td class="px-8 py-5">
                                <select
                                    wire:change="updateStatus({{ $project->id }}, $event.target.value)"
                                    class="min-w-[150px] rounded-full border border-[#7fac9f]/50 bg-[#eef5f2] px-4 py-2 text-sm font-medium text-[#2f6f61] outline-none focus:border-[#2f6f61] dark:border-emerald-800 dark:bg-emerald-950 dark:text-emerald-200"
                                >
                                    <option value="in_progress" @selected($project->status === 'in_progress')>In Progress</option>
                                    <option value="review" @selected($project->status === 'review')>Review</option>
                                    <option value="completed" @selected($project->status === 'completed')>Completed</option>
                                </select>
                            </td>

                            <td class="px-8 py-5 text-sm text-gray-500 dark:text-slate-400">
                                {{ optional($project->start_date)->format('M d, Y') ?: '-' }}
                                —
                                {{ optional($project->end_date)->format('M d, Y') ?: '-' }}
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
                                        type="button"
                                        wire:click="openEditModal({{ $project->id }})"
                                        class="rounded-lg px-3 py-1 text-sm font-semibold text-blue-600 hover:bg-blue-50 dark:text-blue-300 dark:hover:bg-blue-950/40"
                                    >
                                        Edit
                                    </button>

                                    <button
                                        type="button"
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
                <div wire:key="project-card-{{ $project->id }}" class="rounded-2xl border border-gray-100 p-5 dark:border-slate-800">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="font-semibold text-slate-950 dark:text-white">
                                {{ $project->name }}
                            </h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">
                                {{ $project->client ?: '-' }}
                            </p>
                        </div>

                        <button
                            type="button"
                            wire:click="deleteProject({{ $project->id }})"
                            wire:confirm="Hapus project ini?"
                            class="text-sm text-red-500"
                        >
                            Delete
                        </button>
                    </div>

                    <p class="mt-3 text-sm text-gray-500 dark:text-slate-400">
                        {{ $project->category ?: '-' }}
                    </p>

                    <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                        <select
                            wire:change="updateStatus({{ $project->id }}, $event.target.value)"
                            class="min-w-[150px] rounded-full border border-[#7fac9f]/50 bg-[#eef5f2] px-4 py-2 text-xs font-medium text-[#2f6f61] outline-none dark:border-emerald-800 dark:bg-emerald-950 dark:text-emerald-200"
                        >
                            <option value="in_progress" @selected($project->status === 'in_progress')>In Progress</option>
                            <option value="review" @selected($project->status === 'review')>Review</option>
                            <option value="completed" @selected($project->status === 'completed')>Completed</option>
                        </select>

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
                                type="button"
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
        <div
            class="fixed inset-0 z-[80] flex items-center justify-center bg-black/40 p-4"
            wire:click.self="closeCreateModal"
            wire:keydown.escape.window="closeCreateModal"
        >
            <div
                class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-[28px] bg-white p-6 shadow-2xl dark:bg-slate-900"
                x-data="projectImageUploader({
                    signUrl: @js(route('projects.image-upload-url')),
                    existingImage: @js($existingImageUrl),
                })"
                x-on:project-image-reset.window="resetUploader()"
            >
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
                        type="button"
                        wire:click.prevent.stop="closeCreateModal"
                        wire:loading.attr="disabled"
                        wire:target="closeCreateModal"
                        class="rounded-xl bg-gray-100 px-3 py-2 disabled:opacity-60 dark:bg-slate-800 dark:text-white"
                        aria-label="Close modal"
                    >
                        ✕
                    </button>
                </div>

                @if (session()->has('error'))
                    <div class="mt-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700 dark:border-red-900 dark:bg-red-950/40 dark:text-red-300">
                        {{ session('error') }}
                    </div>
                @endif

                <form wire:submit.prevent="saveProject">
                    <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label for="project-name" class="text-sm font-medium text-slate-700 dark:text-slate-200">
                                Project Name
                            </label>
                            <input
                                id="project-name"
                                wire:model="name"
                                type="text"
                                maxlength="120"
                                class="mt-2 w-full rounded-xl border border-gray-200 px-4 py-2 outline-none dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                            >
                            @error('name')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="project-category" class="text-sm font-medium text-slate-700 dark:text-slate-200">
                                Category
                            </label>
                            <input
                                id="project-category"
                                wire:model="category"
                                type="text"
                                maxlength="120"
                                class="mt-2 w-full rounded-xl border border-gray-200 px-4 py-2 outline-none dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                            >
                            @error('category')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="project-client" class="text-sm font-medium text-slate-700 dark:text-slate-200">
                                Client
                            </label>
                            <input
                                id="project-client"
                                wire:model="client"
                                type="text"
                                maxlength="120"
                                class="mt-2 w-full rounded-xl border border-gray-200 px-4 py-2 outline-none dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                            >
                            @error('client')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="project-status" class="text-sm font-medium text-slate-700 dark:text-slate-200">
                                Status
                            </label>
                            <select
                                id="project-status"
                                wire:model="projectStatus"
                                class="mt-2 w-full rounded-xl border border-gray-200 px-4 py-2 outline-none focus:border-[#7fac9f] dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                            >
                                <option value="in_progress">In Progress</option>
                                <option value="review">Review</option>
                                <option value="completed">Completed</option>
                            </select>
                            @error('projectStatus')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="project-start-date" class="text-sm font-medium text-slate-700 dark:text-slate-200">
                                Start Date
                            </label>
                            <input
                                id="project-start-date"
                                wire:model="startDate"
                                type="date"
                                class="mt-2 w-full rounded-xl border border-gray-200 px-4 py-2 outline-none dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                            >
                            @error('startDate')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="project-end-date" class="text-sm font-medium text-slate-700 dark:text-slate-200">
                                End Date
                            </label>
                            <input
                                id="project-end-date"
                                wire:model="endDate"
                                type="date"
                                class="mt-2 w-full rounded-xl border border-gray-200 px-4 py-2 outline-none dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                            >
                            @error('endDate')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="project-website" class="text-sm font-medium text-slate-700 dark:text-slate-200">
                                Website URL
                            </label>
                            <input
                                id="project-website"
                                wire:model="websiteUrl"
                                type="text"
                                maxlength="255"
                                placeholder="https://example.com"
                                class="mt-2 w-full rounded-xl border border-gray-200 px-4 py-2 outline-none dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                            >
                            <p class="mt-1 text-xs text-gray-500 dark:text-slate-400">
                                Jika gambar tidak diupload, sistem membuat preview otomatis dari Website URL.
                            </p>
                            @error('websiteUrl')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="project-image" class="text-sm font-medium text-slate-700 dark:text-slate-200">
                                Upload Project Image
                                <span class="text-xs font-normal text-gray-500 dark:text-slate-400">(Opsional)</span>
                            </label>

                            <div class="mt-2 rounded-2xl border border-dashed border-gray-300 p-5 dark:border-slate-700">
                                <input
                                    id="project-image"
                                    x-ref="imageInput"
                                    type="file"
                                    accept=".jpg,.jpeg,.png,.gif,.webp,.bmp"
                                    x-on:change="chooseImage($event)"
                                    x-bind:disabled="uploading"
                                    class="w-full text-sm text-slate-700 file:mr-4 file:rounded-xl file:border-0 file:bg-[#7fac9f] file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white disabled:cursor-not-allowed disabled:opacity-60 dark:text-slate-200"
                                >

                                <p class="mt-3 text-xs text-gray-500 dark:text-slate-400">
                                    Format JPG, JPEG, PNG, GIF, WEBP, atau BMP. Maksimal 4 MB.
                                </p>

                                <div
                                    x-show="uploading"
                                    x-cloak
                                    class="mt-3 rounded-xl bg-[#eef5f2] px-4 py-3 text-sm text-[#2f6f61] dark:bg-emerald-950 dark:text-emerald-300"
                                >
                                    Mengunggah gambar... <span x-text="progress"></span>%
                                </div>

                                <div
                                    x-show="error"
                                    x-cloak
                                    class="mt-3 rounded-lg bg-red-50 px-3 py-2 text-xs font-medium text-red-600 dark:bg-red-950/40 dark:text-red-300"
                                >
                                    <span x-text="error"></span>
                                </div>

                                @error('uploadedImagePath')
                                    <p class="mt-3 rounded-lg bg-red-50 px-3 py-2 text-xs font-medium text-red-600 dark:bg-red-950/40 dark:text-red-300">
                                        {{ $message }}
                                    </p>
                                @enderror

                                <template x-if="preview">
                                    <div class="mt-4">
                                        <div class="overflow-hidden rounded-2xl border border-gray-100 dark:border-slate-800">
                                            <img
                                                x-bind:src="preview"
                                                class="h-48 w-full object-cover"
                                                alt="Preview project image"
                                            >
                                        </div>

                                        <button
                                            x-show="hasNewUpload"
                                            x-cloak
                                            type="button"
                                            x-on:click="cancelNewImage()"
                                            x-bind:disabled="uploading"
                                            class="mt-3 text-sm font-semibold text-red-600 disabled:opacity-60"
                                        >
                                            Batalkan gambar baru
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <label for="project-tags" class="text-sm font-medium text-slate-700 dark:text-slate-200">
                                Tags
                            </label>
                            <input
                                id="project-tags"
                                wire:model="tagsInput"
                                type="text"
                                maxlength="255"
                                placeholder="UI, Dashboard, Branding"
                                class="mt-2 w-full rounded-xl border border-gray-200 px-4 py-2 outline-none dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                            >
                            @error('tagsInput')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="project-description" class="text-sm font-medium text-slate-700 dark:text-slate-200">
                                Description
                            </label>
                            <textarea
                                id="project-description"
                                wire:model="description"
                                rows="4"
                                maxlength="500"
                                class="mt-2 w-full rounded-xl border border-gray-200 px-4 py-2 outline-none dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                            ></textarea>
                            @error('description')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button
                            type="button"
                            wire:click.prevent.stop="closeCreateModal"
                            wire:loading.attr="disabled"
                            wire:target="closeCreateModal,saveProject"
                            x-bind:disabled="uploading"
                            class="rounded-xl border border-gray-200 px-5 py-3 text-sm font-semibold disabled:opacity-60 dark:border-slate-700 dark:text-white"
                        >
                            Cancel
                        </button>

                        <button
                            type="submit"
                            x-bind:disabled="uploading"
                            wire:loading.attr="disabled"
                            wire:target="saveProject"
                            class="rounded-xl bg-[#7fac9f] px-5 py-3 text-sm font-semibold text-white disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            <span x-show="! uploading" wire:loading.remove wire:target="saveProject">
                                {{ $isEditing ? 'Update Project' : 'Save Project' }}
                            </span>
                            <span x-show="uploading" x-cloak>Uploading Image...</span>
                            <span wire:loading wire:target="saveProject">Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
