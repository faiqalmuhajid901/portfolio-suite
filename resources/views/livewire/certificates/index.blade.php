<div
    x-data="certificatePdfUploader()"
    x-on:certificate-saved.window="resetUploader()"
    class="space-y-8"
>
    <script>
        if (typeof window.certificatePdfUploader !== 'function') {
            window.certificatePdfUploader = function () {
                return {
                    uploading: false,
                    progress: 0,
                    error: '',
                    ready: false,
                    fileName: '',
                    fileSize: '',
                    selectedFile: null,

                    firstError(payload, fallback) {
                        if (payload?.message) {
                            return payload.message;
                        }

                        return fallback;
                    },

                    formatBytes(bytes) {
                        if (!Number.isFinite(bytes) || bytes <= 0) {
                            return '';
                        }

                        const units = ['B', 'KB', 'MB', 'GB'];
                        const index = Math.min(
                            Math.floor(Math.log(bytes) / Math.log(1024)),
                            units.length - 1
                        );

                        const value = bytes / Math.pow(1024, index);

                        return `${value.toFixed(index === 0 ? 0 : 2)} ${units[index]}`;
                    },

                    validateFile(file) {
                        const allowedTypes = [
                            'application/pdf',
                            'application/x-pdf',
                        ];

                        const isPdfName = file.name
                            .toLowerCase()
                            .endsWith('.pdf');

                        if (
                            !allowedTypes.includes(file.type)
                            || !isPdfName
                        ) {
                            throw new Error(
                                'Format file harus PDF.'
                            );
                        }

                        if (file.size > 10 * 1024 * 1024) {
                            throw new Error(
                                'Ukuran file PDF maksimal 10 MB.'
                            );
                        }
                    },

                    uploadToSupabase(signedUrl, file) {
                        return new Promise((resolve, reject) => {
                            const xhr = new XMLHttpRequest();

                            xhr.open('PUT', signedUrl, true);
                            xhr.setRequestHeader(
                                'x-upsert',
                                'false'
                            );

                            xhr.upload.onprogress = (event) => {
                                if (event.lengthComputable) {
                                    this.progress = Math.round(
                                        (
                                            event.loaded
                                            / event.total
                                        ) * 100
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
                                    const payload = JSON.parse(
                                        xhr.responseText
                                    );

                                    message =
                                        payload.message
                                        || message;
                                } catch (exception) {
                                    if (xhr.responseText) {
                                        message =
                                            xhr.responseText;
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

                            body.append(
                                'cacheControl',
                                '3600'
                            );

                            body.append('', file);

                            xhr.send(body);
                        });
                    },

                    async choosePdf(event) {
                        const input = event.target;
                        const file = input.files?.[0];

                        this.error = '';
                        this.progress = 0;
                        this.ready = false;
                        this.selectedFile = null;
                        this.fileName = '';
                        this.fileSize = '';

                        await this.$wire.clearUploadedPdf();

                        if (!file) {
                            return;
                        }

                        try {
                            this.validateFile(file);

                            this.selectedFile = file;
                            this.fileName = file.name;
                            this.fileSize =
                                this.formatBytes(file.size);
                            this.uploading = true;

                            const signed =
                                await this.$wire
                                    .createCertificatePdfUpload(
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
                            ) {
                                throw new Error(
                                    'Respons signed upload URL tidak lengkap.'
                                );
                            }

                            await this.uploadToSupabase(
                                signed.signed_url,
                                file
                            );

                            const stored =
                                await this.$wire.setUploadedPdf(
                                    signed.path,
                                    file.name
                                );

                            if (!stored?.ok) {
                                throw new Error(
                                    this.firstError(
                                        stored,
                                        'Gagal menyimpan path file PDF.'
                                    )
                                );
                            }

                            this.progress = 100;
                            this.ready = true;
                        } catch (exception) {
                            await this.$wire.clearUploadedPdf();

                            this.selectedFile = null;
                            this.fileName = '';
                            this.fileSize = '';
                            this.ready = false;
                            this.error =
                                exception?.message
                                || 'Upload file PDF gagal.';

                            input.value = '';
                        } finally {
                            this.uploading = false;
                        }
                    },

                    async removePdf() {
                        if (this.uploading) {
                            return;
                        }

                        await this.$wire.clearUploadedPdf();

                        this.resetUploader();
                    },

                    resetUploader() {
                        this.uploading = false;
                        this.progress = 0;
                        this.error = '';
                        this.ready = false;
                        this.fileName = '';
                        this.fileSize = '';
                        this.selectedFile = null;

                        if (this.$refs.pdfInput) {
                            this.$refs.pdfInput.value = '';
                        }
                    },
                };
            };
        }
    </script>

    {{-- Header --}}
    <section class="rounded-[28px] bg-white p-6 shadow-[0_20px_60px_rgba(15,23,42,0.06)] dark:bg-slate-900 dark:shadow-none lg:p-8">
        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-[#2f6f61] dark:text-emerald-300">
            Certificate Manager
        </p>

        <h1 class="mt-4 text-3xl font-bold text-slate-950 dark:text-white sm:text-4xl">
            Certificates
        </h1>

        <p class="mt-3 max-w-2xl text-sm leading-relaxed text-gray-600 dark:text-slate-300 sm:text-base">
            Upload sertifikat dalam format PDF. Sertifikat yang aktif akan tampil di halaman public portfolio.
        </p>
    </section>

    @if (session('success'))
        <div class="rounded-2xl bg-[#eef5f2] px-5 py-4 text-sm font-medium text-[#2f6f61] dark:bg-emerald-950 dark:text-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="rounded-2xl bg-red-50 px-5 py-4 text-sm font-medium text-red-700 dark:bg-red-950 dark:text-red-200">
            {{ session('error') }}
        </div>
    @endif

    {{-- Upload Form --}}
    <section class="rounded-[28px] bg-white p-6 shadow-[0_20px_60px_rgba(15,23,42,0.06)] dark:bg-slate-900 dark:shadow-none lg:p-8">
        <h2 class="text-2xl font-bold text-slate-950 dark:text-white">
            Upload Sertifikat Baru
        </h2>

        <p class="mt-2 text-sm text-gray-500 dark:text-slate-400">
            Gunakan file PDF maksimal 10 MB. File dikirim langsung dari browser ke Supabase dan tidak melewati temporary upload Vercel.
        </p>

        <form
            wire:submit.prevent="saveCertificate"
            class="mt-6 grid grid-cols-1 gap-5 lg:grid-cols-2"
        >
            <div>
                <label class="text-sm font-medium text-slate-700 dark:text-slate-200">
                    Judul Sertifikat
                </label>

                <input
                    wire:model="title"
                    type="text"
                    placeholder="Contoh: Laravel Developer Certificate"
                    class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:border-[#7fac9f] dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                >

                @error('title')
                    <p class="mt-1 text-xs text-red-500">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label class="text-sm font-medium text-slate-700 dark:text-slate-200">
                    Penerbit / Institusi
                </label>

                <input
                    wire:model="issuer"
                    type="text"
                    placeholder="Contoh: Dicoding Indonesia"
                    class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:border-[#7fac9f] dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                >

                @error('issuer')
                    <p class="mt-1 text-xs text-red-500">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label class="text-sm font-medium text-slate-700 dark:text-slate-200">
                    Tanggal Terbit
                </label>

                <input
                    wire:model="issuedAt"
                    type="date"
                    class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:border-[#7fac9f] dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                >

                @error('issuedAt')
                    <p class="mt-1 text-xs text-red-500">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label class="text-sm font-medium text-slate-700 dark:text-slate-200">
                    File PDF
                </label>

                <input
                    x-ref="pdfInput"
                    x-on:change="choosePdf($event)"
                    x-bind:disabled="uploading"
                    type="file"
                    accept=".pdf,application/pdf"
                    class="mt-2 w-full text-sm text-slate-700 file:mr-4 file:rounded-xl file:border-0 file:bg-[#7fac9f] file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white disabled:cursor-not-allowed disabled:opacity-60 dark:text-slate-200"
                >

                <div
                    x-show="uploading"
                    x-cloak
                    class="mt-3"
                >
                    <div class="flex items-center justify-between text-sm text-[#2f6f61] dark:text-emerald-300">
                        <span>Mengunggah PDF langsung ke Supabase...</span>
                        <span x-text="`${progress}%`"></span>
                    </div>

                    <div class="mt-2 h-2 overflow-hidden rounded-full bg-gray-200 dark:bg-slate-700">
                        <div
                            class="h-full rounded-full bg-[#7fac9f] transition-all"
                            x-bind:style="`width: ${progress}%`"
                        ></div>
                    </div>
                </div>

                <div
                    x-show="ready && !uploading"
                    x-cloak
                    class="mt-3 rounded-xl bg-[#eef5f2] px-4 py-3 dark:bg-emerald-950"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-[#2f6f61] dark:text-emerald-200"
                               x-text="fileName">
                            </p>

                            <p class="mt-1 text-xs text-gray-500 dark:text-slate-400">
                                <span x-text="fileSize"></span>
                                — siap disimpan
                            </p>
                        </div>

                        <button
                            type="button"
                            x-on:click="removePdf()"
                            class="shrink-0 text-xs font-semibold text-red-600 hover:underline dark:text-red-300"
                        >
                            Batalkan
                        </button>
                    </div>
                </div>

                <p
                    x-show="error"
                    x-cloak
                    x-text="error"
                    class="mt-2 text-xs text-red-500"
                ></p>

                @error('uploadedPdfPath')
                    <p class="mt-1 text-xs text-red-500">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="lg:col-span-2">
                <label class="text-sm font-medium text-slate-700 dark:text-slate-200">
                    Deskripsi Singkat
                </label>

                <textarea
                    wire:model="description"
                    rows="4"
                    placeholder="Tuliskan deskripsi singkat sertifikat ini."
                    class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:border-[#7fac9f] dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                ></textarea>

                @error('description')
                    <p class="mt-1 text-xs text-red-500">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="flex items-center justify-between gap-4 rounded-2xl bg-[#f5f8f8] p-4 dark:bg-slate-800 lg:col-span-2">
                <label class="flex items-center gap-3 text-sm font-medium text-slate-700 dark:text-slate-200">
                    <input
                        wire:model="isVisible"
                        type="checkbox"
                        class="rounded border-gray-300 text-[#7fac9f] focus:ring-[#7fac9f]"
                    >

                    Tampilkan di halaman public
                </label>

                <button
                    type="submit"
                    x-bind:disabled="!ready || uploading"
                    wire:loading.attr="disabled"
                    wire:target="saveCertificate"
                    class="rounded-xl bg-[#7fac9f] px-5 py-3 text-sm font-semibold text-white disabled:cursor-not-allowed disabled:opacity-60"
                >
                    <span
                        wire:loading.remove
                        wire:target="saveCertificate"
                    >
                        Simpan Sertifikat
                    </span>

                    <span
                        wire:loading
                        wire:target="saveCertificate"
                    >
                        Menyimpan...
                    </span>
                </button>
            </div>
        </form>
    </section>

    {{-- List --}}
    <section class="rounded-[28px] bg-white shadow-[0_20px_60px_rgba(15,23,42,0.06)] dark:bg-slate-900 dark:shadow-none">
        <div class="flex flex-col gap-4 p-6 lg:flex-row lg:items-center lg:justify-between lg:p-8">
            <div>
                <h2 class="text-2xl font-bold text-slate-950 dark:text-white">
                    Daftar Sertifikat
                </h2>

                <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">
                    Kelola sertifikat yang akan ditampilkan di landing page.
                </p>
            </div>

            <div class="w-full lg:w-80">
                <input
                    wire:model.live.debounce.350ms="search"
                    type="search"
                    placeholder="Cari sertifikat..."
                    class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:border-[#7fac9f] dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                >
            </div>
        </div>

        <div class="border-t border-gray-100 dark:border-slate-800">
            @forelse ($certificates as $certificate)
                <article
                    wire:key="certificate-{{ $certificate->id }}"
                    class="border-b border-gray-100 p-6 last:border-b-0 dark:border-slate-800 lg:p-8"
                >
                    <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-3">
                                <h3 class="text-xl font-bold text-slate-950 dark:text-white">
                                    {{ $certificate->title }}
                                </h3>

                                <span
                                    @class([
                                        'rounded-full px-3 py-1 text-xs font-semibold',
                                        'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-200' =>
                                            $certificate->is_visible,
                                        'bg-gray-100 text-gray-600 dark:bg-slate-800 dark:text-slate-300' =>
                                            ! $certificate->is_visible,
                                    ])
                                >
                                    {{ $certificate->is_visible ? 'Visible' : 'Hidden' }}
                                </span>
                            </div>

                            <p class="mt-2 text-sm font-medium text-[#2f6f61] dark:text-emerald-300">
                                {{ $certificate->issuer ?: 'Tanpa penerbit' }}
                            </p>

                            <p class="mt-3 max-w-3xl text-sm leading-relaxed text-gray-600 dark:text-slate-300">
                                {{ $certificate->description ?: 'Tidak ada deskripsi.' }}
                            </p>

                            <p class="mt-3 text-xs text-gray-500 dark:text-slate-400">
                                {{ $certificate->issued_at
                                    ? $certificate->issued_at->format('d M Y')
                                    : 'Tanggal tidak diisi' }}
                            </p>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <a
                                href="{{ $certificate->pdf_path }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="rounded-xl border border-gray-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
                            >
                                Lihat PDF
                            </a>

                            <button
                                type="button"
                                wire:click="toggleVisibility({{ $certificate->id }})"
                                wire:loading.attr="disabled"
                                wire:target="toggleVisibility({{ $certificate->id }})"
                                class="rounded-xl bg-[#eef5f2] px-4 py-2 text-sm font-semibold text-[#2f6f61] disabled:opacity-60 dark:bg-emerald-950 dark:text-emerald-200"
                            >
                                {{ $certificate->is_visible
                                    ? 'Sembunyikan'
                                    : 'Tampilkan' }}
                            </button>

                            <button
                                type="button"
                                wire:click="deleteCertificate({{ $certificate->id }})"
                                wire:confirm="Hapus sertifikat ini?"
                                wire:loading.attr="disabled"
                                wire:target="deleteCertificate({{ $certificate->id }})"
                                class="rounded-xl bg-red-50 px-4 py-2 text-sm font-semibold text-red-600 disabled:opacity-60 dark:bg-red-950 dark:text-red-300"
                            >
                                Hapus
                            </button>
                        </div>
                    </div>
                </article>
            @empty
                <div class="p-10 text-center text-sm text-gray-500 dark:text-slate-400">
                    Belum ada sertifikat.
                </div>
            @endforelse
        </div>

        @if ($certificates->hasPages())
            <div class="border-t border-gray-100 p-6 dark:border-slate-800 lg:p-8">
                {{ $certificates->links() }}
            </div>
        @endif
    </section>
</div>
