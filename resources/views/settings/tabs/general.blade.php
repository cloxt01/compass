<div class="bg-[#0a0a0a] border border-[#262626] rounded-md">
    <div class="px-5 py-4 border-b border-[#262626]">
        <h2 class="text-sm font-semibold text-[#fafafa]">System Configuration</h2>
    </div>

    <div class="divide-y divide-[#262626]">
        <div class="p-5 flex items-start justify-between gap-4">
            <div class="space-y-1">
                <h3 class="text-sm font-semibold text-[#fafafa]">Pause Automation</h3>
                <p class="text-xs text-[#71717a]">
                    Jika diaktifkan, proses automasi akan dihentikan sampai di nonaktifkan kembali
                </p>
            </div>
            <div>
                <label class="relative inline-flex items-center cursor-pointer select-none pt-0.5">
                    <input
                        type="checkbox"
                        id="toggle-automation"
                        class="sr-only peer"
                        {{ auth()->user()->automation_paused ? 'checked' : '' }}
                    >
                    <div class="w-10 h-5 bg-[#1e1e1e] border border-[#333] rounded-full peer peer-focus:ring-0 peer-checked:after:translate-x-[22px] peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[3px] after:bg-[#a1a1aa] peer-checked:after:bg-[#fafafa] after:rounded-full after:h-3 after:w-3 after:transition-all peer-checked:bg-[#262626] peer-checked:border-[#52525b]"></div>
                    <span id="automation-text" class="ml-2.5 text-xs font-medium text-[#a1a1aa] w-8">
                        {{ auth()->user()->automation_paused ? 'On' : 'Off' }}
                    </span>
                </label>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggle = document.getElementById('toggle-automation');
            const toggleText = document.getElementById('automation-text');

            function applyProfileState(isPaused) {
                toggle.checked = isPaused;
                toggleText.textContent = isPaused ? 'On' : 'Off';
            }

            if (toggle) {
                toggle.addEventListener('change', function () {
                    const isChecked = this.checked;
                    applyProfileState(isChecked);

                    fetch('{{ route("profile.toggle-automation") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ automation_paused: isChecked })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (!data.success) {
                                applyProfileState(!isChecked);
                                alert('Gagal memperbarui konfigurasi sistem.');
                                return;
                            }
                            // Broadcast ke komponen lain (navbar) biar ikut sync
                            window.dispatchEvent(new CustomEvent('automation:updated', {
                                detail: { paused: isChecked }
                            }));
                        })
                        .catch(() => {
                            applyProfileState(!isChecked);
                            alert('Terjadi kesalahan jaringan.');
                        });
                });

                window.addEventListener('automation:updated', function (e) {
                    applyProfileState(e.detail.paused);
                });
            }
        });
    </script>
@endpush
