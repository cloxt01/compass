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
                'duration_days' => 0,
                'daily_limit' => 10,
                'monthly_limit' => 50,
                'features' => [
                    '5 Apply / Hari',
                    '50 Apply / Bulan'
                ],
            ],
            [
                'code' => 'STARTER',
                'name' => 'Starter',
                'price' => 15000,
                'duration_days' => 30,
                'daily_limit' => 50,
                'monthly_limit' => 1000,
                'features' => [
                    '10 Apply / Hari',
                    '200 Apply / Bulan',
                    'Auto Apply'
                ],
            ],

            [
                'code' => 'PRO',
                'name' => 'Pro',
                'price' => 45000,
                'duration_days' => 30,
                'daily_limit' => 50,
                'monthly_limit' => 1000,
                'features' => [
                    '500 Apply / Hari',
                    '1.000 Apply / Bulan',
                    'Auto Apply',
                    'AI Auto Answer'
                ],
            ],

            [
                'code' => 'PREMIUM',
                'name' => 'Premium',
                'price' => 99000,
                'duration_days' => 30,
                'daily_limit' => 100,
                'monthly_limit' => 2000,
                'features' => [
                    '1.00 Apply / Hari',
                    '2.000 Apply / Bulan',
                    'Auto Apply',
                    'AI Auto Answer',
                    'Priority Queue',
                    'Analytics'
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
