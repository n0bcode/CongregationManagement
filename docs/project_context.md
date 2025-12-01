---
project_name: "Managing the Congregation (at the organizational level)"
user_name: "Wavister"
date: "2025-12-01"
sections_completed:
  [
    "technology_stack",
    "language_rules",
    "framework_rules",
    "testing_rules",
    "quality_rules",
    "workflow_rules",
    "anti_patterns",
  ]
status: "complete"
rule_count: 18
optimized_for_llm: true
---

# Project Context for AI Agents

_This file contains critical rules and patterns that AI agents must follow when implementing code in this project. Focus on unobvious details that agents might otherwise miss._

---

## Technology Stack & Versions

- **PHP:** 8.x (Strict Typing)
- **Laravel:** 11.x
- **MySQL:** 8.0
- **Frontend:** Blade + Tailwind CSS + Alpine.js
- **Livewire:** 3.x (Use sparingly for dynamic UI)
- **Testing:** Pest (Preferred) or PHPUnit
- **Build Tool:** Vite
- **Container:** Docker (Laravel Sail)

## Critical Implementation Rules

### Language-Specific Rules (PHP 8.x)

- **Strict Typing:** Always use `declare(strict_types=1);` in all new PHP files.
- **Type Hinting:** Explicitly type all method arguments and return values.
- **Modern Features:** Use Constructor Property Promotion and Match expressions where appropriate.

### Framework-Specific Rules (Laravel 11)

- **Authorization:** NEVER skip authorization. Use Policies in Controllers (`$this->authorize(...)`).
- **Validation:** ALWAYS use `FormRequest` classes. NEVER validate in Controller methods.
- **Business Logic:** Complex logic (especially Formation/Canon Law) MUST go in `app/Services`.
- **Scoping:** ALWAYS apply `ScopeByHouse` or `where('house_id', ...)` for multi-tenancy.

### Testing Rules (Pest)

- **Syntax:** Use Pest PHP syntax (`test('description', function () { ... })`).
- **State:** Use `RefreshDatabase` trait.
- **Coverage:** Test happy path AND authorization failures (e.g., "User cannot see other house's members").

### Code Quality & Style Rules

- **Naming:** Controllers (`PascalCase`), DB (`snake_case`), Routes (`kebab-case`).
- **Blade:** Use Components (`<x-card>`) to avoid repetitive Tailwind classes.
- **Formatting:** Follow Laravel Pint (PSR-12) standards.

### Critical Don't-Miss Rules

- **Anti-Pattern:** No logic in Controllers ("Fat Controllers"). Delegate to Services or Models.
- **Anti-Pattern:** No `env()` calls outside config files. Use `config('app.name')`.
- **Security:** Do not expose internal IDs without scoping.
- **Security:** Ensure `storage/app/private` is used for sensitive documents.

---

## Usage Guidelines

**For AI Agents:**

- Read this file before implementing any code
- Follow ALL rules exactly as documented
- When in doubt, prefer the more restrictive option
- Update this file if new patterns emerge

**For Humans:**

- Keep this file lean and focused on agent needs
- Update when technology stack changes
- Review quarterly for outdated rules
- Remove rules that become obvious over time

Last Updated: 2025-12-01
