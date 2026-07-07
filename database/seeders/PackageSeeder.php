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
                'code' => 'STARTER',
                'name' => 'Starter',
                'price' => 49000,
                'duration_days' => 30,
                'daily_limit' => 50,
                'monthly_limit' => 1500,
                'features' => [
                    '50 Apply / Hari',
                    '1.500 Apply / Bulan',
                    'Auto Apply',
                    'Email Support',
                ],
            ],

            [
                'code' => 'PRO',
                'name' => 'Professional',
                'price' => 99000,
                'duration_days' => 30,
                'daily_limit' => 250,
                'monthly_limit' => 7500,
                'features' => [
                    '250 Apply / Hari',
                    '7.500 Apply / Bulan',
                    'Priority Queue',
                    'Email Support',
                    'Telegram Notification',
                ],
            ],

            [
                'code' => 'BUSINESS',
                'name' => 'Business',
                'price' => 199000,
                'duration_days' => 30,
                'daily_limit' => 1000,
                'monthly_limit' => 30000,
                'features' => [
                    '1.000 Apply / Hari',
                    '30.000 Apply / Bulan',
                    'Priority Queue',
                    'API Access',
                    'Telegram Notification',
                    'Priority Support',
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
