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
        @if(auth()->user()->automation_paused)
            <span class="inline-flex items-center gap-2 rounded-full border border-red-500/30 bg-red-500/10 px-3 py-1 text-xs text-red-400">
                <span class="h-2 w-2 rounded-full bg-red-500"></span>
                Automation Offline
            </span>
        @else
            <span class="inline-flex items-center gap-2 rounded-full border border-emerald-500/30 bg-emerald-500/10 px-3 py-1 text-xs text-emerald-400">
                <span class="relative flex h-2 w-2">
                    <span class="absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75 animate-ping"></span>
                    <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-400"></span>
                </span>
                Automation Online
            </span>
        @endif

        <div class="hidden items-center rounded-xl border border-[#262626] bg-[#111111] px-3 py-2 lg:flex">
            <i class="fas fa-search mr-2 text-xs text-[#71717a]"></i>
            <input type="text" placeholder="Search..." class="w-40 bg-transparent text-sm text-[#fafafa] outline-none placeholder:text-[#71717a]">
        </div>



        <div class="hidden rounded-xl border border-[#262626] bg-[#111111] px-3 py-2 text-right sm:block">
            <p class="text-sm font-medium text-[#fafafa]">{{ auth()->user()->name }}</p>
            <p class="text-xs text-[#71717a]">{{ auth()->user()->email }}</p>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="h-10 rounded-xl bg-[#171717] px-4 text-xs font-medium text-[#fafafa] transition hover:bg-[#262626]">
                Logout
            </button>
        </form>

    </div>
</nav>
