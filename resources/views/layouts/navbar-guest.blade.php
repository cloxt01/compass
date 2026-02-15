<nav class="w-full h-16 bg-[#0d1117] border-b border-[#30363d] sticky top-0 z-50 backdrop-blur-md">
    <div class="max-w-7xl mx-auto px-6 h-full flex items-center justify-between">
        {{-- LEFT: LOGO --}}
        <div class="flex items-center gap-3">
            <img class="w-8 h-8" src="{{ asset('icon.png') }}" alt="Compass Icon">
            <span class="text-lg font-bold text-[#c9d1d9]">Compass</span>
        </div>

        {{-- CENTER: NAV LINKS --}}
        <div class="hidden md:flex items-center gap-8">
            <a href="#features" class="text-sm text-[#8b949e] hover:text-[#58a6ff] transition duration-200">
                Fitur
            </a>
            <a href="#howitworks" class="text-sm text-[#8b949e] hover:text-[#58a6ff] transition duration-200">
                Cara Kerja
            </a>
            <a href="#contact" class="text-sm text-[#8b949e] hover:text-[#58a6ff] transition duration-200">
                Tentang
            </a>
        </div>

        {{-- RIGHT: BUTTONS --}}
        <div class="flex items-center gap-3">
            {{-- MOBILE MENU TOGGLE --}}
            <button id="mobileToggle" class="md:hidden p-1.5 rounded-md hover:bg-[#21262d] transition">
                <svg class="w-5 h-5 text-[#8b949e]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            {{-- SIGN IN BUTTON --}}
            <a href="{{ route('login') ?? '#' }}" class="hidden sm:block px-4 py-2 text-sm font-semibold text-[#58a6ff] border border-[#30363d] rounded-md hover:border-[#58a6ff] hover:bg-[#0d1117] transition duration-200">
                Sign In
            </a>

            {{-- GET STARTED BUTTON --}}
            <a href="{{ route('register') ?? '#' }}" class="px-4 py-2 text-sm font-semibold text-white bg-[#238636] rounded-md hover:bg-[#2ea043] transition duration-200">
                Get Started
            </a>
        </div>
    </div>

    {{-- MOBILE MENU --}}
    <div id="mobileMenu" class="hidden md:hidden bg-[#161b22] border-t border-[#30363d]">
        <div class="px-6 py-3 space-y-1">
            <a href="#features" class="block px-3 py-2 text-sm text-[#8b949e] hover:text-[#58a6ff] hover:bg-[#0d1117] rounded transition duration-200">
                Fitur
            </a>
            <a href="#howitworks" class="block px-3 py-2 text-sm text-[#8b949e] hover:text-[#58a6ff] hover:bg-[#0d1117] rounded transition duration-200">
                Cara Kerja
            </a>
            <a href="#contact" class="block px-3 py-2 text-sm text-[#8b949e] hover:text-[#58a6ff] hover:bg-[#0d1117] rounded transition duration-200">
                Tentang
            </a>
            <div class="pt-2 border-t border-[#30363d] space-y-2">
                <a href="{{ route('login') ?? '#' }}" class="block w-full px-3 py-2 text-sm text-[#58a6ff] border border-[#30363d] rounded hover:border-[#58a6ff] text-center transition duration-200">
                    Sign In
                </a>
            </div>
        </div>
    </div>
</nav>

<script>
    document.getElementById('mobileToggle')?.addEventListener('click', () => {
        document.getElementById('mobileMenu').classList.toggle('hidden');
    });
</script>
