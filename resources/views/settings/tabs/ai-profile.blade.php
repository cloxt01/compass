@php
    $configuration = auth()->user()->apply_configuration ?? [];
    $autoAnswer = is_array($configuration) && is_array($configuration['auto_answer'] ?? null) ? $configuration['auto_answer'] : [];
    $p = is_array($autoAnswer['profile'] ?? null) ? $autoAnswer['profile'] : [];

    $get = fn ($key, $default = '') => $p[$key] ?? $default;

    $skills = is_array($p['skills'] ?? null) ? $p['skills'] : [];
    $sertifikasi = is_array($p['sertifikasi'] ?? null) ? $p['sertifikasi'] : [];
    $bahasa = is_array($p['bahasa'] ?? null) ? $p['bahasa'] : [];

    $educationOptions = ['SMA/SMK', 'Diploma', 'S1', 'S2', 'S3'];
    $noticePeriodOptions = [
        'IMMEDIATELY' => 'Segera / ASAP',
        'TWO_WEEKS' => '2 minggu',
        'ONE_MONTH' => '1 bulan',
        'TWO_MONTHS' => '2 bulan',
    ];
    $experienceLevelOptions = [
        'FRESH_GRADUATE' => 'Fresh Graduate',
        'LESS_THAN_ONE_YEAR' => 'Kurang dari 1 tahun',
        'ONE_TO_THREE_YEARS' => '1 - 3 tahun',
        'THREE_TO_FIVE_YEARS' => '3 - 5 tahun',
        'FIVE_TO_TEN_YEARS' => '5 - 10 tahun',
        'GREATER_THAN_TEN_YEARS' => '10+ tahun',
    ];
    $skillLevelOptions = [
        'NO_EXPERIENCE' => 'No Experience',
        'BASIC' => 'Basic',
        'INTERMEDIATE' => 'Intermediate',
        'ADVANCED' => 'Advanced',
    ];
    $languageLevelOptions = [
        'BASIC' => 'Basic',
        'INTERMEDIATE' => 'Intermediate',
        'FLUENT' => 'Fluent',
        'NATIVE' => 'Native',
    ];
@endphp

<div class="space-y-6" data-testid="ai-profile-settings"
     x-data="aiProfileForm({
         skills: @js($skills),
         sertifikasi: @js($sertifikasi),
         bahasa: @js($bahasa),
     })">

    @if(session('success'))
        <div class="rounded-md border border-emerald-500/40 bg-emerald-500/10 px-3 py-2 text-xs text-emerald-300"
             data-testid="ai-profile-flash-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-md border border-rose-500/40 bg-rose-500/10 px-3 py-2 text-xs text-rose-300"
             data-testid="ai-profile-flash-error">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-[#0a0a0a] border border-[#262626] rounded-md">
        <div class="px-5 py-4 border-b border-[#262626] flex items-start justify-between gap-4">
            <div>
                <h2 class="text-sm font-semibold text-[#fafafa]">Profil Kandidat untuk AI</h2>
                <p class="mt-1 text-xs text-[#71717a]">
                    Data ini dipakai <strong class="text-[#e4e4e7]">Auto AI Answer</strong> untuk menjawab screening question
                    &amp; menghitung <strong class="text-[#e4e4e7]">Match Score</strong>. Isi sejujurnya —
                    AI hanya boleh menjawab berdasarkan data ini, tanpa mengarang.
                </p>
            </div>
            <span class="hidden md:inline-flex items-center gap-1.5 rounded-full border border-sky-400/30 bg-sky-400/10 px-2.5 py-1 text-[11px] font-medium text-sky-200">
                <i data-lucide="user-round-search" class="w-3 h-3"></i> Profil kandidat
            </span>
        </div>

        <form method="POST" action="{{ route('settings.ai-profile.save') }}"
              class="divide-y divide-[#262626]"
              data-testid="ai-profile-form">
            @csrf

            {{-- 1. INFO PRIBADI --}}
            <section class="p-5 space-y-4">
                <div class="flex items-center gap-2 mb-2">
                    <i data-lucide="id-card" class="w-4 h-4 text-sky-300"></i>
                    <h3 class="text-sm font-semibold text-[#fafafa]">Info Pribadi</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs text-[#a1a1aa] mb-1.5">Nama Lengkap</label>
                        <input type="text" name="nama" value="{{ old('nama', $get('nama', auth()->user()->name)) }}"
                               data-testid="ai-profile-nama"
                               class="w-full rounded-md border border-[#333] bg-[#111] px-3 py-2 text-sm text-[#fafafa] focus:outline-none focus:border-sky-400">
                    </div>

                    <div>
                        <label class="block text-xs text-[#a1a1aa] mb-1.5">Nomor HP / WhatsApp</label>
                        <input type="text" name="phone" value="{{ old('phone', $get('phone')) }}"
                               placeholder="+62..." data-testid="ai-profile-phone"
                               class="w-full rounded-md border border-[#333] bg-[#111] px-3 py-2 text-sm text-[#fafafa] focus:outline-none focus:border-sky-400">
                    </div>

                    <div>
                        <label class="block text-xs text-[#a1a1aa] mb-1.5">Lokasi Domisili</label>
                        <input type="text" name="lokasi" value="{{ old('lokasi', $get('lokasi')) }}"
                               placeholder="mis. Jakarta Selatan" data-testid="ai-profile-lokasi"
                               class="w-full rounded-md border border-[#333] bg-[#111] px-3 py-2 text-sm text-[#fafafa] focus:outline-none focus:border-sky-400">
                    </div>

                    <div>
                        <label class="block text-xs text-[#a1a1aa] mb-1.5">Kewarganegaraan</label>
                        <select name="kewarganegaraan" data-testid="ai-profile-kewarganegaraan"
                                class="w-full rounded-md border border-[#333] bg-[#111] px-3 py-2 text-sm text-[#fafafa] focus:outline-none focus:border-sky-400">
                            <option value="">— pilih —</option>
                            <option value="Indonesia" @selected($get('kewarganegaraan') === 'Indonesia')>WNI (Indonesia)</option>
                            <option value="Lainnya" @selected($get('kewarganegaraan') === 'Lainnya')>WNA / Lainnya</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs text-[#a1a1aa] mb-1.5">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $get('tanggal_lahir')) }}"
                               data-testid="ai-profile-tanggal-lahir"
                               class="w-full rounded-md border border-[#333] bg-[#111] px-3 py-2 text-sm text-[#fafafa] focus:outline-none focus:border-sky-400">
                    </div>

                    <div>
                        <label class="block text-xs text-[#a1a1aa] mb-1.5">Gender</label>
                        <select name="gender" data-testid="ai-profile-gender"
                                class="w-full rounded-md border border-[#333] bg-[#111] px-3 py-2 text-sm text-[#fafafa] focus:outline-none focus:border-sky-400">
                            <option value="">— pilih —</option>
                            <option value="Laki-laki" @selected($get('gender') === 'Laki-laki')>Laki-laki</option>
                            <option value="Perempuan" @selected($get('gender') === 'Perempuan')>Perempuan</option>
                        </select>
                    </div>
                </div>
            </section>

            {{-- 2. PENDIDIKAN --}}
            <section class="p-5 space-y-4">
                <div class="flex items-center gap-2 mb-2">
                    <i data-lucide="graduation-cap" class="w-4 h-4 text-emerald-300"></i>
                    <h3 class="text-sm font-semibold text-[#fafafa]">Pendidikan Terakhir</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs text-[#a1a1aa] mb-1.5">Tingkat</label>
                        <select name="pendidikan" data-testid="ai-profile-pendidikan"
                                class="w-full rounded-md border border-[#333] bg-[#111] px-3 py-2 text-sm text-[#fafafa] focus:outline-none focus:border-sky-400">
                            <option value="">— pilih —</option>
                            @foreach($educationOptions as $opt)
                                <option value="{{ $opt }}" @selected($get('pendidikan') === $opt)>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs text-[#a1a1aa] mb-1.5">Jurusan</label>
                        <input type="text" name="jurusan" value="{{ old('jurusan', $get('jurusan')) }}"
                               placeholder="mis. Teknik Informatika" data-testid="ai-profile-jurusan"
                               class="w-full rounded-md border border-[#333] bg-[#111] px-3 py-2 text-sm text-[#fafafa] focus:outline-none focus:border-sky-400">
                    </div>

                    <div>
                        <label class="block text-xs text-[#a1a1aa] mb-1.5">Institusi</label>
                        <input type="text" name="institusi" value="{{ old('institusi', $get('institusi')) }}"
                               placeholder="mis. Universitas Indonesia" data-testid="ai-profile-institusi"
                               class="w-full rounded-md border border-[#333] bg-[#111] px-3 py-2 text-sm text-[#fafafa] focus:outline-none focus:border-sky-400">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs text-[#a1a1aa] mb-1.5">Tahun Lulus</label>
                            <input type="number" min="1970" max="2035" name="tahun_lulus"
                                   value="{{ old('tahun_lulus', $get('tahun_lulus')) }}"
                                   data-testid="ai-profile-tahun-lulus"
                                   class="w-full rounded-md border border-[#333] bg-[#111] px-3 py-2 text-sm text-[#fafafa] focus:outline-none focus:border-sky-400">
                        </div>
                        <div>
                            <label class="block text-xs text-[#a1a1aa] mb-1.5">IPK</label>
                            <input type="number" step="0.01" min="0" max="4" name="ipk"
                                   value="{{ old('ipk', $get('ipk')) }}"
                                   placeholder="0.00 - 4.00" data-testid="ai-profile-ipk"
                                   class="w-full rounded-md border border-[#333] bg-[#111] px-3 py-2 text-sm text-[#fafafa] focus:outline-none focus:border-sky-400">
                        </div>
                    </div>
                </div>
            </section>

            {{-- 3. PENGALAMAN KERJA --}}
            <section class="p-5 space-y-4">
                <div class="flex items-center gap-2 mb-2">
                    <i data-lucide="briefcase" class="w-4 h-4 text-amber-300"></i>
                    <h3 class="text-sm font-semibold text-[#fafafa]">Pengalaman Kerja</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs text-[#a1a1aa] mb-1.5">Level Pengalaman</label>
                        <select name="pengalaman_level" data-testid="ai-profile-pengalaman-level"
                                class="w-full rounded-md border border-[#333] bg-[#111] px-3 py-2 text-sm text-[#fafafa] focus:outline-none focus:border-sky-400">
                            <option value="">— pilih —</option>
                            @foreach($experienceLevelOptions as $val => $label)
                                <option value="{{ $val }}" @selected($get('pengalaman_level') === $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs text-[#a1a1aa] mb-1.5">Total Tahun Pengalaman</label>
                        <input type="number" step="0.5" min="0" max="50" name="pengalaman_tahun"
                               value="{{ old('pengalaman_tahun', $get('pengalaman_tahun')) }}"
                               data-testid="ai-profile-pengalaman-tahun"
                               class="w-full rounded-md border border-[#333] bg-[#111] px-3 py-2 text-sm text-[#fafafa] focus:outline-none focus:border-sky-400">
                    </div>

                    <div>
                        <label class="block text-xs text-[#a1a1aa] mb-1.5">Posisi / Role Terakhir</label>
                        <input type="text" name="posisi_terakhir" value="{{ old('posisi_terakhir', $get('posisi_terakhir')) }}"
                               placeholder="mis. Network Engineer" data-testid="ai-profile-posisi-terakhir"
                               class="w-full rounded-md border border-[#333] bg-[#111] px-3 py-2 text-sm text-[#fafafa] focus:outline-none focus:border-sky-400">
                    </div>

                    <div>
                        <label class="block text-xs text-[#a1a1aa] mb-1.5">Perusahaan Terakhir</label>
                        <input type="text" name="perusahaan_terakhir" value="{{ old('perusahaan_terakhir', $get('perusahaan_terakhir')) }}"
                               placeholder="mis. PT ANS Radius" data-testid="ai-profile-perusahaan-terakhir"
                               class="w-full rounded-md border border-[#333] bg-[#111] px-3 py-2 text-sm text-[#fafafa] focus:outline-none focus:border-sky-400">
                    </div>
                </div>
            </section>

            {{-- 4. SKILLS (dynamic array) --}}
            <section class="p-5 space-y-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <i data-lucide="wrench" class="w-4 h-4 text-indigo-300"></i>
                        <h3 class="text-sm font-semibold text-[#fafafa]">Skills</h3>
                    </div>
                    <button type="button" @click="addSkill()" data-testid="ai-profile-add-skill"
                            class="text-[11px] px-2.5 py-1 rounded-md border border-indigo-400/40 bg-indigo-400/10 text-indigo-200 hover:bg-indigo-400/20">
                        <i data-lucide="plus" class="inline-block w-3 h-3 -mt-0.5"></i> Tambah Skill
                    </button>
                </div>
                <p class="text-xs text-[#71717a] -mt-2">
                    Tambahkan skill teknis yang paling relevan (TCP/IP, VLAN, Docker, Laravel, dll). AI akan cocokkan dengan pertanyaan screening.
                </p>

                <template x-if="skills.length === 0">
                    <p class="text-xs italic text-[#52525b]" data-testid="ai-profile-skills-empty">Belum ada skill. Klik "Tambah Skill" untuk mulai.</p>
                </template>

                <template x-for="(skill, i) in skills" :key="'skill-' + i">
                    <div class="grid grid-cols-12 gap-2 items-start" data-testid="ai-profile-skill-row">
                        <input type="text" x-model="skill.name" :name="'skills[' + i + '][name]'"
                               placeholder="Nama skill (mis. Laravel)"
                               class="col-span-7 rounded-md border border-[#333] bg-[#111] px-3 py-2 text-sm text-[#fafafa] focus:outline-none focus:border-indigo-400">
                        <select x-model="skill.level" :name="'skills[' + i + '][level]'"
                                class="col-span-4 rounded-md border border-[#333] bg-[#111] px-3 py-2 text-sm text-[#fafafa] focus:outline-none focus:border-indigo-400">
                            @foreach($skillLevelOptions as $val => $label)
                                <option value="{{ $val }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <button type="button" @click="removeSkill(i)"
                                class="col-span-1 flex items-center justify-center h-9 rounded-md border border-rose-500/40 bg-rose-500/10 text-rose-300 hover:bg-rose-500/20">
                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>
                </template>
            </section>

            {{-- 5. SERTIFIKASI (dynamic array) --}}
            <section class="p-5 space-y-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <i data-lucide="badge-check" class="w-4 h-4 text-yellow-300"></i>
                        <h3 class="text-sm font-semibold text-[#fafafa]">Sertifikasi</h3>
                    </div>
                    <button type="button" @click="addSertifikasi()" data-testid="ai-profile-add-sertifikasi"
                            class="text-[11px] px-2.5 py-1 rounded-md border border-yellow-400/40 bg-yellow-400/10 text-yellow-200 hover:bg-yellow-400/20">
                        <i data-lucide="plus" class="inline-block w-3 h-3 -mt-0.5"></i> Tambah Sertifikasi
                    </button>
                </div>

                <template x-if="sertifikasi.length === 0">
                    <p class="text-xs italic text-[#52525b]">Belum ada sertifikasi.</p>
                </template>

                <template x-for="(item, i) in sertifikasi" :key="'cert-' + i">
                    <div class="grid grid-cols-12 gap-2 items-start" data-testid="ai-profile-sertifikasi-row">
                        <input type="text" x-model="item.nama" :name="'sertifikasi[' + i + '][nama]'"
                               placeholder="Nama sertifikat (HCIA, AWS CCP, dll)"
                               class="col-span-5 rounded-md border border-[#333] bg-[#111] px-3 py-2 text-sm text-[#fafafa] focus:outline-none focus:border-yellow-400">
                        <input type="text" x-model="item.issuer" :name="'sertifikasi[' + i + '][issuer]'"
                               placeholder="Issuer (Huawei, AWS, dll)"
                               class="col-span-4 rounded-md border border-[#333] bg-[#111] px-3 py-2 text-sm text-[#fafafa] focus:outline-none focus:border-yellow-400">
                        <input type="number" x-model="item.tahun" :name="'sertifikasi[' + i + '][tahun]'"
                               placeholder="Tahun" min="1970" max="2035"
                               class="col-span-2 rounded-md border border-[#333] bg-[#111] px-3 py-2 text-sm text-[#fafafa] focus:outline-none focus:border-yellow-400">
                        <button type="button" @click="removeSertifikasi(i)"
                                class="col-span-1 flex items-center justify-center h-9 rounded-md border border-rose-500/40 bg-rose-500/10 text-rose-300 hover:bg-rose-500/20">
                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>
                </template>
            </section>

            {{-- 6. BAHASA (dynamic array) --}}
            <section class="p-5 space-y-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <i data-lucide="languages" class="w-4 h-4 text-pink-300"></i>
                        <h3 class="text-sm font-semibold text-[#fafafa]">Bahasa</h3>
                    </div>
                    <button type="button" @click="addBahasa()" data-testid="ai-profile-add-bahasa"
                            class="text-[11px] px-2.5 py-1 rounded-md border border-pink-400/40 bg-pink-400/10 text-pink-200 hover:bg-pink-400/20">
                        <i data-lucide="plus" class="inline-block w-3 h-3 -mt-0.5"></i> Tambah Bahasa
                    </button>
                </div>

                <template x-if="bahasa.length === 0">
                    <p class="text-xs italic text-[#52525b]">Belum ada bahasa.</p>
                </template>

                <template x-for="(item, i) in bahasa" :key="'lang-' + i">
                    <div class="grid grid-cols-12 gap-2 items-start" data-testid="ai-profile-bahasa-row">
                        <input type="text" x-model="item.nama" :name="'bahasa[' + i + '][nama]'"
                               placeholder="Nama bahasa (mis. English)"
                               class="col-span-7 rounded-md border border-[#333] bg-[#111] px-3 py-2 text-sm text-[#fafafa] focus:outline-none focus:border-pink-400">
                        <select x-model="item.level" :name="'bahasa[' + i + '][level]'"
                                class="col-span-4 rounded-md border border-[#333] bg-[#111] px-3 py-2 text-sm text-[#fafafa] focus:outline-none focus:border-pink-400">
                            @foreach($languageLevelOptions as $val => $label)
                                <option value="{{ $val }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <button type="button" @click="removeBahasa(i)"
                                class="col-span-1 flex items-center justify-center h-9 rounded-md border border-rose-500/40 bg-rose-500/10 text-rose-300 hover:bg-rose-500/20">
                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>
                </template>
            </section>

            {{-- 7. EKSPEKTASI & AVAILABILITY --}}
            <section class="p-5 space-y-4">
                <div class="flex items-center gap-2 mb-2">
                    <i data-lucide="wallet" class="w-4 h-4 text-emerald-300"></i>
                    <h3 class="text-sm font-semibold text-[#fafafa]">Ekspektasi &amp; Availability</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs text-[#a1a1aa] mb-1.5">Gaji Terakhir (Rp)</label>
                        <input type="number" min="0" step="100000" name="gaji_terakhir"
                               value="{{ old('gaji_terakhir', $get('gaji_terakhir')) }}"
                               placeholder="mis. 5000000" data-testid="ai-profile-gaji-terakhir"
                               class="w-full rounded-md border border-[#333] bg-[#111] px-3 py-2 text-sm text-[#fafafa] focus:outline-none focus:border-emerald-400">
                    </div>

                    <div>
                        <label class="block text-xs text-[#a1a1aa] mb-1.5">Ekspektasi Gaji (Rp)</label>
                        <input type="number" min="0" step="100000" name="ekspektasi_gaji"
                               value="{{ old('ekspektasi_gaji', $get('ekspektasi_gaji')) }}"
                               placeholder="mis. 8000000" data-testid="ai-profile-ekspektasi-gaji"
                               class="w-full rounded-md border border-[#333] bg-[#111] px-3 py-2 text-sm text-[#fafafa] focus:outline-none focus:border-emerald-400">
                    </div>

                    <div>
                        <label class="block text-xs text-[#a1a1aa] mb-1.5">Notice Period</label>
                        <select name="notice_period" data-testid="ai-profile-notice-period"
                                class="w-full rounded-md border border-[#333] bg-[#111] px-3 py-2 text-sm text-[#fafafa] focus:outline-none focus:border-emerald-400">
                            <option value="">— pilih —</option>
                            @foreach($noticePeriodOptions as $val => $label)
                                <option value="{{ $val }}" @selected($get('notice_period') === $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs text-[#a1a1aa] mb-1.5">Preferensi Lokasi Kerja</label>
                        <div class="flex flex-wrap gap-3 pt-1.5">
                            @foreach(['wfo' => 'WFO / On-site', 'wfh' => 'WFH / Remote', 'hybrid' => 'Hybrid'] as $k => $label)
                                <label class="inline-flex items-center gap-2 text-xs text-[#e4e4e7]">
                                    <input type="checkbox" name="bersedia_{{ $k }}" value="1"
                                           @checked($get('bersedia_' . $k))
                                           data-testid="ai-profile-bersedia-{{ $k }}"
                                           class="accent-emerald-400 h-4 w-4">
                                    {{ $label }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="inline-flex items-start gap-2 text-xs text-[#e4e4e7]">
                        <input type="checkbox" name="bersedia_luar_kota" value="1"
                               @checked($get('bersedia_luar_kota'))
                               data-testid="ai-profile-bersedia-luar-kota"
                               class="mt-0.5 accent-emerald-400 h-4 w-4">
                        <span>
                            Bersedia bekerja di luar kota domisili
                            <span class="block text-[11px] text-[#71717a] mt-0.5">Termasuk relokasi bila diperlukan.</span>
                        </span>
                    </label>

                    <label class="inline-flex items-start gap-2 text-xs text-[#e4e4e7]">
                        <input type="checkbox" name="bersedia_industri_banking" value="1"
                               @checked($get('bersedia_industri_banking'))
                               data-testid="ai-profile-bersedia-banking"
                               class="mt-0.5 accent-emerald-400 h-4 w-4">
                        <span>
                            Bersedia bekerja di industri Banking / Finance
                            <span class="block text-[11px] text-[#71717a] mt-0.5">Beberapa perusahaan filter ini di screening.</span>
                        </span>
                    </label>
                </div>
            </section>

            {{-- 8. CATATAN BEBAS --}}
            <section class="p-5 space-y-3">
                <div class="flex items-center gap-2 mb-2">
                    <i data-lucide="notebook-text" class="w-4 h-4 text-[#a1a1aa]"></i>
                    <h3 class="text-sm font-semibold text-[#fafafa]">Catatan Tambahan untuk AI</h3>
                </div>
                <p class="text-xs text-[#71717a]">
                    Info bebas yang mungkin ditanyakan tapi belum tercakup di atas. Contoh: <em>"Saya punya SIM A &amp; C aktif"</em>,
                    <em>"Bersedia lembur/shift"</em>, <em>"Pernah magang di startup fintech"</em>. AI hanya boleh pakai fakta yang lu tulis di sini.
                </p>
                <textarea name="catatan" rows="4" data-testid="ai-profile-catatan"
                          placeholder="Tulis fakta tambahan tentang diri lu..."
                          class="w-full rounded-md border border-[#333] bg-[#111] px-3 py-2 text-sm text-[#fafafa] focus:outline-none focus:border-sky-400">{{ old('catatan', $get('catatan')) }}</textarea>
            </section>

            {{-- ACTIONS --}}
            <div class="p-5 flex items-center justify-between gap-3">
                <a href="{{ route('apply') }}"
                   class="text-xs text-[#71717a] hover:text-[#e4e4e7] inline-flex items-center gap-1.5">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                    Kembali ke Apply
                </a>
                <button type="submit" data-testid="ai-profile-save-button"
                        class="inline-flex items-center gap-2 rounded-md bg-sky-500 px-4 py-2 text-sm font-medium text-white hover:bg-sky-400 transition">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Simpan Profil
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function aiProfileForm(initial) {
        return {
            skills: initial.skills.length ? initial.skills : [],
            sertifikasi: initial.sertifikasi.length ? initial.sertifikasi : [],
            bahasa: initial.bahasa.length ? initial.bahasa : [],
            addSkill() { this.skills.push({ name: '', level: 'INTERMEDIATE' }); this.$nextTick(() => window.lucide?.createIcons()); },
            removeSkill(i) { this.skills.splice(i, 1); },
            addSertifikasi() { this.sertifikasi.push({ nama: '', issuer: '', tahun: '' }); this.$nextTick(() => window.lucide?.createIcons()); },
            removeSertifikasi(i) { this.sertifikasi.splice(i, 1); },
            addBahasa() { this.bahasa.push({ nama: '', level: 'INTERMEDIATE' }); this.$nextTick(() => window.lucide?.createIcons()); },
            removeBahasa(i) { this.bahasa.splice(i, 1); },
        };
    }
    document.addEventListener('DOMContentLoaded', function () {
        if (window.lucide?.createIcons) window.lucide.createIcons();
    });
</script>
@endpush
