<div id="overlay" class="fixed inset-0 z-40 hidden bg-black/50 md:hidden"></div>

<aside id="sidebar"
       class="fixed inset-y-0 left-0 z-50 w-[240px] -translate-x-full border-r border-[#262626] bg-[#0a0a0a] transition-transform duration-300 md:translate-x-0">

    <div class="flex h-16 items-center gap-3 border-b border-[#262626] px-5">
        <img src="{{ asset(config('ui.brand.logo')) }}" alt="{{ config('ui.brand.name') }}" class="h-7 w-7 rounded-md border border-[#333]" />
        <div>
            <p class="text-sm font-semibold text-[#fafafa]">{{ config('ui.brand.name') }}</p>
            <p class="text-xs text-[#a1a1aa]">Enterprise Automation</p>
        </div>
    </div>

    <nav class="flex flex-col gap-0.5 px-3 py-4 text-sm h-[calc(100%-4rem)] overflow-y-auto">

        <div class="px-3 pt-4 pb-2 text-[11px] font-semibold text-[#71717a] uppercase tracking-wider">General</div>

        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-3 rounded-md px-3 py-2 transition-colors duration-200 {{ request()->routeIs('dashboard') ? 'bg-[#1e1e1e] text-[#fafafa]' : 'text-[#a1a1aa] hover:bg-[#1e1e1e] hover:text-[#fafafa]' }}">
            <i data-lucide="layout-grid" class="w-4 h-4 opacity-70"></i>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('apply') }}"
           class="flex items-center gap-3 rounded-md px-3 py-2 transition-colors duration-200 {{ request()->routeIs('apply') ? 'bg-[#1e1e1e] text-[#fafafa]' : 'text-[#a1a1aa] hover:bg-[#1e1e1e] hover:text-[#fafafa]' }}">
            <i data-lucide="zap" class="w-4 h-4 opacity-70"></i>
            <span>Apply</span>
        </a>

        <div class="px-3 pt-4 pb-2 text-[11px] font-semibold text-[#71717a] uppercase tracking-wider">Provider</div>

        <div class="flex flex-col">
            <button type="button" id="btn-provider"
                    class="flex items-center justify-between gap-3 rounded-md px-3 py-2 text-[#a1a1aa] hover:bg-[#1e1e1e] hover:text-[#fafafa] transition-colors duration-200">
                <div class="flex items-center gap-3">
                    <i data-lucide="network" class="w-4 h-4 opacity-70"></i>
                    <span>Connection</span>
                </div>
                <i data-lucide="chevron-down" id="chevron-provider"
                   class="w-3 h-3 opacity-50 transition-transform duration-300 {{ request()->routeIs('connection.*') ? 'rotate-180' : '' }}"></i>
            </button>

            <div id="submenu-provider"
                 class="mt-1 flex flex-col gap-0.5 pl-9 transition-all duration-300 ease-in-out {{ request()->routeIs('connection.*') ? 'max-h-40 opacity-100' : 'max-h-0 opacity-0 overflow-hidden' }}">

                <a href="{{ route('connection.glints') }}"
                   class="flex items-center rounded-md px-3 py-2 text-xs transition-colors duration-200 {{ request()->routeIs('connection.glints') ? 'bg-[#1e1e1e] text-[#fafafa]' : 'text-[#a1a1aa] hover:bg-[#1e1e1e] hover:text-[#fafafa]' }}">
                    <i data-lucide="briefcase" class="w-3.5 h-3.5 mr-2 {{ request()->routeIs('connection.glints') ? 'text-[#fafafa]' : 'opacity-70' }}"></i>
                    Glints
                </a>

                <a href="{{ route('connection.jobstreet') }}"
                   class="flex items-center rounded-md px-3 py-2 text-xs transition-colors duration-200 {{ request()->routeIs('connection.jobstreet') ? 'bg-[#1e1e1e] text-[#fafafa]' : 'text-[#a1a1aa] hover:bg-[#1e1e1e] hover:text-[#fafafa]' }}">
                    <i data-lucide="building" class="w-3.5 h-3.5 mr-2 {{ request()->routeIs('connection.jobstreet') ? 'text-[#fafafa]' : 'opacity-70' }}"></i>
                    Jobstreet
                </a>
            </div>
        </div>

        <div class="px-3 pt-4 pb-2 text-[11px] font-semibold text-[#71717a] uppercase tracking-wider">User</div>

        <a href="{{ route('settings') }}"
           class="flex items-center gap-3 rounded-md px-3 py-2 transition-colors duration-200 {{ request()->routeIs('settings') ? 'bg-[#1e1e1e] text-[#fafafa]' : 'text-[#a1a1aa] hover:bg-[#1e1e1e] hover:text-[#fafafa]' }}">
            <i data-lucide="settings" class="w-4 h-4 opacity-70"></i>
            <span>Settings</span>
        </a>
    </nav>

    <div class="absolute inset-x-0 bottom-0 border-t border-[#262626] bg-[#0a0a0a] p-4 flex items-center gap-3">
        <img
            src="{{ asset('assets/img/goat/messi.png') }}"
            alt="Avatar"
            class="w-9 h-9 rounded-md object-cover border border-[#333]"
        >

        <div class="min-w-0 flex-1">
            <p class="text-sm font-medium text-[#fafafa] truncate">{{ auth()->user()->name }}</p>
            <p class="text-xs text-[#71717a] truncate">{{ auth()->user()->email }}</p>
        </div>
    </div>
</aside>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Inisialisasi ikon Lucide
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        const btnProvider = document.getElementById('btn-provider');
        const submenuProvider = document.getElementById('submenu-provider');
        const chevronProvider = document.getElementById('chevron-provider');

        if (btnProvider && submenuProvider && chevronProvider) {
            btnProvider.addEventListener('click', function () {
                submenuProvider.classList.toggle('max-h-40');
                submenuProvider.classList.toggle('opacity-100');
                submenuProvider.classList.toggle('opacity-0');
                submenuProvider.classList.toggle('max-h-0');
                chevronProvider.classList.toggle('rotate-180');
            });
        }
    });
</script>
