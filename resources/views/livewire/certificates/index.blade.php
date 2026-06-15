<div class="space-y-8">
    {{-- Header --}}
    <section class="rounded-[28px] bg-white p-6 shadow-[0_20px_60px_rgba(15,23,42,0.06)] dark:bg-slate-900 dark:shadow-none lg:p-8">
        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-[#2f6f61] dark:text-emerald-300">
            Certificate Manager
        </p>

        <h1 class="mt-4 text-3xl font-bold text-slate-950 dark:text-white sm:text-4xl">
            Certificates
        </h1>

        <p class="mt-3 max-w-2xl text-sm leading-relaxed text-gray-600 dark:text-slate-300 sm:text-base">
            Upload sertifikat dalam format PDF. Sertifikat yang aktif akan tampil di halaman public portfolio sebagai preview gambar.
        </p>
    </section>

    @if (session('success'))
        <div class="rounded-2xl bg-[#eef5f2] px-5 py-4 text-sm font-medium text-[#2f6f61] dark:bg-emerald-950 dark:text-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    {{-- Upload Form --}}
    <section class="rounded-[28px] bg-white p-6 shadow-[0_20px_60px_rgba(15,23,42,0.06)] dark:bg-slate-900 dark:shadow-none lg:p-8">
        <h2 class="text-2xl font-bold text-slate-950 dark:text-white">
            Upload Sertifikat Baru
        </h2>

        <p class="mt-2 text-sm text-gray-500 dark:text-slate-400">
            Gunakan file PDF maksimal 10 MB.
        </p>

        <form wire:submit.prevent="saveCertificate" class="mt-6 grid grid-cols-1 gap-5 lg:grid-cols-2">
            <div>
                <label class="text-sm font-medium text-slate-700 dark:text-slate-200">
                    Judul Sertifikat
                </label>

                <input
                    wire:model="title"
                    type="text"
                    placeholder="Contoh: Laravel Web Development Certificate"
                    class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:border-[#7fac9f] dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                >

                @error('title')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="text-sm font-medium text-slate-700 dark:text-slate-200">
                    Penerbit / Institusi
                </label>

                <input
                    wire:model="issuer"
                    type="text"
                    placeholder="Contoh: Dicoding, Coursera, BNSP"
                    class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:border-[#7fac9f] dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                >

                @error('issuer')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
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
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="text-sm font-medium text-slate-700 dark:text-slate-200">
                    File PDF
                </label>

                <input
                    wire:model="pdfUpload"
                    type="file"
                    accept="application/pdf,.pdf"
                    class="mt-2 w-full text-sm text-slate-700 file:mr-4 file:rounded-xl file:border-0 file:bg-[#7fac9f] file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white dark:text-slate-200"
                >

                <div wire:loading.delay.longer wire:target="pdfUpload" class="mt-2 text-sm text-[#2f6f61] dark:text-emerald-300">
                    Sedang menyiapkan file PDF...
                </div>

                @if ($pdfUpload)
                    <p class="mt-2 text-xs font-medium text-[#2f6f61] dark:text-emerald-300">
                        File dipilih: {{ $pdfUpload->getClientOriginalName() }}
                    </p>
                @endif

                @error('pdfUpload')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
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
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="lg:col-span-2 flex items-center justify-between gap-4 rounded-2xl bg-[#f5f8f8] p-4 dark:bg-slate-800">
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
                    wire:loading.attr="disabled"
                    wire:target="saveCertificate,pdfUpload"
                    class="rounded-xl bg-[#7fac9f] px-5 py-3 text-sm font-semibold text-white disabled:opacity-60"
                >
                    <span wire:loading.remove wire:target="saveCertificate">
                        Simpan Sertifikat
                    </span>

                    <span wire:loading wire:target="saveCertificate">
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

            <input
                wire:model.live.debounce.400ms="search"
                type="text"
                placeholder="Cari sertifikat..."
                class="rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:border-[#7fac9f] dark:border-slate-700 dark:bg-slate-950 dark:text-white"
            >
        </div>

        <div class="grid grid-cols-1 gap-5 p-6 pt-0 md:grid-cols-2 xl:grid-cols-3 lg:p-8 lg:pt-0">
            @forelse ($certificates as $certificate)
                <div class="rounded-[24px] border border-gray-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-950">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-bold text-slate-950 dark:text-white">
                                {{ $certificate->title }}
                            </h3>

                            <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">
                                {{ $certificate->issuer ?: 'Tanpa penerbit' }}
                            </p>
                        </div>

                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $certificate->is_visible ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-gray-100 text-gray-500 dark:bg-slate-800 dark:text-slate-400' }}">
                            {{ $certificate->is_visible ? 'Visible' : 'Hidden' }}
                        </span>
                    </div>

                    <p class="mt-4 text-sm leading-relaxed text-gray-600 dark:text-slate-300">
                        {{ $certificate->description ?: 'Tidak ada deskripsi.' }}
                    </p>

                    <p class="mt-4 text-xs text-gray-500 dark:text-slate-400">
                        {{ $certificate->issued_at ? $certificate->issued_at->format('d M Y') : 'Tanggal tidak diisi' }}
                    </p>

                    <div class="mt-5 flex flex-wrap gap-3">
                        <a
                            href="{{ asset($certificate->pdf_path) }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="rounded-xl border border-[#7fac9f] px-4 py-2 text-sm font-semibold text-[#2f6f61] dark:text-emerald-300"
                        >
                            Lihat PDF
                        </a>

                        <button
                            wire:click="toggleVisibility({{ $certificate->id }})"
                            class="rounded-xl bg-[#eef5f2] px-4 py-2 text-sm font-semibold text-[#2f6f61] dark:bg-emerald-950 dark:text-emerald-300"
                        >
                            {{ $certificate->is_visible ? 'Sembunyikan' : 'Tampilkan' }}
                        </button>

                        <button
                            wire:click="deleteCertificate({{ $certificate->id }})"
                            wire:confirm="Hapus sertifikat ini?"
                            class="rounded-xl px-4 py-2 text-sm font-semibold text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/40"
                        >
                            Hapus
                        </button>
                    </div>
                </div>
            @empty
                <div class="rounded-[24px] border border-dashed border-gray-300 p-8 text-center text-gray-500 dark:border-slate-700 dark:text-slate-400 md:col-span-2 xl:col-span-3">
                    Belum ada sertifikat.
                </div>
            @endforelse
        </div>

        <div class="border-t border-gray-100 p-6 dark:border-slate-800">
            {{ $certificates->links() }}
        </div>
    </section>
</div>
