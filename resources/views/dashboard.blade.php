@extends('layouts.app')

@section('title', 'Dashboard · ' . config('ui.brand.name'))
@section('titleNavbar', 'Dashboard')

@php
    $stats = $stats ?? collect();
    // SEKARANG DIPISAH: Success hanya membaca 'success', Applied hanya membaca 'applied'
    $success = $stats->get('success', 0);
    $applied = $stats->get('applied', 0);
    $questionnaire = $stats->get('questionnaire', 0);
    $linkout = $stats->get('linkout', 0);

    $total = $stats->sum();

    // Tingkat keberhasilan tetap menghitung gabungan success + applied terhadap total
    $successRate = $total > 0 ? round((($success + $applied) / $total) * 100) : 0;

    $badgeMap = [
        'success'       => 'text-emerald-400 bg-emerald-500/10 border-emerald-500/30',
        'applied'       => 'text-violet-400 bg-violet-500/10 border-violet-500/30',
        'questionnaire' => 'text-amber-400 bg-amber-500/10 border-amber-500/30',
        'linkout'       => 'text-blue-400 bg-blue-500/10 border-blue-500/30',
        'error'         => 'text-red-400 bg-red-500/10 border-red-500/30',
    ];

    $providerBadgeMap = [
        'jobstreet' => 'text-blue-400 bg-blue-500/10 border-blue-500/30',
        'glints'    => 'text-violet-400 bg-violet-500/10 border-violet-500/30',
    ];
@endphp

@section('content')
    <div class="mx-auto max-w-[1400px] space-y-6 px-4 pb-6 pt-2">

        {{-- STAT CARDS (Menjadi 5 Kolom di Layar Besar) --}}
        <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
            <article class="saas-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">TOTAL LAMARAN</p>
                <p class="mt-3 text-3xl font-semibold text-[#fafafa]">{{ $total }}</p>
            </article>
            <article class="saas-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">APPLIED</p>
                <p class="mt-3 text-3xl font-semibold text-violet-400">{{ $applied }}</p>
            </article>
            <article class="saas-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">SUCCESS</p>
                <p class="mt-3 text-3xl font-semibold text-emerald-400">{{ $success }}</p>
            </article>
            <article class="saas-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">BUTUH SCREENING</p>
                <p class="mt-3 text-3xl font-semibold text-amber-400">{{ $questionnaire }}</p>
            </article>
            <article class="saas-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">LINKOUT</p>
                <p class="mt-3 text-3xl font-semibold text-blue-400">{{ $linkout }}</p>
            </article>
        </section>

        {{-- SUCCESS RATE STRIP --}}
        <section class="saas-card p-5">
            <div class="flex items-center justify-between text-xs text-[#a1a1aa]">
                <span class="uppercase tracking-[0.14em]">TINGKAT KEBERHASILAN (APPLIED & SUCCESS)</span>
                <span class="text-[#fafafa] font-medium">{{ $successRate }}%</span>
            </div>
            <div class="mt-3 h-2 overflow-hidden rounded-full bg-[#1b1b1b]">
                <div class="h-full rounded-full bg-emerald-500 transition-all duration-500" style="width:{{ $successRate }}%"></div>
            </div>
        </section>

        {{-- CHART (MODERN APEXCHARTS) --}}
        <section class="saas-card p-6">
            <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
                <div>
                    <h2 class="text-lg font-semibold tracking-tight text-[#fafafa]">Tren Lamaran</h2>
                    <p class="mt-1 text-sm text-[#a1a1aa]">7 hari terakhir</p>
                </div>
            </div>
            <div id="applicationChart" class="w-full min-h-[280px]"></div>
        </section>

        {{-- HISTORY TABLE --}}
        <section class="saas-card overflow-hidden">
            <div class="border-b border-[#262626] px-6 py-4">
                <h2 class="text-lg font-semibold tracking-tight text-[#fafafa]">Riwayat Lamaran</h2>
            </div>

            {{-- DESKTOP VIEW --}}
            <div class="hidden overflow-x-auto md:block">
                <table class="w-full text-left">
                    <thead class="bg-[#0a0a0a] text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">
                    <tr>
                        <th class="px-6 py-3">Posisi</th>
                        <th class="px-6 py-3">Perusahaan</th>
                        <th class="px-6 py-3">Provider</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Tanggal</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-[#262626]">
                    @forelse(($appliedJobs ?? []) as $item)
                        <tr class="text-sm">
                            <td class="px-6 py-4 text-[#a1a1aa]">{{ $item->job_title ?? '-' }}</td>
                            <td class="px-6 py-4 text-[#fafafa]">{{ $item->job_company ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <span class="rounded-full border px-2.5 py-0.5 text-xs font-medium {{ $providerBadgeMap[strtolower($item->provider ?? '')] ?? 'text-[#a1a1aa] bg-[#161616] border-[#262626]' }}">
                                    {{ $item->provider ? ucfirst($item->provider) : '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="rounded-full border px-2.5 py-0.5 text-xs font-medium {{ $badgeMap[$item->status ?? ''] ?? 'text-[#a1a1aa] bg-[#161616] border-[#262626]' }}">
                                    {{ ucfirst($item->status ?? 'unknown') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-[#a1a1aa]">{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-6 text-sm italic text-center text-[#8b949e]">Belum ada riwayat lamaran</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- MOBILE VIEW --}}
            <div class="md:hidden divide-y divide-[#262626]">
                @forelse(($appliedJobs ?? []) as $item)
                    <div class="p-4 space-y-2.5">
                        <div class="flex justify-between items-start gap-2">
                            <div class="min-w-0">
                                <p class="text-[#fafafa] font-medium truncate text-sm">{{ $item->job_title ?? '-' }}</p>
                                <p class="text-[#a1a1aa] text-xs truncate mt-0.5">{{ $item->job_company ?? '-' }}</p>
                            </div>
                            <span class="shrink-0 rounded-full border px-2 py-0.5 text-[10px] uppercase font-bold {{ $badgeMap[$item->status ?? ''] ?? 'text-[#a1a1aa] bg-[#161616] border-[#262626]' }}">
                                {{ ucfirst($item->status ?? 'unknown') }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center text-xs">
                            <span class="rounded-full border px-2 py-0.5 text-[10px] {{ $providerBadgeMap[strtolower($item->provider ?? '')] ?? 'text-[#a1a1aa] bg-[#161616] border-[#262626]' }}">
                                {{ $item->provider ? ucfirst($item->provider) : '-' }}
                            </span>
                            <span class="text-[#71717a]">{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y, H:i') }}</span>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-sm italic text-center text-[#8b949e]">Belum ada riwayat lamaran</div>
                @endforelse
            </div>
        </section>
    </div>
@endsection

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@100..900&family=Geist+Mono:wght@100..900&display=swap" rel="stylesheet">

    <style>
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #0a0a0a; }
        ::-webkit-scrollbar-thumb { background: #262626; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #404040; }

        body { font-family: 'Geist', system-ui, sans-serif; background: #0A0A0A; color: #FAFAFA; }
        .font-mono { font-family: 'Geist Mono', monospace !important; }
        .saas-card { background: #111111; border: 1px solid #262626; border-radius: 16px; box-shadow: 0 2px 16px rgba(0,0,0,.28); transition: all .2s ease; }
        .saas-card:hover { border-color: #333333; }

        .apexcharts-tooltip { background: #111111 !important; border: 1px solid #262626 !important; box-shadow: 0 4px 20px rgba(0,0,0,0.5) !important; color: #fafafa !important; }
        .apexcharts-tooltip-title { background: #161616 !important; border-bottom: 1px solid #262626 !important; }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chartData = @json($chartData ?? []);

            const labels = [...Array(7)].map((_, i) => {
                let d = new Date(); d.setDate(d.getDate() - (6 - i));
                return d.toISOString().split('T')[0];
            });

            // Fungsi murni mencari single status (karena sudah dipisahkan)
            const getData = (status) => labels.map(date => (chartData[date] || []).find(x => x.status === status)?.total || 0);

            const formattedLabels = labels.map(l => new Date(l).toLocaleDateString('id-ID', { day: '2-digit', month: 'short' }));

            const options = {
                chart: {
                    type: 'area',
                    height: 280,
                    toolbar: { show: false },
                    fontFamily: 'Geist, sans-serif',
                    background: 'transparent'
                },
                theme: { mode: 'dark' },
                stroke: { curve: 'smooth', width: 2 },
                grid: {
                    borderColor: '#1b1b1b',
                    padding: { top: 0, right: 10, bottom: 0, left: 10 }
                },
                // Ditambahkan dataset untuk 'Applied' secara mandiri
                series: [
                    { name: 'Applied', data: getData('applied') },
                    { name: 'Success', data: getData('success') },
                    { name: 'Questionnaire', data: getData('questionnaire') },
                    { name: 'Linkout', data: getData('linkout') }
                ],
                // Ditambahkan palet warna ungu (#a78bfa) untuk line 'Applied'
                colors: ['#a78bfa', '#4ade80', '#fbbf24', '#60a5fa'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.2,
                        opacityTo: 0.0,
                        stops: [0, 95]
                    }
                },
                xaxis: {
                    categories: formattedLabels,
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                    labels: { style: { colors: '#a1a1aa', fontSize: '12px' } }
                },
                yaxis: {
                    labels: { style: { colors: '#a1a1aa', fontSize: '12px' } }
                },
                legend: {
                    show: true,
                    position: 'top',
                    horizontalAlign: 'right',
                    fontSize: '12px',
                    labels: { colors: '#a1a1aa' },
                    markers: { radius: 12 }
                },
                dataLabels: { enabled: false },
                tooltip: { theme: 'dark' }
            };

            const chart = new ApexCharts(document.querySelector("#applicationChart"), options);
            chart.render();
        });
    </script>
@endpush
