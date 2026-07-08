<nav class="sticky top-0 z-30 flex h-16 items-center border-b border-[#262626] bg-[#0a0a0a]/95 px-4 backdrop-blur md:px-6">
    <div class="flex items-center gap-3">
        <button id="brandToggle" type="button" class="rounded-lg p-2 text-[#a1a1aa] hover:bg-[#171717] md:hidden">
            <i class="fas fa-bars"></i>
        </button>
        <div class="space-y-0.5">
            <p class="text-xs text-[#71717a]">Workspace</p>
            <p class="text-sm font-semibold text-[#fafafa]">@yield('titleNavbar', 'Dashboard')</p>
        </div>
    </div>

    <div class="ml-auto flex items-center gap-3">
{{--        @if(auth()->user()->automation_paused)--}}
{{--            <span class="inline-flex items-center justify-center rounded-full border border-red-500/30 bg-red-500/10 p-2 md:px-3 md:py-1">--}}
{{--                <span class="h-2.5 w-2.5 rounded-full bg-red-500"></span>--}}
{{--                <span class="ml-2 hidden text-xs text-red-400 md:inline">--}}
{{--                    Automation Offline--}}
{{--                </span>--}}
{{--            </span>--}}
{{--                @else--}}
{{--                    <span class="inline-flex items-center justify-center rounded-full border border-emerald-500/30 bg-emerald-500/10 p-2 md:px-3 md:py-1">--}}
{{--                <span class="relative flex h-2.5 w-2.5">--}}
{{--                    <span class="absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75 animate-ping"></span>--}}
{{--                    <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-emerald-400"></span>--}}
{{--                </span>--}}
{{--                <span class="ml-2 hidden text-xs text-emerald-400 md:inline">--}}
{{--                    Automation Online--}}
{{--                </span>--}}
{{--            </span>--}}
{{--        @endif--}}
        <button
            type="button"
            id="automation-status-toggle"
            data-paused="{{ auth()->user()->automation_paused ? '1' : '0' }}"
            class="inline-flex cursor-pointer items-center justify-center rounded-full border p-2 transition-colors md:px-3 md:py-1 disabled:cursor-not-allowed disabled:opacity-60 {{ auth()->user()->automation_paused ? 'border-red-500/30 bg-red-500/10 hover:bg-red-500/20' : 'border-emerald-500/30 bg-emerald-500/10 hover:bg-emerald-500/20' }}"
        >
            @if(auth()->user()->automation_paused)
                <span class="h-2.5 w-2.5 rounded-full bg-red-500"></span>
                <span class="ml-2 hidden text-xs text-red-400 md:inline">Automation Offline</span>
            @else
                <span class="relative flex h-2.5 w-2.5">
            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
            <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-emerald-400"></span>
        </span>
                <span class="ml-2 hidden text-xs text-emerald-400 md:inline">Automation Online</span>
            @endif
        </button>

        {{-- Template markup untuk swap state via JS --}}
        <template id="tpl-automation-online">
        <span class="relative flex h-2.5 w-2.5">
            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
            <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-emerald-400"></span>
        </span>
            <span class="ml-2 hidden text-xs text-emerald-400 md:inline">Automation Online</span>
        </template>
        <template id="tpl-automation-offline">
            <span class="h-2.5 w-2.5 rounded-full bg-red-500"></span>
            <span class="ml-2 hidden text-xs text-red-400 md:inline">Automation Offline</span>
        </template>



        <div class="hidden items-center rounded-xl border border-[#262626] bg-[#111111] px-3 py-2 lg:flex">
            <i class="fas fa-search mr-2 text-xs text-[#71717a]"></i>
            <input type="text" placeholder="Search..." class="w-40 bg-transparent text-sm text-[#fafafa] outline-none placeholder:text-[#71717a]">
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="h-10 rounded-xl bg-[#171717] px-4 text-xs font-medium text-[#fafafa] transition hover:bg-[#262626]">
                Logout
            </button>
        </form>

    </div>
</nav>

@push('scripts')
    <script>
        (function () {
            const btn = document.getElementById('automation-status-toggle');
            if (!btn) return;

            const tplOnline  = document.getElementById('tpl-automation-online');
            const tplOffline = document.getElementById('tpl-automation-offline');

            function applyState(isPaused) {
                btn.dataset.paused = isPaused ? '1' : '0';
                btn.innerHTML = (isPaused ? tplOffline : tplOnline).innerHTML;

                btn.classList.remove(
                    'border-red-500/30', 'bg-red-500/10', 'hover:bg-red-500/20',
                    'border-emerald-500/30', 'bg-emerald-500/10', 'hover:bg-emerald-500/20'
                );
                if (isPaused) {
                    btn.classList.add('border-red-500/30', 'bg-red-500/10', 'hover:bg-red-500/20');
                } else {
                    btn.classList.add('border-emerald-500/30', 'bg-emerald-500/10', 'hover:bg-emerald-500/20');
                }
            }

            btn.addEventListener('click', function () {
                const currentlyPaused = btn.dataset.paused === '1';
                const nextPaused = !currentlyPaused;

                btn.disabled = true;
                applyState(nextPaused);

                fetch('{{ route("settings.toggle-automation") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ automation_paused: nextPaused })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            applyState(currentlyPaused);
                            alert('Gagal memperbarui status automasi.');
                            return;
                        }
                        // Broadcast ke komponen lain (profile page) biar ikut sync
                        window.dispatchEvent(new CustomEvent('automation:updated', {
                            detail: { paused: nextPaused }
                        }));
                    })
                    .catch(() => {
                        applyState(currentlyPaused);
                        alert('Terjadi kesalahan jaringan.');
                    })
                    .finally(() => {
                        btn.disabled = false;
                    });
            });

            window.addEventListener('automation:updated', function (e) {
                applyState(e.detail.paused);
            });
        })();
    </script>
@endpush
