<div id="overlay" class="fixed inset-0 z-40 hidden bg-black/50 md:hidden"></div>

<!-- Overlay -->
<div id="overlay" class="fixed inset-0 z-40 hidden bg-black/50 md:hidden"></div>

<!-- Sidebar -->
<aside id="sidebar"
       class="fixed inset-y-0 left-0 z-50 w-[240px] -translate-x-full border-r border-[#262626] bg-[#0a0a0a] transition-transform duration-300 md:translate-x-0">

    <!-- Brand / Header -->
    <div class="flex h-16 items-center gap-3 border-b border-[#262626] px-5">
        <img src="{{ asset(config('ui.brand.logo')) }}" alt="{{ config('ui.brand.name') }}" class="h-7 w-7 rounded-md border border-[#333]" />
        <div>
            <p class="text-sm font-semibold text-[#fafafa]">{{ config('ui.brand.name') }}</p>
            <p class="text-xs text-[#a1a1aa]">Enterprise Automation</p>
        </div>
    </div>

    <!-- Navigation -->
    <!-- px-3 memberi ruang agar efek rounded-md saat hover tidak menempel ke tepi layar -->
    <nav class="flex flex-col gap-0.5 px-3 py-4 text-sm h-[calc(100%-4rem)] overflow-y-auto">

        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-3 rounded-md px-3 py-2 transition-colors duration-200 {{ request()->routeIs('dashboard') ? 'bg-[#1e1e1e] text-[#fafafa]' : 'text-[#a1a1aa] hover:bg-[#1e1e1e] hover:text-[#fafafa]' }}">
            <i class="fas fa-home w-4 text-center text-xs opacity-70"></i>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('apply') }}"
           class="flex items-center gap-3 rounded-md px-3 py-2 transition-colors duration-200 {{ request()->routeIs('apply') ? 'bg-[#1e1e1e] text-[#fafafa]' : 'text-[#a1a1aa] hover:bg-[#1e1e1e] hover:text-[#fafafa]' }}">
            <i class="fas fa-bolt w-4 text-center text-xs opacity-70"></i>
            <span>Apply</span>
        </a>

        <!-- Contoh garis pemisah (Divider) ala Vercel -->
        <div class="mx-3 my-2 border-t border-[#262626]"></div>

        <a href="{{ route('profile') }}"
           class="flex items-center gap-3 rounded-md px-3 py-2 transition-colors duration-200 {{ request()->routeIs('profile') ? 'bg-[#1e1e1e] text-[#fafafa]' : 'text-[#a1a1aa] hover:bg-[#1e1e1e] hover:text-[#fafafa]' }}">
            <i class="fas fa-cog w-4 text-center text-xs opacity-70"></i>
            <span>Settings</span>
        </a>

    </nav>

    <!-- Footer -->
    <div class="absolute inset-x-0 bottom-0 border-t border-[#262626] bg-[#0a0a0a] p-4 text-xs text-[#71717a]">
        © {{ date('Y') }} {{ config('ui.brand.name') }}
    </div>
</aside>
