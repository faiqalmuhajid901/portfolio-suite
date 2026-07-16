<div class="space-y-8">
    {{-- Header --}}
    <section
        class="flex flex-col gap-5 rounded-[28px] bg-white p-6 shadow-sm
               dark:bg-slate-900 sm:p-8 lg:flex-row lg:items-center
               lg:justify-between"
    >
        <div>
            <p
                class="text-sm font-semibold uppercase tracking-[0.2em]
                       text-[#2f6f61] dark:text-emerald-300"
            >
                Public Profile
            </p>

            <h1
                class="mt-3 text-3xl font-bold text-slate-950
                       dark:text-white sm:text-4xl"
            >
                Manage About Me
            </h1>

            <p
                class="mt-3 max-w-2xl text-sm leading-7 text-gray-500
                       dark:text-slate-400"
            >
                Kelola biodata, deskripsi profesional, pendidikan,
                tautan profesional, dan informasi yang ditampilkan
                pada halaman publik.
            </p>
        </div>

        <a
            href="{{ route('home') }}#about"
            target="_blank"
            rel="noopener noreferrer"
            class="inline-flex items-center justify-center rounded-xl
                   border border-[#7fac9f] px-5 py-3 text-sm font-semibold
                   text-[#2f6f61] transition hover:bg-[#eef5f2]
                   dark:text-emerald-300 dark:hover:bg-slate-800"
        >
            Preview About Me
        </a>
    </section>

    @if (session('profile_success'))
        <div
            class="rounded-2xl border border-emerald-200 bg-emerald-50
                   px-5 py-4 text-sm font-medium text-emerald-700
                   dark:border-emerald-900 dark:bg-emerald-950
                   dark:text-emerald-300"
        >
            {{ session('profile_success') }}
        </div>
    @endif

    {{-- Profile form --}}
    <form
        wire:submit="saveProfile"
        class="space-y-8"
    >
        <section
            class="rounded-[28px] bg-white p-6 shadow-sm
                   dark:bg-slate-900 sm:p-8"
        >
            <div
                class="flex flex-col gap-5 border-b border-gray-100 pb-7
                       dark:border-slate-800 sm:flex-row sm:items-center"
            >
                <div
                    class="flex h-20 w-20 shrink-0 items-center justify-center
                           overflow-hidden rounded-full bg-[#2f6f61]
                           text-2xl font-bold text-white"
                >
                    @if ($profile->avatar)
                        <img
                            src="{{ asset($profile->avatar) }}"
                            alt="{{ $name }}"
                            class="h-full w-full object-cover"
                        >
                    @else
                        {{ strtoupper(substr($name ?: 'A', 0, 1)) }}
                    @endif
                </div>

                <div>
                    <h2
                        class="text-2xl font-bold text-slate-950
                               dark:text-white"
                    >
                        Informasi Utama
                    </h2>

                    <p
                        class="mt-2 text-sm leading-6 text-gray-500
                               dark:text-slate-400"
                    >
                        Informasi ini akan menjadi identitas utama
                        pada section About Me.
                    </p>
                </div>
            </div>

            <div class="mt-7 grid gap-6 md:grid-cols-2">
                <div>
                    <label
                        for="name"
                        class="text-sm font-semibold text-slate-700
                               dark:text-slate-200"
                    >
                        Nama lengkap
                    </label>

                    <input
                        id="name"
                        wire:model="name"
                        type="text"
                        class="mt-2 w-full rounded-xl border border-gray-200
                               bg-white px-4 py-3 text-sm outline-none
                               transition focus:border-[#7fac9f]
                               dark:border-slate-700 dark:bg-slate-800
                               dark:text-white"
                    >

                    @error('name')
                        <p class="mt-2 text-xs text-red-500">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label
                        for="role"
                        class="text-sm font-semibold text-slate-700
                               dark:text-slate-200"
                    >
                        Profesi atau role
                    </label>

                    <input
                        id="role"
                        wire:model="role"
                        type="text"
                        placeholder="Contoh: Full Stack Web Developer"
                        class="mt-2 w-full rounded-xl border border-gray-200
                               bg-white px-4 py-3 text-sm outline-none
                               transition focus:border-[#7fac9f]
                               dark:border-slate-700 dark:bg-slate-800
                               dark:text-white"
                    >

                    @error('role')
                        <p class="mt-2 text-xs text-red-500">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label
                        for="birthDate"
                        class="text-sm font-semibold text-slate-700
                               dark:text-slate-200"
                    >
                        Tanggal lahir
                    </label>

                    <input
                        id="birthDate"
                        wire:model="birthDate"
                        type="date"
                        class="mt-2 w-full rounded-xl border border-gray-200
                               bg-white px-4 py-3 text-sm outline-none
                               transition focus:border-[#7fac9f]
                               dark:border-slate-700 dark:bg-slate-800
                               dark:text-white"
                    >

                    <p
                        class="mt-2 text-xs text-gray-400
                               dark:text-slate-500"
                    >
                        Tanggal lengkap tidak ditampilkan. Sistem hanya
                        menghitung umur.
                    </p>

                    @error('birthDate')
                        <p class="mt-2 text-xs text-red-500">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label
                        for="domicile"
                        class="text-sm font-semibold text-slate-700
                               dark:text-slate-200"
                    >
                        Domisili
                    </label>

                    <input
                        id="domicile"
                        wire:model="domicile"
                        type="text"
                        placeholder="Contoh: Surabaya, Jawa Timur"
                        class="mt-2 w-full rounded-xl border border-gray-200
                               bg-white px-4 py-3 text-sm outline-none
                               transition focus:border-[#7fac9f]
                               dark:border-slate-700 dark:bg-slate-800
                               dark:text-white"
                    >

                    @error('domicile')
                        <p class="mt-2 text-xs text-red-500">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label
                        for="publicEmail"
                        class="text-sm font-semibold text-slate-700
                               dark:text-slate-200"
                    >
                        Email publik
                    </label>

                    <input
                        id="publicEmail"
                        wire:model="publicEmail"
                        type="email"
                        placeholder="nama@email.com"
                        class="mt-2 w-full rounded-xl border border-gray-200
                               bg-white px-4 py-3 text-sm outline-none
                               transition focus:border-[#7fac9f]
                               dark:border-slate-700 dark:bg-slate-800
                               dark:text-white"
                    >

                    @error('publicEmail')
                        <p class="mt-2 text-xs text-red-500">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label
                        for="professionalStatus"
                        class="text-sm font-semibold text-slate-700
                               dark:text-slate-200"
                    >
                        Status profesional
                    </label>

                    <input
                        id="professionalStatus"
                        wire:model="professionalStatus"
                        type="text"
                        placeholder="Contoh: Open to Work"
                        class="mt-2 w-full rounded-xl border border-gray-200
                               bg-white px-4 py-3 text-sm outline-none
                               transition focus:border-[#7fac9f]
                               dark:border-slate-700 dark:bg-slate-800
                               dark:text-white"
                    >

                    @error('professionalStatus')
                        <p class="mt-2 text-xs text-red-500">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label
                        for="workPreference"
                        class="text-sm font-semibold text-slate-700
                               dark:text-slate-200"
                    >
                        Preferensi kerja
                    </label>

                    <input
                        id="workPreference"
                        wire:model="workPreference"
                        type="text"
                        placeholder="Contoh: On-site, Hybrid, atau Remote"
                        class="mt-2 w-full rounded-xl border border-gray-200
                               bg-white px-4 py-3 text-sm outline-none
                               transition focus:border-[#7fac9f]
                               dark:border-slate-700 dark:bg-slate-800
                               dark:text-white"
                    >

                    @error('workPreference')
                        <p class="mt-2 text-xs text-red-500">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label
                        for="bio"
                        class="text-sm font-semibold text-slate-700
                               dark:text-slate-200"
                    >
                        Bio singkat
                    </label>

                    <textarea
                        id="bio"
                        wire:model="bio"
                        rows="4"
                        placeholder="Ringkasan singkat tentang diri dan fokus profesional."
                        class="mt-2 w-full rounded-xl border border-gray-200
                               bg-white px-4 py-3 text-sm leading-7
                               outline-none transition
                               focus:border-[#7fac9f]
                               dark:border-slate-700 dark:bg-slate-800
                               dark:text-white"
                    ></textarea>

                    @error('bio')
                        <p class="mt-2 text-xs text-red-500">
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>
        </section>

        {{-- About description --}}
        <section
            class="rounded-[28px] bg-white p-6 shadow-sm
                   dark:bg-slate-900 sm:p-8"
        >
            <h2
                class="text-2xl font-bold text-slate-950
                       dark:text-white"
            >
                Deskripsi About Me
            </h2>

            <p
                class="mt-2 text-sm text-gray-500
                       dark:text-slate-400"
            >
                Jelaskan latar belakang, kemampuan, dan nilai profesional
                yang Anda tawarkan.
            </p>

            <div class="mt-7 space-y-6">
                <div>
                    <label
                        for="aboutTitle"
                        class="text-sm font-semibold text-slate-700
                               dark:text-slate-200"
                    >
                        Judul section
                    </label>

                    <input
                        id="aboutTitle"
                        wire:model="aboutTitle"
                        type="text"
                        placeholder="Contoh: Building useful digital solutions"
                        class="mt-2 w-full rounded-xl border border-gray-200
                               bg-white px-4 py-3 text-sm outline-none
                               transition focus:border-[#7fac9f]
                               dark:border-slate-700 dark:bg-slate-800
                               dark:text-white"
                    >

                    @error('aboutTitle')
                        <p class="mt-2 text-xs text-red-500">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label
                        for="aboutDescription"
                        class="text-sm font-semibold text-slate-700
                               dark:text-slate-200"
                    >
                        Deskripsi lengkap
                    </label>

                    <textarea
                        id="aboutDescription"
                        wire:model="aboutDescription"
                        rows="7"
                        placeholder="Jelaskan perjalanan, minat, keahlian, dan tujuan profesional."
                        class="mt-2 w-full rounded-xl border border-gray-200
                               bg-white px-4 py-3 text-sm leading-7
                               outline-none transition
                               focus:border-[#7fac9f]
                               dark:border-slate-700 dark:bg-slate-800
                               dark:text-white"
                    ></textarea>

                    @error('aboutDescription')
                        <p class="mt-2 text-xs text-red-500">
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>
        </section>

        {{-- Additional information --}}
        <section
            class="grid gap-6 lg:grid-cols-2"
        >
            <div
                class="rounded-[28px] bg-white p-6 shadow-sm
                       dark:bg-slate-900 sm:p-8"
            >
                <h2
                    class="text-xl font-bold text-slate-950
                           dark:text-white"
                >
                    Bahasa
                </h2>

                <p
                    class="mt-2 text-sm text-gray-500
                           dark:text-slate-400"
                >
                    Masukkan satu bahasa pada setiap baris.
                </p>

                <textarea
                    wire:model="languagesText"
                    rows="6"
                    placeholder="Bahasa Indonesia&#10;English"
                    class="mt-6 w-full rounded-xl border border-gray-200
                           bg-white px-4 py-3 text-sm leading-7
                           outline-none transition focus:border-[#7fac9f]
                           dark:border-slate-700 dark:bg-slate-800
                           dark:text-white"
                ></textarea>

                @error('languagesText')
                    <p class="mt-2 text-xs text-red-500">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div
                class="rounded-[28px] bg-white p-6 shadow-sm
                       dark:bg-slate-900 sm:p-8"
            >
                <h2
                    class="text-xl font-bold text-slate-950
                           dark:text-white"
                >
                    Current Focus
                </h2>

                <p
                    class="mt-2 text-sm text-gray-500
                           dark:text-slate-400"
                >
                    Masukkan satu fokus pada setiap baris.
                </p>

                <textarea
                    wire:model="currentFocusText"
                    rows="6"
                    placeholder="Laravel Web Development&#10;UI Development&#10;Database Design"
                    class="mt-6 w-full rounded-xl border border-gray-200
                           bg-white px-4 py-3 text-sm leading-7
                           outline-none transition focus:border-[#7fac9f]
                           dark:border-slate-700 dark:bg-slate-800
                           dark:text-white"
                ></textarea>

                @error('currentFocusText')
                    <p class="mt-2 text-xs text-red-500">
                        {{ $message }}
                    </p>
                @enderror
            </div>
        </section>

        {{-- Professional links --}}
        <section
            class="rounded-[28px] bg-white p-6 shadow-sm
                   dark:bg-slate-900 sm:p-8"
        >
            <h2
                class="text-2xl font-bold text-slate-950
                       dark:text-white"
            >
                Professional Links
            </h2>

            <div class="mt-7 grid gap-6 md:grid-cols-2">
                <div>
                    <label
                        for="githubUrl"
                        class="text-sm font-semibold text-slate-700
                               dark:text-slate-200"
                    >
                        GitHub URL
                    </label>

                    <input
                        id="githubUrl"
                        wire:model="githubUrl"
                        type="url"
                        placeholder="https://github.com/username"
                        class="mt-2 w-full rounded-xl border border-gray-200
                               bg-white px-4 py-3 text-sm outline-none
                               focus:border-[#7fac9f]
                               dark:border-slate-700 dark:bg-slate-800
                               dark:text-white"
                    >

                    @error('githubUrl')
                        <p class="mt-2 text-xs text-red-500">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label
                        for="linkedinUrl"
                        class="text-sm font-semibold text-slate-700
                               dark:text-slate-200"
                    >
                        LinkedIn URL
                    </label>

                    <input
                        id="linkedinUrl"
                        wire:model="linkedinUrl"
                        type="url"
                        placeholder="https://linkedin.com/in/username"
                        class="mt-2 w-full rounded-xl border border-gray-200
                               bg-white px-4 py-3 text-sm outline-none
                               focus:border-[#7fac9f]
                               dark:border-slate-700 dark:bg-slate-800
                               dark:text-white"
                    >

                    @error('linkedinUrl')
                        <p class="mt-2 text-xs text-red-500">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label
                        for="cvUrl"
                        class="text-sm font-semibold text-slate-700
                               dark:text-slate-200"
                    >
                        CV URL
                    </label>

                    <input
                        id="cvUrl"
                        wire:model="cvUrl"
                        type="url"
                        placeholder="https://alamat-file-cv.pdf"
                        class="mt-2 w-full rounded-xl border border-gray-200
                               bg-white px-4 py-3 text-sm outline-none
                               focus:border-[#7fac9f]
                               dark:border-slate-700 dark:bg-slate-800
                               dark:text-white"
                    >

                    <p
                        class="mt-2 text-xs text-gray-400
                               dark:text-slate-500"
                    >
                        Masukkan URL Google Drive publik, Vercel Blob,
                        atau penyimpanan file lainnya.
                    </p>

                    @error('cvUrl')
                        <p class="mt-2 text-xs text-red-500">
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>
        </section>

        {{-- Visibility --}}
        <section
            class="flex flex-col gap-5 rounded-[28px] bg-[#2f6f61]
                   p-6 text-white shadow-sm sm:p-8 lg:flex-row
                   lg:items-center lg:justify-between"
        >
            <div>
                <h2 class="text-xl font-bold">
                    Public visibility
                </h2>

                <p class="mt-2 max-w-xl text-sm leading-6 text-white/70">
                    Aktifkan agar data profil ini digunakan pada
                    halaman portfolio publik.
                </p>
            </div>

            <label
                class="flex cursor-pointer items-center gap-3
                       rounded-2xl bg-white/10 px-5 py-4"
            >
                <input
                    wire:model="isPublic"
                    type="checkbox"
                    class="h-5 w-5 rounded border-white/30"
                >

                <span class="text-sm font-semibold">
                    Tampilkan sebagai profil publik
                </span>
            </label>
        </section>

        <div class="flex justify-end">
            <button
                type="submit"
                wire:loading.attr="disabled"
                wire:target="saveProfile"
                class="rounded-xl bg-[#7fac9f] px-7 py-3
                       text-sm font-semibold text-white transition
                       hover:bg-[#6d9b8f] disabled:cursor-wait
                       disabled:opacity-60"
            >
                <span wire:loading.remove wire:target="saveProfile">
                    Simpan About Me
                </span>

                <span wire:loading wire:target="saveProfile">
                    Menyimpan...
                </span>
            </button>
        </div>
    </form>

    {{-- Education --}}
    <section class="grid gap-8 xl:grid-cols-[0.9fr_1.1fr]">
        <form
            wire:submit="saveEducation"
            class="rounded-[28px] bg-white p-6 shadow-sm
                   dark:bg-slate-900 sm:p-8"
        >
            <h2
                class="text-2xl font-bold text-slate-950
                       dark:text-white"
            >
                {{ $editingEducationId
                    ? 'Edit Pendidikan'
                    : 'Tambah Pendidikan' }}
            </h2>

            <div class="mt-7 space-y-5">
                <div>
                    <label
                        class="text-sm font-semibold text-slate-700
                               dark:text-slate-200"
                    >
                        Jenjang
                    </label>

                    <select
                        wire:model="educationLevel"
                        class="mt-2 w-full rounded-xl border border-gray-200
                               bg-white px-4 py-3 text-sm outline-none
                               focus:border-[#7fac9f]
                               dark:border-slate-700 dark:bg-slate-800
                               dark:text-white"
                    >
                        <option value="">Pilih jenjang</option>
                        <option value="SMA/SMK">SMA/SMK</option>
                        <option value="D1">D1</option>
                        <option value="D2">D2</option>
                        <option value="D3">D3</option>
                        <option value="D4">D4</option>
                        <option value="S1">S1</option>
                        <option value="S2">S2</option>
                        <option value="S3">S3</option>
                        <option value="Nonformal">Nonformal</option>
                    </select>

                    @error('educationLevel')
                        <p class="mt-2 text-xs text-red-500">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label
                        class="text-sm font-semibold text-slate-700
                               dark:text-slate-200"
                    >
                        Institusi
                    </label>

                    <input
                        wire:model="educationInstitution"
                        type="text"
                        placeholder="Nama universitas atau sekolah"
                        class="mt-2 w-full rounded-xl border border-gray-200
                               bg-white px-4 py-3 text-sm outline-none
                               focus:border-[#7fac9f]
                               dark:border-slate-700 dark:bg-slate-800
                               dark:text-white"
                    >

                    @error('educationInstitution')
                        <p class="mt-2 text-xs text-red-500">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label
                        class="text-sm font-semibold text-slate-700
                               dark:text-slate-200"
                    >
                        Jurusan
                    </label>

                    <input
                        wire:model="educationMajor"
                        type="text"
                        placeholder="Contoh: Teknik Informatika"
                        class="mt-2 w-full rounded-xl border border-gray-200
                               bg-white px-4 py-3 text-sm outline-none
                               focus:border-[#7fac9f]
                               dark:border-slate-700 dark:bg-slate-800
                               dark:text-white"
                    >

                    @error('educationMajor')
                        <p class="mt-2 text-xs text-red-500">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label
                            class="text-sm font-semibold text-slate-700
                                   dark:text-slate-200"
                        >
                            Tahun mulai
                        </label>

                        <input
                            wire:model="educationStartYear"
                            type="number"
                            min="1900"
                            placeholder="2021"
                            class="mt-2 w-full rounded-xl border
                                   border-gray-200 bg-white px-4 py-3
                                   text-sm outline-none
                                   focus:border-[#7fac9f]
                                   dark:border-slate-700
                                   dark:bg-slate-800 dark:text-white"
                        >

                        @error('educationStartYear')
                            <p class="mt-2 text-xs text-red-500">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label
                            class="text-sm font-semibold text-slate-700
                                   dark:text-slate-200"
                        >
                            Tahun selesai
                        </label>

                        <input
                            wire:model="educationEndYear"
                            type="number"
                            min="1900"
                            placeholder="2025"
                            class="mt-2 w-full rounded-xl border
                                   border-gray-200 bg-white px-4 py-3
                                   text-sm outline-none
                                   focus:border-[#7fac9f]
                                   dark:border-slate-700
                                   dark:bg-slate-800 dark:text-white"
                        >

                        @error('educationEndYear')
                            <p class="mt-2 text-xs text-red-500">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label
                            class="text-sm font-semibold text-slate-700
                                   dark:text-slate-200"
                        >
                            IPK
                        </label>

                        <input
                            wire:model="educationGpa"
                            type="number"
                            min="0"
                            max="4"
                            step="0.01"
                            placeholder="3.75"
                            class="mt-2 w-full rounded-xl border
                                   border-gray-200 bg-white px-4 py-3
                                   text-sm outline-none
                                   focus:border-[#7fac9f]
                                   dark:border-slate-700
                                   dark:bg-slate-800 dark:text-white"
                        >

                        @error('educationGpa')
                            <p class="mt-2 text-xs text-red-500">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label
                            class="text-sm font-semibold text-slate-700
                                   dark:text-slate-200"
                        >
                            Status
                        </label>

                        <input
                            wire:model="educationStatus"
                            type="text"
                            placeholder="Lulus / Mahasiswa aktif"
                            class="mt-2 w-full rounded-xl border
                                   border-gray-200 bg-white px-4 py-3
                                   text-sm outline-none
                                   focus:border-[#7fac9f]
                                   dark:border-slate-700
                                   dark:bg-slate-800 dark:text-white"
                        >

                        @error('educationStatus')
                            <p class="mt-2 text-xs text-red-500">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label
                        class="text-sm font-semibold text-slate-700
                               dark:text-slate-200"
                    >
                        Deskripsi
                    </label>

                    <textarea
                        wire:model="educationDescription"
                        rows="4"
                        placeholder="Fokus studi, aktivitas, atau pencapaian akademik."
                        class="mt-2 w-full rounded-xl border border-gray-200
                               bg-white px-4 py-3 text-sm leading-7
                               outline-none focus:border-[#7fac9f]
                               dark:border-slate-700 dark:bg-slate-800
                               dark:text-white"
                    ></textarea>

                    @error('educationDescription')
                        <p class="mt-2 text-xs text-red-500">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label
                        class="text-sm font-semibold text-slate-700
                               dark:text-slate-200"
                    >
                        Urutan
                    </label>

                    <input
                        wire:model="educationSortOrder"
                        type="number"
                        min="0"
                        class="mt-2 w-full rounded-xl border border-gray-200
                               bg-white px-4 py-3 text-sm outline-none
                               focus:border-[#7fac9f]
                               dark:border-slate-700 dark:bg-slate-800
                               dark:text-white"
                    >

                    @error('educationSortOrder')
                        <p class="mt-2 text-xs text-red-500">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <label
                    class="flex cursor-pointer items-center gap-3
                           rounded-xl bg-[#f5f8f8] px-4 py-4
                           dark:bg-slate-800"
                >
                    <input
                        wire:model="educationIsVisible"
                        type="checkbox"
                        class="h-5 w-5 rounded"
                    >

                    <span
                        class="text-sm font-semibold text-slate-700
                               dark:text-slate-200"
                    >
                        Tampilkan pada halaman publik
                    </span>
                </label>
            </div>

            <div class="mt-7 flex flex-wrap gap-3">
                <button
                    type="submit"
                    class="rounded-xl bg-[#2f6f61] px-5 py-3
                           text-sm font-semibold text-white"
                >
                    {{ $editingEducationId
                        ? 'Perbarui Pendidikan'
                        : 'Tambah Pendidikan' }}
                </button>

                @if ($editingEducationId)
                    <button
                        type="button"
                        wire:click="cancelEducationEdit"
                        class="rounded-xl border border-gray-200 px-5 py-3
                               text-sm font-semibold text-slate-600
                               dark:border-slate-700 dark:text-slate-300"
                    >
                        Batal
                    </button>
                @endif
            </div>
        </form>

        <div
            class="rounded-[28px] bg-white p-6 shadow-sm
                   dark:bg-slate-900 sm:p-8"
        >
            <h2
                class="text-2xl font-bold text-slate-950
                       dark:text-white"
            >
                Riwayat Pendidikan
            </h2>

            @if (session('education_success'))
                <div
                    class="mt-5 rounded-xl bg-emerald-50 px-4 py-3
                           text-sm text-emerald-700
                           dark:bg-emerald-950
                           dark:text-emerald-300"
                >
                    {{ session('education_success') }}
                </div>
            @endif

            <div class="mt-7 space-y-4">
                @forelse ($educations as $education)
                    <article
                        wire:key="education-{{ $education->id }}"
                        class="rounded-2xl border border-gray-100 p-5
                               dark:border-slate-800"
                    >
                        <div
                            class="flex flex-col gap-4 sm:flex-row
                                   sm:items-start sm:justify-between"
                        >
                            <div>
                                <div class="flex flex-wrap gap-2">
                                    <span
                                        class="rounded-full bg-[#eef5f2]
                                               px-3 py-1 text-xs font-semibold
                                               text-[#2f6f61]
                                               dark:bg-emerald-950
                                               dark:text-emerald-300"
                                    >
                                        {{ $education->level }}
                                    </span>

                                    <span
                                        class="rounded-full bg-gray-100
                                               px-3 py-1 text-xs
                                               text-gray-500
                                               dark:bg-slate-800
                                               dark:text-slate-300"
                                    >
                                        {{ $education->is_visible
                                            ? 'Public'
                                            : 'Hidden' }}
                                    </span>
                                </div>

                                <h3
                                    class="mt-4 text-lg font-bold
                                           text-slate-950 dark:text-white"
                                >
                                    {{ $education->institution }}
                                </h3>

                                @if ($education->major)
                                    <p
                                        class="mt-1 text-sm font-medium
                                               text-[#2f6f61]
                                               dark:text-emerald-300"
                                    >
                                        {{ $education->major }}
                                    </p>
                                @endif

                                <p
                                    class="mt-2 text-sm text-gray-500
                                           dark:text-slate-400"
                                >
                                    {{ $education->start_year ?: '?' }}
                                    —
                                    {{ $education->end_year ?: 'Sekarang' }}

                                    @if ($education->gpa)
                                        · IPK {{ $education->gpa }}
                                    @endif
                                </p>

                                @if ($education->description)
                                    <p
                                        class="mt-4 text-sm leading-6
                                               text-gray-600
                                               dark:text-slate-300"
                                    >
                                        {{ $education->description }}
                                    </p>
                                @endif
                            </div>

                            <div class="flex shrink-0 gap-2">
                                <button
                                    type="button"
                                    wire:click="editEducation({{ $education->id }})"
                                    class="rounded-lg border border-[#7fac9f]
                                           px-3 py-2 text-xs font-semibold
                                           text-[#2f6f61]
                                           dark:text-emerald-300"
                                >
                                    Edit
                                </button>

                                <button
                                    type="button"
                                    wire:click="deleteEducation({{ $education->id }})"
                                    wire:confirm="Hapus riwayat pendidikan ini?"
                                    class="rounded-lg border border-red-200
                                           px-3 py-2 text-xs font-semibold
                                           text-red-500
                                           dark:border-red-900"
                                >
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </article>
                @empty
                    <div
                        class="rounded-2xl border border-dashed
                               border-gray-200 px-6 py-12 text-center
                               text-sm text-gray-500
                               dark:border-slate-700
                               dark:text-slate-400"
                    >
                        Belum ada riwayat pendidikan.
                    </div>
                @endforelse
            </div>
        </div>
    </section>
</div>
