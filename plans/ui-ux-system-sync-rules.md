# Quy Tắc Đồng Bộ UI/UX và Thiết Kế Hệ Thống

**Ngày tạo:** 2025-12-03  
**Mục đích:** Đảm bảo sự nhất quán giữa thiết kế UX, kiến trúc hệ thống và triển khai code

---

## 1. Nguyên Tắc Đồng Bộ Cốt Lõi

### 1.1 Triết Lý Thiết Kế

- **"Pastoral Efficiency"**: Mọi quyết định thiết kế phải loại bỏ lo lắng về quản trị để các nữ tu tập trung vào sứ mệnh tâm linh
- **"Don't Make Me Think"**: Ẩn các chi tiết kỹ thuật phức tạp, sử dụng ngôn ngữ của cộng đoàn
- **"Forgiving by Design"**: Cho phép hoàn tác dễ dàng, không bao giờ để người dùng cảm thấy bị "mắc kẹt"

### 1.2 Mục Tiêu Cảm Xúc

- **Peace of Mind**: Cảm giác nhẹ nhõm và được hỗ trợ
- **Competence & Confidence**: Người dùng không am hiểu công nghệ vẫn cảm thấy có năng lực
- **Trust**: Tin tưởng tuyệt đối vào độ chính xác của dữ liệu

---

## 2. Đồng Bộ Design System với Architecture

### 2.1 Technology Stack Alignment

**Design System**: Tailwind CSS + Headless UI  
**Architecture**: Laravel Blade + Alpine.js/Livewire

#### Quy Tắc Triển Khai:

```php
// ✅ ĐÚNG: Sử dụng Blade Components để wrap Tailwind
<x-button variant="primary" size="lg">Submit Report</x-button>

// ❌ SAI: Lặp lại Tailwind classes trực tiếp
<button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
    Submit Report
</button>
```

#### Component Mapping:

| UX Component   | Blade Component Path                                  | Tailwind Config             |
| -------------- | ----------------------------------------------------- | --------------------------- |
| Ledger Row     | `resources/views/components/ledger-row.blade.php`     | Custom utility classes      |
| Status Card    | `resources/views/components/status-card.blade.php`    | Sanctuary & Stone palette   |
| Feast Timeline | `resources/views/components/feast-timeline.blade.php` | Horizontal scroll container |

### 2.2 Color System Implementation

**Palette Name**: Sanctuary & Stone

```javascript
// tailwind.config.js
module.exports = {
  theme: {
    extend: {
      colors: {
        sanctuary: {
          slate: "#334155", // Primary - Deep Slate Blue
          stone: "#F5F5DC", // Background - Warm Stone/Cream
          gold: "#D4AF37", // Accent - Muted Gold
        },
        semantic: {
          emerald: "#10B981", // Good
          rose: "#F43F5E", // Attention
          amber: "#F59E0B", // Pending
        },
      },
    },
  },
};
```

### 2.3 Typography System

```javascript
// tailwind.config.js - Typography
fontFamily: {
  'heading': ['Merriweather', 'serif'],  // Traditional, warm
  'body': ['Inter', 'sans-serif'],       // High legibility
}

fontSize: {
  'base': '18px',      // Increased for elderly users (NFR11)
  'h1': '32px',
  'h2': '24px',
}

lineHeight: {
  'relaxed': '1.6',    // Enhanced readability
}
```

---

## 3. Responsive Design Rules

### 3.1 Breakpoint Strategy (Mobile-First)

```css
/* Mobile First - Base styles for 360px+ */
.dashboard-card {
  @apply p-4 mb-4;
}

/* Tablet - 768px+ */
@screen md {
  .dashboard-card {
    @apply p-6 flex;
  }
}

/* Desktop - 1024px+ */
@screen lg {
  .dashboard-card {
    @apply p-8 grid grid-cols-3;
  }
}
```

### 3.2 Navigation Pattern

| Screen Size         | Pattern             | Implementation            |
| ------------------- | ------------------- | ------------------------- |
| Mobile (<768px)     | Bottom Tab Bar      | `<x-mobile-nav />`        |
| Tablet (768-1024px) | Collapsible Sidebar | `<x-sidebar collapsed />` |
| Desktop (>1024px)   | Full Sidebar        | `<x-sidebar expanded />`  |

---

## 4. Accessibility (A11y) Requirements

### 4.1 WCAG 2.1 Level AA Compliance

**Contrast Ratios** (NFR - WCAG AA):

```css
/* Minimum 4.5:1 for normal text */
.text-primary {
  color: #334155;
} /* on #F5F5DC background = 8.2:1 ✅ */

/* Minimum 3:1 for large text (18px+) */
.text-accent {
  color: #d4af37;
} /* on #334155 background = 4.1:1 ✅ */
```

**Touch Targets** (Senior-Focused):

```css
/* Minimum 48x48px (exceeds Apple's 44px recommendation) */
.btn,
.nav-link,
.form-input {
  @apply min-h-[48px] min-w-[48px];
}
```

**Focus States**:

```css
/* High-visibility focus rings for keyboard navigation */
*:focus {
  @apply outline-none ring-4 ring-sanctuary-gold ring-offset-2;
}
```

### 4.2 Motion Sensitivity

```css
/* Respect prefers-reduced-motion */
@media (prefers-reduced-motion: reduce) {
  * {
    animation-duration: 0.01ms !important;
    transition-duration: 0.01ms !important;
  }
}
```

---

## 5. Component Design Patterns

### 5.1 The Ledger Row Pattern

**UX Spec**: "Message-Style" list for expenses (like WhatsApp)

**Implementation**:

```blade
{{-- resources/views/components/ledger-row.blade.php --}}
<div class="ledger-row flex items-center justify-between p-4 border-b hover:bg-sanctuary-stone/50 transition-colors">
    {{-- Date Badge (Left) --}}
    <div class="date-badge flex-shrink-0 w-16 text-center">
        <span class="text-sm text-gray-500">{{ $date->format('M') }}</span>
        <span class="text-2xl font-bold block">{{ $date->format('d') }}</span>
    </div>

    {{-- Description (Center) --}}
    <div class="description flex-grow px-4">
        <p class="font-medium">{{ $description }}</p>
        <p class="text-sm text-gray-500">{{ $category }}</p>
    </div>

    {{-- Amount (Right, Bold) --}}
    <div class="amount flex-shrink-0 text-right">
        <span class="text-xl font-bold">{{ $amount }}</span>
    </div>
</div>
```

**Interaction**: Swipe Left to Delete (Alpine.js), Tap to Edit

### 5.2 The Pastoral Status Card

**UX Spec**: Summarize health/status without "System Alert" anxiety

**Implementation**:

```blade
{{-- resources/views/components/status-card.blade.php --}}
@props(['variant' => 'peace', 'icon', 'title', 'value'])

@php
$variants = [
    'peace' => 'bg-emerald-50 border-emerald-200',
    'attention' => 'bg-rose-50 border-rose-200',
    'pending' => 'bg-amber-50 border-amber-200',
];
@endphp

<div class="status-card {{ $variants[$variant] }} border-2 rounded-lg p-6">
    <div class="flex items-start">
        <div class="icon mr-4 text-3xl">{{ $icon }}</div>
        <div class="content">
            <h3 class="text-lg font-heading font-semibold mb-2">{{ $title }}</h3>
            <p class="text-4xl font-bold">{{ $value }}</p>
        </div>
    </div>
</div>
```

### 5.3 The Feast Timeline

**UX Spec**: Visualize liturgical year and community milestones

**Implementation**:

```blade
{{-- resources/views/components/feast-timeline.blade.php --}}
<div class="feast-timeline overflow-x-auto py-4">
    <div class="flex space-x-4 min-w-max">
        @foreach($events as $event)
            <div class="timeline-node flex flex-col items-center">
                <div class="node-circle
                    {{ $event->isPast() ? 'bg-gray-300' : '' }}
                    {{ $event->isToday() ? 'bg-sanctuary-gold ring-4 ring-sanctuary-gold/30' : '' }}
                    {{ $event->isFuture() ? 'bg-sanctuary-slate' : '' }}
                    w-12 h-12 rounded-full flex items-center justify-center text-white font-bold">
                    {{ $event->date->format('d') }}
                </div>
                <span class="text-xs mt-2 text-center max-w-[80px]">{{ $event->name }}</span>
            </div>
        @endforeach
    </div>
</div>
```

---

## 6. User Journey to Code Mapping

### 6.1 Sr. Mary: Monthly Financial Close

**UX Flow**: Dashboard → Financials Tab → Review Draft → Submit

**Code Mapping**:

```
Route: GET /financials/monthly-report
Controller: FinancialController@showMonthlyReport
View: resources/views/finance/monthly-report.blade.php
Components:
  - <x-ledger-row /> (list of expenses)
  - <x-status-card variant="peace" /> (balance summary)
  - <x-button variant="primary">Submit Report</x-button>
```

**Validation Rules** (Architecture Pattern):

```php
// app/Http/Requests/SubmitMonthlyReportRequest.php
public function rules() {
    return [
        'cash_on_hand' => 'required|numeric|min:0',
        'expenses_total' => 'required|numeric',
        'balance_matches' => 'accepted', // Custom rule
    ];
}

public function messages() {
    return [
        'balance_matches.accepted' => 'Please check the balance. The cash on hand doesn\'t match the system calculation.',
    ];
}
```

### 6.2 Sr. Teresa: Formation Stage Update

**UX Flow**: Dashboard → Formation Widget → Sister Profile → Add Milestone → Celebration

**Code Mapping**:

```
Route: POST /formation/{member}/milestone
Controller: FormationController@storeMilestone
Service: FormationService->calculateNextStageDate()
View: resources/views/formation/timeline.blade.php
Animation: Confetti.js (on success)
```

**Celebration Pattern**:

```javascript
// resources/js/celebration.js
import confetti from "canvas-confetti";

export function celebrateMilestone() {
  confetti({
    particleCount: 100,
    spread: 70,
    origin: { y: 0.6 },
    colors: ["#D4AF37", "#10B981", "#334155"], // Sanctuary colors
  });
}
```

---

## 7. Error Handling & Feedback

### 7.1 Kindness in Code

**Anti-Pattern** (Avoid):

```html
<div class="error">Error 500: Database Exception</div>
```

**Correct Pattern**:

```blade
<div class="alert alert-rose p-4 rounded-lg flex items-start">
    <svg class="w-6 h-6 mr-3 flex-shrink-0" ...><!-- Icon --></svg>
    <div>
        <p class="font-semibold">We couldn't save that right now</p>
        <p class="text-sm mt-1">Please check your internet connection and try again. If the problem continues, contact support.</p>
    </div>
</div>
```

### 7.2 Success Feedback

**Pattern**: Immediate, positive reinforcement

```blade
{{-- Success Toast (Alpine.js) --}}
<div x-data="{ show: false }"
     x-show="show"
     x-init="@this.on('saved', () => { show = true; setTimeout(() => show = false, 3000) })"
     class="fixed top-4 right-4 bg-emerald-500 text-white px-6 py-4 rounded-lg shadow-lg">
    <div class="flex items-center">
        <svg class="w-6 h-6 mr-2"><!-- Checkmark --></svg>
        <span>Report submitted successfully! You're all set.</span>
    </div>
</div>
```

---

## 8. Performance Optimization Rules

### 8.1 Image Optimization

**Member Photos**:

```php
// app/Services/FileStorageService.php
public function storeProfilePhoto($file) {
    return $file->storeAs('photos', [
        'disk' => 'public',
        'format' => 'webp',        // Modern format
        'quality' => 85,
        'resize' => [300, 300],    // Thumbnail
    ]);
}
```

### 8.2 Asset Loading Strategy

```blade
{{-- resources/views/layouts/app.blade.php --}}
<head>
    {{-- Critical CSS inline --}}
    <style>{{ Vite::content('resources/css/critical.css') }}</style>

    {{-- Defer non-critical CSS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Preload fonts --}}
    <link rel="preload" href="/fonts/inter.woff2" as="font" type="font/woff2" crossorigin>
</head>
```

### 8.3 Query Optimization (RBAC Scoping)

```php
// app/Models/Member.php - Global Scope for Performance
protected static function booted() {
    static::addGlobalScope('community', function (Builder $builder) {
        if (Auth::check() && Auth::user()->role === UserRole::DIRECTOR) {
            // Automatic filtering - no need to remember in every query
            $builder->where('community_id', Auth::user()->community_id);
        }
    });
}
```

---

## 9. Testing Strategy for UI/UX

### 9.1 Visual Regression Testing

```bash
# Using Laravel Dusk for browser testing
php artisan dusk:make DashboardVisualTest
```

```php
// tests/Browser/DashboardVisualTest.php
public function testDashboardMatchesDesign() {
    $this->browse(function (Browser $browser) {
        $browser->loginAs(User::factory()->director()->create())
                ->visit('/dashboard')
                ->assertSee('Good Morning')
                ->assertPresent('.status-card')
                ->screenshot('dashboard-director');
    });
}
```

### 9.2 Accessibility Testing

```php
// tests/Feature/AccessibilityTest.php
public function testButtonsHaveMinimumTouchTarget() {
    $response = $this->get('/dashboard');

    $response->assertSee('min-h-[48px]'); // Check Tailwind class
}

public function testContrastRatios() {
    // Use axe-core or pa11y in CI pipeline
    $this->artisan('test:a11y')->assertExitCode(0);
}
```

---

## 10. Documentation & Handoff

### 10.1 Component Documentation Template

````markdown
# Component: Ledger Row

## Purpose

Display a single expense entry with "Notebook" clarity.

## Props

- `date` (Carbon): Transaction date
- `description` (string): Expense description
- `category` (string): Expense category
- `amount` (string): Formatted amount

## Usage

```blade
<x-ledger-row
    :date="$expense->date"
    :description="$expense->description"
    :category="$expense->category->name"
    :amount="money($expense->amount)" />
```
````

## Accessibility

- Full-row touch target (48px min height)
- High contrast numbers for readability
- Keyboard navigable (Tab + Enter to edit)

## Related

- UX Spec: Section 2.5 "Component Strategy"
- Architecture: `app/Models/Expense.php`

````

### 10.2 Design Token Export

```json
// design-tokens.json (for design tools)
{
  "color": {
    "sanctuary": {
      "slate": { "value": "#334155" },
      "stone": { "value": "#F5F5DC" },
      "gold": { "value": "#D4AF37" }
    }
  },
  "fontSize": {
    "base": { "value": "18px" },
    "h1": { "value": "32px" }
  },
  "spacing": {
    "touch-target": { "value": "48px" }
  }
}
````

---

## 11. Checklist Đồng Bộ

### Trước Khi Triển Khai Feature Mới:

- [ ] Kiểm tra UX Spec có định nghĩa flow không?
- [ ] Component đã được thiết kế trong Design System chưa?
- [ ] Architecture có định nghĩa Controller/Service tương ứng không?
- [ ] Naming conventions tuân thủ chuẩn Laravel (snake_case DB, kebab-case routes)?
- [ ] Accessibility requirements (48px touch, 4.5:1 contrast) đã được đáp ứng?
- [ ] RBAC scoping đã được áp dụng (Global Scope hoặc Policy)?
- [ ] Error messages sử dụng "kind language" (không có error codes)?

### Sau Khi Hoàn Thành Feature:

- [ ] Visual regression test đã pass?
- [ ] Accessibility audit (axe-core) không có lỗi?
- [ ] Performance target (<2s page load) đã đạt?
- [ ] Component documentation đã được cập nhật?
- [ ] Design tokens đã được export (nếu có thay đổi)?

---

## 12. Conflict Resolution

### Khi UX Spec và Architecture Xung Đột:

**Ví dụ**: UX yêu cầu "Real-time sync" nhưng Architecture chọn "Server-Side Rendering"

**Quy trình giải quyết**:

1. **Đánh giá Impact**: Real-time có critical cho user journey không?
2. **Tìm Middle Ground**: Sử dụng Livewire (server-driven nhưng cảm giác real-time)
3. **Document Decision**: Ghi lại lý do trong `plans/conflict.md`
4. **Update Both Docs**: Cập nhật cả UX Spec và Architecture để nhất quán

### Khi Design System và Technical Constraints Xung Đột:

**Ví dụ**: Design yêu cầu custom font nhưng Performance budget không cho phép

**Giải pháp**:

1. **Prioritize Performance**: Sử dụng system fonts với fallback tương tự
2. **Subset Fonts**: Chỉ load các ký tự cần thiết (Latin + Vietnamese)
3. **Lazy Load**: Load decorative fonts sau khi critical content đã render

---

## 13. Maintenance & Evolution

### Quarterly Review Checklist:

- [ ] Design System có components mới cần thêm không?
- [ ] User feedback có yêu cầu thay đổi UX patterns không?
- [ ] Performance metrics có đạt target không? (NFR5: <2s)
- [ ] Accessibility compliance vẫn đạt WCAG AA không?
- [ ] RBAC permissions có cần mở rộng không?

### Version Control:

```
plans/ui-ux-system-sync-rules.md
├── v1.0 (2025-12-03): Initial sync rules
├── v1.1 (TBD): Post-MVP updates (Financial module)
└── v2.0 (TBD): Phase 2 (Project Management UI)
```

---

**Tài liệu tham khảo**:

- `docs/ux-design-specification.md` - UX patterns và emotional design
- `docs/architecture.md` - Technical decisions và implementation patterns
- `docs/prd.md` - Functional requirements và success criteria
- `plans/RBAC_System_Documentation.md` - Permission system details
