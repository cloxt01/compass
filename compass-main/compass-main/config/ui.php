<?php

return [
    'brand' => [
        'name' => env('APP_NAME', 'Compass'),
        'tagline' => 'Auto-apply workspace',
        'logo' => 'icon.png',
    ],
    'navigation' => [
        [
            'label' => 'Dashboard',
            'route' => 'dashboard',
            'icon' => 'fas fa-chart-line',
        ],
        [
            'label' => 'Apply',
            'route' => 'apply',
            'icon' => 'fas fa-bolt',
        ],
        [
            'label' => 'Profile',
            'route' => 'profile',
            'icon' => 'fas fa-user-circle',
        ],
    ],
];
