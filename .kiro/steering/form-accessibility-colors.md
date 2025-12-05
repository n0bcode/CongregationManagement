---
inclusion: always
---

# Form Accessibility & Color Contrast Rules

## Critical Issue: Form Input & Label Colors

### Problem Identified

Forms trong project có vấn đề về contrast và accessibility:

1. **Input text màu trắng** (hoặc gần trắng) trên **nền input màu trắng** → Không đọc được
2. **Label màu đen** khi là placeholder ở giữa input → Tương phản quá mạnh
3. **Floating labels** (label di chuyển lên khi focus) có màu không phù hợp

### WCAG 2.1 Level AA Requirements

- **Normal text (< 18px)**: Contrast ratio tối thiểu **4.5:1**
- **Large text (≥ 18px)**: Contrast ratio tối thiểu **3:1**
- **Form inputs**: Phải có contrast rõ ràng với background
- **Labels**: Phải dễ đọc trong mọi trạng thái (default, focus, filled)

## Mandatory Color Rules for Forms

### 1. Input Text Color

```css
/* ✅ CORRECT - Dark text on light background */
input,
textarea,
select {
  color: #1e293b; /* slate-800 - Dark, readable */
  background: white; /* Clear white background */
}

/* ❌ WRONG - Light text on light background */
input {
  color: #f8fafc; /* slate-50 - Too light! */
  background: white;
}
```

**Tailwind Classes to Use**:

- `text-slate-800` or `text-slate-900` for input text
- `bg-white` for input background
- `border-stone-300` for borders

### 2. Label Colors

```css
/* ✅ CORRECT - Medium contrast, readable */
label {
  color: #475569; /* slate-600 - Good contrast */
  font-weight: 500; /* Medium weight for readability */
}

/* For floating labels (when input is empty) */
label.floating {
  color: #64748b; /* slate-500 - Slightly lighter */
}

/* For floating labels (when input is filled/focused) */
label.floating.active {
  color: #d97706; /* amber-600 - Brand color */
}

/* ❌ WRONG - Black label on white */
label {
  color: #000000; /* Too harsh contrast */
}
```

**Tailwind Classes to Use**:

- `text-slate-700` or `text-slate-600` for labels
- `text-amber-600` for active/focused state
- `font-medium` for better readability

### 3. Placeholder Text

```css
/* ✅ CORRECT - Subtle but readable */
input::placeholder {
  color: #94a3b8; /* slate-400 - Subtle but visible */
}

/* ❌ WRONG - Too light */
input::placeholder {
  color: #e2e8f0; /* slate-200 - Invisible! */
}
```

**Tailwind Classes to Use**:

- `placeholder:text-slate-400` or `placeholder:text-slate-500`

### 4. Focus State

```css
/* ✅ CORRECT - Clear focus indication */
input:focus {
  border-color: #d97706; /* amber-600 */
  ring: 4px #fef3c7; /* amber-100 with opacity */
  outline: none;
}
```

**Tailwind Classes to Use**:

- `focus:border-amber-600`
- `focus:ring-4`
- `focus:ring-amber-500`
- `focus:outline-none`

## Standard Form Input Classes

### Base Input Styling

```blade
<input
    type="text"
    class="w-full min-h-[48px] px-4 py-3
           text-base text-slate-800
           bg-white
           border border-stone-300
           rounded-lg
           placeholder:text-slate-400
           focus:border-amber-600
           focus:ring-4
           focus:ring-amber-500
           focus:outline-none
           disabled:bg-stone-100
           disabled:text-slate-500
           disabled:cursor-not-allowed"
>
```

### Base Label Styling

```blade
<label
    for="input-id"
    class="block text-lg font-medium text-slate-700 mb-2"
>
    Label Text
</label>
```

### Error State

```blade
<input
    class="... border-rose-500 focus:border-rose-600 focus:ring-rose-500"
>
<p class="mt-2 text-sm text-rose-600">Error message</p>
```

## Floating Label Pattern (If Used)

```blade
<div class="relative">
    <input
        type="text"
        id="input-id"
        placeholder=" "
        class="peer w-full min-h-[48px] px-4 pt-6 pb-2
               text-base text-slate-800
               bg-white
               border border-stone-300
               rounded-lg
               focus:border-amber-600
               focus:ring-4
               focus:ring-amber-500
               focus:outline-none"
    >
    <label
        for="input-id"
        class="absolute left-4 top-4
               text-slate-500
               transition-all duration-200
               peer-focus:top-2
               peer-focus:text-xs
               peer-focus:text-amber-600
               peer-[:not(:placeholder-shown)]:top-2
               peer-[:not(:placeholder-shown)]:text-xs
               peer-[:not(:placeholder-shown)]:text-slate-600"
    >
        Label Text
    </label>
</div>
```

## Color Palette Reference

### Text Colors (Dark to Light)

- `text-slate-900` (#0f172a) - Darkest, highest contrast
- `text-slate-800` (#1e293b) - **Recommended for input text**
- `text-slate-700` (#334155) - **Recommended for labels**
- `text-slate-600` (#475569) - Alternative for labels
- `text-slate-500` (#64748b) - Floating labels (inactive)
- `text-slate-400` (#94a3b8) - **Recommended for placeholders**
- `text-slate-300` (#cbd5e1) - Too light for text
- `text-slate-200` (#e2e8f0) - Too light for text
- `text-slate-100` (#f1f5f9) - Too light for text

### Brand Colors

- `text-amber-600` (#d97706) - **Primary brand color**
- `text-amber-700` (#b45309) - Darker variant
- `text-amber-500` (#f59e0b) - Lighter variant

### Error Colors

- `text-rose-600` (#e11d48) - **Error messages**
- `border-rose-500` (#f43f5e) - Error borders

## Validation Checklist

Before committing any form:

- [ ] Input text is `text-slate-800` or darker
- [ ] Labels are `text-slate-700` or `text-slate-600`
- [ ] Placeholders are `text-slate-400` or `text-slate-500`
- [ ] Focus state has clear visual indication (ring + border color change)
- [ ] Error states use `text-rose-600` and `border-rose-500`
- [ ] Disabled states are clearly distinguishable
- [ ] All text has minimum 4.5:1 contrast ratio (test with browser tools)
- [ ] Touch targets are minimum 48px height
- [ ] Labels are associated with inputs (for/id attributes)

## Testing Tools

1. **Browser DevTools**:
   - Inspect element → Accessibility tab → Contrast ratio
2. **Online Tools**:

   - WebAIM Contrast Checker: https://webaim.org/resources/contrastchecker/
   - Coolors Contrast Checker: https://coolors.co/contrast-checker

3. **Browser Extensions**:
   - axe DevTools
   - WAVE Evaluation Tool

## Common Mistakes to Avoid

❌ **DON'T**:

```blade
<!-- White/light text on white background -->
<input class="text-white bg-white">
<input class="text-slate-100 bg-white">

<!-- Black labels (too harsh) -->
<label class="text-black">

<!-- Invisible placeholders -->
<input class="placeholder:text-slate-200">

<!-- No focus indication -->
<input class="focus:outline-none"> <!-- Without ring/border change -->
```

✅ **DO**:

```blade
<!-- Dark text on white background -->
<input class="text-slate-800 bg-white">

<!-- Medium contrast labels -->
<label class="text-slate-700">

<!-- Visible placeholders -->
<input class="placeholder:text-slate-400">

<!-- Clear focus indication -->
<input class="focus:outline-none focus:ring-4 focus:ring-amber-500 focus:border-amber-600">
```

## Implementation Priority

1. **Immediate**: Fix all existing forms with white/light text
2. **High**: Ensure all labels have proper contrast
3. **Medium**: Implement consistent focus states
4. **Low**: Add floating label animations (if desired)

## Related Files to Check

- `resources/views/financials/create.blade.php`
- `resources/views/financials/edit.blade.php`
- `resources/views/members/create.blade.php`
- `resources/views/members/edit.blade.php`
- `resources/views/auth/*.blade.php`
- Any custom form components in `resources/views/components/`

## Notes

- These rules apply to ALL forms in the application
- Sanctuary & Stone design system prioritizes accessibility
- When in doubt, test with actual users (especially elderly users)
- Color contrast is not just aesthetic - it's a legal requirement (ADA, Section 508)

---

## CRITICAL: Form Padding Requirements

### Problem: Forms Without Padding

Many forms have inputs with **NO PADDING**, causing:

- Text touches the border edges
- Uncomfortable to read and interact
- Looks unprofessional
- Poor UX especially on mobile

### Mandatory Padding Rules

**ALL form inputs MUST have**:

1. **Horizontal Padding**: `px-4` (16px minimum)

   - Prevents text from touching left/right borders
   - Provides comfortable reading space

2. **Vertical Padding**: `py-3` (12px minimum)

   - Creates comfortable touch target
   - Ensures 48px minimum height with text

3. **Never Use**:
   - ❌ `p-0` - No padding at all
   - ❌ `px-0` - Text touches sides
   - ❌ `py-0` - Input too short
   - ❌ `p-1` or `p-2` - Too little padding

### Form Container Padding

Forms should also have proper container padding:

```blade
<!-- ✅ CORRECT - Form with proper padding -->
<div class="bg-white rounded-lg shadow-sm border border-stone-200 p-8">
    <form class="space-y-6">
        <!-- Form fields -->
    </form>
</div>

<!-- ❌ WRONG - No container padding -->
<div class="bg-white rounded-lg shadow-sm border border-stone-200">
    <form>
        <!-- Form fields cramped against edges -->
    </form>
</div>
```

**Container Padding Guidelines**:

- Desktop forms: `p-8` (32px) - Spacious, comfortable
- Mobile forms: `p-6` (24px) - Adequate space
- Compact forms: `p-4` (16px) minimum - Never less!

### Form Field Spacing

Fields should have proper spacing between them:

```blade
<!-- ✅ CORRECT - Proper spacing -->
<form class="space-y-6">
    <div>
        <label>Field 1</label>
        <input>
    </div>
    <div>
        <label>Field 2</label>
        <input>
    </div>
</form>

<!-- ❌ WRONG - No spacing -->
<form>
    <div>
        <label>Field 1</label>
        <input>
    </div>
    <div>
        <label>Field 2</label>
        <input>
    </div>
</form>
```

**Spacing Guidelines**:

- Between fields: `space-y-6` (24px) - Clear separation
- Between label and input: `mb-2` (8px) - Visual connection
- Between sections: `space-y-8` (32px) - Clear grouping

### Complete Form Template with Proper Padding

```blade
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow-sm border border-stone-200 p-8">
        <form method="POST" action="{{ route('...') }}" class="space-y-6">
            @csrf

            {{-- Field with proper padding --}}
            <div>
                <label for="field" class="block text-lg font-medium text-slate-700 mb-2">
                    {{ __('Field Label') }} <span class="text-rose-600">*</span>
                </label>
                <input
                    type="text"
                    id="field"
                    name="field"
                    required
                    class="w-full min-h-[48px] px-4 py-3
                           text-base text-slate-800
                           bg-white
                           border border-stone-300
                           rounded-lg
                           placeholder:text-slate-400
                           focus:border-amber-600
                           focus:ring-4
                           focus:ring-amber-500
                           focus:outline-none"
                    placeholder="{{ __('Enter text...') }}"
                >
                @error('field')
                    <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Form Actions with proper spacing --}}
            <div class="flex gap-4 pt-6 border-t border-stone-200">
                <x-button type="submit" variant="primary" class="flex-1">
                    {{ __('Submit') }}
                </x-button>
                <x-button variant="secondary" href="{{ route('...') }}" class="flex-1">
                    {{ __('Cancel') }}
                </x-button>
            </div>
        </form>
    </div>
</div>
```

### Validation Checklist (Updated)

Before committing any form:

- [ ] Input text is `text-slate-800` or darker
- [ ] Labels are `text-slate-700` or `text-slate-600`
- [ ] Placeholders are `text-slate-400` or `text-slate-500`
- [ ] **Inputs have `px-4 py-3` padding minimum**
- [ ] **Form container has `p-8` or `p-6` padding**
- [ ] **Fields have `space-y-6` spacing**
- [ ] Focus state has clear visual indication
- [ ] Error states use proper colors
- [ ] Touch targets are minimum 48px height
- [ ] Labels are associated with inputs (for/id)

### CSS Classes Updated

The `.form-input`, `.form-select`, `.form-textarea` classes in `app.css` now include:

- ✅ `text-base text-slate-800` - Dark, readable text
- ✅ `bg-white` - Clear white background
- ✅ `px-4 py-3` - Proper padding
- ✅ `placeholder:text-slate-400` - Visible placeholders
- ✅ `focus:ring-4 focus:ring-amber-500` - Clear focus state

All forms using these classes will automatically have proper styling!

---

## Form Layout Optimization: Multi-Column Fields

### Principle: Group Short-Value Fields

When input values are predictably short, group multiple fields in one row to:

- ✅ Reduce page length
- ✅ Improve content overview
- ✅ Better use of screen space
- ✅ Reduce scrolling for users
- ✅ Create visual grouping of related fields

### When to Use Multi-Column Layout

**Use 2-3 columns for**:

- Date fields (birth date, entry date, etc.)
- Short text fields (first name + last name)
- Numeric fields (age, year, amount)
- Dropdowns with limited options (status, category)
- Phone numbers split into parts
- Address components (city + postal code)

**Keep single column for**:

- Long text fields (description, notes, address)
- Email addresses (can be long)
- Textareas
- File uploads
- Fields that don't have thematic relationship

### Critical Rule: Thematic Grouping

**✅ CORRECT - Fields in same row MUST be thematically related**:

```blade
{{-- Good: Name fields together --}}
<div class="grid grid-cols-2 gap-4">
    <div>
        <label>First Name</label>
        <input type="text" name="first_name">
    </div>
    <div>
        <label>Last Name</label>
        <input type="text" name="last_name">
    </div>
</div>

{{-- Good: Date fields together --}}
<div class="grid grid-cols-2 gap-4">
    <div>
        <label>Birth Date</label>
        <input type="date" name="birth_date">
    </div>
    <div>
        <label>Entry Date</label>
        <input type="date" name="entry_date">
    </div>
</div>

{{-- Good: Location fields together --}}
<div class="grid grid-cols-2 gap-4">
    <div>
        <label>City</label>
        <input type="text" name="city">
    </div>
    <div>
        <label>Postal Code</label>
        <input type="text" name="postal_code">
    </div>
</div>
```

**❌ WRONG - Unrelated fields in same row**:

```blade
{{-- Bad: No thematic relationship --}}
<div class="grid grid-cols-2 gap-4">
    <div>
        <label>First Name</label>
        <input type="text" name="first_name">
    </div>
    <div>
        <label>Birth Date</label>
        <input type="date" name="birth_date">
    </div>
</div>

{{-- Bad: Different contexts mixed --}}
<div class="grid grid-cols-2 gap-4">
    <div>
        <label>Email</label>
        <input type="email" name="email">
    </div>
    <div>
        <label>Status</label>
        <select name="status">...</select>
    </div>
</div>
```

### Responsive Grid Classes

**Desktop (2 columns)**:

```blade
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <!-- Fields -->
</div>
```

**Desktop (3 columns)** - Use sparingly, only for very short fields:

```blade
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <!-- Very short fields like day/month/year -->
</div>
```

**Mobile-first approach**:

- Always `grid-cols-1` on mobile
- Use `md:grid-cols-2` or `lg:grid-cols-2` for larger screens
- Never force multi-column on small screens

### Gap Spacing

**Standard gap sizes**:

- `gap-4` (16px) - Standard spacing between fields in same row
- `gap-6` (24px) - More breathing room for complex forms
- `gap-3` (12px) - Tighter spacing for very related fields (e.g., phone parts)

### Examples from Project

**Member Form - Name Fields** (Good example):

```blade
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label for="first_name" class="block text-lg font-medium text-slate-700 mb-2">
            {{ __('First Name') }} <span class="text-rose-600">*</span>
        </label>
        <input type="text" id="first_name" name="first_name" required
               class="w-full min-h-[48px] px-4 py-3 text-base text-slate-800 bg-white border border-stone-300 rounded-lg">
    </div>
    <div>
        <label for="last_name" class="block text-lg font-medium text-slate-700 mb-2">
            {{ __('Last Name') }} <span class="text-rose-600">*</span>
        </label>
        <input type="text" id="last_name" name="last_name" required
               class="w-full min-h-[48px] px-4 py-3 text-base text-slate-800 bg-white border border-stone-300 rounded-lg">
    </div>
</div>
```

**Financial Form - Date and Category** (Good example):

```blade
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label for="date" class="block text-lg font-medium text-slate-700 mb-2">
            {{ __('Date') }} <span class="text-rose-600">*</span>
        </label>
        <input type="date" id="date" name="date" required
               class="w-full min-h-[48px] px-4 py-3 text-base text-slate-800 bg-white border border-stone-300 rounded-lg">
    </div>
    <div>
        <label for="category" class="block text-lg font-medium text-slate-700 mb-2">
            {{ __('Category') }} <span class="text-rose-600">*</span>
        </label>
        <input type="text" id="category" name="category" required
               class="w-full min-h-[48px] px-4 py-3 text-base text-slate-800 bg-white border border-stone-300 rounded-lg">
    </div>
</div>
```

### Decision Tree: Should Fields Share a Row?

Ask these questions in order:

1. **Are the values predictably short?**

   - No → Keep single column
   - Yes → Continue to #2

2. **Are the fields thematically related?**

   - No → Keep single column (each field gets its own row)
   - Yes → Continue to #3

3. **Do they belong to the same conceptual group?**
   - Examples:
     - ✅ First name + Last name (identity)
     - ✅ Birth date + Entry date (timeline)
     - ✅ City + Postal code (location)
     - ❌ Name + Email (different concepts)
     - ❌ Date + Status (different concepts)
   - No → Keep single column
   - Yes → Use multi-column layout

### Visual Hierarchy

When using multi-column layout:

1. **Group related sections** with visual separation:

```blade
{{-- Personal Information Section --}}
<div class="space-y-6">
    <h3 class="text-xl font-semibold text-slate-800">Personal Information</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Name fields -->
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Date fields -->
    </div>
</div>

{{-- Contact Information Section --}}
<div class="space-y-6 pt-8 border-t border-stone-200">
    <h3 class="text-xl font-semibold text-slate-800">Contact Information</h3>

    <!-- Contact fields -->
</div>
```

2. **Use consistent spacing**:
   - Between sections: `space-y-8` or `space-y-10`
   - Between field groups: `space-y-6`
   - Between fields in same row: `gap-4`

### Accessibility Considerations

Even with multi-column layout:

- ✅ Each field MUST have its own label
- ✅ Labels MUST be associated with inputs (for/id)
- ✅ Touch targets MUST remain 48px minimum height
- ✅ Focus order MUST be logical (left-to-right, top-to-bottom)
- ✅ Error messages MUST be clearly associated with their field
- ✅ Mobile MUST stack to single column

### Testing Checklist

Before committing multi-column forms:

- [ ] All fields in same row are thematically related
- [ ] Values are predictably short (won't overflow)
- [ ] Mobile view stacks to single column
- [ ] Tab order is logical
- [ ] Each field has proper label association
- [ ] Touch targets are 48px minimum
- [ ] Gap spacing is consistent
- [ ] Visual grouping is clear
- [ ] Form doesn't feel cramped or cluttered

### Common Patterns

**Pattern 1: Name Fields (2 columns)**

```blade
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div><!-- First Name --></div>
    <div><!-- Last Name --></div>
</div>
```

**Pattern 2: Date Range (2 columns)**

```blade
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div><!-- Start Date --></div>
    <div><!-- End Date --></div>
</div>
```

**Pattern 3: Location (2 columns)**

```blade
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div><!-- City --></div>
    <div><!-- Postal Code --></div>
</div>
```

**Pattern 4: Date Split (3 columns)** - Use rarely:

```blade
<div class="grid grid-cols-3 gap-3">
    <div><!-- Day --></div>
    <div><!-- Month --></div>
    <div><!-- Year --></div>
</div>
```

**Pattern 5: Mixed Width (using col-span)**:

```blade
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="md:col-span-2"><!-- Wider field --></div>
    <div><!-- Narrower field --></div>
</div>
```

### Anti-Patterns to Avoid

❌ **Don't mix unrelated fields**:

```blade
{{-- Bad: Name and status are different concepts --}}
<div class="grid grid-cols-2 gap-4">
    <div><!-- First Name --></div>
    <div><!-- Status --></div>
</div>
```

❌ **Don't force long values into narrow columns**:

```blade
{{-- Bad: Email can be very long --}}
<div class="grid grid-cols-2 gap-4">
    <div><!-- Email --></div>
    <div><!-- Phone --></div>
</div>
```

❌ **Don't use too many columns**:

```blade
{{-- Bad: 4 columns is too cramped --}}
<div class="grid grid-cols-4 gap-4">
    <!-- Too many fields -->
</div>
```

❌ **Don't forget mobile responsiveness**:

```blade
{{-- Bad: Forces 2 columns on mobile --}}
<div class="grid grid-cols-2 gap-4">
    <!-- Should be grid-cols-1 md:grid-cols-2 -->
</div>
```

---

**Summary**: Multi-column layout is a powerful tool for improving form UX, but it MUST be used thoughtfully. Only group fields that are thematically related and have predictably short values. When in doubt, keep fields in single column - clarity is more important than compactness
