@php
    $settingsUser = auth()->user();
    $settingsProfile = $settingsUser?->profile;

    $avatarValue = $settingsProfile?->avatar;
    $backgroundValue = $settingsProfile?->hero_background;

    $avatarUrl = $avatarValue
        ? (filter_var($avatarValue, FILTER_VALIDATE_URL)
            ? $avatarValue
            : asset($avatarValue))
        : null;

    $backgroundUrl = $backgroundValue
        ? (filter_var($backgroundValue, FILTER_VALIDATE_URL)
            ? $backgroundValue
            : asset($backgroundValue))
        : null;
@endphp

<div class="space-y-8">
    <script>
        if (typeof window.settingsImageUploader !== 'function') {
            window.settingsImageUploader = function (config) {
                return {
                    uploading: false,
                    saving: false,
                    ready: false,
                    progress: 0,
                    error: '',
                    pendingPath: null,
                    preview: config.currentUrl || null,
                    originalPreview: config.currentUrl || null,
                    objectUrl: null,

                    validateFile(file) {
                        const allowedTypes = [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                            'image/webp',
                            'image/bmp',
                            'image/x-ms-bmp',
                        ];

                        if (!allowedTypes.includes(file.type)) {
                            throw new Error(
                                'Format gambar harus JPG, PNG, GIF, WEBP, atau BMP.'
                            );
                        }

                        if (file.size > config.maxBytes) {
                            throw new Error(config.maxMessage);
                        }
                    },

                    firstError(payload, fallback) {
                        if (payload?.message) {
                            return payload.message;
                        }

                        return fallback;
                    },

                    releaseObjectUrl() {
                        if (this.objectUrl) {
                            URL.revokeObjectURL(this.objectUrl);
                            this.objectUrl = null;
                        }
                    },

                    resetPending() {
                        this.releaseObjectUrl();
                        this.uploading = false;
                        this.saving = false;
                        this.ready = false;
                        this.progress = 0;
                        this.error = '';
                        this.pendingPath = null;
                        this.preview = this.originalPreview;

                        if (this.$refs.fileInput) {
                            this.$refs.fileInput.value = '';
                        }
                    },

                    uploadToSupabase(signedUrl, file) {
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
                                if (
                                    xhr.status >= 200
                                    && xhr.status < 300
                                ) {
                                    resolve();
                                    return;
                                }

                                let message =
                                    `Upload Supabase gagal (${xhr.status}).`;

                                try {
                                    const response = JSON.parse(
                                        xhr.responseText
                                    );

                                    message = response.message || message;
                                } catch (error) {
                                    if (xhr.responseText) {
                                        message = xhr.responseText;
                                    }
                                }

                                reject(new Error(message));
                            };

                            xhr.onerror = () => {
                                reject(
                                    new Error(
                                        'Koneksi browser ke Supabase gagal. Periksa koneksi dan konfigurasi CORS.'
                                    )
                                );
                            };

                            const body = new FormData();

                            body.append('cacheControl', '3600');
                            body.append('', file);

                            xhr.send(body);
                        });
                    },

                    async chooseFile(event) {
                        const input = event.target;
                        const file = input.files?.[0];

                        this.error = '';
                        this.progress = 0;
                        this.ready = false;
                        this.pendingPath = null;

                        if (!file) {
                            return;
                        }

                        try {
                            this.validateFile(file);

                            this.releaseObjectUrl();

                            this.objectUrl = URL.createObjectURL(file);
                            this.preview = this.objectUrl;
                            this.uploading = true;

                            const signed =
                                await this.$wire.createSettingsImageUpload(
                                    config.target,
                                    file.name,
                                    file.type,
                                    file.size
                                );

                            if (!signed?.ok) {
                                throw new Error(
                                    this.firstError(
                                        signed,
                                        'Gagal membuat signed upload URL.'
                                    )
                                );
                            }

                            if (
                                !signed.signed_url
                                || !signed.path
                                || !signed.public_url
                            ) {
                                throw new Error(
                                    'Respons signed upload URL tidak lengkap.'
                                );
                            }

                            await this.uploadToSupabase(
                                signed.signed_url,
                                file
                            );

                            this.releaseObjectUrl();

                            this.preview = signed.public_url;
                            this.pendingPath = signed.path;
                            this.progress = 100;
                            this.ready = true;
                        } catch (exception) {
                            this.releaseObjectUrl();

                            this.preview = this.originalPreview;
                            this.pendingPath = null;
                            this.ready = false;
                            this.error =
                                exception?.message
                                || 'Upload gambar gagal.';

                            input.value = '';
                        } finally {
                            this.uploading = false;
                        }
                    },

                    async save() {
                        if (
                            this.uploading
                            || this.saving
                            || !this.ready
                            || !this.pendingPath
                        ) {
                            return;
                        }

                        this.error = '';
                        this.saving = true;

                        try {
                            const result =
                                await this.$wire.saveSettingsImage(
                                    config.target,
                                    this.pendingPath
                                );

                            if (!result?.ok) {
                                throw new Error(
                                    this.firstError(
                                        result,
                                        'Gagal menyimpan gambar.'
                                    )
                                );
                            }

                            this.preview = result.public_url;
                            this.originalPreview = result.public_url;
                            this.pendingPath = null;
                            this.ready = false;

                            if (this.$refs.fileInput) {
                                this.$refs.fileInput.value = '';
                            }
                        } catch (exception) {
                            this.error =
                                exception?.message
                                || 'Gagal menyimpan gambar.';
                        } finally {
                            this.saving = false;
                        }
                    },
                };
            };
        }
    </script>

    {{-- Header --}}
    <section class="rounded-[28px] bg-white p-6 shadow-[0_20px_60px_rgba(15,23,42,0.06)] dark:bg-slate-900 dark:shadow-none lg:p-8">
        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-[#2f6f61] dark:text-emerald-300">
            Account Settings
        </p>

        <h1 class="mt-4 text-3xl font-bold text-slate-950 dark:text-white sm:text-4xl">
            Settings
        </h1>

        <p class="mt-3 max-w-2xl text-sm leading-relaxed text-gray-600 dark:text-slate-300 sm:text-base">
            Kelola informasi akun, foto profil, konten halaman public, email login, dan password.
        </p>
    </section>

    {{-- Profile Photo --}}
    <section
        x-data="settingsImageUploader({
            target: 'profile-photo',
            currentUrl: @js($avatarUrl),
            maxBytes: 4 * 1024 * 1024,
            maxMessage: 'Ukuran foto profil maksimal 4 MB.',
        })"
        class="rounded-[28px] bg-white p-6 shadow-[0_20px_60px_rgba(15,23,42,0.06)] dark:bg-slate-900 dark:shadow-none lg:p-8"
    >
        <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
            <div class="flex items-center gap-5">
                <template x-if="preview">
                    <img
                        x-bind:src="preview"
                        alt="{{ $settingsUser?->name ?? 'User' }}"
                        class="h-24 w-24 rounded-full object-cover ring-4 ring-[#eef5f2] dark:ring-slate-800"
                    >
                </template>

                <template x-if="!preview">
                    <div class="flex h-24 w-24 items-center justify-center rounded-full bg-[#2f6f61] text-3xl font-bold text-white ring-4 ring-[#eef5f2] dark:ring-slate-800">
                        {{ strtoupper(substr($settingsUser?->name ?? 'A', 0, 1)) }}
                    </div>
                </template>

                <div>
                    <h2 class="text-2xl font-bold text-slate-950 dark:text-white">
                        Foto Profil
                    </h2>

                    <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">
                        Foto ini akan tampil di public page, sidebar, dan topbar.
                    </p>
                </div>
            </div>

            <div class="w-full md:max-w-md">
                @if (session('photo_success'))
                    <div class="mb-4 rounded-2xl bg-[#eef5f2] px-5 py-4 text-sm font-medium text-[#2f6f61] dark:bg-emerald-950 dark:text-emerald-200">
                        {{ session('photo_success') }}
                    </div>
                @endif

                <input
                    x-ref="fileInput"
                    x-on:change="chooseFile($event)"
                    x-bind:disabled="uploading || saving"
                    type="file"
                    accept="image/jpeg,image/png,image/gif,image/webp,image/bmp"
                    class="w-full text-sm text-slate-700 file:mr-4 file:rounded-xl file:border-0 file:bg-[#7fac9f] file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white disabled:opacity-60 dark:text-slate-200"
                >

                <p class="mt-2 text-xs text-gray-500 dark:text-slate-400">
                    Format JPG, PNG, GIF, WEBP, atau BMP. Maksimal 4 MB.
                    File dikirim langsung ke Supabase.
                </p>

                <div
                    x-show="uploading"
                    x-cloak
                    class="mt-3 text-sm text-[#2f6f61] dark:text-emerald-300"
                >
                    Mengunggah gambar:
                    <span x-text="progress"></span>%
                </div>

                <div
                    x-show="ready && !uploading"
                    x-cloak
                    class="mt-3 rounded-xl bg-blue-50 px-4 py-3 text-sm text-blue-700 dark:bg-blue-950 dark:text-blue-200"
                >
                    Preview sudah siap. Klik “Simpan Foto Profil”.
                </div>

                <p
                    x-show="error"
                    x-cloak
                    x-text="error"
                    class="mt-3 text-sm text-red-500"
                ></p>

                <button
                    type="button"
                    x-on:click="save()"
                    x-bind:disabled="!ready || uploading || saving"
                    class="mt-5 rounded-xl bg-[#7fac9f] px-5 py-3 text-sm font-semibold text-white disabled:cursor-not-allowed disabled:opacity-60"
                >
                    <span x-show="!saving">Simpan Foto Profil</span>
                    <span x-show="saving" x-cloak>Menyimpan...</span>
                </button>
            </div>
        </div>
    </section>

    {{-- Public Hero Background --}}
    <section
        x-data="settingsImageUploader({
            target: 'hero-background',
            currentUrl: @js($backgroundUrl),
            maxBytes: 8 * 1024 * 1024,
            maxMessage: 'Ukuran background maksimal 8 MB.',
        })"
        class="rounded-[28px] bg-white p-6 shadow-[0_20px_60px_rgba(15,23,42,0.06)] dark:bg-slate-900 dark:shadow-none lg:p-8"
    >
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-[#2f6f61] dark:text-emerald-300">
                Public Hero Background
            </p>

            <h2 class="mt-3 text-2xl font-bold text-slate-950 dark:text-white">
                Background Halaman Public
            </h2>

            <p class="mt-2 text-sm text-gray-500 dark:text-slate-400">
                Gambar ini akan menjadi background dari bagian hero sampai Portfolio Summary.
            </p>
        </div>

        @if (session('hero_background_success'))
            <div class="mt-5 rounded-2xl bg-[#eef5f2] px-5 py-4 text-sm font-medium text-[#2f6f61] dark:bg-emerald-950 dark:text-emerald-200">
                {{ session('hero_background_success') }}
            </div>
        @endif

        <div class="mt-6 grid gap-6 lg:grid-cols-2">
            <div>
                <input
                    x-ref="fileInput"
                    x-on:change="chooseFile($event)"
                    x-bind:disabled="uploading || saving"
                    type="file"
                    accept="image/jpeg,image/png,image/gif,image/webp,image/bmp"
                    class="w-full text-sm text-slate-700 file:mr-4 file:rounded-xl file:border-0 file:bg-[#7fac9f] file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white disabled:opacity-60 dark:text-slate-200"
                >

                <p class="mt-2 text-xs text-gray-500 dark:text-slate-400">
                    Format JPG, PNG, GIF, WEBP, atau BMP. Maksimal 8 MB.
                    File dikirim langsung ke Supabase.
                </p>

                <div
                    x-show="uploading"
                    x-cloak
                    class="mt-3 text-sm text-[#2f6f61] dark:text-emerald-300"
                >
                    Mengunggah background:
                    <span x-text="progress"></span>%
                </div>

                <div
                    x-show="ready && !uploading"
                    x-cloak
                    class="mt-3 rounded-xl bg-blue-50 px-4 py-3 text-sm text-blue-700 dark:bg-blue-950 dark:text-blue-200"
                >
                    Preview sudah siap. Klik “Simpan Background Public”.
                </div>

                <p
                    x-show="error"
                    x-cloak
                    x-text="error"
                    class="mt-3 text-sm text-red-500"
                ></p>

                <button
                    type="button"
                    x-on:click="save()"
                    x-bind:disabled="!ready || uploading || saving"
                    class="mt-5 rounded-xl bg-[#7fac9f] px-5 py-3 text-sm font-semibold text-white disabled:cursor-not-allowed disabled:opacity-60"
                >
                    <span x-show="!saving">Simpan Background Public</span>
                    <span x-show="saving" x-cloak>Menyimpan...</span>
                </button>
            </div>

            <div>
                <p class="mb-2 text-sm font-medium text-slate-700 dark:text-slate-200">
                    Background Saat Ini / Preview
                </p>

                <template x-if="preview">
                    <div class="overflow-hidden rounded-2xl border border-gray-100 dark:border-slate-800">
                        <img
                            x-bind:src="preview"
                            alt="Hero background preview"
                            class="h-56 w-full object-cover"
                        >
                    </div>
                </template>

                <template x-if="!preview">
                    <div class="flex h-56 items-center justify-center rounded-2xl border border-dashed border-gray-300 text-sm text-gray-500 dark:border-slate-700 dark:text-slate-400">
                        Belum ada background.
                    </div>
                </template>
            </div>
        </div>
    </section>

    {{-- Public Hero Content --}}
    <section class="rounded-[28px] bg-white p-6 shadow-[0_20px_60px_rgba(15,23,42,0.06)] dark:bg-slate-900 dark:shadow-none lg:p-8">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-[#2f6f61] dark:text-emerald-300">
                Public Page Content
            </p>

            <h2 class="mt-3 text-2xl font-bold text-slate-950 dark:text-white">
                Edit Hero Halaman Public
            </h2>

            <p class="mt-2 text-sm text-gray-500 dark:text-slate-400">
                Konten ini akan muncul di bagian paling atas halaman public portfolio.
            </p>
        </div>

        @if (session('hero_success'))
            <div class="mt-5 rounded-2xl bg-[#eef5f2] px-5 py-4 text-sm font-medium text-[#2f6f61] dark:bg-emerald-950 dark:text-emerald-200">
                {{ session('hero_success') }}
            </div>
        @endif

        <form wire:submit.prevent="updateHeroContent" class="mt-6 space-y-5">
            <div>
                <label class="text-sm font-medium text-slate-700 dark:text-slate-200">
                    Label Kecil
                </label>

                <input
                    wire:model="heroBadge"
                    type="text"
                    placeholder="MY PORTFOLIO"
                    class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:border-[#7fac9f] dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                >

                @error('heroBadge')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="text-sm font-medium text-slate-700 dark:text-slate-200">
                    Judul Utama
                </label>

                <input
                    wire:model="heroTitle"
                    type="text"
                    placeholder="Creative portfolio system for refined digital projects."
                    class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:border-[#7fac9f] dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                >

                @error('heroTitle')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="text-sm font-medium text-slate-700 dark:text-slate-200">
                    Deskripsi
                </label>

                <textarea
                    wire:model="heroDescription"
                    rows="4"
                    placeholder="Explore selected works, project progress, design systems..."
                    class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:border-[#7fac9f] dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                ></textarea>

                @error('heroDescription')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end">
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="updateHeroContent"
                    class="rounded-xl bg-[#7fac9f] px-5 py-3 text-sm font-semibold text-white hover:bg-[#6c9a8e] disabled:opacity-60"
                >
                    Simpan Konten Hero
                </button>
            </div>
        </form>
    </section>

    <section class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        {{-- Account Information --}}
        <div class="rounded-[28px] bg-white p-6 shadow-[0_20px_60px_rgba(15,23,42,0.06)] dark:bg-slate-900 dark:shadow-none lg:p-8">
            <div>
                <h2 class="text-2xl font-bold text-slate-950 dark:text-white">
                    Informasi Akun
                </h2>

                <p class="mt-2 text-sm text-gray-500 dark:text-slate-400">
                    Ubah nama tampilan dan email akun kamu.
                </p>
            </div>

            @if (session('account_success'))
                <div class="mt-5 rounded-2xl bg-[#eef5f2] px-5 py-4 text-sm font-medium text-[#2f6f61] dark:bg-emerald-950 dark:text-emerald-200">
                    {{ session('account_success') }}
                </div>
            @endif

            <form wire:submit.prevent="updateAccount" class="mt-6 space-y-5">
                <div>
                    <label class="text-sm font-medium text-slate-700 dark:text-slate-200">
                        Nama
                    </label>

                    <input
                        wire:model="name"
                        type="text"
                        class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:border-[#7fac9f] dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                    >

                    @error('name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-700 dark:text-slate-200">
                        Email
                    </label>

                    <input
                        wire:model="email"
                        type="email"
                        class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:border-[#7fac9f] dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                    >

                    @error('email')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end">
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="updateAccount"
                        class="rounded-xl bg-[#7fac9f] px-5 py-3 text-sm font-semibold text-white hover:bg-[#6c9a8e] disabled:opacity-60"
                    >
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        {{-- Password --}}
        <div class="rounded-[28px] bg-white p-6 shadow-[0_20px_60px_rgba(15,23,42,0.06)] dark:bg-slate-900 dark:shadow-none lg:p-8">
            <div>
                <h2 class="text-2xl font-bold text-slate-950 dark:text-white">
                    Ubah Password
                </h2>

                <p class="mt-2 text-sm text-gray-500 dark:text-slate-400">
                    Gunakan password yang kuat agar akun tetap aman.
                </p>
            </div>

            @if (session('password_success'))
                <div class="mt-5 rounded-2xl bg-[#eef5f2] px-5 py-4 text-sm font-medium text-[#2f6f61] dark:bg-emerald-950 dark:text-emerald-200">
                    {{ session('password_success') }}
                </div>
            @endif

            <form wire:submit.prevent="updatePassword" class="mt-6 space-y-5">
                <div>
                    <label class="text-sm font-medium text-slate-700 dark:text-slate-200">
                        Password Lama
                    </label>

                    <input
                        wire:model="currentPassword"
                        type="password"
                        autocomplete="current-password"
                        class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:border-[#7fac9f] dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                    >

                    @error('currentPassword')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-700 dark:text-slate-200">
                        Password Baru
                    </label>

                    <input
                        wire:model="newPassword"
                        type="password"
                        autocomplete="new-password"
                        class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:border-[#7fac9f] dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                    >

                    @error('newPassword')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-700 dark:text-slate-200">
                        Konfirmasi Password Baru
                    </label>

                    <input
                        wire:model="newPasswordConfirmation"
                        type="password"
                        autocomplete="new-password"
                        class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:border-[#7fac9f] dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                    >

                    @error('newPasswordConfirmation')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end">
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="updatePassword"
                        class="rounded-xl bg-[#7fac9f] px-5 py-3 text-sm font-semibold text-white hover:bg-[#6c9a8e] disabled:opacity-60"
                    >
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </section>

    {{-- Help Card --}}
    <section class="rounded-[28px] bg-[#eef5f2] p-6 dark:bg-slate-800 lg:p-8">
        <h2 class="text-xl font-bold text-slate-950 dark:text-white">
            Catatan Keamanan
        </h2>

        <p class="mt-3 max-w-3xl text-sm leading-relaxed text-gray-600 dark:text-slate-300">
            Setelah mengganti password, gunakan password baru saat login berikutnya.
            Jika email diganti, pastikan email tersebut aktif agar nanti bisa digunakan
            untuk fitur pemulihan akun.
        </p>
    </section>
</div>
