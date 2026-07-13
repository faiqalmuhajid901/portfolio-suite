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

                <form
                    wire:submit="saveProject"
                    x-data="{
                        uploading: false,
                        progress: 0,
                        error: '',
                        oldPreview: @js($existingImageUrl),
                        preview: @js($existingImageUrl),
                        objectUrl: null,

                        readableError(data, fallback) {
                            if (data?.message) return data.message;

                            if (data?.errors) {
                                const first = Object.values(data.errors).flat()[0];
                                if (first) return first;
                            }

                            return fallback;
                        },

                        uploadToSignedUrl(signedUrl, file) {
                            return new Promise((resolve, reject) => {
                                const xhr = new XMLHttpRequest();

                                xhr.open('PUT', signedUrl, true);
                                xhr.setRequestHeader('x-upsert', 'false');

                                xhr.upload.onprogress = (event) => {
                                    if (event.lengthComputable) {
                                        this.progress = Math.round(
                                            (event.loaded / event.total) * 100
                                        );
                                    }
                                };

                                xhr.onload = () => {
                                    if (xhr.status >= 200 && xhr.status < 300) {
                                        resolve();
                                        return;
                                    }

                                    reject(
                                        new Error(
                                            xhr.responseText
                                            || `Upload gagal dengan status ${xhr.status}.`
                                        )
                                    );
                                };

                                xhr.onerror = () => {
                                    reject(new Error('Koneksi ke Supabase gagal.'));
                                };

                                const body = new FormData();
                                body.append('cacheControl', '3600');
                                body.append('', file);

                                xhr.send(body);
                            });
                        },

                        async chooseImage(event) {
                            const input = event.target;
                            const file = input.files?.[0];

                            this.error = '';
                            this.progress = 0;

                            if (! file) return;

                            const allowedTypes = [
                                'image/jpeg',
                                'image/png',
                                'image/gif',
                                'image/webp',
                                'image/bmp',
                                'image/x-ms-bmp',
                            ];

                            if (! allowedTypes.includes(file.type)) {
                                this.error =
                                    'Format gambar harus JPG, JPEG, PNG, GIF, WEBP, atau BMP.';
                                input.value = '';
                                return;
                            }

                            if (file.size > 4 * 1024 * 1024) {
                                this.error = 'Ukuran gambar maksimal 4 MB.';
                                input.value = '';
                                return;
                            }

                            const previousPreview = this.preview;
                            const previousObjectUrl = this.objectUrl;
                            const nextObjectUrl = URL.createObjectURL(file);

                            this.preview = nextObjectUrl;
                            this.objectUrl = nextObjectUrl;
                            this.uploading = true;

                            try {
                                const csrf = document.querySelector(
                                    'meta[name=csrf-token]'
                                )?.getAttribute('content');

                                if (! csrf) {
                                    throw new Error(
                                        'CSRF token tidak ditemukan pada layout dashboard.'
                                    );
                                }

                                const signResponse = await fetch(
                                    @js(route('projects.image-upload-url')),
                                    {
                                        method: 'POST',
                                        headers: {
                                            'Accept': 'application/json',
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': csrf,
                                        },
                                        credentials: 'same-origin',
                                        body: JSON.stringify({
                                            name: file.name,
                                            type: file.type,
                                            size: file.size,
                                        }),
                                    }
                                );

                                const signData = await signResponse
                                    .json()
                                    .catch(() => ({}));

                                if (! signResponse.ok) {
                                    throw new Error(
                                        this.readableError(
                                            signData,
                                            'Gagal meminta signed upload URL.'
                                        )
                                    );
                                }

                                await this.uploadToSignedUrl(
                                    signData.signed_url,
                                    file
                                );

                                await this.$wire.setUploadedImage(signData.path);

                                if (
                                    previousObjectUrl
                                    && previousObjectUrl !== nextObjectUrl
                                ) {
                                    URL.revokeObjectURL(previousObjectUrl);
                                }

                                URL.revokeObjectURL(nextObjectUrl);
                                this.objectUrl = null;
                                this.preview = signData.public_url;
                                this.progress = 100;
                            } catch (exception) {
                                URL.revokeObjectURL(nextObjectUrl);

                                this.objectUrl = previousObjectUrl;
                                this.preview = previousPreview;
                                this.error =
                                    exception?.message
                                    || 'Upload gambar gagal.';

                                input.value = '';
                            } finally {
                                this.uploading = false;
                            }
                        },

                        async clearImage() {
                            if (this.uploading) return;

                            await this.$wire.removeUploadedImage();

                            if (this.objectUrl) {
                                URL.revokeObjectURL(this.objectUrl);
                            }

                            this.objectUrl = null;
                            this.preview = this.oldPreview;
                            this.progress = 0;
                            this.error = '';

                            if (this.$refs.imageInput) {
                                this.$refs.imageInput.value = '';
                            }
                        },

                        resetImageUi() {
                            if (this.objectUrl) {
                                URL.revokeObjectURL(this.objectUrl);
                            }

                            this.objectUrl = null;
                            this.preview = this.oldPreview;
                            this.progress = 0;
                            this.error = '';

                            if (this.$refs.imageInput) {
                                this.$refs.imageInput.value = '';
                            }
                        },
                    }"
                    x-on:project-image-reset.window="resetImageUi()"
                >

                {{-- IMAGE UPLOAD --}}
                <div class="md:col-span-2">
                    <label
                        for="project-image"
                        class="text-sm font-medium text-slate-700 dark:text-slate-200"
                    >
                        Upload Project Image

                        <span class="text-xs font-normal text-gray-500 dark:text-slate-400">
                            (Opsional)
                        </span>
                    </label>

                    <div
                        class="mt-2 rounded-2xl border border-dashed border-gray-300 p-5
                            dark:border-slate-700"
                    >
                        <input
                            id="project-image"
                            x-ref="imageInput"
                            type="file"
                            accept=".jpg,.jpeg,.png,.gif,.webp,.bmp"
                            x-on:change="chooseImage($event)"
                            x-bind:disabled="uploading"
                            class="w-full text-sm text-slate-700
                                file:mr-4 file:rounded-xl file:border-0
                                file:bg-[#7fac9f] file:px-4 file:py-2
                                file:text-sm file:font-semibold file:text-white
                                disabled:cursor-not-allowed disabled:opacity-60
                                dark:text-slate-200"
                        >

                        <p class="mt-3 text-xs text-gray-500 dark:text-slate-400">
                            Format JPG, JPEG, PNG, GIF, WEBP, atau BMP.
                            Ukuran maksimal 4 MB. File diunggah langsung ke Supabase.
                        </p>

                        <div
                            x-show="uploading"
                            x-cloak
                            class="mt-3 rounded-xl bg-[#eef5f2] px-4 py-3
                                text-sm text-[#2f6f61]
                                dark:bg-emerald-950 dark:text-emerald-300"
                        >
                            Mengunggah gambar...
                            <span x-text="progress"></span>%
                        </div>

                        <div
                            x-show="error"
                            x-cloak
                            class="mt-3 rounded-lg bg-red-50 px-3 py-2
                                text-xs font-medium text-red-600
                                dark:bg-red-950/40 dark:text-red-300"
                        >
                            <span x-text="error"></span>
                        </div>

                        @error('uploadedImagePath')
                            <p
                                class="mt-3 rounded-lg bg-red-50 px-3 py-2
                                    text-xs font-medium text-red-600
                                    dark:bg-red-950/40 dark:text-red-300"
                            >
                                {{ $message }}
                            </p>
                        @enderror

                        <template x-if="preview">
                            <div class="mt-4">
                                <div
                                    class="overflow-hidden rounded-2xl border
                                        border-gray-100 dark:border-slate-800"
                                >
                                    <img
                                        x-bind:src="preview"
                                        class="h-48 w-full object-cover"
                                        alt="Preview project image"
                                    >
                                </div>

                                <button
                                    type="button"
                                    x-on:click="clearImage()"
                                    x-bind:disabled="uploading"
                                    class="mt-3 text-sm font-semibold text-red-600
                                        disabled:cursor-not-allowed disabled:opacity-60"
                                >
                                    Hapus gambar baru
                                </button>
                            </div>
                        </template>

                        <template x-if="! preview && @js(filled($websiteUrl))">
                            <div class="mt-4 rounded-2xl bg-[#eef5f2] p-4 dark:bg-slate-800">
                                <p
                                    class="text-xs font-medium text-[#2f6f61]
                                        dark:text-emerald-300"
                                >
                                    Jika tidak ada file yang diupload, preview akan dibuat
                                    dari Website URL.
                                </p>
                            </div>
                        </template>
                    </div>
                </div>

                {{--
                Pada tombol Save/Update, pastikan bentuknya seperti ini:
                --}}
                <button
                    type="submit"
                    x-bind:disabled="uploading"
                    wire:loading.attr="disabled"
                    wire:target="saveProject"
                    class="rounded-xl bg-[#7fac9f] px-5 py-3
                        text-sm font-semibold text-white
                        disabled:cursor-not-allowed disabled:opacity-60"
                >
                    <span
                        x-show="! uploading"
                        wire:loading.remove
                        wire:target="saveProject"
                    >
                        {{ $isEditing ? 'Update Project' : 'Save Project' }}
                    </span>

                    <span x-show="uploading" x-cloak>
                        Uploading Image...
                    </span>

                    <span wire:loading wire:target="saveProject">
                        Saving...
                    </span>
                </button>

                </form>

            </div>
        </div>
    @endif
</div>
