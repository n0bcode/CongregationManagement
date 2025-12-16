# Database Security Best Practices

## Overview

This document provides guidelines for writing secure database queries in the Laravel 11 Congregation Management System. Following these practices will help prevent SQL injection and other database-related vulnerabilities.

## Table of Contents

1. [SQL Injection Prevention](#sql-injection-prevention)
2. [Parameter Binding](#parameter-binding)
3. [Field Whitelisting](#field-whitelisting)
4. [LIKE Query Escaping](#like-query-escaping)
5. [Code Review Checklist](#code-review-checklist)

---

## SQL Injection Prevention

### What is SQL Injection?

SQL injection is a code injection technique that exploits security vulnerabilities in database queries. Attackers can manipulate SQL queries to:

-   Access unauthorized data
-   Modify or delete data
-   Execute administrative operations
-   Compromise the entire database

### Common Vulnerabilities

#### ❌ DANGEROUS: String Interpolation in Raw Queries

```php
// NEVER DO THIS
$userId = $request->input('user_id');
DB::select("SELECT * FROM users WHERE id = $userId");

// NEVER DO THIS
$date = $request->input('date');
$query->whereRaw("created_at > '{$date}'");
```

**Why it's dangerous**: User input is directly embedded in SQL, allowing attackers to inject malicious SQL code.

#### ✅ SAFE: Use Parameter Binding

```php
// DO THIS
$userId = $request->input('user_id');
DB::select("SELECT * FROM users WHERE id = ?", [$userId]);

// DO THIS
$date = $request->input('date');
$query->whereRaw("created_at > ?", [$date]);
```

---

## Parameter Binding

### Using Question Mark Placeholders

```php
// Single parameter
DB::select("SELECT * FROM members WHERE id = ?", [$id]);

// Multiple parameters
DB::select(
    "SELECT * FROM members WHERE community_id = ? AND status = ?",
    [$communityId, $status]
);

// In whereRaw
$query->whereRaw(
    "DATE_ADD(dob, INTERVAL YEAR(?) - YEAR(dob) YEAR) BETWEEN ? AND ?",
    [$startDate, $startDate, $endDate]
);
```

### Using Named Placeholders

```php
DB::select(
    "SELECT * FROM members WHERE community_id = :community AND status = :status",
    ['community' => $communityId, 'status' => $status]
);
```

### Eloquent Query Builder (Preferred)

```php
// Eloquent automatically handles parameter binding
Member::where('community_id', $communityId)
    ->where('status', $status)
    ->get();

// Even with multiple conditions
Member::where('first_name', 'like', "%{$search}%")
    ->orWhere('last_name', 'like', "%{$search}%")
    ->get();
```

---

## Field Whitelisting

### Dynamic Field Names

When allowing users to specify field names (e.g., for sorting or filtering), **always use a whitelist**.

#### ❌ DANGEROUS: Unvalidated Field Names

```php
// NEVER DO THIS
public function sortBy($field)
{
    return Member::orderBy($field)->get(); // SQL injection risk!
}

// NEVER DO THIS
public function updateField($id, $field, $value)
{
    Member::find($id)->update([$field => $value]); // Unauthorized field access!
}
```

#### ✅ SAFE: Whitelist Allowed Fields

```php
// DO THIS
public function sortBy($field)
{
    $allowedFields = ['first_name', 'last_name', 'created_at', 'status'];

    if (!in_array($field, $allowedFields)) {
        throw new InvalidArgumentException("Invalid sort field");
    }

    return Member::orderBy($field)->get();
}

// DO THIS
public function updateField($id, $field, $value)
{
    $allowedFields = ['first_name', 'last_name', 'email'];

    if (!in_array($field, $allowedFields)) {
        throw new InvalidArgumentException("Field update not allowed");
    }

    $member = Member::find($id);
    $this->authorize('update', $member);

    $member->update([$field => $value]);
}
```

---

## LIKE Query Escaping

### The Problem

SQL wildcards (`%` and `_`) in user input can cause unintended matches:

```php
// User searches for "50% discount"
Member::where('notes', 'like', "%50% discount%")->get();
// This matches "50X discount", "500 discount", etc. (% is wildcard)

// User searches for "test_user"
Member::where('username', 'like', "%test_user%")->get();
// This matches "test1user", "testXuser", etc. (_ is single char wildcard)
```

### The Solution

Escape wildcards before using in LIKE queries:

```php
// Escape SQL wildcards
$escapedSearch = addcslashes($search, '%_');

Member::where('notes', 'like', "%{$escapedSearch}%")->get();
```

### Example Implementation

```php
public function scopeSearch($query, $term)
{
    // Escape SQL wildcards (% and _) to treat them as literal characters
    $escapedTerm = addcslashes($term, '%_');

    return $query->where(function ($query) use ($escapedTerm) {
        $query->where('first_name', 'like', "%{$escapedTerm}%")
            ->orWhere('last_name', 'like', "%{$escapedTerm}%")
            ->orWhere('email', 'like', "%{$escapedTerm}%");
    });
}
```

---

## Code Review Checklist

Use this checklist when reviewing database-related code:

### Raw Queries

-   [ ] All `DB::select()` calls use parameter binding (`?` or named parameters)
-   [ ] All `DB::statement()` calls use parameter binding
-   [ ] All `DB::raw()` calls contain only static SQL (no user input)
-   [ ] No string concatenation or interpolation in raw queries

### Eloquent Queries

-   [ ] All `whereRaw()` calls use parameter binding
-   [ ] All `orderByRaw()` calls use parameter binding or static SQL
-   [ ] All `selectRaw()` calls use parameter binding or static SQL
-   [ ] All `havingRaw()` calls use parameter binding

### Dynamic Fields

-   [ ] All dynamic field names are validated against a whitelist
-   [ ] All dynamic `orderBy()` fields are whitelisted
-   [ ] All dynamic `where()` fields are whitelisted
-   [ ] No user input directly used as field names

### LIKE Queries

-   [ ] All LIKE queries with user input escape wildcards
-   [ ] Search functionality uses `addcslashes($term, '%_')`

### Validation & Authorization

-   [ ] All user input is validated before use in queries
-   [ ] Authorization checks are in place for data access
-   [ ] Form Request validation is used for complex inputs
-   [ ] Enum values are validated against allowed cases

### Testing

-   [ ] Security tests exist for SQL injection prevention
-   [ ] Tests verify wildcard escaping in LIKE queries
-   [ ] Tests verify field whitelisting
-   [ ] Tests verify parameter binding in raw queries

---

## Common Patterns

### Safe Aggregate Queries

```php
// These are SAFE - no user input
DB::raw('COUNT(*) as count')
DB::raw('SUM(amount) as total')
DB::raw('AVG(score) as average')
DB::raw('MONTH(created_at) as month')
DB::raw('YEAR(created_at) as year')
```

### Safe CASE Statements

```php
// SAFE - static SQL only
DB::raw('CASE
    WHEN age < 30 THEN "Young"
    WHEN age BETWEEN 30 AND 50 THEN "Middle"
    ELSE "Senior"
END as age_group')
```

### Safe Date Calculations

```php
// Use parameter binding for dates
$query->whereRaw(
    "DATE_ADD(dob, INTERVAL YEAR(?) - YEAR(dob) YEAR) BETWEEN ? AND ?",
    [$currentDate, $currentDate, $endDate]
);
```

---

## Security Testing

### Running Security Tests

```bash
# Run all security tests
php artisan test --filter=Security

# Run specific test suites
php artisan test --filter=QuerySecurityTest
php artisan test --filter=LivewireSecurityTest
php artisan test --filter=CodeScannerTest
```

### Writing Security Tests

```php
/** @test */
public function it_prevents_sql_injection_in_search()
{
    $payloads = [
        "' OR '1'='1",
        "'; DROP TABLE users; --",
        "' UNION SELECT * FROM passwords --",
    ];

    foreach ($payloads as $payload) {
        $results = Member::search($payload)->get();

        // Should not return all members
        $this->assertLessThan(
            Member::count(),
            $results->count(),
            "SQL injection may have worked!"
        );
    }
}
```

---

## Resources

-   [Laravel Query Builder Documentation](https://laravel.com/docs/11.x/queries)
-   [OWASP SQL Injection Prevention Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/SQL_Injection_Prevention_Cheat_Sheet.html)
-   [PHP PDO Prepared Statements](https://www.php.net/manual/en/pdo.prepared-statements.php)

---

## Questions?

If you have questions about database security or need help reviewing code, please:

1. Consult this document first
2. Review existing security tests for examples
3. Ask the security team for guidance

**Remember**: When in doubt, use Eloquent Query Builder instead of raw SQL!
