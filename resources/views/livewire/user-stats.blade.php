<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1">
        <div class="bg-gradient-to-br from-[#0d1117] to-[#161b22] border border-[#30363d] rounded-lg shadow-lg p-6 h-full flex flex-col">
            <h2 class="text-lg font-semibold text-[#e6edf3] text-center mb-6">
                STATISTIK LAMARAN 
            </h2>
            <div class="flex flex-col gap-3 flex-1">
                <div class="bg-gradient-to-br from-[#1f6feb]/10 to-transparent border border-[#30363d] rounded-lg px-4 py-4 hover:border-[#58a6ff] transition flex-1 flex flex-col justify-center">
                    <span class="text-[#8b949e] text-xs uppercase tracking-wider">Total Lamaran Hari Ini</span>
                    <p class="text-[#58a6ff] text-2xl font-bold mt-2">{{ $userStat ? $userStat->total_applied : 0 }}</p>
                </div>
                <div class="bg-gradient-to-br from-[#1f6feb]/10 to-transparent border border-[#30363d] rounded-lg px-4 py-4 hover:border-[#58a6ff] transition flex-1 flex flex-col justify-center">
                    <span class="text-[#8b949e] text-xs uppercase tracking-wider">Total Lamaran</span>
                    <p class="text-[#58a6ff] text-2xl font-bold mt-2">{{ count($jobs ?? []) }}</p>
                </div>
                <div class="bg-gradient-to-br from-[#1f6feb]/10 to-transparent border border-[#30363d] rounded-lg px-4 py-4 hover:border-[#58a6ff] transition flex-1 flex flex-col justify-center">
                    <span class="text-[#8b949e] text-xs uppercase tracking-wider">Rata - Rata / Minggu</span>
                    <p class="text-[#58a6ff] text-2xl font-bold mt-2">{{ $weeklyAverage }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="lg:col-span-2">
        <div class="overflow-x-auto rounded-md border border-[#30363d] bg-[#161b22] h-full">
            @if(count($jobs))
                <table class="min-w-full text-sm text-left text-[#e6edf3]">
                    <thead class="bg-[#0d1117] border-b border-[#30363d] sticky top-0">
                        <tr>
                            <th class="px-4 py-3 font-medium">Posisi</th>
                            <th class="px-4 py-3 font-medium">Perusahaan</th>
                            <th class="px-4 py-3 font-medium">Lokasi</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-[#30363d]">
                        @foreach(array_slice($jobs, 0, 5) as $job)
                            <tr class="hover:bg-[#21262d] transition">
                                <td class="px-4 py-3 font-medium">
                                    {{ $job['job_title'] ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-[#8b949e]">
                                    {{ $job['company'] ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-[#8b949e]">
                                    {{ $job['job_location'] ?? '-' }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full bg-[#1f6feb]/20 px-2 py-0.5 text-xs font-medium text-[#58a6ff]">
                                        {{ $job['status'] ?? 'Unknown' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="p-6 text-center text-sm text-[#8b949e]">
                    You haven't applied to any jobs yet.
                </div>
            @endif
        </div>
    </div>
    
    
</div>