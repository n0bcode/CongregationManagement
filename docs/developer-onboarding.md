# Developer Onboarding Guide - Congregation Management System

**Last Updated:** 2025-12-27  
**Version:** 1.0  
**Estimated Time:** 2-3 hours

---

## Welcome! 👋

Welcome to the Congregation Management System development team! This guide will help you get up and running quickly.

---

## What You're Building

A specialized member management solution for religious congregations, built with:

- **Backend:** Laravel 11, PHP 8.3.6
- **Frontend:** Blade, Livewire 3.7, Tailwind CSS 3.4
- **Database:** MySQL 8.0
- **Key Features:** Member tracking, formation management, financial reporting, project management with AI, celebration cards, advanced exports

**System Scale:**

- 28 database models
- 186 routes
- 14 major feature modules
- Production-ready with 500+ members capacity

---

## Prerequisites

### Required Software

- **PHP:** 8.3.6 or higher
- **Composer:** 2.x
- **Node.js:** 18.x LTS or higher
- **Docker Desktop:** Latest version
- **Git:** Latest version
- **IDE:** VS Code, PHPStorm, or similar

### Recommended VS Code Extensions

```json
{
  "recommendations": [
    "bmewburn.vscode-intelephense-client",
    "amiralizadeh9480.laravel-extra-intellisense",
    "onecentlin.laravel-blade",
    "bradlc.vscode-tailwindcss",
    "esbenp.prettier-vscode"
  ]
}
```

---

## Quick Start (5 Minutes)

### 1. Clone Repository

```bash
git clone https://github.com/your-org/managing-congregation.git
cd managing-congregation
```

### 2. Start Development Environment

```bash
# Copy environment file
cp .env.example .env

# Start Docker containers
docker compose up -d

# Install PHP dependencies
docker compose exec app composer install

# Install Node dependencies
docker compose exec app npm install

# Generate application key
docker compose exec app php artisan key:generate

# Run migrations and seeders
docker compose exec app php artisan migrate --seed

# Build frontend assets
docker compose exec app npm run dev
```

### 3. Access Application

- **Application:** http://localhost:8000
- **Database:** localhost:3306
- **Redis:** localhost:6379
- **Mailpit (Email Testing):** http://localhost:8025

### 4. Login Credentials

After seeding, use these credentials:

```
Email: admin@example.com
Password: password
Role: Super Admin
```

---

## Project Structure

```
managing-congregation/
├── app/
│   ├── Enums/           # Type-safe enums (UserRole, PermissionKey)
│   ├── Http/
│   │   ├── Controllers/ # Traditional controllers
│   │   ├── Requests/    # Form validation classes
│   │   └── Middleware/  # Custom middleware
│   ├── Livewire/        # Livewire components (Dashboard, Reports, etc.)
│   ├── Models/          # 28 Eloquent models
│   ├── Policies/        # Authorization policies
│   └── Services/        # Business logic (Formation, PDF, Permission)
├── database/
│   ├── migrations/      # 54 migration files
│   ├── seeders/         # Database seeders
│   └── factories/       # Model factories for testing
├── resources/
│   ├── views/           # Blade templates
│   │   ├── components/  # Reusable UI components
│   │   ├── livewire/    # Livewire component views
│   │   └── members/     # Feature-specific views
│   ├── css/             # Tailwind CSS
│   └── js/              # Alpine.js and app.js
├── routes/
│   ├── web.php          # All application routes (186 routes)
│   └── auth.php         # Authentication routes
├── tests/
│   ├── Feature/         # Feature tests
│   └── Unit/            # Unit tests
└── docs/                # Comprehensive documentation
```

---

## Key Concepts

### 1. RBAC System

The system uses a **dynamic role-based access control** system:

**Default Roles:**

- `super_admin` - Full system access
- `general` - General Secretary role
- `director` - Community Director (scoped to their community)
- `treasurer` - Financial access
- `member` - Basic member access

**Permission Checking:**

```php
// In controllers
$this->authorize('view', $member);

// In Livewire components
$this->authorize('view-financials');

// In models/services
if ($user->hasPermission(PermissionKey::TERRITORIES_VIEW)) {
    // ...
}
```

**Reference:** `docs/project_context.md` (RBAC Rules section)

### 2. Community Scoping

Directors can only see data for their assigned community:

```php
// Automatic scoping via Global Scope
$members = Member::all(); // Automatically filtered for Directors

// Manual scoping
$expenses = Expense::where('community_id', auth()->user()->community_id)->get();
```

### 3. Livewire Architecture

The system uses **Livewire heavily** for interactive components:

**Existing Components:**

- `Dashboard` - Main dashboard
- `FinancialDashboard` - Financial overview
- `Reports\ReportBuilder` - Advanced report builder
- `Notifications\NotificationCenter` - Notifications

**Pattern:**

```php
use Livewire\Component;
use Livewire\Attributes\Computed;

class MyComponent extends Component
{
    public $filter = '';

    #[Computed]
    public function data()
    {
        return Model::where('name', 'like', "%{$this->filter}%")->get();
    }

    public function render()
    {
        return view('livewire.my-component');
    }
}
```

**Reference:** `docs/livewire-patterns.md`

### 4. Export System

Multi-format export support (PDF, Excel, DOCX):

```php
// PDF
use Barryvdh\DomPDF\Facade\Pdf;
$pdf = Pdf::loadView('reports.template', $data);
return $pdf->download('report.pdf');

// Excel
use Maatwebsite\Excel\Facades\Excel;
return Excel::download(new MembersExport, 'members.xlsx');

// DOCX
use PhpOffice\PhpWord\PhpWord;
$phpWord = new PhpWord();
// ... build document
```

**Reference:** `docs/export-architecture.md`

---

## Development Workflow

### Daily Development

```bash
# Start containers
docker compose up -d

# Start Vite dev server (hot reload)
docker compose exec app npm run dev

# Watch for file changes
docker compose exec app php artisan pail
```

### Running Tests

```bash
# Run all tests
docker compose exec app php artisan test

# Run specific test file
docker compose exec app php artisan test tests/Feature/MemberTest.php

# Run with coverage
docker compose exec app php artisan test --coverage
```

### Code Quality

```bash
# Format code (Laravel Pint)
docker compose exec app ./vendor/bin/pint

# Static analysis (PHPStan)
docker compose exec app ./vendor/bin/phpstan analyse

# Run both
docker compose exec app composer check
```

### Database Operations

```bash
# Create migration
docker compose exec app php artisan make:migration create_table_name

# Run migrations
docker compose exec app php artisan migrate

# Rollback
docker compose exec app php artisan migrate:rollback

# Fresh database with seed
docker compose exec app php artisan migrate:fresh --seed

# Create seeder
docker compose exec app php artisan make:seeder TableNameSeeder
```

### Creating New Features

**1. Create Model & Migration:**

```bash
docker compose exec app php artisan make:model MyModel -m
```

**2. Create Controller:**

```bash
docker compose exec app php artisan make:controller MyModelController --resource
```

**3. Create Form Request:**

```bash
docker compose exec app php artisan make:request StoreMyModelRequest
```

**4. Create Policy:**

```bash
docker compose exec app php artisan make:policy MyModelPolicy --model=MyModel
```

**5. Create Livewire Component (if needed):**

```bash
docker compose exec app php artisan make:livewire MyComponent
```

**6. Add Routes:**

```php
// routes/web.php
Route::resource('my-models', MyModelController::class);
```

---

## Common Tasks

### Adding a New Permission

```php
// 1. Add to PermissionKey enum
// app/Enums/PermissionKey.php
enum PermissionKey: string {
    case MY_NEW_PERMISSION = 'my.new.permission';
}

// 2. Add to database seeder
// database/seeders/PermissionSeeder.php
Permission::create([
    'key' => 'my.new.permission',
    'name' => 'My New Permission',
    'module' => 'My Module',
]);

// 3. Assign to roles
DB::table('role_permissions')->insert([
    'role' => 'super_admin',
    'permission_id' => $permission->id,
]);
```

### Creating a PDF Export

```php
// 1. Create Blade view
// resources/views/exports/my-report.blade.php

// 2. Create controller method
public function exportPdf()
{
    $data = $this->prepareData();
    $pdf = Pdf::loadView('exports.my-report', compact('data'));
    return $pdf->download('my-report.pdf');
}
```

### Adding a Livewire Component

```bash
# Create component
docker compose exec app php artisan make:livewire MyFeature

# Register route
Route::get('/my-feature', \App\Livewire\MyFeature::class)->name('my-feature');
```

---

## Debugging

### Laravel Telescope (if installed)

```bash
# Install Telescope
docker compose exec app composer require laravel/telescope --dev
docker compose exec app php artisan telescope:install
docker compose exec app php artisan migrate

# Access at: http://localhost:8000/telescope
```

### Debugging Tools

```php
// Dump and die
dd($variable);

// Dump to logs
logger()->info('Debug info', ['data' => $variable]);

// Ray (if installed)
ray($variable);

// SQL query logging
DB::enableQueryLog();
// ... run queries
dd(DB::getQueryLog());
```

### Common Issues

**Issue: Permission Denied on Storage**

```bash
docker compose exec app chmod -R 775 storage
docker compose exec app chown -R www-data:www-data storage
```

**Issue: Vite Not Hot Reloading**

```bash
# Restart Vite
docker compose exec app npm run dev
```

**Issue: Database Connection Failed**

```bash
# Check .env DB settings match docker-compose.yml
# Restart database container
docker compose restart db
```

---

## Best Practices

### Code Style

✅ **DO:**

```php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreMemberRequest;
use App\Models\Member;

class MemberController extends Controller
{
    public function store(StoreMemberRequest $request): RedirectResponse
    {
        $this->authorize('create', Member::class);

        $member = Member::create($request->validated());

        return to_route('members.show', $member)
            ->with('success', 'Member created successfully');
    }
}
```

❌ **DON'T:**

```php
// No strict types
// No authorization
// Validation in controller
// No type hints
public function store(Request $request)
{
    $request->validate([...]); // Should be FormRequest
    $member = Member::create($request->all());
    return redirect('/members/' . $member->id); // Use named routes
}
```

### Database Queries

✅ **DO:**

```php
// Eager load relationships
$members = Member::with('community', 'assignments')->get();

// Select specific columns
$members = Member::select('id', 'name', 'community_id')->get();

// Use scopes
$activeMembers = Member::active()->get();
```

❌ **DON'T:**

```php
// N+1 queries
$members = Member::all();
foreach ($members as $member) {
    echo $member->community->name; // N+1!
}

// Select all columns when not needed
$members = Member::all(); // Loads everything
```

### Livewire Performance

✅ **DO:**

```php
#[Computed]
public function members()
{
    return Member::select('id', 'name')->get();
}

// Use wire:model.blur for non-critical inputs
<input wire:model.blur="search">
```

❌ **DON'T:**

```php
// No computed property (queries on every render)
public function render()
{
    return view('livewire.component', [
        'members' => Member::all() // Runs every render!
    ]);
}

// wire:model.live everywhere (performance hit)
<input wire:model.live="search">
```

---

## Resources

### Documentation

- **Project Context:** `docs/project_context.md` - **READ THIS FIRST**
- **Features:** `docs/features-implemented.md`
- **Livewire Patterns:** `docs/livewire-patterns.md`
- **Export Architecture:** `docs/export-architecture.md`
- **Deployment:** `docs/deployment-guide.md`

### External Resources

- [Laravel 11 Documentation](https://laravel.com/docs/11.x)
- [Livewire 3 Documentation](https://livewire.laravel.com/docs)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [Alpine.js Documentation](https://alpinejs.dev)

### Team Communication

- **Slack/Discord:** [channel link]
- **GitHub Issues:** [repo link]
- **Code Reviews:** Required for all PRs
- **Daily Standup:** [time/location]

---

## Next Steps

1. ✅ Complete this onboarding guide
2. ✅ Set up local development environment
3. ✅ Read `docs/project_context.md`
4. ✅ Review `docs/features-implemented.md`
5. ✅ Pick a "good first issue" from GitHub
6. ✅ Submit your first PR!

---

## Getting Help

**Stuck? Here's how to get help:**

1. **Check Documentation:** Start with `docs/project_context.md`
2. **Search Issues:** GitHub issues might have the answer
3. **Ask the Team:** Slack/Discord channel
4. **Pair Programming:** Schedule time with a senior dev

**When asking for help, include:**

- What you're trying to do
- What you've tried
- Error messages (full stack trace)
- Relevant code snippets

---

**Welcome to the team! Happy coding! 🚀**

---

**Document Status:** ✅ Complete  
**Maintained By:** Development Team  
**Review Frequency:** When onboarding process changes
