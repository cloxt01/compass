# Compass Design System

A comprehensive design system for the Laravel application, built following UI/UX Pro Max Skill principles. This design system provides consistent styling, theming, and reusable components to ensure a cohesive user experience across the application.

## Table of Contents

- [Overview](#overview)
- [Installation](#installation)
- [Design Tokens](#design-tokens)
- [Component Library](#component-library)
- [Usage Guidelines](#usage-guidelines)
- [Customization](#customization)
- [Theming](#theming)
- [Best Practices](#best-practices)
- [Contributing](#contributing)

## Overview

The Compass Design System is a collection of design tokens, utilities, and reusable Blade components that implement a dark-themed UI following modern design principles. It includes:

- **Design Tokens**: CSS custom properties for colors, typography, spacing, shadows, and more
- **Utility Classes**: Pre-built classes for rapid development
- **Blade Components**: Reusable, accessible components (Button, Input, Card, Badge)
- **Dark Theme Support**: Built-in dark theme with thoughtful color contrasts

## Installation

The design system is already integrated into the Laravel application. To use it:

1. The design system tokens are automatically loaded via the application's CSS
2. Components are available as Blade components under the `x-` namespace
3. No additional installation steps are required

### Manual Inclusion (if needed)

If you need to manually include the design system tokens:

```blade
<!-- In your Blade layout or view -->
<head>
    <!-- Design System Tokens -->
    <link rel="stylesheet" href="{{ mix('css/design-system.css') }}">
    
    <!-- Or include directly -->
    <style>
        @import url('{{ asset('design-system/tokens/comprehensive.css') }}');
    </style>
</head>
```

## Design Tokens

The design system uses CSS custom properties (variables) to ensure consistency and easy theming.

### File Structure

```
design-system/
├── tokens/
│   ├── colors.css          # Basic color tokens
│   └── comprehensive.css   # Complete design system
└── Compass/
    └── components/         # Reusable Blade components
```

### Using Design Tokens in CSS

```css
/* Access design system tokens */
.element {
    background-color: var(--color-bg-primary);
    color: var(--color-text-primary);
    padding: var(--spacing-4);
    border-radius: var(--radius-md);
    box-shadow: var(--shadow);
    font-size: var(--text-lg);
    font-weight: var(--font-weight-semibold);
}
```

### Token Categories

#### Color System
- **Primary**: `--color-primary-50` through `--color-primary-950`
- **Background**: `--color-bg-primary` through `--color-bg-active`
- **Text**: `--color-text-primary` through `--color-text-disabled`
- **Semantic**: Success, warning, error, info colors
- **Borders**: `--color-border`, `--color-border-hover`, etc.

#### Typography
- **Font Families**: `--font-sans`, `--font-serif`, `--font-mono`
- **Sizes**: `--text-xs` through `--text-9xl`
- **Weights**: `--font-weight-thin` through `--font-weight-black`
- **Line Heights**: `--leading-none` through `--leading-loose`
- **Letter Spacing**: `--tracking-tighter` through `--tracking-widest`

#### Spacing
- 8px-based scale: `--spacing-0` through `--spacing-96`
- Includes half increments for fine-tuning

#### Shadows
- `--shadow-sm` through `--shadow-2xl`
- `--shadow-inner` and `--shadow-none`

#### Border Radius
- `--radius-none` through `--radius-full`

#### Additional Systems
- **Opacity**: `--opacity-0` through `--opacity-100`
- **Transitions**: `--transition-fast` through `--transition-slow`
- **Z-index**: `--z-index-0` through `--z-index-50`

## Component Library

The Compass Design System includes reusable Blade components located in `design-system/Compass/components/`.

### Available Components

#### Button (`x-button`)
A versatile button component with multiple variants, sizes, and states.

**Props:**
- `type` (string): Button type (button, submit, reset) - default: 'button'
- `variant` (string): Visual variant (primary, secondary, outline, danger, success) - default: 'primary'
- `size` (string): Size (xs, sm, md, lg, xl) - default: 'md'
- `disabled` (boolean): Disabled state - default: false
- `block` (boolean): Full width - default: false
- `outline` (boolean): Outline variant - default: false
- `round` (boolean): Fully rounded - default: false
- `icon` (string): Icon HTML - default: null
- `iconPosition` (string): Icon position (left, right) - default: 'left'
- `loading` (boolean): Loading state - default: false

**Usage:**
```blade
<x-button variant="primary" size="lg">
    Click Me
</x-button>

<x-button variant="outline" size="sm" icon="<svg>...</svg>" iconPosition="left">
    Action
</x-button>

<x-button variant="danger" loading>
    Processing...
</x-button>
```

#### Input (`x-input`)
A flexible input component with support for prefixes, suffixes, icons, and validation.

**Props:**
- `type` (string): Input type (text, email, password, etc.) - default: 'text'
- `name` (string): Field name - default: ''
- `id` (string): Field ID - default: ''
- `value` (string): Field value - default: ''
- `placeholder` (string): Placeholder text - default: ''
- `disabled` (boolean): Disabled state - default: false
- `readonly` (boolean): Read-only state - default: false
- `required` (boolean): Required field - default: false
- `size` (string): Size (xs, sm, md, lg, xl) - default: 'md'
- `variant` (string): Visual variant (default, outline, filled) - default: 'default'
- `error` (string): Error message - default: null
- `icon` (string): Icon HTML - default: null
- `iconPosition` (string): Icon position (left, right) - default: 'left'
- `prefix` (string): Prefix text - default: null
- `suffix` (string): Suffix text - default: null
- `autocomplete` (string): Autocomplete attribute - default: 'off'

**Usage:**
```blade
<x-input type="email" placeholder="Enter your email" size="lg" variant="outline" />
<x-input type="password" icon="<svg>...</svg>" iconPosition="left" placeholder="Password" />
<x-input type="text" value="John Doe" disabled />
<x-input type="text" label="Username" error="This field is required" />
```

#### Card (`x-card`)
A container component with elevation, hover effects, and clickable capabilities.

**Props:**
- `variant` (string): Visual variant (default, primary, secondary, outline, elevated) - default: 'default'
- `size` (string): Padding size (xs, sm, md, lg, xl) - default: 'md'
- `padding` (boolean): Apply padding - default: true
- `shadow` (boolean): Apply shadow - default: true
- `border` (boolean): Apply border - default: true
- `rounded` (boolean): Apply border radius - default: true
- `hover` (boolean): Enable hover effects - default: false
- `clickable` (boolean): Make card clickable - default: false
- `class` (string): Additional CSS classes - default: ''

**Usage:**
```blade
<x-card variant="elevated" size="lg" hover>
    <h3 class="text-lg font-bold">Card Title</h3>
    <p class="text-text-tertiary">Card content goes here.</p>
</x-card>

<x-card clickable class="cursor-pointer">
    <!-- Card content -->
</x-card>
```

#### Badge (`x-badge`)
A small count or status indicator component.

**Props:**
- `variant` (string): Visual variant (primary, secondary, success, danger, warning, info, light, dark) - default: 'primary'
- `size` (string): Size (xs, sm, md, lg, xl) - default: 'md'
- `pill` (boolean): Pill shape - default: false
- `outline` (boolean): Outline style - default: false
- `class` (string): Additional CSS classes - default: ''

**Usage:**
```blade
<x-badge variant="success" size="sm">
    99+
</x-badge>

<x-badge variant="outline" variant="warning" pill>
    New
</x-badge>
```

## Usage Guidelines

### In Blade Templates

Components are automatically registered and can be used directly:

```blade
<div class="p-6">
    <x-card variant="elevated">
        <x-button variant="primary" size="lg">
            Get Started
        </x-button>
        
        <x-input type="email" placeholder="Enter your email" class="mt-4" />
    </x-card>
</div>
```

### In CSS/SCSS

Leverage the design system tokens for custom styling:

```scss
.custom-component {
    @apply bg-bg-primary text-text-primary;
    
    &:hover {
        @apply bg-bg-hover;
    }
    
    /* Or using var() directly */
    background-color: var(--color-bg-primary);
    color: var(--color-text-primary);
    
    &:hover {
        background-color: var(--color-bg-hover);
    }
}
```

### Responsive Design

Use the spacing system for consistent responsive design:

```blade
<div class="p-4 md:p-6 lg:p-8">
    <!-- Content -->
</div>
```

## Customization

### Extending Design Tokens

To add or modify design tokens, edit the files in `design-system/tokens/`:

1. **colors.css** - For basic color overrides
2. **comprehensive.css** - For complete design system modifications

Example: Adding a new color
```css
/* In comprehensive.css */
:root {
    --color-accent-500: #ff6b6b;
    --color-accent-600: #ff5252;
}
```

### Creating Custom Components

When creating new components, follow these guidelines:

1. **Use Design Tokens**: Always reference design system tokens instead of hardcoded values
2. **Follow Naming Conventions**: Use descriptive, consistent prop names
3. **Implement Accessibility**: Include proper ARIA labels, keyboard navigation, and focus states
4. **Document Props**: Clearly document all available props and their defaults
5. **Follow Blade Best Practices**: Use proper escaping, type hinting, and default values

Example custom component structure:
```blade
{{-- resources/views/components/custom-component.blade.php --}}
<?php
$props = [
    'variant' => 'default',
    'size' => 'md',
    // ... other props with defaults
];

foreach ($props as $key => $default) {
    $$key = ${$key} ?? $default;
}
?>

<div class="<!-- token-based classes -->">
    <!-- Component content -->
</div>
```

### Overriding Component Styles

To override component styles in your application:

```blade
<!-- In your view or layout -->
<style>
    /* Override button primary background */
    .btn-primary-override {
        @apply bg-primary-600 hover:bg-primary-700;
    }
</style>

<x-button variant="primary" class="btn-primary-override">
    Custom Styled Button
</x-button>
```

## Theming

### Dark Theme (Default)

The design system is built with a dark theme as the default:

```css
:root {
    --color-bg-primary: #0a0a0a;
    --color-text-primary: #ffffff;
    /* ... dark theme values */
}
```

### Light Theme Support

To implement a light theme, override the variables:

```css
.light-theme {
    --color-bg-primary: #ffffff;
    --color-text-primary: #0a0a0a;
    --color-bg-secondary: #f8f9fa;
    /* ... light theme values */
}
```

Then apply the class to your HTML element:
```blade
<body class="light-theme">
    <!-- Application content -->
</body>
```

### Theme Switching

Implement theme switching with JavaScript:

```javascript
function toggleTheme() {
    document.documentElement.classList.toggle('dark');
    document.documentElement.classList.toggle('light');
    
    // Save preference
    const isDark = document.documentElement.classList.contains('dark');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
}

// On page load
document.addEventListener('DOMContentLoaded', () => {
    const savedTheme = localStorage.getItem('theme') || 'dark';
    if (savedTheme === 'light') {
        document.documentElement.classList.add('light');
        document.documentElement.classList.remove('dark');
    }
});
```

## Best Practices

### Consistency
- Always use design system tokens instead of hardcoded values
- Follow the 8px spacing grid for layout and sizing
- Use the defined typography scale for text elements
- Apply consistent border radius and shadow usage

### Accessibility
- Ensure sufficient color contrast (WCAG AA/AAA compliance)
- Provide meaningful focus states for interactive elements
- Use semantic HTML elements where appropriate
- Include proper labels and ARIA attributes for form elements
- Ensure touch targets are at least 44x44px

### Performance
- Leverage CSS custom properties for runtime theming
- Minimize unnecessary re-renders in Blade components
- Use lazy loading for non-critical components
- Optimize icons and images

### Maintenance
- Keep components focused and single-responsibility
- Document all props and their default values
- Follow existing code patterns and conventions
- Test components in isolation and in context
- Update documentation when making changes

## Contributing

### Adding New Components

1. Create the component file in `design-system/Compass/components/`
2. Follow the existing component structure and patterns
3. Use design system tokens for all styling
4. Add comprehensive prop documentation
5. Include usage examples in the component's docblock
6. Add the component to this README.md

### Modifying Design Tokens

1. Edit the appropriate file in `design-system/tokens/`
2. Ensure changes maintain proper contrast and accessibility
3. Update any affected components
4. Document breaking changes
5. Test changes across different views and components

### Reporting Issues

Please report any issues or suggestions through the project's issue tracking system.

## License

This design system is part of the Compass Laravel application and follows the project's licensing terms.

---

*Built with UI/UX Pro Max Skill principles for consistent, accessible, and beautiful user interfaces.*