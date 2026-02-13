<div class="overflow-x-auto rounded-md border border-[#30363d] bg-[#161b22]">
    @if(count($jobs))
        <table class="min-w-full text-sm text-left text-[#e6edf3]">
            <thead class="bg-[#0d1117] border-b border-[#30363d]">
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
                            <span class="inline-flex items-center rounded-full
                                         bg-[#1f6feb]/20 px-2 py-0.5
                                         text-xs font-medium text-[#58a6ff]">
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
