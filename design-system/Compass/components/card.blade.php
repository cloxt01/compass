<?php
/**
 * Card Component - UI/UX Pro Max Skill Inspired
 * 
 * A reusable card component that follows the design system tokens
 * and implements elevation and spacing best practices.
 */

$variant = $variant ?? 'default';
$size = $size ?? 'md';
$padding = $padding ?? true;
$shadow = $shadow ?? true;
$border = $border ?? true;
$rounded = $rounded ?? true;
$hover = $hover ?? false;
$clickable = $clickable ?? false;
$class = $class ?? '';

// Variant configurations
$variants = [
    'default' => [
        'bg' => 'bg-bg-secondary',
        'border' => 'border-border',
    ],
    'primary' => [
        'bg' => 'bg-primary-500',
        'border' => 'border-transparent',
    ],
    'secondary' => [
        'bg' => 'bg-bg-tertiary',
        'border' => 'border-border',
    ],
    'outline' => [
        'bg' => 'bg-transparent',
        'border' => 'border-border',
    ],
    'elevated' => [
        'bg' => 'bg-bg-tertiary',
        'border' => 'border-transparent',
    ]
];

// Size configurations (for padding)
$sizes = [
    'xs' => [
        'p' => 'p-2',
    ],
    'sm' => [
        'p' => 'p-3',
    ],
    'md' => [
        'p' => 'p-4',
    ],
    'lg' => [
        'p' => 'p-5',
    ],
    'xl' => [
        'p' => 'p-6',
    ]
];

// Get variant and size configs
$variantConfig = $variants[$variant] ?? $variants['default'];
$sizeConfig = $sizes[$size] ?? $sizes['md'];

// Build classes
$classes = [
    // Base classes
    'block',
    'transition-all',
    'focus:outline-none',
    
    // Variant classes
    $variantConfig['bg'],
    $variantConfig['border'],
    
    // Size classes (padding)
    $padding ? $sizeConfig['p'] : 'p-0',
    
    // Border
    $border ? '' : 'border-0',
    
    // Radius
    $rounded ? 'rounded-lg' : 'rounded-none',
    
    // Shadow
    $shadow && !$hover ? 'shadow' : '',
    $shadow && $hover ? 'shadow hover:shadow-md transition-shadow' : '',
    
    // Hover effects
    $hover && !$shadow ? 'hover:bg-bg-hover transition-colors' : '',
    
    // Clickable
    $clickable ? 'cursor-pointer hover:bg-bg-hover' : '',
    
    // Custom class
    $class
];

// Filter out empty classes
$classes = array_filter($classes);
$classString = implode(' ', $classes);
?>
<div 
    class="{{ $classString }}"
    {{ $clickable ? 'role="button" tabindex="0"' : '' }}
    {{ $clickable ? 'aria-pressed="false"' : '' }}
>
    {{ $slot }}
</div>

<?php if($clickable): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('[role="button"]');
        cards.forEach(card => {
            card.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.click();
                }
            });
            
            card.addEventListener('mousedown', function() {
                this.setAttribute('aria-pressed', 'true');
            });
            
            card.addEventListener('mouseup', function() {
                this.setAttribute('aria-pressed', 'false');
            });
            
            card.addEventListener('mouseleave', function() {
                this.setAttribute('aria-pressed', 'false');
            });
        });
    });
</script>
<?php endif; ?>