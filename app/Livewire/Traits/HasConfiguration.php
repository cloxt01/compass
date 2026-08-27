<?php

namespace App\Livewire\Traits;

use Illuminate\Support\Facades\Auth;

trait HasConfiguration
{
    /**
     * Default konfigurasi untuk semua provider.
     */
    protected function defaultConfiguration(): array
    {
        return [
            'jobstreet' => [
                'enabled' => false,
                'keyword' => [],
                'batch' => 1,
                'resume' => '',
                'role' => '',
                'location' => '',
            ],
            'glints' => [
                'enabled' => false,
                'keyword' => [],
                'batch' => 1,
                'location_ids' => [],
                'location_names' => [],
            ],
            'auto_answer' => [
                'enabled' => false,
                'profile' => [
                    'ekspektasi_gaji' => null,
                    'notice_period' => null,
                    'bersedia_industri_banking' => null,
                    'kewarganegaraan' => null,
                    'sertifikasi' => [],
                ],
            ],
        ];
    }

    /**
     * Ambil konfigurasi terbaru dari database, di-merge dengan default.
     */
    protected function configuration(): array
    {
        $saved = Auth::user()
            ->fresh()
            ->apply_configuration ?? [];

        return array_replace_recursive(
            $this->defaultConfiguration(),
            $saved
        );
    }

    /**
     * Simpan perubahan konfigurasi dengan merge ke data terbaru.
     */
    protected function saveConfiguration(array $changes): array
    {
        $user = Auth::user()->fresh();

        $merged = array_replace_recursive(
            $this->defaultConfiguration(),
            $user->apply_configuration ?? [],
            $changes
        );

        $user->update([
            'apply_configuration' => $merged,
        ]);

        return $merged;
    }

}
