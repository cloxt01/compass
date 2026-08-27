<?php

namespace App\Services\AI;

use App\Models\User;

/**
 * Bangun profil kandidat untuk konteks AI.
 *
 * Sumber data:
 * 1. Profile dari platform (Glints/Jobstreet) — data yang sudah ada di provider
 * 2. Input manual dari user's apply_configuration.auto_answer.profile
 *    (personal: gaji, notice period, dsb yang tidak ada di platform)
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
            if (!is_array($manual)) {
                $manual = [];
            }
        }

        $base = match ($provider) {
            'glints' => self::fromGlints($platformProfile),
            'jobstreet' => self::fromJobstreet($platformProfile),
            default => [],
        };

        // Manual profile overrides + supplements platform profile
        $merged = array_merge($base, array_filter($manual, fn ($v) => $v !== null && $v !== ''));

        // Standard keys yang diharapkan AI (semua boleh null)
        return array_merge([
            'nama' => null,
            'email' => null,
            'phone' => null,
            'pendidikan' => null,
            'pengalaman' => null,
            'lokasi' => null,
            'skills' => [],
            'sertifikasi' => [],
            'gaji_terakhir' => null,
            'ekspektasi_gaji' => null,
            'notice_period' => null,
            'bersedia_industri_banking' => null,
            'kewarganegaraan' => null,
        ], $merged);
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
            'pengalaman' => $exp,
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
        if ($years <= 0) return 'Fresh Graduate';
        if ($years < 3) return '1-3 tahun';
        if ($years < 5) return '3-5 tahun';
        if ($years < 10) return '5-10 tahun';
        return '10+ tahun';
    }
}
