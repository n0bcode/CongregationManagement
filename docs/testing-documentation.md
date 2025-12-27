# Testing Documentation - Congregation Management System

**Last Updated:** 2025-12-27  
**Version:** 1.0  
**Testing Framework:** PHPUnit 11.5.3

---

## Overview

This document outlines the testing strategy, patterns, and best practices for the Congregation Management System. The project uses PHPUnit for both unit and feature tests, with Livewire testing support.

---

## Testing Philosophy

**Core Principles:**

1. **Test behavior, not implementation**
2. **Write tests before fixing bugs**
3. **Maintain high coverage for critical paths**
4. **Keep tests fast and isolated**
5. **Use factories for test data**

**Coverage Goals:**

- **Critical Features:** 90%+ coverage (Auth, RBAC, Financial, Formation)
- **Standard Features:** 70%+ coverage
- **UI Components:** Key user flows tested

---

## Running Tests

### Basic Commands

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/MemberTest.php

# Run specific test method
php artisan test --filter test_user_can_create_member

# Run with coverage
php artisan test --coverage

# Run with coverage minimum threshold
php artisan test --coverage --min=80

# Parallel testing (faster)
php artisan test --parallel
```

### Docker Commands

```bash
# Run tests in Docker
docker compose exec app php artisan test

# With coverage
docker compose exec app php artisan test --coverage
```

---

## Test Structure

### Directory Organization

```
tests/
├── Feature/              # Feature/Integration tests
│   ├── Auth/
│   │   ├── RbacPermissionTest.php
│   │   ├── PermissionSeederTest.php
│   │   └── RbacIntegrationTest.php
│   ├── MemberTest.php
│   ├── CommunityTest.php
│   ├── FormationTest.php
│   ├── FinancialTest.php
│   └── ...
├── Unit/                 # Unit tests
│   ├── Enums/
│   │   └── PermissionKeyTest.php
│   ├── Services/
│   │   ├── FormationServiceTest.php
│   │   └── PermissionServiceTest.php
│   └── ...
├── TestCase.php          # Base test case
└── CreatesApplication.php
```

---

## Test Patterns

### Feature Test Pattern

**Purpose:** Test full HTTP request/response cycles

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_members_list(): void
    {
        // Arrange
        $user = User::factory()->create(['role' => 'general']);
        Member::factory()->count(5)->create();

        // Act
        $response = $this->actingAs($user)->get(route('members.index'));

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('members.index');
        $response->assertViewHas('members');
    }

    public function test_user_can_create_member(): void
    {
        $user = User::factory()->create(['role' => 'general']);

        $memberData = [
            'religious_name' => 'Sr. Mary',
            'civil_name' => 'Mary Smith',
            'date_of_birth' => '1990-01-01',
            'entry_date' => '2020-01-01',
            'status' => 'Active',
        ];

        $response = $this->actingAs($user)
            ->post(route('members.store'), $memberData);

        $response->assertRedirect();
        $this->assertDatabaseHas('members', [
            'religious_name' => 'Sr. Mary',
        ]);
    }

    public function test_director_cannot_view_other_community_members(): void
    {
        $community1 = Community::factory()->create();
        $community2 = Community::factory()->create();

        $director = User::factory()->create([
            'role' => 'director',
            'community_id' => $community1->id,
        ]);

        $member = Member::factory()->create([
            'community_id' => $community2->id,
        ]);

        $response = $this->actingAs($director)
            ->get(route('members.show', $member));

        $response->assertForbidden();
    }
}
```

### Unit Test Pattern

**Purpose:** Test isolated logic without HTTP layer

```php
<?php

namespace Tests\Unit\Services;

use App\Services\FormationService;
use App\Models\Member;
use Carbon\Carbon;
use Tests\TestCase;

class FormationServiceTest extends TestCase
{
    private FormationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new FormationService();
    }

    public function test_calculates_first_vows_eligibility_date(): void
    {
        $entryDate = Carbon::parse('2020-01-01');

        $eligibilityDate = $this->service->calculateFirstVowsEligibility($entryDate);

        $this->assertEquals('2021-01-01', $eligibilityDate->format('Y-m-d'));
    }

    public function test_validates_canon_law_requirements(): void
    {
        $member = new Member([
            'entry_date' => Carbon::parse('2020-01-01'),
            'novitiate_start' => Carbon::parse('2021-01-01'),
        ]);

        $isValid = $this->service->validateCanonLawRequirements($member);

        $this->assertTrue($isValid);
    }
}
```

### Livewire Test Pattern

```php
<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Dashboard;
use App\Models\User;
use App\Models\Member;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_displays_member_stats(): void
    {
        $user = User::factory()->create(['role' => 'general']);
        Member::factory()->count(10)->create(['status' => 'Active']);
        Member::factory()->count(2)->create(['status' => 'Deceased']);

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->assertSee('Total Members')
            ->assertSee('12') // Total count
            ->assertSee('Active Members')
            ->assertSee('10'); // Active count
    }

    public function test_dashboard_refreshes_data(): void
    {
        $user = User::factory()->create(['role' => 'general']);

        $component = Livewire::actingAs($user)
            ->test(Dashboard::class);

        // Create new member after component loads
        Member::factory()->create();

        $component->call('refresh')
            ->assertDispatched('dashboard-refreshed');
    }
}
```

### Policy Test Pattern

```php
<?php

namespace Tests\Unit\Policies;

use App\Models\User;
use App\Models\Member;
use App\Policies\MemberPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberPolicyTest extends TestCase
{
    use RefreshDatabase;

    private MemberPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new MemberPolicy();
    }

    public function test_super_admin_can_do_anything(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        $member = Member::factory()->create();

        $this->assertTrue($this->policy->view($superAdmin, $member));
        $this->assertTrue($this->policy->update($superAdmin, $member));
        $this->assertTrue($this->policy->delete($superAdmin, $member));
    }

    public function test_director_can_only_view_own_community_members(): void
    {
        $community = Community::factory()->create();
        $director = User::factory()->create([
            'role' => 'director',
            'community_id' => $community->id,
        ]);

        $ownMember = Member::factory()->create(['community_id' => $community->id]);
        $otherMember = Member::factory()->create();

        $this->assertTrue($this->policy->view($director, $ownMember));
        $this->assertFalse($this->policy->view($director, $otherMember));
    }
}
```

---

## Testing RBAC

### Permission Testing

```php
public function test_permission_seeder_creates_all_permissions(): void
{
    $this->seed(PermissionSeeder::class);

    $this->assertDatabaseHas('permissions', [
        'key' => 'territories.view',
    ]);

    $this->assertDatabaseHas('permissions', [
        'key' => 'publishers.manage',
    ]);

    // Verify all PermissionKey enum values are seeded
    $permissionKeys = array_column(PermissionKey::cases(), 'value');
    $seededKeys = Permission::pluck('key')->toArray();

    $this->assertEquals(sort($permissionKeys), sort($seededKeys));
}

public function test_user_has_permission_check(): void
{
    $user = User::factory()->create(['role' => 'general']);

    $this->assertTrue($user->hasPermission(PermissionKey::TERRITORIES_VIEW));
    $this->assertFalse($user->hasPermission(PermissionKey::SOME_ADMIN_ONLY_PERMISSION));
}
```

---

## Testing Exports

### PDF Export Testing

```php
public function test_generates_financial_report_pdf(): void
{
    $user = User::factory()->create(['role' => 'general']);
    Expense::factory()->count(5)->create();

    $response = $this->actingAs($user)
        ->get(route('financials.monthly-report'));

    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'application/pdf');
}
```

### Excel Export Testing

```php
use Maatwebsite\Excel\Facades\Excel;

public function test_exports_members_to_excel(): void
{
    Excel::fake();

    $user = User::factory()->create(['role' => 'general']);
    Member::factory()->count(10)->create();

    $this->actingAs($user)
        ->get(route('reports.demographic.export'));

    Excel::assertDownloaded('members-export.xlsx', function (MembersExport $export) {
        return $export->collection()->count() === 10;
    });
}
```

---

## Database Testing

### Using Factories

```php
// Create single model
$member = Member::factory()->create();

// Create with specific attributes
$member = Member::factory()->create([
    'religious_name' => 'Sr. Mary',
    'status' => 'Active',
]);

// Create multiple
$members = Member::factory()->count(10)->create();

// Create with relationships
$member = Member::factory()
    ->for(Community::factory())
    ->has(Assignment::factory()->count(3))
    ->create();
```

### Database Assertions

```php
// Assert record exists
$this->assertDatabaseHas('members', [
    'religious_name' => 'Sr. Mary',
]);

// Assert record doesn't exist
$this->assertDatabaseMissing('members', [
    'id' => 999,
]);

// Assert count
$this->assertDatabaseCount('members', 10);

// Soft deletes
$this->assertSoftDeleted('members', [
    'id' => $member->id,
]);
```

---

## Test Data Management

### Seeders for Testing

```php
// Run specific seeder
$this->seed(PermissionSeeder::class);

// Run all seeders
$this->seed();

// Custom test seeder
class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        Community::factory()->count(3)->create();
        Member::factory()->count(20)->create();
    }
}
```

### RefreshDatabase vs DatabaseTransactions

```php
// RefreshDatabase - Migrates fresh database before each test
use Illuminate\Foundation\Testing\RefreshDatabase;

class MyTest extends TestCase
{
    use RefreshDatabase;
}

// DatabaseTransactions - Rolls back after each test (faster)
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MyTest extends TestCase
{
    use DatabaseTransactions;
}
```

---

## Mocking & Faking

### Faking External Services

```php
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

public function test_sends_birthday_email(): void
{
    Mail::fake();

    $member = Member::factory()->create();

    $this->post(route('celebrations.birthday.email', $member));

    Mail::assertSent(BirthdayCardMail::class, function ($mail) use ($member) {
        return $mail->member->id === $member->id;
    });
}

public function test_stores_uploaded_photo(): void
{
    Storage::fake('public');

    $file = UploadedFile::fake()->image('photo.jpg');

    $response = $this->actingAs($user)
        ->put(route('members.photo.update', $member), [
            'photo' => $file,
        ]);

    Storage::disk('public')->assertExists('photos/' . $file->hashName());
}
```

---

## Performance Testing

### Query Count Testing

```php
use Illuminate\Support\Facades\DB;

public function test_members_index_avoids_n_plus_one(): void
{
    Member::factory()->count(10)->create();

    DB::enableQueryLog();

    $this->actingAs($user)->get(route('members.index'));

    $queryCount = count(DB::getQueryLog());

    // Should be around 3-5 queries (not 10+)
    $this->assertLessThan(10, $queryCount);
}
```

---

## Continuous Integration

### GitHub Actions Example

```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest

    services:
      mysql:
        image: mysql:8.0
        env:
          MYSQL_DATABASE: testing
          MYSQL_ROOT_PASSWORD: password
        ports:
          - 3306:3306

    steps:
      - uses: actions/checkout@v2

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.3
          extensions: mbstring, pdo_mysql

      - name: Install Dependencies
        run: composer install

      - name: Run Tests
        run: php artisan test --coverage --min=70
        env:
          DB_CONNECTION: mysql
          DB_HOST: 127.0.0.1
          DB_PORT: 3306
          DB_DATABASE: testing
          DB_USERNAME: root
          DB_PASSWORD: password
```

---

## Best Practices

### ✅ DO

```php
// Use descriptive test names
public function test_director_cannot_delete_members_from_other_communities(): void

// Arrange, Act, Assert pattern
public function test_example(): void
{
    // Arrange
    $user = User::factory()->create();

    // Act
    $response = $this->actingAs($user)->get('/dashboard');

    // Assert
    $response->assertOk();
}

// Test one thing per test
public function test_creates_member(): void { /* ... */ }
public function test_validates_required_fields(): void { /* ... */ }

// Use factories for test data
$member = Member::factory()->create();
```

### ❌ DON'T

```php
// Vague test names
public function test_it_works(): void

// No clear structure
public function test_stuff(): void
{
    $user = User::factory()->create();
    $response = $this->actingAs($user)->get('/dashboard');
    $member = Member::factory()->create();
    // What are we testing?
}

// Testing multiple things
public function test_member_crud(): void
{
    // Creates, reads, updates, deletes - too much!
}

// Hard-coded test data
$member = Member::create([
    'religious_name' => 'Test',
    // ...50 fields
]);
```

---

## Coverage Reports

### Generating Coverage

```bash
# HTML coverage report
php artisan test --coverage-html coverage

# Open coverage/index.html in browser
```

### Coverage Goals by Module

| Module               | Target Coverage | Priority |
| -------------------- | --------------- | -------- |
| Authentication       | 95%             | Critical |
| RBAC/Permissions     | 95%             | Critical |
| Member Management    | 85%             | High     |
| Formation Tracking   | 85%             | High     |
| Financial Management | 85%             | High     |
| Community Management | 80%             | Medium   |
| Exports              | 75%             | Medium   |
| UI Components        | 60%             | Low      |

---

## Troubleshooting

### Common Issues

**Issue: Tests fail with database errors**

```bash
# Ensure test database is configured
# Check .env.testing or phpunit.xml
php artisan config:clear
php artisan test
```

**Issue: Slow tests**

```bash
# Use parallel testing
php artisan test --parallel

# Use DatabaseTransactions instead of RefreshDatabase
```

**Issue: Livewire tests failing**

```bash
# Clear Livewire cache
php artisan livewire:delete-cache
php artisan test
```

---

**Document Status:** ✅ Complete  
**Maintained By:** Development Team  
**Review Frequency:** When testing strategy changes
