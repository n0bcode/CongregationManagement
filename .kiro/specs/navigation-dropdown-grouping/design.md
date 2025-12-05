# Design Document

## Overview

This design implements a grouped navigation system using dropdown menus to organize related features. The solution leverages Alpine.js for interactivity and extends the existing dropdown component pattern used in the application. The navigation will maintain accessibility standards, support keyboard navigation, and work seamlessly across desktop and mobile devices.

## Architecture

### Component Structure

```
Navigation Bar
├── Logo & Brand
├── Primary Navigation (Desktop)
│   ├── Dashboard (standalone link)
│   ├── Management Dropdown
│   │   ├── Members
│   │   └── Documents (permission-based)
│   ├── Finance Dropdown
│   │   ├── Financials
│   │   └── Financial Reports
│   ├── Reports Dropdown
│   │   └── Demographic Reports
│   └── System Dropdown (permission-based)
│       └── Audit Logs
└── User Profile Dropdown (existing)

Responsive Navigation (Mobile)
├── Dashboard
├── Management Section
│   ├── Members
│   └── Documents (permission-based)
├── Finance Section
│   ├── Financials
│   └── Financial Reports
├── Reports Section
│   └── Demographic Reports
└── System Section (permission-based)
    └── Audit Logs
```

### Technology Stack

- **Frontend Framework**: Alpine.js (already in use)
- **Styling**: Tailwind CSS with Sanctuary & Stone design system
- **Component Pattern**: Blade components extending existing `x-dropdown`
- **State Management**: Alpine.js reactive data

## Components and Interfaces

### 1. Navigation Dropdown Component

Create a new Blade component `nav-dropdown.blade.php` that extends the dropdown pattern for navigation-specific needs.

**Props:**

- `label` (string): The dropdown trigger text
- `active` (boolean): Whether any child route is currently active
- `align` (string): Alignment of dropdown menu (default: 'left')

**Slots:**

- `trigger`: Custom trigger content (optional)
- `default`: Dropdown menu items

**Example Usage:**

```blade
<x-nav-dropdown label="Management" :active="request()->routeIs('members.*', 'documents.*')">
    <x-dropdown-link :href="route('members.index')">
        {{ __('Members') }}
    </x-dropdown-link>
    @can('viewAny', \App\Models\Document::class)
        <x-dropdown-link :href="route('documents.index')">
            {{ __('Documents') }}
        </x-dropdown-link>
    @endcan
</x-nav-dropdown>
```

### 2. Navigation Dropdown Link Component

Extend or create `dropdown-link.blade.php` to support active state detection within navigation dropdowns.

**Props:**

- `href` (string): Link destination
- `active` (boolean): Whether this link is currently active

### 3. Responsive Navigation Accordion

For mobile view, implement an accordion-style expansion for grouped items.

**Alpine.js State:**

```javascript
{
    openSection: null,
    toggleSection(section) {
        this.openSection = this.openSection === section ? null : section;
    }
}
```

## Data Models

No database changes required. This is a pure UI/UX enhancement.

## Correctness Properties

_A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees._

### Property 1: Dropdown visibility toggle

_For any_ dropdown menu, clicking the trigger should toggle the dropdown between open and closed states
**Validates: Requirements 1.3**

### Property 2: Active state propagation

_For any_ navigation dropdown, when a child route is active, the parent dropdown trigger should display active styling
**Validates: Requirements 1.4**

### Property 3: Permission-based visibility

_For any_ navigation item with permission requirements, the item should only be visible when the user has the required permissions
**Validates: Requirements 2.3, 5.3**

### Property 4: Click outside closes dropdown

_For any_ open dropdown menu, clicking outside the dropdown area should close the dropdown
**Validates: Requirements 1.5**

### Property 5: Mobile accordion expansion

_For any_ grouped section in mobile view, tapping the section header should expand/collapse that section
**Validates: Requirements 6.2**

### Property 6: Keyboard navigation focus management

_For any_ dropdown menu, pressing Tab should move focus through dropdown items in sequential order
**Validates: Requirements 8.3**

### Property 7: Escape key closes dropdown

_For any_ open dropdown menu, pressing the Escape key should close the dropdown
**Validates: Requirements 8.4**

### Property 8: Visual indicator rotation

_For any_ dropdown trigger, when the dropdown is open, the arrow icon should rotate to indicate the open state
**Validates: Requirements 7.1**

## Error Handling

### Permission Errors

- If a user loses permissions while viewing a page, the next navigation action will redirect them appropriately
- Empty dropdowns (all items hidden by permissions) should hide the entire dropdown

### JavaScript Errors

- If Alpine.js fails to load, navigation links should still be accessible (progressive enhancement)
- Fallback to CSS-only hover states for desktop if JavaScript is disabled

### Mobile Interaction Errors

- Prevent dropdown menus from extending beyond viewport boundaries
- Handle touch events properly to avoid conflicts with scroll gestures

## Testing Strategy

### Unit Tests

**Example-based tests:**

1. Test that dropdown component renders with correct props
2. Test that permission checks hide/show appropriate items
3. Test that active route detection works for nested routes
4. Test that mobile accordion state toggles correctly

**Edge cases:**

1. User with no permissions sees minimal navigation
2. Very long dropdown labels don't break layout
3. Rapid clicking doesn't cause state inconsistencies

### Property-Based Tests

We will use **Pest PHP** with **Pest Property Testing** plugin for property-based testing.

**Property tests to implement:**

1. **Property 1: Dropdown visibility toggle**

   - Generate random dropdown states (open/closed)
   - Verify clicking trigger always toggles state

2. **Property 2: Active state propagation**

   - Generate random route patterns
   - Verify parent dropdown is active when any child route matches

3. **Property 3: Permission-based visibility**
   - Generate random permission sets
   - Verify only authorized items are rendered

**Test configuration:**

- Minimum 100 iterations per property test
- Each test tagged with: `**Feature: navigation-dropdown-grouping, Property {number}: {property_text}**`

### Integration Tests

1. Test full navigation rendering with different user roles
2. Test navigation state persistence across page loads
3. Test mobile/desktop responsive behavior
4. Test keyboard navigation flow through entire navigation

### Browser Testing

1. Test dropdown interactions in Chrome, Firefox, Safari
2. Test touch interactions on iOS and Android
3. Test keyboard navigation with screen readers
4. Test with JavaScript disabled (graceful degradation)

## Implementation Details

### Desktop Navigation Structure

```blade
<div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex items-center">
    <!-- Dashboard - standalone -->
    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
        {{ __('Dashboard') }}
    </x-nav-link>

    <!-- Management Dropdown -->
    <x-nav-dropdown
        label="{{ __('Management') }}"
        :active="request()->routeIs('members.*', 'documents.*')">
        <x-dropdown-link :href="route('members.index')" :active="request()->routeIs('members.*')">
            {{ __('Members') }}
        </x-dropdown-link>
        @can('viewAny', \App\Models\Document::class)
            <x-dropdown-link :href="route('documents.index')" :active="request()->routeIs('documents.*')">
                {{ __('Documents') }}
            </x-dropdown-link>
        @endcan
    </x-nav-dropdown>

    <!-- Finance Dropdown -->
    <x-nav-dropdown
        label="{{ __('Finance') }}"
        :active="request()->routeIs('financials.*', 'reports.financial')">
        <x-dropdown-link :href="route('financials.index')" :active="request()->routeIs('financials.*')">
            {{ __('Financials') }}
        </x-dropdown-link>
        <x-dropdown-link :href="route('reports.financial')" :active="request()->routeIs('reports.financial')">
            {{ __('Financial Reports') }}
        </x-dropdown-link>
    </x-nav-dropdown>

    <!-- Reports Dropdown -->
    <x-nav-dropdown
        label="{{ __('Reports') }}"
        :active="request()->routeIs('reports.*')">
        <x-dropdown-link :href="route('reports.demographic')" :active="request()->routeIs('reports.demographic')">
            {{ __('Demographic Reports') }}
        </x-dropdown-link>
    </x-nav-dropdown>

    <!-- System Dropdown (admin only) -->
    @can('viewAny', \App\Models\AuditLog::class)
        <x-nav-dropdown
            label="{{ __('System') }}"
            :active="request()->routeIs('audit-logs.*')">
            <x-dropdown-link :href="route('audit-logs.index')" :active="request()->routeIs('audit-logs.*')">
                {{ __('Audit Logs') }}
            </x-dropdown-link>
        </x-nav-dropdown>
    @endcan
</div>
```

### Mobile Navigation Structure

```blade
<div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
    <div class="pt-2 pb-3 space-y-1">
        <!-- Dashboard -->
        <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
            {{ __('Dashboard') }}
        </x-responsive-nav-link>

        <!-- Management Section -->
        <div x-data="{ expanded: {{ request()->routeIs('members.*', 'documents.*') ? 'true' : 'false' }} }">
            <button @click="expanded = !expanded"
                    class="w-full flex items-center justify-between px-4 py-2 text-base font-medium text-slate-700">
                <span>{{ __('Management') }}</span>
                <svg class="w-5 h-5 transition-transform" :class="{'rotate-180': expanded}"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="expanded" x-collapse class="pl-4">
                <x-responsive-nav-link :href="route('members.index')" :active="request()->routeIs('members.*')">
                    {{ __('Members') }}
                </x-responsive-nav-link>
                @can('viewAny', \App\Models\Document::class)
                    <x-responsive-nav-link :href="route('documents.index')" :active="request()->routeIs('documents.*')">
                        {{ __('Documents') }}
                    </x-responsive-nav-link>
                @endcan
            </div>
        </div>

        <!-- Additional sections follow same pattern -->
    </div>
</div>
```

### Styling Guidelines

**Dropdown Trigger (Active State):**

```css
/* When dropdown contains active page */
.nav-dropdown-trigger.active {
  @apply text-amber-600 border-b-2 border-amber-600;
}
```

**Dropdown Menu:**

```css
.nav-dropdown-menu {
  @apply absolute left-0 mt-2 w-48 rounded-lg shadow-lg bg-white border border-stone-200 z-50;
}
```

**Dropdown Items:**

```css
.nav-dropdown-item {
  @apply block px-4 py-2 text-sm text-slate-700 hover:bg-stone-50 hover:text-amber-600;
}

.nav-dropdown-item.active {
  @apply bg-amber-50 text-amber-600 font-medium;
}
```

**Mobile Accordion:**

```css
.mobile-nav-section-header {
  @apply w-full flex items-center justify-between px-4 py-2 text-base font-medium text-slate-700 hover:bg-stone-50;
}

.mobile-nav-section-content {
  @apply pl-4 space-y-1;
}
```

### Accessibility Considerations

1. **ARIA Attributes:**

   - `aria-expanded` on dropdown triggers
   - `aria-haspopup="true"` on dropdown triggers
   - `role="menu"` on dropdown containers
   - `role="menuitem"` on dropdown links

2. **Keyboard Navigation:**

   - Tab/Shift+Tab: Navigate between triggers
   - Enter/Space: Open/close dropdown
   - Escape: Close dropdown
   - Arrow keys: Navigate within dropdown (optional enhancement)

3. **Focus Management:**

   - Visible focus indicators on all interactive elements
   - Focus trap within open dropdown (optional)
   - Return focus to trigger when closing with Escape

4. **Screen Reader Support:**
   - Announce dropdown state changes
   - Announce active page location
   - Provide context for grouped items

## Performance Considerations

1. **Lazy Loading:** Dropdown content is rendered but hidden, no additional loading needed
2. **Animation Performance:** Use CSS transforms for smooth transitions
3. **Event Delegation:** Use Alpine.js event handling to minimize listeners
4. **Mobile Optimization:** Use CSS `will-change` for accordion animations

## Security Considerations

1. **Permission Checks:** All navigation items must respect Laravel policies
2. **Route Protection:** Navigation visibility doesn't replace route middleware
3. **XSS Prevention:** All labels and content properly escaped via Blade

## Migration Strategy

1. **Phase 1:** Create new components without modifying existing navigation
2. **Phase 2:** Update navigation.blade.php to use new dropdown components
3. **Phase 3:** Test with different user roles and permissions
4. **Phase 4:** Deploy and monitor for issues
5. **Phase 5:** Remove old navigation code after verification

## Rollback Plan

If issues arise:

1. Revert navigation.blade.php to previous version
2. Keep new components for future use
3. No database changes to rollback
4. Clear view cache: `php artisan view:clear`
