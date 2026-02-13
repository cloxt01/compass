<nav class="w-full h-14 bg-[#0d1117] border-b border-[#30363d] flex items-center px-4">
    {{-- LEFT --}}
    <div class="flex items-center gap-3">

        {{-- TOGGLE --}}
        <button id="sidebarToggle" class="p-1.5 rounded-md hover:bg-[#21262d] transition">
            <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        {{-- GITHUB ICON --}}
        <img class="w-5 h-5 invert" src="{{ asset('icon.png') }}" alt="Compass Icon">
        
        {{-- TITLE --}}
        <span class="text-sm font-semibold text-white">
            Dashboard
        </span>
    </div>

    {{-- RIGHT (optional, kosong juga fine) --}}
    <div class="ml-auto flex items-center gap-3">
        {{-- future: notif / profile --}}
    </div>
</nav>
