<div class="space-y-8">
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
    <section class="rounded-[28px] bg-white p-6 shadow-[0_20px_60px_rgba(15,23,42,0.06)] dark:bg-slate-900 dark:shadow-none lg:p-8">
        <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
            <div class="flex items-center gap-5">
                @if (auth()->user()?->profile?->avatar)
                    <img
                        src="{{ asset(auth()->user()->profile->avatar) }}"
                        alt="{{ auth()->user()->name }}"
                        class="h-24 w-24 rounded-full object-cover ring-4 ring-[#eef5f2] dark:ring-slate-800"
                    >
                @else
                    <div class="flex h-24 w-24 items-center justify-center rounded-full bg-[#2f6f61] text-3xl font-bold text-white ring-4 ring-[#eef5f2] dark:ring-slate-800">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </div>
                @endif

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
                    wire:model="photoUpload"
                    type="file"
                    accept="image/*"
                    class="w-full text-sm text-slate-700 file:mr-4 file:rounded-xl file:border-0 file:bg-[#7fac9f] file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white dark:text-slate-200"
                >

                <p class="mt-2 text-xs text-gray-500 dark:text-slate-400">
                    Format boleh JPG, PNG, GIF, WEBP, atau BMP. Sistem akan menyimpan foto sebagai .webp.
                </p>

                <div wire:loading wire:target="photoUpload" class="mt-3 text-sm text-[#2f6f61] dark:text-emerald-300">
                    Uploading preview...
                </div>

                @if ($photoUpload)
                    <div class="mt-4">
                        <p class="mb-2 text-xs font-medium text-gray-500 dark:text-slate-400">
                            Preview:
                        </p>

                        <img
                            src="{{ $photoUpload->temporaryUrl() }}"
                            alt="Preview foto profil"
                            class="h-32 w-32 rounded-full object-cover"
                        >
                    </div>
                @endif

                @error('photoUpload')
                    <p class="mt-2 text-xs text-red-500">{{ $message }}</p>
                @enderror

                <button
                    wire:click="updateProfilePhoto"
                    wire:loading.attr="disabled"
                    wire:target="updateProfilePhoto,photoUpload"
                    class="mt-5 rounded-xl bg-[#7fac9f] px-5 py-3 text-sm font-semibold text-white disabled:opacity-60"
                >
                    Simpan Foto Profil
                </button>
            </div>
        </div>
    </section>

    {{-- Public Hero Background --}}
    <section class="rounded-[28px] bg-white p-6 shadow-[0_20px_60px_rgba(15,23,42,0.06)] dark:bg-slate-900 dark:shadow-none lg:p-8">
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

        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div>
                <input
                    wire:model="heroBackgroundUpload"
                    type="file"
                    accept="image/*"
                    class="w-full text-sm text-slate-700 file:mr-4 file:rounded-xl file:border-0 file:bg-[#7fac9f] file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white dark:text-slate-200"
                >

                <p class="mt-2 text-xs text-gray-500 dark:text-slate-400">
                    Format boleh JPG, PNG, GIF, WEBP, atau BMP. Sistem akan menyimpan sebagai .webp.
                </p>

                <div wire:loading wire:target="heroBackgroundUpload" class="mt-3 text-sm text-[#2f6f61] dark:text-emerald-300">
                    Uploading preview...
                </div>

                @if ($heroBackgroundUpload)
                    <div class="mt-4 overflow-hidden rounded-2xl border border-gray-100 dark:border-slate-800">
                        <img
                            src="{{ $heroBackgroundUpload->temporaryUrl() }}"
                            alt="Preview background"
                            class="h-56 w-full object-cover"
                        >
                    </div>
                @endif

                @error('heroBackgroundUpload')
                    <p class="mt-2 text-xs text-red-500">{{ $message }}</p>
                @enderror

                <button
                    wire:click="updateHeroBackground"
                    wire:loading.attr="disabled"
                    wire:target="updateHeroBackground,heroBackgroundUpload"
                    class="mt-5 rounded-xl bg-[#7fac9f] px-5 py-3 text-sm font-semibold text-white disabled:opacity-60"
                >
                    Simpan Background Public
                </button>
            </div>

            <div>
                <p class="mb-2 text-sm font-medium text-slate-700 dark:text-slate-200">
                    Background Saat Ini
                </p>

                @if (auth()->user()?->profile?->hero_background)
                    <div class="overflow-hidden rounded-2xl border border-gray-100 dark:border-slate-800">
                        <img
                            src="{{ asset(auth()->user()->profile->hero_background) }}"
                            alt="Current hero background"
                            class="h-56 w-full object-cover"
                        >
                    </div>
                @else
                    <div class="flex h-56 items-center justify-center rounded-2xl border border-dashed border-gray-300 text-sm text-gray-500 dark:border-slate-700 dark:text-slate-400">
                        Belum ada background.
                    </div>
                @endif
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
                    class="rounded-xl bg-[#7fac9f] px-5 py-3 text-sm font-semibold text-white hover:bg-[#6c9a8e]"
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
                        class="rounded-xl bg-[#7fac9f] px-5 py-3 text-sm font-semibold text-white hover:bg-[#6c9a8e]"
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
                        class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:border-[#7fac9f] dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                    >

                    @error('newPasswordConfirmation')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end">
                    <button
                        type="submit"
                        class="rounded-xl bg-[#7fac9f] px-5 py-3 text-sm font-semibold text-white hover:bg-[#6c9a8e]"
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
            Jika email diganti, pastikan email tersebut aktif agar nanti bisa digunakan untuk fitur pemulihan akun.
        </p>
    </section>
</div>