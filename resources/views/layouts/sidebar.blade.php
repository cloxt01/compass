<!-- BACKDROP -->
<div id="sidebar-backdrop"
     class="fixed inset-0 bg-black/60 z-40 hidden"></div>

<!-- SIDEBAR -->
<aside id="sidebar"
  class="fixed inset-y-0 left-0 z-50
         w-64 bg-[#0d1117] text-[#c9d1d9]
         flex flex-col
         border-r border-[#30363d]
         -translate-x-full
         transition-transform duration-300 ease-in-out">

    <!-- BRAND -->
    <div class="flex items-center gap-2 px-4 h-14
                border-b border-[#30363d]">
        <img
            src="{{ asset('icon.png') }}"
            alt="Compass Logo"
            class="w-5 h-5 filter invert brightness-200"
        >
        <span class="text-sm font-semibold tracking-tight text-white">
            OMPASS
        </span>
    </div>

    <!-- MENU -->
    <nav class="flex-1 px-2 py-3">
        <ul class="space-y-1 text-sm">

            <li>
                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-md
                          transition
                          {{ request()->routeIs('dashboard')
                            ? 'bg-[#30363d] text-white'
                            : 'hover:bg-[#21262d]' }}">
                    <i class="fas fa-tachometer-alt opacity-80"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li>
                <a href="{{ route('profile') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-md
                          transition
                          {{ request()->routeIs('profile')
                            ? 'bg-[#30363d] text-white'
                            : 'hover:bg-[#21262d]' }}">
                    <i class="fas fa-user opacity-80"></i>
                    <span>Profile</span>
                </a>
            </li>

            <li>
                <a href="{{ route('apply') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-md
                          transition
                          {{ request()->routeIs('apply')
                            ? 'bg-[#30363d] text-white'
                            : 'hover:bg-[#21262d]' }}">
                    <i class="fas fa-table opacity-80"></i>
                    <span>Apply</span>
                </a>
            </li>

        </ul>
    </nav>

    <!-- FOOTER -->
    <div class="px-4 py-3 text-xs text-[#8b949e] border-t border-[#30363d]">
        © Compass 2026
    </div>
</aside>
