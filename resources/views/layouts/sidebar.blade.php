<div id="overlay" class="fixed inset-0 z-40 hidden bg-black/50 md:hidden"></div>

<aside id="sidebar"
       class="fixed inset-y-0 left-0 z-50 w-[240px] -translate-x-full border-r border-[#262626] bg-[#0a0a0a] transition-transform duration-300 md:translate-x-0">

    <div class="flex h-16 items-center gap-3 border-b border-[#262626] px-5">
        @include ('partials.logo')

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

        <div class="flex flex-col">

            <button type="button"
                    id="btn-billing"
                    class="flex items-center justify-between gap-3 rounded-md px-3 py-2 text-[#a1a1aa] hover:bg-[#1e1e1e] hover:text-[#fafafa] transition-colors duration-200">

                <div class="flex items-center gap-3">
                    <i data-lucide="credit-card" class="w-4 h-4 opacity-70"></i>
                    <span>Billing</span>
                </div>

                <i data-lucide="chevron-down"
                   id="chevron-billing"
                   class="w-3 h-3 opacity-50 transition-transform duration-300 {{ request()->routeIs('billing.*') ? 'rotate-180' : '' }}"></i>

            </button>

            <div id="submenu-billing"
                 class="mt-1 flex flex-col gap-0.5 pl-9 transition-all duration-300 ease-in-out {{ request()->routeIs('billing.*') ? 'max-h-60 opacity-100' : 'max-h-0 opacity-0 overflow-hidden' }}">

                <a href="{{ route('billing') }}"
                   class="flex items-center rounded-md px-3 py-2 text-xs {{ request()->routeIs('billing') ? 'bg-[#1e1e1e] text-[#fafafa]' : 'text-[#a1a1aa] hover:bg-[#1e1e1e] hover:text-[#fafafa]' }}">
                    <i data-lucide="layout-dashboard" class="w-3.5 h-3.5 mr-2"></i>
                    Overview
                </a>

                <a href="{{ route('billing.subscription') }}"
                   class="flex items-center rounded-md px-3 py-2 text-xs {{ request()->routeIs('billing.subscription') ? 'bg-[#1e1e1e] text-[#fafafa]' : 'text-[#a1a1aa] hover:bg-[#1e1e1e] hover:text-[#fafafa]' }}">
                    <i data-lucide="badge-check" class="w-3.5 h-3.5 mr-2"></i>
                    Subscription
                </a>

                <a href="{{ route('billing.invoices') }}"
                   class="flex items-center rounded-md px-3 py-2 text-xs {{ request()->routeIs('billing.invoices') ? 'bg-[#1e1e1e] text-[#fafafa]' : 'text-[#a1a1aa] hover:bg-[#1e1e1e] hover:text-[#fafafa]' }}">
                    <i data-lucide="receipt" class="w-3.5 h-3.5 mr-2"></i>
                    Invoices
                </a>

                <a href="{{ route('billing.payments') }}"
                   class="flex items-center rounded-md px-3 py-2 text-xs {{ request()->routeIs('billing.payments') ? 'bg-[#1e1e1e] text-[#fafafa]' : 'text-[#a1a1aa] hover:bg-[#1e1e1e] hover:text-[#fafafa]' }}">
                    <i data-lucide="wallet" class="w-3.5 h-3.5 mr-2"></i>
                    Payments
                </a>

                <a href="{{ route('billing.packages') }}"
                   class="flex items-center rounded-md px-3 py-2 text-xs {{ request()->routeIs('billing.packages') ? 'bg-[#1e1e1e] text-[#fafafa]' : 'text-[#a1a1aa] hover:bg-[#1e1e1e] hover:text-[#fafafa]' }}">
                    <i data-lucide="package" class="w-3.5 h-3.5 mr-2"></i>
                    Packages
                </a>

            </div>

        </div>

        <a href="{{ route('apply') }}"
           class="flex items-center gap-3 rounded-md px-3 py-2 transition-colors duration-200 {{ request()->routeIs('apply') ? 'bg-[#1e1e1e] text-[#fafafa]' : 'text-[#a1a1aa] hover:bg-[#1e1e1e] hover:text-[#fafafa]' }}">
            <i data-lucide="zap" class="w-4 h-4 opacity-70"></i>
            <span>Apply</span>
        </a>

        <a href="{{ route('applications') }}"
           class="flex items-center gap-3 rounded-md px-3 py-2 transition-colors duration-200 {{ request()->routeIs('applications') ? 'bg-[#1e1e1e] text-[#fafafa]' : 'text-[#a1a1aa] hover:bg-[#1e1e1e] hover:text-[#fafafa]' }}">
            <i data-lucide="file-text" class="w-4 h-4 opacity-70"></i>
            <span>Application</span>
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

        <div class="px-3 pt-4 pb-2 text-[11px] font-semibold text-[#71717a] uppercase tracking-wider">Product</div>

        <a href="{{ route('products.compass-link') }}"
           class="flex items-center gap-3 rounded-md px-3 py-2 transition-colors duration-200 {{ request()->routeIs('products.compass-link') ? 'bg-[#1e1e1e] text-[#fafafa]' : 'text-[#a1a1aa] hover:bg-[#1e1e1e] hover:text-[#fafafa]' }}">
            <i data-lucide="settings" class="w-4 h-4 opacity-70"></i>
            <span>CompassLink</span>
        </a>
    </nav>

    <div class="absolute inset-x-0 bottom-0  bg-[#0a0a0a] p-4 flex items-center gap-3 border-t-1 border-[#262626]">
        <img
            src="{{ asset('assets/img/goat/messi.png') }}"
            alt="Avatar"
            class="w-9 h-9 rounded-md object-cover"
        >

        <div class="min-w-0 flex-1">
            <p class="text-sm font-medium text-[#fafafa] truncate">{{ auth()->user()->name }}</p>
            <p class="text-xs text-[#71717a] truncate">{{ auth()->user()->email }}</p>
        </div>
    </div>
</aside>

<script>
    function initDropdown(buttonId, submenuId, chevronId, expandedHeight = 'max-h-40') {
        const button = document.getElementById(buttonId);
        const submenu = document.getElementById(submenuId);
        const chevron = document.getElementById(chevronId);

        if (!button || !submenu || !chevron) return;

        button.addEventListener('click', () => {
            submenu.classList.toggle(expandedHeight);
            submenu.classList.toggle('max-h-0');
            submenu.classList.toggle('opacity-100');
            submenu.classList.toggle('opacity-0');
            chevron.classList.toggle('rotate-180');
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        initDropdown(
            'btn-billing',
            'submenu-billing',
            'chevron-billing',
            'max-h-60'
        );

        initDropdown(
            'btn-provider',
            'submenu-provider',
            'chevron-provider',
            'max-h-40'
        );
    });
</script>
