# Compass Design System Components

This directory contains reusable Blade components built following UI/UX Pro Max Skill principles and utilizing the Compass Design System tokens.

## Components

### Button (`button.blade.php`)
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

### Input (`input.blade.php`)
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

### Card (`card.blade.php`)
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

### Badge (`badge.blade.php`)
A small count or status indicator component.

**Props:**
- `variant` (string): Visual variant (primary, secondary, success, danger, warning, info, light, dark) - default: 'primary'
- `size` (string): Size (xs, sm, md, lg, xl) - default: 'md'
- `pill` (boolean): Pill shape - default: false
- `outline` (boolean): Outline style - default: false
- `class` (string): Additional CSS classes - default: ''

## Usage Examples

### Button
```blade
<x-button variant="primary" size="lg">
    Click Me
</x-button>

<x-button variant="outline" size="sm" icon="<svg>...</svg>" iconPosition="left">
    Action
</x-button>
```

### Input
```blade
<x-input type="email" placeholder="Enter your email" size="lg" variant="outline" />
<x-input type="password" icon="<svg>...</svg>" iconPosition="left" placeholder="Password" />
```

### Card
```blade
<x-card variant="elevated" size="lg" hover>
    <h3 class="text-lg font-bold">Card Title</h3>
    <p class="text-text-tertiary">Card content goes here.</p>
</x-card>

<x-card clickable class="cursor-pointer">
    <!-- Card content -->
</x-card>
```

### Badge
```blade
<x-badge variant="success" size="sm">
    99+
</x-badge>

<x-badge variant="outline" variant="warning" pill>
    New
</x-badge>
```

## Design System Integration

All components automatically use the design system tokens defined in:
- `C:\xampp\compass\design-system\tokens\colors.css`
- `C:\xampp\compass\design-system\tokens\comprehensive.css`

This ensures consistent theming, spacing, typography, and visual hierarchy across the application.