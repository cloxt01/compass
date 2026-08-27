<?php

namespace App\Services\AI;

use App\Models\User;

/**
 * Bangun profil kandidat untuk konteks AI.
 *
 * Sumber data:
 * 1. Manual profile dari `apply_configuration.auto_answer.profile` (Settings → AI Profile)
 *    Ini SUMBER UTAMA — data yang user isi sendiri.
 * 2. Fallback dari profile platform (Glints/Jobstreet) — dipakai hanya kalau field manual kosong.
 *
 * Tujuan: AI hanya menjawab berdasarkan data faktual, tanpa dilebih-lebihkan.
 */
class ProfileBuilder
{
    public static function build(string $provider, array $platformProfile, ?User $user = null): array
    {
        $manual = [];
        if ($user) {
            $cfg = $user->apply_configuration ?? [];
            $manual = data_get($cfg, 'auto_answer.profile', []);
            if (!is_array($manual)) $manual = [];
        }

        // Data dari platform sebagai fallback
        $platform = match ($provider) {
            'glints' => self::fromGlints($platformProfile),
            'jobstreet' => self::fromJobstreet($platformProfile),
            default => [],
        };

        // Merge: manual > platform > default
        $skills = self::normalizeArray($manual['skills'] ?? [], ['name', 'level']);
        $sertifikasi = self::normalizeArray($manual['sertifikasi'] ?? [], ['nama', 'issuer', 'tahun']);
        $bahasa = self::normalizeArray($manual['bahasa'] ?? [], ['nama', 'level']);

        // Preferensi lokasi kerja → array yang readable
        $prefLokasiKerja = array_values(array_filter([
            !empty($manual['bersedia_wfo']) ? 'WFO' : null,
            !empty($manual['bersedia_wfh']) ? 'WFH/Remote' : null,
            !empty($manual['bersedia_hybrid']) ? 'Hybrid' : null,
        ]));

        $profile = [
            // Info pribadi
            'nama' => self::pick($manual, 'nama', $platform['nama'] ?? ($user->name ?? null)),
            'email' => self::pick($manual, 'email', $platform['email'] ?? ($user->email ?? null)),
            'phone' => self::pick($manual, 'phone', $platform['phone'] ?? null),
            'lokasi' => self::pick($manual, 'lokasi', $platform['lokasi'] ?? null),
            'kewarganegaraan' => self::pick($manual, 'kewarganegaraan', null),
            'tanggal_lahir' => self::pick($manual, 'tanggal_lahir', null),
            'gender' => self::pick($manual, 'gender', null),

            // Pendidikan
            'pendidikan' => self::pick($manual, 'pendidikan', $platform['pendidikan'] ?? null),
            'jurusan' => self::pick($manual, 'jurusan', null),
            'institusi' => self::pick($manual, 'institusi', null),
            'tahun_lulus' => self::pick($manual, 'tahun_lulus', null),
            'ipk' => self::pick($manual, 'ipk', null),

            // Pengalaman
            'pengalaman_level' => self::pick($manual, 'pengalaman_level', $platform['pengalaman_level'] ?? null),
            'pengalaman_tahun' => self::pick($manual, 'pengalaman_tahun', null),
            'posisi_terakhir' => self::pick($manual, 'posisi_terakhir', $platform['pengalaman_terakhir'] ?? null),
            'perusahaan_terakhir' => self::pick($manual, 'perusahaan_terakhir', $platform['perusahaan_terakhir'] ?? null),

            // Skills, sertifikat, bahasa
            'skills' => $skills,
            'sertifikasi' => $sertifikasi,
            'bahasa' => $bahasa,

            // Ekspektasi
            'gaji_terakhir' => self::pick($manual, 'gaji_terakhir', null),
            'ekspektasi_gaji' => self::pick($manual, 'ekspektasi_gaji', null),
            'notice_period' => self::pick($manual, 'notice_period', null),

            // Preferensi
            'preferensi_lokasi_kerja' => $prefLokasiKerja,
            'bersedia_luar_kota' => (bool) ($manual['bersedia_luar_kota'] ?? false),
            'bersedia_industri_banking' => (bool) ($manual['bersedia_industri_banking'] ?? false),

            // Catatan
            'catatan' => self::pick($manual, 'catatan', null),
        ];

        return $profile;
    }

    protected static function pick(array $manual, string $key, $fallback)
    {
        $v = $manual[$key] ?? null;
        if ($v === null || $v === '' || (is_array($v) && empty($v))) {
            return ($fallback === '' ? null : $fallback);
        }
        return $v;
    }

    protected static function normalizeArray($rows, array $keys): array
    {
        if (!is_array($rows)) return [];
        $out = [];
        foreach ($rows as $r) {
            if (!is_array($r)) continue;
            $item = [];
            $hasValue = false;
            foreach ($keys as $k) {
                $v = $r[$k] ?? null;
                if ($v === '' ) $v = null;
                $item[$k] = $v;
                if ($v !== null) $hasValue = true;
            }
            if ($hasValue) $out[] = $item;
        }
        return $out;
    }

    protected static function fromGlints(array $p): array
    {
        $edu = self::educationLabel($p['highestEducation'] ?? null);
        $exp = self::experienceFromStart($p['careerStartDate'] ?? null);
        $lokasi = null;
        if (!empty($p['preferredLocations']) && is_array($p['preferredLocations'])) {
            $first = $p['preferredLocations'][0] ?? null;
            $lokasi = $first['formattedName'] ?? $first['name'] ?? null;
        }

        return array_filter([
            'nama' => trim(($p['first_name'] ?? '') . ' ' . ($p['last_name'] ?? '')) ?: null,
            'email' => $p['email'] ?? null,
            'phone' => $p['phone'] ?? $p['whatsappNumber'] ?? null,
            'pendidikan' => $edu,
            'pengalaman_level' => $exp,
            'lokasi' => $lokasi,
        ], fn ($v) => $v !== null && $v !== '');
    }

    protected static function fromJobstreet(array $p): array
    {
        $role = $p['latest_roles'] ?? [];
        return array_filter([
            'pengalaman_terakhir' => $role['title']['text'] ?? null,
            'perusahaan_terakhir' => $role['company']['text'] ?? null,
        ], fn ($v) => $v !== null && $v !== '');
    }

    protected static function educationLabel(?string $level): ?string
    {
        if (!$level) return null;
        return match ($level) {
            'PRIMARY_SCHOOL' => 'SD',
            'SECONDARY_SCHOOL' => 'SMP',
            'HIGH_SCHOOL' => 'SMA/SMK',
            'DIPLOMA' => 'Diploma',
            'BACHELOR_DEGREE' => 'S1',
            'MASTER_DEGREE' => 'S2',
            'DOCTORATE' => 'S3',
            'PROFESSIONAL_EDUCATION' => 'Pendidikan Profesi',
            default => $level,
        };
    }

    protected static function experienceFromStart(?string $startDate): ?string
    {
        if (!$startDate) return null;
        $ts = strtotime($startDate);
        if (!$ts) return null;
        $years = (int) floor((time() - $ts) / (365.25 * 24 * 3600));
        if ($years <= 0) return 'FRESH_GRADUATE';
        if ($years < 3) return 'ONE_TO_THREE_YEARS';
        if ($years < 5) return 'THREE_TO_FIVE_YEARS';
        if ($years < 10) return 'FIVE_TO_TEN_YEARS';
        return 'GREATER_THAN_TEN_YEARS';
    }

    /**
     * Hitung profile completeness (0-100).
     * Digunakan sebagai baseline score sebelum AI menjawab pertanyaan.
     */
    public static function completeness(array $profile): int
    {
        $weights = [
            'nama' => 3, 'phone' => 3, 'lokasi' => 3,
            'pendidikan' => 5, 'jurusan' => 3,
            'pengalaman_level' => 6, 'posisi_terakhir' => 4, 'perusahaan_terakhir' => 3,
            'gaji_terakhir' => 3, 'ekspektasi_gaji' => 6, 'notice_period' => 6,
            'kewarganegaraan' => 3, 'catatan' => 2,
        ];

        $total = array_sum($weights);
        $score = 0;
        foreach ($weights as $k => $w) {
            $v = $profile[$k] ?? null;
            if ($v !== null && $v !== '') $score += $w;
        }

        // Bonus: arrays
        if (!empty($profile['skills'])) $score += 10;
        if (!empty($profile['sertifikasi'])) $score += 6;
        if (!empty($profile['bahasa'])) $score += 4;
        if (!empty($profile['preferensi_lokasi_kerja'])) $score += 3;
        $total += 23;

        return (int) round(($score / $total) * 100);
    }
}
