<?php
/**
 * Input Component - UI/UX Pro Max Skill Inspired
 * 
 * A reusable input component that follows the design system tokens
 * and implements accessibility best practices.
 */

$type = $type ?? 'text';
$name = $name ?? '';
$id = $id ?? '';
$value = $value ?? '';
$placeholder = $placeholder ?? '';
$disabled = $disabled ?? false;
$readonly = $readonly ?? false;
$required = $required ?? false;
$size = $size ?? 'md';
$variant = $variant ?? 'default';
$error = $error ?? null;
$icon = $icon ?? null;
$iconPosition = $iconPosition ?? 'left'; // left or right
$prefix = $prefix ?? null;
$suffix = $suffix ?? null;
$autocomplete = $autocomplete ?? 'off';

// Size configurations
$sizes = [
    'xs' => [
        'px' => 'px-2',
        'py' => 'py-1',
        'text' => 'text-xs',
        'height' => 'h-8'
    ],
    'sm' => [
        'px' => 'px-3',
        'py' => 'py-2',
        'text' => 'text-sm',
        'height' => 'h-9'
    ],
    'md' => [
        'px' => 'px-4',
        'py' => 'py-2',
        'text' => 'text-base',
        'height' => 'h-10'
    ],
    'lg' => [
        'px' => 'px-5',
        'py' => 'py-3',
        'text' => 'text-lg',
        'height' => 'h-11'
    ],
    'xl' => [
        'px' => 'px-6',
        'py' => 'py-4',
        'text' => 'text-xl',
        'height' => 'h-12'
    ]
];

// Variant configurations
$variants = [
    'default' => [
        'bg' => 'bg-bg-tertiary',
        'border' => 'border-border',
        'text' => 'text-text-primary',
        'placeholder' => 'placeholder:text-text-tertiary',
        'focus' => 'focus:border-primary-500 focus:ring-2 focus:ring-primary-500 focus:ring-offset-0',
        'disabled' => 'disabled:bg-bg-quaternary disabled:opacity-50 disabled:cursor-not-allowed'
    ],
    'outline' => [
        'bg' => 'bg-transparent',
        'border' => 'border-border hover:border-border-hover',
        'text' => 'text-text-primary',
        'placeholder' => 'placeholder:text-text-tertiary',
        'focus' => 'focus:border-primary-500 focus:ring-2 focus:ring-primary-500 focus:ring-offset-0',
        'disabled' => 'disabled:bg-transparent disabled:opacity-50 disabled:cursor-not-allowed'
    ],
    'filled' => [
        'bg' => 'bg-bg-secondary',
        'border' => 'border-border',
        'text' => 'text-text-primary',
        'placeholder' => 'placeholder:text-text-tertiary',
        'focus' => 'focus:border-primary-500 focus:ring-2 focus:ring-primary-500 focus:ring-offset-0',
        'disabled' => 'disabled:bg-bg-quaternary disabled:opacity-50 disabled:cursor-not-allowed'
    ]
];

// Get size and variant configs
$sizeConfig = $sizes[$size] ?? $sizes['md'];
$variantConfig = $variants[$variant] ?? $variants['default'];

// Build classes
$classes = [
    // Base classes
    'w-full',
    'appearance-none',
    'block',
    'transition-all',
    'focus:outline-none',
    'focus:ring-offset-0',
    
    // Size classes
    $sizeConfig['px'],
    $sizeConfig['py'],
    $sizeConfig['text'],
    $sizeConfig['height'],
    
    // Variant classes
    $variantConfig['bg'],
    $variantConfig['border'],
    $variantConfig['text'],
    $variantConfig['placeholder'],
    $variantConfig['focus'],
    $variantConfig['disabled'],
    
    // Error state
    $error ? 'border-error-500 focus:border-error-500 focus:ring-error-500' : '',
    
    // Rounded
    'rounded-md'
];

// Filter out empty classes
$classes = array_filter($classes);
$classString = implode(' ', $classes);
?>
<div class="relative w-full">
    @if($prefix || $icon && $iconPosition === 'left')
        <div class="absolute left-0 top-0 flex h-full items-center pl-3 pointer-events-none">
            @if($icon && $iconPosition === 'left')
                {!! $icon !!}
            @endif
            @if($prefix && !$icon)
                <span class="text-text-tertiary">{{ $prefix }}</span>
            @endif
        </div>
    @endif
    
    @if($suffix || $icon && $iconPosition === 'right')
        <div class="absolute right-0 top-0 flex h-full items-center pr-3 pointer-events-none">
            @if($icon && $iconPosition === 'right')
                {!! $icon !!}
            @endif
            @if($suffix && !$icon)
                <span class="text-text-tertiary">{{ $suffix }}</span>
            @endif
        </div>
    @endif
    
    <input 
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $id }}"
        value="{{ $value }}"
        placeholder="{{ $placeholder }}"
        class="{{ $classString }} 
                {{ $prefix || ($icon && $iconPosition === 'left') ? 'pl-10' : 'pl-4' }}
                {{ $suffix || ($icon && $iconPosition === 'right') ? 'pr-10' : 'pr-4' }}
                {{ $readonly ? 'readonly bg-bg-quaternary cursor-not-allowed' : '' }}"
        {{ $disabled ? 'disabled' : '' }}
        {{ $readonly ? 'readonly' : '' }}
        {{ $required ? 'required' : '' }}
        autocomplete="{{ $autocomplete }}"
        aria-invalid="{{ $error ? 'true' : 'false' }}"
        @if($error)
            aria-describedby="{{ $id }}-error"
        @endif
    >
    
    @if($error)
        <p id="{{ $id }}-error" class="mt-1 text-xs text-error-500">
            {{ $error }}
        </p>
    @endif
</div>