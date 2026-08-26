<?php
/**
 * Badge Component - UI/UX Pro Max Skill Inspired
 * 
 * A reusable badge component that follows the design system tokens
 * and implements visual hierarchy best practices.
 */

$variant = $variant ?? 'primary';
$size = $size ?? 'md';
$pill = $pill ?? false;
$outline = $outline ?? false;
$class = $class ?? '';

// Variant configurations
$variants = [
    'primary' => [
        'bg' => 'bg-primary-500',
        'text' => 'text-text-primary',
        'outline' => 'border border-primary-500 bg-transparent text-primary-500',
    ],
    'secondary' => [
        'bg' => 'bg-bg-tertiary',
        'text' => 'text-text-primary',
        'outline' => 'border border-border bg-transparent text-text-primary',
    ],
    'success' => [
        'bg' => 'bg-success-500',
        'text' => 'text-text-primary',
        'outline' => 'border border-success-500 bg-transparent text-success-500',
    ],
    'danger' => [
        'bg' => 'bg-error-500',
        'text' => 'text-text-primary',
        'outline' => 'border border-error-500 bg-transparent text-error-500',
    ],
    'warning' => [
        'bg' => 'bg-warning-500',
        'text' => 'text-text-primary',
        'outline' => 'border border-warning-500 bg-transparent text-warning-500',
    ],
    'info' => [
        'bg' => 'bg-info-500',
        'text' => 'text-text-primary',
        'outline' => 'border border-info-500 bg-transparent text-info-500',
    ],
    'light' => [
        'bg' => 'bg-bg-quaternary',
        'text' => 'text-text-secondary',
        'outline' => 'border border-border-hover bg-transparent text-text-tertiary',
    ],
    'dark' => [
        'bg' => 'bg-bg-primary',
        'text' => 'text-text-primary',
        'outline' => 'border border-border bg-transparent text-text-primary',
    ]
];

// Size configurations
$sizes = [
    'xs' => [
        'px' => 'px-1.5',
        'py' => 'py-0.5',
        'text' => 'text-xs',
        'leading' => 'leading-tight',
    ],
    'sm' => [
        'px' => 'px-2',
        'py' => 'py-0.5',
        'text' => 'text-xs',
        'leading' => 'leading-tight',
    ],
    'md' => [
        'px' => 'px-2.5',
        'py' => 'py-0.5',
        'text' => 'text-sm',
        'leading' => 'leading-tight',
    ],
    'lg' => [
        'px' => 'px-3',
        'py' => 'py-1',
        'text' => 'text-base',
        'leading' => 'leading-normal',
    ],
    'xl' => [
        'px' => 'px-4',
        'py' => 'py-1.5',
        'text' => 'text-lg',
        'leading' => 'leading-normal',
    ]
];

// Get variant and size configs
$variantConfig = $variants[$variant] ?? $variants['primary'];
$sizeConfig = $sizes[$size] ?? $sizes['md'];

// Build classes
$classes = [
    // Base classes
    'inline-flex items-center justify-center',
    'font-medium',
    'transition-all',
    'whitespace-nowrap',
    
    // Variant classes
    $outline ? $variantConfig['outline'] : $variantConfig['bg'] . ' ' . $variantConfig['text'],
    
    // Size classes
    $sizeConfig['px'],
    $sizeConfig['py'],
    $sizeConfig['text'],
    $sizeConfig['leading'],
    
    // Shape
    $pill ? 'rounded-full' : 'rounded-md',
    
    // Custom class
    $class
];

// Filter out empty classes
$classes = array_filter($classes);
$classString = implode(' ', $classes);
?>
<span class="{{ $classString }}">
    {{ $slot }}
</span>