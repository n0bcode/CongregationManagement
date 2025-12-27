# Livewire Patterns - Congregation Management System

**Last Updated:** 2025-12-27  
**Livewire Version:** 3.7  
**Purpose:** Document Livewire component architecture, state management patterns, and best practices

---

## Overview

The Congregation Management System uses **Livewire 3.7** extensively for interactive components, moving beyond the original "sprinkles of interactivity" approach to a **Livewire-heavy architecture**. This document defines the patterns and practices for all Livewire components.

---

## Livewire Components Inventory

### Current Components

1. **`Dashboard`** - Main application dashboard
2. **`FinancialDashboard`** - Financial overview and metrics
3. **`Reports\ReportBuilder`** - Advanced report builder with dynamic filters
4. **`Notifications\NotificationCenter`** - Notification management interface

---

## Component Architecture Patterns

### Pattern 1: Full-Page Components

**Use Case:** Dashboard, main application screens

**Implementation:**

```php
namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    // State properties
    public $stats = [];
    public $upcomingEvents = [];

    // Lifecycle hooks
    public function mount()
    {
        $this->loadDashboardData();
    }

    // Actions
    public function refresh()
    {
        $this->loadDashboardData();
        $this->dispatch('dashboard-refreshed');
    }

    // Render
    public function render()
    {
        return view('livewire.dashboard');
    }

    // Private methods
    private function loadDashboardData()
    {
        $this->stats = [
            'total_members' => Member::count(),
            'active_members' => Member::active()->count(),
            // ... more stats
        ];

        $this->upcomingEvents = PeriodicEvent::upcoming()->limit(5)->get();
    }
}
```

**Route Registration:**

```php
Route::get('/dashboard', \App\Livewire\Dashboard::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');
```

**Key Principles:**

- Use `#[Layout]` and `#[Title]` attributes
- Initialize state in `mount()`
- Keep render method simple
- Extract complex logic to private methods
- Dispatch events for component communication

---

### Pattern 2: Embedded Components

**Use Case:** Report builder, notification center

**Implementation:**

```php
namespace App\Livewire\Reports;

use Livewire\Component;
use Livewire\Attributes\Computed;

class ReportBuilder extends Component
{
    // Form state
    public $reportType = 'demographic';
    public $filters = [];
    public $dateRange = [];

    // UI state
    public $isGenerating = false;
    public $previewData = null;

    // Computed properties
    #[Computed]
    public function availableFilters()
    {
        return match($this->reportType) {
            'demographic' => ['age_range', 'community', 'status'],
            'financial' => ['date_range', 'category', 'community'],
            default => [],
        };
    }

    // Actions
    public function updatedReportType()
    {
        // Reset filters when report type changes
        $this->filters = [];
        $this->previewData = null;
    }

    public function generatePreview()
    {
        $this->validate([
            'reportType' => 'required|in:demographic,financial',
            'filters' => 'array',
        ]);

        $this->isGenerating = true;

        // Generate preview data
        $this->previewData = $this->buildReportData();

        $this->isGenerating = false;
    }

    public function export($format)
    {
        $this->validate([
            'reportType' => 'required',
        ]);

        // Redirect to export route
        return redirect()->route('reports.export', [
            'type' => $this->reportType,
            'format' => $format,
            'filters' => $this->filters,
        ]);
    }

    public function render()
    {
        return view('livewire.reports.report-builder');
    }

    private function buildReportData()
    {
        // Complex query building logic
        return [];
    }
}
```

**View Template:**

```blade
<div>
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Report Type</label>
        <select wire:model.live="reportType" class="mt-1 block w-full">
            <option value="demographic">Demographic Report</option>
            <option value="financial">Financial Report</option>
        </select>
    </div>

    @if(count($this->availableFilters) > 0)
        <div class="mb-4">
            <h3 class="text-lg font-medium">Filters</h3>
            @foreach($this->availableFilters as $filter)
                {{-- Dynamic filter inputs --}}
            @endforeach
        </div>
    @endif

    <div class="flex gap-2">
        <button
            wire:click="generatePreview"
            wire:loading.attr="disabled"
            class="btn btn-primary"
        >
            <span wire:loading.remove wire:target="generatePreview">Generate Preview</span>
            <span wire:loading wire:target="generatePreview">Generating...</span>
        </button>

        @if($previewData)
            <button wire:click="export('pdf')" class="btn btn-secondary">
                Export PDF
            </button>
            <button wire:click="export('excel')" class="btn btn-secondary">
                Export Excel
            </button>
        @endif
    </div>

    @if($previewData)
        <div class="mt-4">
            {{-- Preview table --}}
        </div>
    @endif
</div>
```

**Key Principles:**

- Use `wire:model.live` for reactive updates
- Implement `#[Computed]` for derived state
- Use `updatedPropertyName()` hooks for side effects
- Show loading states with `wire:loading`
- Validate before actions
- Redirect for downloads (don't return files directly)

---

## State Management Patterns

### Pattern 1: Form State

**Use Case:** Multi-step forms, complex inputs

```php
class FinancialDashboard extends Component
{
    // Form properties
    public $month;
    public $year;
    public $communityId;

    // Computed data
    #[Computed]
    public function expenses()
    {
        return Expense::query()
            ->when($this->month, fn($q) => $q->whereMonth('date', $this->month))
            ->when($this->year, fn($q) => $q->whereYear('date', $this->year))
            ->when($this->communityId, fn($q) => $q->where('community_id', $this->communityId))
            ->get();
    }

    #[Computed]
    public function totalExpenses()
    {
        return $this->expenses->sum('amount');
    }

    public function mount()
    {
        $this->month = now()->month;
        $this->year = now()->year;
    }

    public function resetFilters()
    {
        $this->month = now()->month;
        $this->year = now()->year;
        $this->communityId = null;
    }
}
```

**View:**

```blade
<div>
    <div class="filters mb-4">
        <select wire:model.live="month">
            @for($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
            @endfor
        </select>

        <select wire:model.live="year">
            @for($y = now()->year - 5; $y <= now()->year; $y++)
                <option value="{{ $y }}">{{ $y }}</option>
            @endfor
        </select>

        <button wire:click="resetFilters">Reset</button>
    </div>

    <div class="stats">
        <p>Total Expenses: ${{ number_format($this->totalExpenses, 2) }}</p>
        <p>Transaction Count: {{ $this->expenses->count() }}</p>
    </div>

    <table>
        @foreach($this->expenses as $expense)
            <tr>
                <td>{{ $expense->date->format('Y-m-d') }}</td>
                <td>{{ $expense->category }}</td>
                <td>${{ number_format($expense->amount, 2) }}</td>
            </tr>
        @endforeach
    </table>
</div>
```

**Key Principles:**

- Use computed properties for derived data
- Use `wire:model.live` for instant filtering
- Initialize state in `mount()`
- Provide reset functionality

---

### Pattern 2: UI State

**Use Case:** Modals, tabs, collapsible sections

```php
class NotificationCenter extends Component
{
    // UI state
    public $activeTab = 'unread';
    public $selectedNotification = null;
    public $showModal = false;

    // Actions
    public function selectNotification($notificationId)
    {
        $this->selectedNotification = Notification::find($notificationId);
        $this->showModal = true;

        // Mark as read
        $this->selectedNotification->markAsRead();

        // Refresh unread count
        $this->dispatch('notification-read');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedNotification = null;
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
    }

    #[Computed]
    public function notifications()
    {
        return Notification::query()
            ->when($this->activeTab === 'unread', fn($q) => $q->unread())
            ->when($this->activeTab === 'read', fn($q) => $q->read())
            ->latest()
            ->get();
    }
}
```

---

## Performance Optimization

### Pattern 1: Lazy Loading

```php
use Livewire\Attributes\Lazy;

#[Lazy]
class ExpensiveComponent extends Component
{
    public function placeholder()
    {
        return view('livewire.placeholders.loading');
    }

    public function render()
    {
        // Expensive query
        $data = $this->loadExpensiveData();

        return view('livewire.expensive-component', compact('data'));
    }
}
```

**Usage:**

```blade
@livewire('expensive-component', lazy: true)
```

---

### Pattern 2: Pagination

```php
use Livewire\WithPagination;

class MemberList extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.member-list', [
            'members' => Member::query()
                ->when($this->search, fn($q) => $q->where('religious_name', 'like', "%{$this->search}%"))
                ->paginate(20),
        ]);
    }
}
```

---

### Pattern 3: Query Optimization

```php
#[Computed]
public function members()
{
    return Member::query()
        ->select(['id', 'religious_name', 'community_id', 'status'])
        ->with('community:id,name')
        ->active()
        ->get();
}
```

**Key Principles:**

- Use `select()` to limit columns
- Eager load relationships
- Apply scopes for common filters
- Cache computed properties

---

## Event Communication

### Pattern 1: Component-to-Component

**Dispatching Events:**

```php
public function refresh()
{
    $this->loadData();
    $this->dispatch('dashboard-refreshed');
}
```

**Listening to Events:**

```php
use Livewire\Attributes\On;

#[On('dashboard-refreshed')]
public function handleDashboardRefresh()
{
    $this->loadRelatedData();
}
```

---

### Pattern 2: Component-to-JavaScript

**From Component:**

```php
public function notifyUser($message)
{
    $this->dispatch('show-toast', message: $message, type: 'success');
}
```

**In Blade:**

```blade
<script>
document.addEventListener('livewire:init', () => {
    Livewire.on('show-toast', (event) => {
        // Show toast notification
        alert(event.message);
    });
});
</script>
```

---

## Validation Patterns

### Real-Time Validation

```php
use Livewire\Attributes\Validate;

class ExpenseForm extends Component
{
    #[Validate('required|numeric|min:0')]
    public $amount = '';

    #[Validate('required|string|max:255')]
    public $description = '';

    #[Validate('required|date')]
    public $date = '';

    public function save()
    {
        $this->validate();

        Expense::create([
            'amount' => $this->amount,
            'description' => $this->description,
            'date' => $this->date,
        ]);

        session()->flash('success', 'Expense created successfully');
        return redirect()->route('financials.index');
    }
}
```

**View:**

```blade
<form wire:submit="save">
    <div>
        <input type="number" wire:model.blur="amount" step="0.01">
        @error('amount') <span class="error">{{ $message }}</span> @enderror
    </div>

    <div>
        <input type="text" wire:model.blur="description">
        @error('description') <span class="error">{{ $message }}</span> @enderror
    </div>

    <div>
        <input type="date" wire:model.blur="date">
        @error('date') <span class="error">{{ $message }}</span> @enderror
    </div>

    <button type="submit">Save</button>
</form>
```

---

## Authorization Patterns

### Policy-Based Authorization

```php
class FinancialDashboard extends Component
{
    public function mount()
    {
        $this->authorize('view-financials');
    }

    public function lockPeriod($month, $year)
    {
        $this->authorize('lock-financial-period');

        // Lock logic
    }
}
```

---

## Testing Patterns

### Component Testing

```php
use Livewire\Livewire;

public function test_dashboard_loads_stats()
{
    $user = User::factory()->create(['role' => 'general']);

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->assertSee('Total Members')
        ->assertSee('Active Members');
}

public function test_report_builder_generates_preview()
{
    $user = User::factory()->create(['role' => 'general']);

    Livewire::actingAs($user)
        ->test(ReportBuilder::class)
        ->set('reportType', 'demographic')
        ->call('generatePreview')
        ->assertSet('previewData', fn($data) => !is_null($data));
}
```

---

## Common Pitfalls & Solutions

### Issue 1: N+1 Queries

**Problem:** Computed properties causing multiple queries

**Solution:**

```php
// Bad
#[Computed]
public function members()
{
    return Member::all(); // Will cause N+1 on relationships
}

// Good
#[Computed]
public function members()
{
    return Member::with('community', 'assignments')->get();
}
```

---

### Issue 2: Stale Data

**Problem:** Component not refreshing after external changes

**Solution:**

```php
// Listen for events
#[On('member-updated')]
public function handleMemberUpdate()
{
    unset($this->members); // Clear computed property cache
}
```

---

### Issue 3: Large Payloads

**Problem:** Sending too much data in component state

**Solution:**

```php
// Bad
public $allMembers = []; // Thousands of records

// Good
public $selectedMemberId = null;

#[Computed]
public function selectedMember()
{
    return Member::find($this->selectedMemberId);
}
```

---

## Best Practices Checklist

When creating Livewire components:

- [ ] Use `#[Computed]` for derived data
- [ ] Eager load relationships
- [ ] Implement loading states (`wire:loading`)
- [ ] Add authorization checks
- [ ] Validate user input
- [ ] Use `wire:model.live` sparingly (prefer `.blur` or `.change`)
- [ ] Extract complex logic to services
- [ ] Write component tests
- [ ] Document public methods
- [ ] Handle errors gracefully

---

## Migration from Traditional Controllers

### When to Use Livewire vs. Traditional Controllers

**Use Livewire When:**

- Real-time filtering/searching
- Dynamic form inputs
- Interactive dashboards
- Multi-step processes
- Inline editing

**Use Traditional Controllers When:**

- Simple CRUD operations
- File downloads
- Redirects after form submission
- SEO-critical pages
- Complex authorization logic

---

## Future Enhancements

Potential improvements to Livewire architecture:

1. **Component Library:** Reusable UI components (DataTable, Modal, etc.)
2. **Real-Time Updates:** WebSocket integration for live notifications
3. **Offline Support:** Service worker integration
4. **Component Documentation:** Auto-generated component API docs
5. **Performance Monitoring:** Track component render times

---

**Document Status:** ✅ Complete  
**Maintained By:** Development Team  
**Review Frequency:** When new Livewire components are added
