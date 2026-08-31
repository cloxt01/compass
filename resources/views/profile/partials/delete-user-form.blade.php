{{-- Delete Account --}}




    <div class="px-5 pt-4">
        <p class="text-sm font-medium text-red-200">
            Perhatian
        </p>

        <p class="mt-1 text-sm text-red-100/60 leading-relaxed">
            Menghapus akun akan menghapus akun dan data yang terkait secara permanen.
            Pastikan Anda benar-benar ingin melanjutkan sebelum melakukan penghapusan.
        </p>
    </div>

    <div class="p-5">
        <button
            type="button"
            x-data
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            class="inline-flex items-center rounded-md bg-red-950/40 border border-red-500/30 px-4 py-2 text-xs font-semibold text-red-300 hover:bg-red-950/70 hover:border-red-500/50 transition"
        >
            Delete Account
        </button>
    </div>

</div>


{{-- Confirmation Modal --}}

<x-modal
    name="confirm-user-deletion"
    :show="$errors->userDeletion->isNotEmpty()"
    focusable
>
    <form
        method="post"
        action="{{ route('settings.profile.destroy') }}"
        class="bg-[#0a0a0a] border border-[#262626] rounded-md p-6"
    >
        @csrf
        @method('delete')

        <h2 class="text-base font-semibold text-[#fafafa]">
            Delete Account?
        </h2>

        <p class="mt-2 text-sm text-[#a1a1aa] leading-relaxed">
            Tindakan ini akan menghapus akun dan data yang terkait secara permanen.
            Masukkan password Anda untuk mengonfirmasi penghapusan akun.
        </p>

        <div class="mt-5">
            <label
                for="password"
                class="block text-xs font-medium text-[#a1a1aa] uppercase tracking-wider mb-2"
            >
                Password
            </label>

            <input
                id="password"
                name="password"
                type="password"
                class="w-full bg-[#141414] border border-[#262626] rounded-md px-3 py-2 text-sm text-[#fafafa] placeholder-[#52525b] focus:outline-none focus:border-[#333] focus:ring-1 focus:ring-[#333] transition"
                placeholder="Masukkan password"
                required
            >

            @if($errors->userDeletion->get('password'))
                <p class="mt-2 text-xs text-red-400">
                    {{ $errors->userDeletion->get('password')[0] }}
                </p>
            @endif
        </div>

        <div class="mt-6 flex justify-end gap-2">

            <button
                type="button"
                x-on:click="$dispatch('close')"
                class="inline-flex items-center rounded-md bg-[#141414] border border-[#262626] px-4 py-2 text-xs font-semibold text-[#a1a1aa] hover:bg-[#1c1c1c] hover:text-[#fafafa] transition"
            >
                Cancel
            </button>

            <button
                type="submit"
                class="cursor-pointer inline-flex items-center rounded-md bg-red-950/40 border border-red-500/30 px-4 py-2 text-xs font-semibold text-red-300 hover:bg-red-950/70 hover:border-red-500/50 transition"
            >
                Delete Account
            </button>

    </form>
</x-modal>