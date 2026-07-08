<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            [
                'code' => 'FREE',
                'name' => 'Free',
                'price' => 0,
                'duration_days' => 999999,
                'daily_limit' => 10,
                'monthly_limit' => 300,
                'features' => [
                    '10 Apply / Hari',
                    '1.500 Apply / Bulan',
                    'Auto Apply'
                ],
            ],
            [
                'code' => 'STARTER',
                'name' => 'Starter',
                'price' => 15000,
                'duration_days' => 30,
                'daily_limit' => 50,
                'monthly_limit' => 1500,
                'features' => [
                    '50 Apply / Hari',
                    '1.500 Apply / Bulan',
                    'Auto Apply'
                ],
            ],

            [
                'code' => 'PROFESSIONAL',
                'name' => 'Professional',
                'price' => 45000,
                'duration_days' => 30,
                'daily_limit' => 200,
                'monthly_limit' => 6000,
                'features' => [
                    '250 Apply / Hari',
                    '6.000 Apply / Bulan',
                    'Auto Apply'
                ],
            ],

            [
                'code' => 'PREMIUM',
                'name' => 'Premium',
                'price' => 150000,
                'duration_days' => 30,
                'daily_limit' => 1000,
                'monthly_limit' => 30000,
                'features' => [
                    '1.000 Apply / Hari',
                    '30.000 Apply / Bulan',
                    'Priority Queue'
                ],
            ],

        ];

        foreach ($packages as $package) {

            Package::updateOrCreate(
                ['code' => $package['code']],
                [
                    'name' => $package['name'],
                    'is_active' => true,
                    'price' => $package['price'],
                    'duration_days' => $package['duration_days'],
                    'daily_limit' => $package['daily_limit'],
                    'monthly_limit' => $package['monthly_limit'],
                    'features' => json_encode($package['features']),
                ]
            );

        }
    }
}
