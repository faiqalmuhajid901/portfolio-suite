<div
    class="space-y-8 lg:space-y-10"
    wire:poll.30s="refreshAnalytics"
    x-data="portfolioAnalyticsCharts(@js($chartPayload))"
    x-on:analytics-refreshed.window="updateCharts($event.detail.payload)"
>
    <section class="rounded-[28px] bg-white p-6 shadow-[0_20px_60px_rgba(15,23,42,0.06)] dark:bg-slate-900 lg:p-8">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <span class="text-xs font-semibold uppercase tracking-[0.25em] text-[#2f6f61] dark:text-emerald-300">
                    Portfolio Intelligence
                </span>
                <h1 class="mt-3 text-3xl font-bold sm:text-4xl">Portfolio Analytics</h1>
                <p class="mt-3 max-w-3xl text-sm leading-7 text-gray-600 dark:text-slate-300 sm:text-base">
                    Pantau pengunjung, sumber trafik, halaman terpopuler, perangkat, dan kualitas portfolio.
                    Data statistik diperbarui otomatis setiap 30 detik.
                </p>
            </div>

            <div class="rounded-[22px] bg-[#eef5f2] px-6 py-5 dark:bg-slate-800">
                <p class="text-sm text-gray-500 dark:text-slate-400">Portfolio completeness score</p>
                <div class="mt-2 flex items-end gap-2">
                    <strong class="text-4xl font-bold text-[#2f6f61] dark:text-emerald-300">
                        {{ data_get($quality, 'score', 0) }}
                    </strong>
                    <span class="pb-1 text-sm text-gray-500 dark:text-slate-400">/ 100</span>
                </div>
            </div>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-5">
        <article class="rounded-[24px] bg-[#2f6f61] p-6 text-white shadow-sm">
            <p class="text-sm text-white/75">Online now</p>
            <h2 class="mt-4 text-4xl font-bold">{{ number_format(data_get($summary, 'online_now', 0)) }}</h2>
            <p class="mt-2 text-xs text-white/70">Aktif dalam dua menit terakhir</p>
        </article>

        <article class="rounded-[24px] bg-white p-6 shadow-sm dark:bg-slate-900">
            <p class="text-sm text-gray-500 dark:text-slate-400">Visitors today</p>
            <h2 class="mt-4 text-4xl font-bold">{{ number_format(data_get($summary, 'visitors_today', 0)) }}</h2>
        </article>

        <article class="rounded-[24px] bg-white p-6 shadow-sm dark:bg-slate-900">
            <p class="text-sm text-gray-500 dark:text-slate-400">Views today</p>
            <h2 class="mt-4 text-4xl font-bold">{{ number_format(data_get($summary, 'views_today', 0)) }}</h2>
        </article>

        <article class="rounded-[24px] bg-white p-6 shadow-sm dark:bg-slate-900">
            <p class="text-sm text-gray-500 dark:text-slate-400">Views — 7 days</p>
            <h2 class="mt-4 text-4xl font-bold">{{ number_format(data_get($summary, 'views_7_days', 0)) }}</h2>
        </article>

        <article class="rounded-[24px] bg-white p-6 shadow-sm dark:bg-slate-900">
            <p class="text-sm text-gray-500 dark:text-slate-400">7-day growth</p>
            @php($growth = (float) data_get($summary, 'growth_7_days', 0))
            <h2 class="mt-4 text-4xl font-bold {{ $growth < 0 ? 'text-rose-600' : 'text-[#2f6f61] dark:text-emerald-300' }}">
                {{ $growth > 0 ? '+' : '' }}{{ number_format($growth, 1) }}%
            </h2>
        </article>
    </section>

    <section class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <article class="rounded-[28px] bg-white p-6 shadow-sm dark:bg-slate-900 xl:col-span-2 lg:p-8">
            <div>
                <h2 class="text-2xl font-bold">Visitor trend</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">Page views dan unique visitors selama 14 hari.</p>
            </div>

            <div class="mt-6 h-[340px]" wire:ignore>
                <canvas x-ref="trendChart"></canvas>
            </div>
        </article>

        <article class="rounded-[28px] bg-white p-6 shadow-sm dark:bg-slate-900 lg:p-8">
            <h2 class="text-2xl font-bold">Device split</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">Perangkat yang dipakai pengunjung.</p>

            <div class="mt-6 h-[300px]" wire:ignore>
                <canvas x-ref="deviceChart"></canvas>
            </div>
        </article>
    </section>

    <section class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <article class="rounded-[28px] bg-white p-6 shadow-sm dark:bg-slate-900 lg:p-8">
            <h2 class="text-2xl font-bold">Traffic sources</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">Sumber eksternal dan kunjungan langsung.</p>

            <div class="mt-6 h-[300px]" wire:ignore>
                <canvas x-ref="sourceChart"></canvas>
            </div>
        </article>

        <article class="rounded-[28px] bg-white p-6 shadow-sm dark:bg-slate-900 xl:col-span-2 lg:p-8">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold">Most visited pages</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">Akumulasi 30 hari terakhir.</p>
                </div>
                <div class="text-right">
                    <p class="text-xs uppercase tracking-wide text-gray-400">Unique visitors</p>
                    <p class="text-xl font-bold">{{ number_format(data_get($summary, 'visitors_30_days', 0)) }}</p>
                </div>
            </div>

            <div class="mt-6 overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="border-b border-gray-100 text-xs uppercase tracking-wide text-gray-400 dark:border-slate-800">
                        <tr>
                            <th class="px-3 py-3 font-semibold">Page</th>
                            <th class="px-3 py-3 text-right font-semibold">Views</th>
                            <th class="px-3 py-3 text-right font-semibold">Visitors</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                        @forelse ($topPages as $page)
                            <tr>
                                <td class="max-w-[420px] truncate px-3 py-4 font-medium">{{ $page['path'] }}</td>
                                <td class="px-3 py-4 text-right">{{ number_format($page['views']) }}</td>
                                <td class="px-3 py-4 text-right">{{ number_format($page['visitors']) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-3 py-10 text-center text-gray-500 dark:text-slate-400">
                                    Belum ada data pengunjung. Buka halaman publik melalui browser incognito untuk menguji tracking.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </section>

    <section class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <article class="rounded-[28px] bg-white p-6 shadow-sm dark:bg-slate-900 xl:col-span-2 lg:p-8">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold">Realtime visitors</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">Sesi aktif berdasarkan heartbeat halaman publik.</p>
                </div>
                <span class="inline-flex items-center gap-2 rounded-full bg-[#e2f2ec] px-3 py-1 text-xs font-semibold text-[#2f6f61] dark:bg-emerald-950 dark:text-emerald-300">
                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                    Live
                </span>
            </div>

            <div class="mt-6 overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="border-b border-gray-100 text-xs uppercase tracking-wide text-gray-400 dark:border-slate-800">
                        <tr>
                            <th class="px-3 py-3 font-semibold">Visitor</th>
                            <th class="px-3 py-3 font-semibold">Page</th>
                            <th class="px-3 py-3 font-semibold">Device</th>
                            <th class="px-3 py-3 font-semibold">Location</th>
                            <th class="px-3 py-3 text-right font-semibold">Last seen</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                        @forelse ($realtimeVisitors as $visitor)
                            <tr>
                                <td class="px-3 py-4 font-medium">{{ $visitor['visitor'] }}</td>
                                <td class="max-w-[260px] truncate px-3 py-4">{{ $visitor['path'] }}</td>
                                <td class="px-3 py-4">{{ $visitor['device'] }} · {{ $visitor['browser'] }}</td>
                                <td class="px-3 py-4">{{ $visitor['location'] }}</td>
                                <td class="px-3 py-4 text-right text-gray-500 dark:text-slate-400">{{ $visitor['last_seen'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-3 py-10 text-center text-gray-500 dark:text-slate-400">
                                    Tidak ada pengunjung aktif dalam dua menit terakhir.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>

        <article class="rounded-[28px] bg-white p-6 shadow-sm dark:bg-slate-900 lg:p-8">
            <h2 class="text-2xl font-bold">Top locations</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">Tersedia pada deployment Vercel.</p>

            <div class="mt-6 space-y-4">
                @forelse ($locations as $location)
                    <div class="flex items-center justify-between gap-4 rounded-2xl bg-[#f5f8f8] px-4 py-3 dark:bg-slate-800">
                        <span class="truncate text-sm font-medium">{{ $location['location'] }}</span>
                        <span class="text-sm font-bold">{{ number_format($location['views']) }}</span>
                    </div>
                @empty
                    <p class="rounded-2xl bg-[#f5f8f8] px-4 py-5 text-sm leading-6 text-gray-500 dark:bg-slate-800 dark:text-slate-400">
                        Lokasi belum tersedia. Pada localhost, header geolokasi Vercel memang tidak dikirim.
                    </p>
                @endforelse
            </div>
        </article>
    </section>

    <section class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <article class="rounded-[28px] bg-white p-6 shadow-sm dark:bg-slate-900 lg:p-8">
            <h2 class="text-2xl font-bold">Quality breakdown</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">Skor deterministik, bukan opini AI.</p>

            <div class="mt-6 space-y-5">
                @foreach (data_get($quality, 'breakdown', []) as $item)
                    @php($percentage = $item['maximum'] > 0 ? ($item['score'] / $item['maximum']) * 100 : 0)
                    <div>
                        <div class="flex items-center justify-between gap-3 text-sm">
                            <span>{{ $item['label'] }}</span>
                            <strong>{{ $item['score'] }}/{{ $item['maximum'] }}</strong>
                        </div>
                        <div class="mt-2 h-2 overflow-hidden rounded-full bg-gray-100 dark:bg-slate-800">
                            <div class="h-full rounded-full bg-[#2f6f61]" style="width: {{ min($percentage, 100) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if (data_get($quality, 'quick_wins'))
                <div class="mt-8 border-t border-gray-100 pt-6 dark:border-slate-800">
                    <h3 class="font-bold">Immediate improvements</h3>
                    <ol class="mt-4 space-y-3 text-sm leading-6 text-gray-600 dark:text-slate-300">
                        @foreach ($quality['quick_wins'] as $index => $quickWin)
                            <li class="flex gap-3">
                                <span class="font-bold text-[#2f6f61] dark:text-emerald-300">{{ $index + 1 }}.</span>
                                <span>{{ $quickWin }}</span>
                            </li>
                        @endforeach
                    </ol>
                </div>
            @endif
        </article>

        <article class="rounded-[28px] bg-[#173f37] p-6 text-white shadow-sm xl:col-span-2 lg:p-8">
            <div class="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-[0.22em] text-emerald-200">AI Portfolio Review</span>
                    <h2 class="mt-2 text-2xl font-bold">Kritik dan rekomendasi berbasis data portfolio</h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-white/70">
                        Analisis hanya dijalankan saat tombol ditekan. Endpoint Ollama hanya diakses dari backend.
                    </p>
                </div>

                <button
                    type="button"
                    wire:click="generateAiReview"
                    wire:loading.attr="disabled"
                    wire:target="generateAiReview"
                    class="shrink-0 rounded-xl bg-white px-5 py-3 text-sm font-bold text-[#173f37] transition hover:bg-emerald-50 disabled:cursor-not-allowed disabled:opacity-60"
                >
                    <span wire:loading.remove wire:target="generateAiReview">
                        {{ $aiReview ? 'Run new review' : 'Run AI review' }}
                    </span>
                    <span wire:loading wire:target="generateAiReview">Analyzing...</span>
                </button>
            </div>

            @if ($aiError)
                <div class="mt-6 rounded-2xl border border-rose-300/30 bg-rose-400/10 px-4 py-3 text-sm text-rose-100">
                    {{ $aiError }}
                </div>
            @endif

            @if ($aiReview)
                <div class="mt-8 grid gap-6 lg:grid-cols-[180px_1fr]">
                    <div class="rounded-[24px] bg-white/10 p-6 text-center">
                        <p class="text-sm text-white/65">AI score</p>
                        <p class="mt-3 text-5xl font-bold">{{ $aiReview['score'] }}</p>
                        <p class="mt-2 text-xs text-white/50">Model: {{ $aiReview['model'] }}</p>
                        <p class="mt-1 text-xs text-white/50">{{ $aiReview['generated_at'] }}</p>
                    </div>

                    <div>
                        <p class="text-sm leading-7 text-white/85">{{ $aiReview['summary'] }}</p>

                        <div class="mt-7 grid gap-5 md:grid-cols-3">
                            <div class="rounded-2xl bg-white/8 p-5">
                                <h3 class="font-bold text-emerald-200">Strengths</h3>
                                <ul class="mt-4 space-y-3 text-sm leading-6 text-white/75">
                                    @foreach ($aiReview['strengths'] as $item)
                                        <li>• {{ $item }}</li>
                                    @endforeach
                                </ul>
                            </div>

                            <div class="rounded-2xl bg-white/8 p-5">
                                <h3 class="font-bold text-amber-200">Weaknesses</h3>
                                <ul class="mt-4 space-y-3 text-sm leading-6 text-white/75">
                                    @foreach ($aiReview['weaknesses'] as $item)
                                        <li>• {{ $item }}</li>
                                    @endforeach
                                </ul>
                            </div>

                            <div class="rounded-2xl bg-white/8 p-5">
                                <h3 class="font-bold text-sky-200">Priority actions</h3>
                                <ol class="mt-4 space-y-3 text-sm leading-6 text-white/75">
                                    @foreach ($aiReview['recommendations'] as $index => $item)
                                        <li>{{ $index + 1 }}. {{ $item }}</li>
                                    @endforeach
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="mt-8 rounded-[24px] border border-white/10 bg-white/5 p-6 text-sm leading-7 text-white/70">
                    Belum ada review AI. Statistik pengunjung dan quality score tetap berfungsi walaupun Ollama sedang offline.
                </div>
            @endif
        </article>
    </section>

    <p class="pb-2 text-xs leading-5 text-gray-400 dark:text-slate-500">
        Privasi: implementasi ini tidak menyimpan alamat IP mentah. IP hanya disimpan sebagai hash satu arah untuk kebutuhan agregasi dan pencegahan duplikasi dasar.
    </p>
</div>
