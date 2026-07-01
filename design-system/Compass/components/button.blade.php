<?php
/**
 * Button Component - UI/UX Pro Max Skill Inspired
 * 
 * A reusable button component that follows the design system tokens
 * and implements accessibility best practices.
 */

$type = $type ?? 'button';
$variant = $variant ?? 'primary';
$size = $size ?? 'md';
$disabled = $disabled ?? false;
$block = $block ?? false;
$outline = $outline ?? false;
$round = $round ?? false;
$icon = $icon ?? null;
$iconPosition = $iconPosition ?? 'left'; // left or right
$loading = $loading ?? false;

// Variant configurations
$variants = [
    'primary' => [
        'bg' => 'bg-primary-500 hover:bg-primary-600 active:bg-primary-700',
        'text' => 'text-text-primary',
        'border' => 'border-transparent',
        'shadow' => 'shadow-sm hover:shadow-md'
    ],
    'secondary' => [
        'bg' => 'bg-bg-tertiary hover:bg-bg-hover',
        'text' => 'text-text-primary',
        'border' => 'border-border',
        'shadow' => 'shadow-sm hover:shadow-md'
    ],
    'outline' => [
        'bg' => 'bg-transparent hover:bg-bg-hover',
        'text' => 'text-text-primary',
        'border' => 'border-border hover:border-border-hover',
        'shadow' => 'shadow-sm'
    ],
    'danger' => [
        'bg' => 'bg-error-500 hover:bg-error-600 active:bg-error-700',
        'text' => 'text-text-primary',
        'border' => 'border-transparent',
        'shadow' => 'shadow-sm hover:shadow-md'
    ],
    'success' => [
        'bg' => 'bg-success-500 hover:bg-success-600 active:bg-success-700',
        'text' => 'text-text-primary',
        'border' => 'border-transparent',
        'shadow' => 'shadow-sm hover:shadow-md'
    ]
];

// Size configurations
$sizes = [
    'xs' => [
        'px' => 'px-2',
        'py' => 'py-1',
        'text' => 'text-xs',
        'gap' => 'gap-1'
    ],
    'sm' => [
        'px' => 'px-3',
        'py' => 'py-2',
        'text' => 'text-sm',
        'gap' => 'gap-1'
    ],
    'md' => [
        'px' => 'px-4',
        'py' => 'py-2',
        'text' => 'text-base',
        'gap' => 'gap-2'
    ],
    'lg' => [
        'px' => 'px-5',
        'py' => 'py-3',
        'text' => 'text-lg',
        'gap' => 'gap-2'
    ],
    'xl' => [
        'px' => 'px-6',
        'py' => 'py-4',
        'text' => 'text-xl',
        'gap' => 'gap-3'
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
    'transition-colors',
    'focus:outline-none focus:ring-2 focus:ring-offset-2',
    'focus:ring-primary-500',
    'disabled:opacity-50 disabled:pointer-events-none',
    'whitespace-nowrap',
    
    // Variant classes
    $variantConfig['bg'],
    $variantConfig['text'],
    $variantConfig['border'],
    $variantConfig['shadow'],
    
    // Size classes
    $sizeConfig['px'],
    $sizeConfig['py'],
    $sizeConfig['text'],
    'gap-' . $sizeConfig['gap'],
    
    // Additional modifiers
    $block ? 'w-full' : '',
    $round ? 'rounded-full' : 'rounded-md',
    $loading ? 'pointer-events-none' : '',
    
    // Icon positioning
    $icon && $iconPosition === 'left' ? 'text-left' : '',
    $icon && $iconPosition === 'right' ? 'text-right' : ''
];

// Filter out empty classes
$classes = array_filter($classes);
$classString = implode(' ', $classes);
?>
<button 
    type="{{ $type }}"
    class="{{ $classString }}"
    {{ $disabled ? 'disabled' : '' }}
    {{ $loading ? 'aria-busy="true"' : '' }}
>
    @if($loading)
        <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
        </svg>
    @else
        @if($icon && $iconPosition === 'left')
            {!! $icon !!}
        @endif
        
        {{ $slot }}
        
        @if($icon && $iconPosition === 'right')
            {!! $icon !!}
        @endif
    @endif
</button>