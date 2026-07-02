{{-- Isian Tab Profil Akun dan Formulir Data Pengguna --}}
<div class="bg-[#0a0a0a] border border-[#262626] rounded-md">
    <div class="px-5 py-4 border-b border-[#262626]">
        <h2 class="text-sm font-semibold text-[#fafafa]">Account Settings</h2>
    </div>

    <form action="{{ route('profile.update') }}" method="POST" class="p-5 space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-xs font-medium text-[#a1a1aa] uppercase tracking-wider mb-2">Full Name</label>
            <input
                type="text"
                name="name"
                value="{{ old('name', auth()->user()->name) }}"
                class="w-full bg-[#141414] border border-[#262626] rounded-md px-3 py-2 text-sm text-[#fafafa] placeholder-[#71717a] focus:outline-none focus:border-[#333] focus:ring-1 focus:ring-[#333] transition"
                required
            >
        </div>

        <div>
            <label class="block text-xs font-medium text-[#a1a1aa] uppercase tracking-wider mb-2">Email Address</label>
            <input
                type="email"
                name="email"
                value="{{ old('email', auth()->user()->email) }}"
                class="w-full bg-[#141414] border border-[#262626] rounded-md px-3 py-2 text-sm text-[#fafafa] placeholder-[#71717a] focus:outline-none focus:border-[#333] focus:ring-1 focus:ring-[#333] transition"
                required
            >
        </div>

        <div>
            <label class="block text-xs font-medium text-[#a1a1aa] uppercase tracking-wider mb-2">New Password (Optional)</label>
            <input
                type="password"
                name="password"
                placeholder="Leave blank to keep current password"
                class="w-full bg-[#141414] border border-[#262626] rounded-md px-3 py-2 text-sm text-[#fafafa] placeholder-[#52525b] focus:outline-none focus:border-[#333] focus:ring-1 focus:ring-[#333] transition"
            >
        </div>

        <div class="pt-2">
            <button
                type="submit"
                class="inline-flex items-center rounded-md bg-[#1e1e1e] border border-[#333] px-4 py-2 text-xs font-semibold text-[#fafafa] hover:bg-[#262626] transition"
            >
                Update Profile
            </button>
        </div>
    </form>
</div>
