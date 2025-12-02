RBAC Sequence Diagrams
Here are Mermaid sequence diagrams illustrating your Role-Based Access Control system:

1. Admin Access Authorization Flow
   This shows how a Super Admin accesses protected admin routes:

````mermaid
sequenceDiagram
    actor Admin as Super Admin
    participant Route as Admin Route
    participant Middleware as Auth Middleware
    participant Gate as view-admin Gate
    participant User as User Model
    participant Policy as UserPolicy
    participant Controller as Admin Controller

    Admin->>Route: GET /admin/dashboard
    Route->>Middleware: Check authentication
    Middleware->>User: Verify session
    User-->>Middleware: Authenticated ✓
    Middleware->>Gate: Check 'view-admin'
    Gate->>User: hasRole(SUPER_ADMIN)?
    User-->>Gate: role = SUPER_ADMIN ✓
    Gate-->>Middleware: Authorized ✓
    Middleware->>Controller: Forward request
    Controller->>Policy: before() check
    Policy->>User: isSuperAdmin()?
    User-->>Policy: true ✓
    Policy-->>Controller: Grant all access
    Controller-->>Admin: Return admin dashboard
```
2. Director Restricted Access Flow
This shows how a Director is blocked from admin routes:
```mermaid
sequenceDiagram
    actor Director as Director
    participant Route as Admin Route
    participant Middleware as Auth Middleware
    participant Gate as view-admin Gate
    participant User as User Model

    Director->>Route: GET /admin/users
    Route->>Middleware: Check authentication
    Middleware->>User: Verify session
    User-->>Middleware: Authenticated ✓
    Middleware->>Gate: Check 'view-admin'
    Gate->>User: hasRole(SUPER_ADMIN)?
    User-->>Gate: role = DIRECTOR ✗
    Gate->>User: hasRole(GENERAL)?
    User-->>Gate: role = DIRECTOR ✗
    Gate-->>Middleware: Unauthorized ✗
    Middleware-->>Director: 403 Forbidden
    ```
3. User Policy Authorization Flow
This shows policy-based authorization for user management actions:
```mermaid
sequenceDiagram
    actor Admin as Super Admin
    actor Director as Director
    participant Controller as UserController
    participant Policy as UserPolicy
    participant User as User Model
    participant DB as Database

    Note over Admin,DB: Scenario 1: Super Admin creates user
    Admin->>Controller: POST /users (create)
    Controller->>Policy: authorize('create', User)
    Policy->>User: Check before() method
    User-->>Policy: isSuperAdmin() = true
    Policy-->>Controller: Authorized ✓
    Controller->>DB: Create new user
    DB-->>Admin: User created successfully

    Note over Director,DB: Scenario 2: Director attempts to create user
    Director->>Controller: POST /users (create)
    Controller->>Policy: authorize('create', User)
    Policy->>User: Check before() method
    User-->>Policy: isSuperAdmin() = false
    Policy->>Policy: Check create() method
    Policy-->>Controller: Unauthorized ✗
    Controller-->>Director: 403 Forbidden
    ```
4. Community-Scoped Access Flow
This shows how Directors access their community-scoped data:
```mermaid
sequenceDiagram
    actor Director as Director
    participant Route as Community Route
    participant Middleware as Auth Middleware
    participant User as User Model
    participant Community as Community Model
    participant DB as Database

    Director->>Route: GET /communities/{id}/members
    Route->>Middleware: Check authentication
    Middleware->>User: Verify session
    User-->>Middleware: Authenticated ✓
    Middleware->>User: Load community relationship
    User->>DB: Get community_id
    DB-->>User: community_id = 5
    User-->>Middleware: User belongs to Community 5
    Middleware->>Route: Check route parameter
    Route->>Route: Requested community_id = 5?
    alt Community ID matches
        Route->>Community: Load community data
        Community->>DB: WHERE id = 5
        DB-->>Community: Community data
        Community-->>Director: Return community members
    else Community ID mismatch
        Route-->>Director: 403 Forbidden (wrong community)
    end
```
5. Role Assignment and Seeding Flow
This shows the database seeding process with role assignment:
```mermaid
sequenceDiagram
    participant Seeder as DatabaseSeeder
    participant UserRole as UserRole Enum
    participant User as User Model
    participant Community as Community Model
    participant DB as Database

    Seeder->>Community: Create test communities
    Community->>DB: INSERT communities
    DB-->>Community: Community IDs [1, 2, 3]

    Seeder->>UserRole: Get SUPER_ADMIN constant
    UserRole-->>Seeder: 'super_admin'
    Seeder->>User: Create admin user
    User->>DB: INSERT (role='super_admin', community_id=null)
    DB-->>Seeder: Admin created ✓

    Seeder->>UserRole: Get GENERAL constant
    UserRole-->>Seeder: 'general'
    Seeder->>User: Create general user
    User->>DB: INSERT (role='general', community_id=null)
    DB-->>Seeder: General created ✓

    Seeder->>UserRole: Get DIRECTOR constant
    UserRole-->>Seeder: 'director'
    Seeder->>User: Create director user
    User->>DB: INSERT (role='director', community_id=1)
    DB-->>Seeder: Director created ✓

    Seeder->>UserRole: Get MEMBER constant
    UserRole-->>Seeder: 'member'
    Seeder->>User: Create member user
    User->>DB: INSERT (role='member', community_id=1)
    DB-->>Seeder: Member created ✓
````
