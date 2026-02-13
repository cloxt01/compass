{{-- OVERLAY (mobile) --}}
<div id="sidebarOverlay"
     class="fixed inset-0 bg-black/40 z-30 hidden md:hidden"></div>

{{-- SIDEBAR --}}
<aside id="sidebar"
       class="fixed md:static inset-y-0 left-0 z-40
              w-64 bg-[#1f2230] text-gray-300
              flex flex-col
              -translate-x-full md:translate-x-0
              transition-transform duration-300 ease-in-out">

    {{-- BRAND --}}
    <div id="brandToggle"
         class="flex items-center gap-3 px-5 py-4
                border-b border-white/10
                cursor-pointer select-none">

        <div id="compassIcon" class="transition-transform duration-300">
            <i class="fas fa-compass text-xl text-white"></i>
        </div>

        <span class="sidebar-text text-white font-semibold tracking-wide">
            COMPASS
        </span>
    </div>

    {{-- MENU --}}
    <nav class="flex-1 px-3 py-4">
        <ul class="space-y-1">

            {{-- Dashboard --}}
            <li>
                <a href="{{ route('dashboard') }}"
                   class="group flex items-center gap-3 px-3 py-2 rounded-lg
                          text-sm font-medium
                          transition
                          {{ request()->routeIs('dashboard')
                                ? 'bg-white/10 text-white'
                                : 'hover:bg-white/5 hover:text-white' }}">
                    <i class="fas fa-tachometer-alt text-sm opacity-80 group-hover:opacity-100"></i>
                    <span class="sidebar-text">Dashboard</span>
                </a>
            </li>

            {{-- Profile --}}
            <li>
                <a href="{{ route('profile') }}"
                   class="group flex items-center gap-3 px-3 py-2 rounded-lg
                          text-sm font-medium
                          transition
                          {{ request()->routeIs('profile')
                                ? 'bg-white/10 text-white'
                                : 'hover:bg-white/5 hover:text-white' }}">
                    <i class="fas fa-user text-sm opacity-80 group-hover:opacity-100"></i>
                    <span class="sidebar-text">Profile</span>
                </a>
            </li>

            {{-- Apply --}}
            <li>
                <a href="{{ route('apply') }}"
                   class="group flex items-center gap-3 px-3 py-2 rounded-lg
                          text-sm font-medium
                          transition
                          {{ request()->routeIs('apply')
                                ? 'bg-white/10 text-white'
                                : 'hover:bg-white/5 hover:text-white' }}">
                    <i class="fas fa-table text-sm opacity-80 group-hover:opacity-100"></i>
                    <span class="sidebar-text">Apply</span>
                </a>
            </li>

        </ul>
    </nav>

    {{-- FOOTER --}}
    <div class="px-5 py-4 border-t border-white/10 text-xs text-gray-400">
        © Compass 2026
    </div>
</aside>
