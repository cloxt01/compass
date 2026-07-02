@extends('layouts.app')

@section('title', 'Dashboard · ' . config('ui.brand.name'))
@section('titleNavbar', 'Dashboard')

@php
    $stats = $stats ?? collect();
    $success = ($stats->get('success', 0)) + ($stats->get('applied', 0));
    $questionnaire = $stats->get('questionnaire', 0);
    $linkout = $stats->get('linkout', 0);
    $total = $stats->sum();
    $successRate = $total > 0 ? round(($success / $total) * 100) : 0;
@endphp

@section('content')
    <div class="mx-auto max-w-[1400px] space-y-6 px-1 pb-6 pt-2">

        {{-- STAT CARDS --}}
        <section class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
            <article class="saas-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">TOTAL LAMARAN</p>
                <p class="mt-3 text-3xl font-semibold text-[#fafafa]">{{ $total }}</p>
            </article>
            <article class="saas-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">BERHASIL DIKIRIM</p>
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
                <span class="uppercase tracking-[0.14em]">TINGKAT KEBERHASILAN</span>
                <span class="text-[#fafafa] font-medium">{{ $successRate }}%</span>
            </div>
            <div class="mt-3 h-2 overflow-hidden rounded-full bg-[#1b1b1b]">
                <div class="h-full rounded-full bg-emerald-500 transition-all duration-500" style="width:{{ $successRate }}%"></div>
            </div>
        </section>

        {{-- CHART --}}
        <section class="saas-card p-6">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div>
                    <h2 class="text-lg font-semibold tracking-tight text-[#fafafa]">Tren Lamaran</h2>
                    <p class="mt-1 text-sm text-[#a1a1aa]">7 hari terakhir</p>
                </div>
                <div class="flex items-center gap-4 text-xs text-[#a1a1aa]">
                    <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-emerald-400"></span>Success</span>
                    <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-amber-400"></span>Questionnaire</span>
                    <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-blue-400"></span>Linkout</span>
                </div>
            </div>
            <div class="mt-5">
                <canvas id="applicationChart" height="80"></canvas>
            </div>
        </section>

        {{-- HISTORY TABLE --}}
        <section class="saas-card overflow-hidden">
            <div class="border-b border-[#262626] px-6 py-4">
                <h2 class="text-lg font-semibold tracking-tight text-[#fafafa]">Riwayat Lamaran</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-[#0a0a0a]">
                    <tr class="text-left text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">
                        <th class="px-6 py-3">Posisi</th>
                        <th class="px-6 py-3">Perusahaan</th>
                        <th class="px-6 py-3">Provider</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Tanggal</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-[#262626]">
                    @forelse(($appliedJobs ?? []) as $item)
                        @php
                            $badgeMap = [
                                'success' => 'text-emerald-400 bg-emerald-500/10 border-emerald-500/30',
                                'applied' => 'text-emerald-400 bg-emerald-500/10 border-emerald-500/30',
                                'questionnaire' => 'text-amber-400 bg-amber-500/10 border-amber-500/30',
                                'linkout' => 'text-blue-400 bg-blue-500/10 border-blue-500/30',
                                'error' => 'text-red-400 bg-red-500/10 border-red-500/30',
                            ];
                            $badge = $badgeMap[$item->status ?? ''] ?? 'text-[#a1a1aa] bg-[#161616] border-[#262626]';

                            $providerBadgeMap = [
                                'jobstreet' => 'text-blue-400 bg-blue-500/10 border-blue-500/30',
                                'glints' => 'text-violet-400 bg-violet-500/10 border-violet-500/30',
                            ];
                            $providerKey = strtolower($item->provider ?? '');
                            $providerBadge = $providerBadgeMap[$providerKey] ?? 'text-[#a1a1aa] bg-[#161616] border-[#262626]';
                        @endphp
                        <tr class="text-sm">
                            <td class="px-6 py-4 text-[#a1a1aa]">{{ $item->job_title ?? '-' }}</td>
                            <td class="px-6 py-4 text-[#fafafa]">{{ $item->job_company ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <span class="rounded-full border px-2.5 py-0.5 text-xs font-medium {{ $providerBadge }}">{{ $item->provider ? ucfirst($item->provider) : '-' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="rounded-full border px-2.5 py-0.5 text-xs font-medium {{ $badge }}">{{ ucfirst($item->status ?? 'unknown') }}</span>
                            </td>
                            <td class="px-6 py-4 text-[#a1a1aa]">{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y, H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-6 text-sm italic text-[#8b949e]">Belum ada riwayat lamaran</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <style>
        body { font-family: Inter, system-ui, sans-serif; background:#0A0A0A; color:#FAFAFA; }
        .saas-card { background:#111111; border:1px solid #262626; border-radius:16px; box-shadow:0 2px 16px rgba(0,0,0,.28); transition:all .2s ease; }
        .saas-card:hover { border-color:#333333; }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('applicationChart').getContext('2d');
            const chartData = @json($chartData ?? []);

            const labels = [...Array(7)].map((_, i) => {
                let d = new Date(); d.setDate(d.getDate() - (6 - i));
                return d.toISOString().split('T')[0];
            });

            const getData = (status) => labels.map(date => (chartData[date] || []).find(x => x.status === status)?.total || 0);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels.map(l => new Date(l).toLocaleDateString('id-ID', { day: '2-digit', month: 'short' })),
                    datasets: [
                        { label: 'Success', data: getData('success'), borderColor: '#4ade80', backgroundColor: 'rgba(74,222,128,0.08)', tension: 0.3, fill: true },
                        { label: 'Questionnaire', data: getData('questionnaire'), borderColor: '#fbbf24', backgroundColor: 'rgba(251,191,36,0.08)', tension: 0.3, fill: true },
                        { label: 'Linkout', data: getData('linkout'), borderColor: '#60a5fa', backgroundColor: 'rgba(96,165,250,0.08)', tension: 0.3, fill: true }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#1b1b1b' }, ticks: { color: '#a1a1aa' } },
                        x: { grid: { display: false }, ticks: { color: '#a1a1aa' } }
                    }
                }
            });
        });
    </script>
@endpush
