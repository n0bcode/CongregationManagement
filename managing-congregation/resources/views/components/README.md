# Blade Components Documentation

This directory contains reusable Blade components following the **Sanctuary & Stone Design System** as defined in the UI/UX Sync Rules.

## Component Catalog

### Form Components

#### `<x-input>`

Text input field with label, validation, and accessibility features.

**Props:**

-   `label` (string): Field label
-   `name` (string): Input name attribute
-   `type` (string): Input type (default: 'text')
-   `value` (string): Default value
-   `required` (bool): Mark as required
-   `error` (string): Custom error message
-   `help` (string): Help text

**Usage:**

```blade
<x-input
    label="Full Name"
    name="name"
    type="text"
    required
    help="Enter your full legal name"
/>
```

**Accessibility:**

-   Minimum 48px height (touch target)
-   High contrast focus ring
-   Associated label with `for` attribute
-   Error messages linked via ARIA

---

#### `<x-select>`

Dropdown select field with options.

**Props:**

-   `label` (string): Field label
-   `name` (string): Select name attribute
-   `options` (array): Key-value pairs for options
-   `value` (string): Selected value
-   `placeholder` (string): Placeholder option
-   `required` (bool): Mark as required

**Usage:**

```blade
<x-select
    label="Community"
    name="community_id"
    :options="$communities"
    placeholder="Select a community"
    required
/>
```

---

#### `<x-textarea>`

Multi-line text input.

**Props:**

-   `label` (string): Field label
-   `name` (string): Textarea name
-   `value` (string): Default value
-   `rows` (int): Number of rows (default: 4)
-   `required` (bool): Mark as required

**Usage:**

```blade
<x-textarea
    label="Notes"
    name="notes"
    rows="6"
/>
```

---

### UI Components

#### `<x-button>`

Accessible button with variants and sizes.

**Props:**

-   `variant` (string): 'primary', 'secondary', 'success', 'danger' (default: 'primary')
-   `size` (string): 'sm', 'md', 'lg' (default: 'md')
-   `type` (string): 'button', 'submit', 'reset' (default: 'button')
-   `href` (string): If provided, renders as link
-   `icon` (string): HTML icon to display

**Usage:**

```blade
<x-button variant="primary" type="submit">
    Save Changes
</x-button>

<x-button variant="secondary" href="{{ route('members.index') }}">
    Cancel
</x-button>
```

**Accessibility:**

-   Minimum 48px height
-   High contrast focus states
-   Disabled state styling

---

#### `<x-card>`

Container card with optional header.

**Props:**

-   `title` (string): Card title
-   `subtitle` (string): Card subtitle
-   `padding` (bool): Apply padding (default: true)

**Usage:**

```blade
<x-card title="Member Details" subtitle="Personal information">
    <p>Card content here</p>
</x-card>
```

---

#### `<x-alert>`

Alert/notification box with types.

**Props:**

-   `type` (string): 'success', 'error', 'warning', 'info' (default: 'info')
-   `title` (string): Alert title
-   `dismissible` (bool): Show close button

**Usage:**

```blade
<x-alert type="success" title="Success!" dismissible>
    Your changes have been saved successfully.
</x-alert>
```

**UX Pattern:** "Kindness in Code" - friendly, helpful messages

---

### Specialized Components

#### `<x-status-card>`

Pastoral status card for dashboard metrics.

**Props:**

-   `variant` (string): 'peace', 'attention', 'pending' (default: 'peace')
-   `icon` (string): SVG icon HTML
-   `title` (string): Card title
-   `value` (string): Main metric value
-   `description` (string): Supporting text

**Usage:**

```blade
<x-status-card
    variant="peace"
    title="Active Members"
    value="127"
    description="All communities"
    icon='<svg>...</svg>'
/>
```

**Design Pattern:** From UX Spec Section 2.5 "The Pastoral Status Card"

---

#### `<x-ledger-row>`

Digital ledger row for financial/activity lists.

**Props:**

-   `date` (Carbon): Transaction date
-   `description` (string): Main description
-   `category` (string): Category/subcategory
-   `amount` (string): Amount or status
-   `href` (string): Optional link

**Usage:**

```blade
<x-ledger-row
    :date="$expense->date"
    description="Medical supplies"
    category="Healthcare"
    amount="$125.00"
    href="{{ route('expenses.show', $expense) }}"
/>
```

**Design Pattern:** From UX Spec "The Digital Ledger" - WhatsApp-style list

**Interaction:**

-   Swipe left to delete (mobile)
-   Tap to edit
-   Full-row touch target (64px min)

---

#### `<x-feast-timeline>`

Horizontal timeline for liturgical events.

**Props:**

-   `events` (array): Array of event objects with `date` and `name`

**Usage:**

```blade
<x-feast-timeline :events="[
    (object)['date' => now()->addDays(2), 'name' => 'St. Francis'],
    (object)['date' => now()->addDays(5), 'name' => 'St. Teresa'],
]" />
```

**Design Pattern:** From UX Spec "The Feast Timeline"

**States:**

-   Past: Dimmed (gray)
-   Today: Highlighted (gold with ring)
-   Future: Standard (slate)

---

### Navigation Components

#### `<x-nav-link>`

Navigation link with active state.

**Props:**

-   `active` (bool): Is current page
-   `icon` (string): Optional icon HTML

**Usage:**

```blade
<x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
    Dashboard
</x-nav-link>
```

---

#### `<x-responsive-nav-link>`

Mobile-responsive navigation link.

**Props:**

-   `active` (bool): Is current page

**Usage:**

```blade
<x-responsive-nav-link :href="route('members.index')" :active="request()->routeIs('members.*')">
    Members
</x-responsive-nav-link>
```

---

### Utility Components

#### `<x-loading>`

Loading spinner with message.

**Props:**

-   `text` (string): Loading message (default: 'Loading...')

**Usage:**

```blade
<x-loading text="Fetching members..." />
```

---

## Design System Reference

### Colors (Sanctuary & Stone Palette)

```css
Primary: #334155 (Deep Slate Blue)
Background: #F5F5DC (Warm Stone/Cream)
Accent: #D4AF37 (Muted Gold)

Semantic:
- Success: #10B981 (Emerald)
- Warning: #F59E0B (Amber)
- Error: #F43F5E (Rose)
```

### Typography

```css
Headings: 'Merriweather', serif
Body: 'Inter', sans-serif
Base Size: 18px (for elderly users)
Line Height: 1.6
```

### Spacing

```css
Touch Targets: 48x48px minimum
Card Padding: 24px (1.5rem)
Grid Gap: 24px (1.5rem)
```

### Accessibility

All components follow **WCAG 2.1 Level AA**:

-   Contrast ratio: 4.5:1 minimum
-   Touch targets: 48px minimum
-   Keyboard navigation: Full support
-   Screen readers: ARIA labels
-   Motion: Respects `prefers-reduced-motion`

---

## Component Development Guidelines

### Creating New Components

1. **Check UX Spec**: Verify component is defined in `docs/ux-design-specification.md`
2. **Follow Naming**: Use kebab-case for component names
3. **Accessibility First**: Include ARIA attributes, focus states
4. **Mobile First**: Design for 360px width first
5. **Document**: Add to this README with usage examples

### Component Template

```blade
@props([
    'variant' => 'default',
    'required' => false,
])

@php
$classes = match($variant) {
    'primary' => 'bg-amber-600',
    default => 'bg-stone-200',
};
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</div>
```

### Testing Components

```bash
# Visual regression test
php artisan dusk:make ComponentNameTest

# Accessibility audit
npm run test:a11y
```

---

## Related Documentation

-   **UI/UX Sync Rules**: `plans/ui-ux-system-sync-rules.md`
-   **UX Design Spec**: `docs/ux-design-specification.md`
-   **Architecture**: `docs/architecture.md`
-   **Tailwind Config**: `tailwind.config.js`

---

**Last Updated**: 2025-12-03  
**Version**: 1.0
