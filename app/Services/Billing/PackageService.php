<?php

namespace App\Services\Billing;

use App\Models\Package;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PackageService
{
    /**
     * Semua package yang aktif.
     */
    public function all(): Collection
    {
        return Package::where('is_active', true)
            ->orderBy('price')
            ->get();
    }

    /**
     * Cari package berdasarkan ID.
     */
    public function find(int $id): Package
    {
        return Package::where('is_active', true)
            ->findOrFail($id);
    }

    /**
     * Cari package berdasarkan code.
     */
    public function findByCode(string $code): Package
    {
        return Package::where('code', $code)
            ->where('is_active', true)
            ->firstOrFail();
    }

    /**
     * Membuat package baru.
     */
    public function create(array $data): Package
    {
        return Package::create([
            'code'           => $data['code'],
            'name'           => $data['name'],
            'price'          => $data['price'],
            'duration_days'  => $data['duration_days'],
            'daily_limit'    => $data['daily_limit'],
            'monthly_limit'  => $data['monthly_limit'],
            'features'       => $data['features'] ?? [],
            'is_active'      => true,
        ]);
    }

    /**
     * Update package.
     */
    public function update(Package $package, array $data): Package
    {
        $package->update([
            'name'           => $data['name'] ?? $package->name,
            'price'          => $data['price'] ?? $package->price,
            'duration_days'  => $data['duration_days'] ?? $package->duration_days,
            'daily_limit'    => $data['daily_limit'] ?? $package->daily_limit,
            'monthly_limit'  => $data['monthly_limit'] ?? $package->monthly_limit,
            'features'       => $data['features'] ?? $package->features,
        ]);

        return $package->fresh();
    }

    /**
     * Nonaktifkan package.
     */
    public function deactivate(Package $package): void
    {
        $package->update([
            'is_active' => false,
        ]);
    }

    /**
     * Aktifkan kembali package.
     */
    public function activate(Package $package): void
    {
        $package->update([
            'is_active' => true,
        ]);
    }

    /**
     * Ambil fitur package.
     */
    public function features(Package $package): array
    {
        return $package->features ?? [];
    }
}
