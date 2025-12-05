# RBAC System Implementation - Overview & Examples

## Tổng Quan Hệ Thống

### Kiến Trúc Hiện Tại (Story 1.4 - Đã Hoàn Thành)

Hệ thống RBAC đã được triển khai với kiến trúc **Hybrid Model** kết hợp:

- **Role-Based Access**: Phân quyền dựa trên vai trò (SUPER_ADMIN, GENERAL, DIRECTOR, MEMBER)
- **Permission-Based Access**: Quyền chi tiết cho từng hành động cụ thể

```
┌─────────────────────────────────────────────────────────────┐
│                    RBAC Architecture                         │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  ┌──────────┐      ┌──────────────┐      ┌──────────────┐  │
│  │  User    │──────│  UserRole    │──────│ Permissions  │  │
│  │          │      │   (Enum)     │      │   (Table)    │  │
│  │ - role   │      │              │      │              │  │
│  │ - comm_id│      │ SUPER_ADMIN  │      │ - key        │  │
│  └──────────┘      │ GENERAL      │      │ - name       │  │
│                    │ DIRECTOR     │      │ - module     │  │
│                    │ MEMBER       │      └──────────────┘  │
│                    └──────────────┘             │          │
│                           │                     │          │
│                           └─────────────────────┘          │
│                                   │                        │
│                          ┌────────▼────────┐               │
│                          │ role_permissions│               │
│                          │    (Pivot)      │               │
│                          │                 │               │
│                          │ - role          │               │
│                          │ - permission_id │               │
│                          └─────────────────┘               │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

### 4 Vai Trò Chính

| Role            | Mô Tả                               | Community Scope        | Ví Dụ User                        |
| --------------- | ----------------------------------- | ---------------------- | --------------------------------- |
| **SUPER_ADMIN** | Quản trị viên hệ thống, toàn quyền  | Tất cả communities     | IT Administrator                  |
| **GENERAL**     | Ban Tổng Quyền, quản lý toàn tu hội | Tất cả communities     | Mother General, General Secretary |
| **DIRECTOR**    | Bề Trên Địa Phương                  | Chỉ community được gán | Sr. Mary (Director of House A)    |
| **MEMBER**      | Tu sĩ thông thường                  | Chỉ community của mình | Sr. Teresa (Member of House A)    |

### Type-Safe Permission Keys (PermissionKey Enum)

Hiện tại đã có permissions cho 3 modules:

```php
// app/Enums/PermissionKey.php
enum PermissionKey: string {
    // Territories Module
    case TERRITORIES_VIEW = 'territories.view';
    case TERRITORIES_ASSIGN = 'territories.assign';
    case TERRITORIES_MANAGE = 'territories.manage';

    // Publishers Module
    case PUBLISHERS_VIEW = 'publishers.view';
    case PUBLISHERS_MANAGE = 'publishers.manage';

    // Reports Module
    case REPORTS_VIEW = 'reports.view';
    case REPORTS_EXPORT = 'reports.export';
}
```

**Cần bổ sung:** Members, Financials, Documents, Communities modules

## Ví Dụ Cụ Thể

### Ví Dụ 1: Community Director Xem Danh Sách Tu Sĩ

**Kịch Bản:**

- Sr. Mary là Director của House A (community_id = 1)
- Sr. Mary muốn xem danh sách tu sĩ trong cộng đoàn của mình

**Code Flow:**

```php
// 1. User đăng nhập
$user = Auth::user(); // Sr. Mary
// $user->role = UserRole::DIRECTOR
// $user->community_id = 1

// 2. Controller kiểm tra quyền
class MemberController extends Controller {
    public function index() {
        // Policy check
        $this->authorize('viewAny', Member::class);

        // Query tự động scope theo community
        $members = Member::all(); // Chỉ lấy members của House A

        return view('members.index', compact('members'));
    }
}

// 3. Policy logic
class MemberPolicy {
    public function before(User $user, string $ability): bool|null {
        // Super admin bypass
        if ($user->role === UserRole::SUPER_ADMIN) {
            return true;
        }
        return null;
    }

    public function viewAny(User $user): bool {
        // Check permission
        return $user->hasPermission(PermissionKey::MEMBERS_VIEW);
    }
}

// 4. Global Scope tự động filter
class Member extends Model {
    protected static function booted() {
        static::addGlobalScope('community', function (Builder $builder) {
            if (Auth::check() && Auth::user()->role === UserRole::DIRECTOR) {
                $builder->where('community_id', Auth::user()->community_id);
            }
        });
    }
}
```

**Kết Quả:**

- ✅ Sr. Mary chỉ thấy tu sĩ của House A
- ✅ Không thể thấy tu sĩ của House B, C, D
- ✅ Query tự động filter, không cần code thủ công

### Ví Dụ 2: General Secretary Xuất Báo Cáo Toàn Tu Hội

**Kịch Bản:**

- Sr. Anne là General Secretary (role = GENERAL)
- Sr. Anne muốn xuất báo cáo demographic cho toàn tu hội

**Code Flow:**

```php
// 1. User đăng nhập
$user = Auth::user(); // Sr. Anne
// $user->role = UserRole::GENERAL
// $user->community_id = null (không bị giới hạn)

// 2. Controller
class ReportController extends Controller {
    public function demographic() {
        // Policy check
        $this->authorize('export', Report::class);

        // Query KHÔNG bị scope (vì là GENERAL)
        $members = Member::all(); // Lấy TẤT CẢ members

        $pdf = $this->generateDemographicReport($members);
        return $pdf->download('demographic-report.pdf');
    }
}

// 3. Policy logic
class ReportPolicy {
    public function export(User $user): bool {
        // Check permission
        return $user->hasPermission(PermissionKey::REPORTS_EXPORT);
    }
}

// 4. Permission check
// User model
public function hasPermission(PermissionKey|string $permission): bool {
    // Super admin bypass
    if ($this->role === UserRole::SUPER_ADMIN) {
        return true;
    }

    $key = $permission instanceof PermissionKey
        ? $permission->value
        : $permission;

    // Query role_permissions
    return DB::table('role_permissions')
        ->join('permissions', 'permissions.id', '=', 'role_permissions.permission_id')
        ->where('role_permissions.role', $this->role->value)
        ->where('permissions.key', $key)
        ->exists();
}
```

**Kết Quả:**

- ✅ Sr. Anne thấy TẤT CẢ tu sĩ (không bị scope)
- ✅ Có quyền export báo cáo
- ✅ Tạo PDF thành công

### Ví Dụ 3: Super Admin Quản Lý Permissions

**Kịch Bản:**

- Admin muốn gán thêm quyền "financials.approve" cho role DIRECTOR

**Code Flow (Hiện Tại - Chưa Có UI):**

```php
// Sử dụng PermissionService
$permissionService = app(PermissionService::class);

// Lấy permissions hiện tại của DIRECTOR
$currentPermissions = $permissionService->getRolePermissions(UserRole::DIRECTOR);
// ['territories.view', 'territories.assign', 'publishers.view', 'publishers.manage']

// Thêm permission mới
$newPermissions = $currentPermissions->push('financials.approve');

// Cập nhật
$permissionService->assignPermissionsToRole(
    UserRole::DIRECTOR,
    $newPermissions->toArray()
);
```

**Code Flow (Mục Tiêu - Có UI):**

```php
// Route
Route::get('/admin/permissions', [PermissionManagementController::class, 'index'])
    ->middleware(['auth', 'can:view-admin']);

// Controller
class PermissionManagementController extends Controller {
    public function index() {
        $roles = UserRole::cases();
        $permissions = Permission::all()->groupBy('module');
        $rolePermissions = $this->getRolePermissionMatrix();

        return view('admin.permissions.index', compact(
            'roles', 'permissions', 'rolePermissions'
        ));
    }

    public function update(Request $request) {
        $role = UserRole::from($request->role);
        $permissions = $request->permissions; // Array of permission keys

        $this->permissionService->assignPermissionsToRole($role, $permissions);

        // Log audit trail
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'update_role_permissions',
            'target_role' => $role->value,
            'changes' => $permissions
        ]);

        return back()->with('success', 'Permissions updated successfully');
    }
}
```

**UI Mockup:**

```
┌─────────────────────────────────────────────────────────┐
│ Permission Management                                    │
├─────────────────────────────────────────────────────────┤
│                                                          │
│ Select Role: [DIRECTOR ▼]                               │
│                                                          │
│ ┌─ Members Module ──────────────────────────────────┐   │
│ │ ☑ members.view      View members                  │   │
│ │ ☑ members.create    Create new members            │   │
│ │ ☑ members.edit      Edit member information       │   │
│ │ ☐ members.delete    Delete members                │   │
│ │ ☐ members.export    Export member data            │   │
│ └───────────────────────────────────────────────────┘   │
│                                                          │
│ ┌─ Financials Module ───────────────────────────────┐   │
│ │ ☑ financials.view     View financial records      │   │
│ │ ☑ financials.create   Create expenses             │   │
│ │ ☑ financials.approve  Approve expenses            │   │
│ │ ☐ financials.export   Export financial reports    │   │
│ │ ☐ financials.manage   Manage all financials       │   │
│ └───────────────────────────────────────────────────┘   │
│                                                          │
│ [Save Changes]  [Cancel]                                │
│                                                          │
└─────────────────────────────────────────────────────────┘
```

## Những Gì Cần Hoàn Thiện

### 1. Complete Permission Keys

**Hiện Tại:** Chỉ có 3 modules (Territories, Publishers, Reports)

**Cần Thêm:**

```php
// Members Module
case MEMBERS_VIEW = 'members.view';
case MEMBERS_CREATE = 'members.create';
case MEMBERS_EDIT = 'members.edit';
case MEMBERS_DELETE = 'members.delete';
case MEMBERS_EXPORT = 'members.export';

// Financials Module
case FINANCIALS_VIEW = 'financials.view';
case FINANCIALS_CREATE = 'financials.create';
case FINANCIALS_APPROVE = 'financials.approve';
case FINANCIALS_EXPORT = 'financials.export';
case FINANCIALS_MANAGE = 'financials.manage';

// Documents Module
case DOCUMENTS_VIEW = 'documents.view';
case DOCUMENTS_UPLOAD = 'documents.upload';
case DOCUMENTS_DOWNLOAD = 'documents.download';
case DOCUMENTS_DELETE = 'documents.delete';
case DOCUMENTS_MANAGE = 'documents.manage';

// Communities Module
case COMMUNITIES_VIEW = 'communities.view';
case COMMUNITIES_CREATE = 'communities.create';
case COMMUNITIES_EDIT = 'communities.edit';
case COMMUNITIES_ASSIGN_MEMBERS = 'communities.assign_members';
```

### 2. Caching Layer

**Hiện Tại:** Mỗi lần check permission đều query database

**Cần Thêm:**

```php
public function hasPermission(PermissionKey|string $permission): bool {
    if ($this->role === UserRole::SUPER_ADMIN) {
        return true;
    }

    $cacheKey = "user_permissions_{$this->id}";

    // Cache 1 hour
    $permissions = Cache::remember($cacheKey, 3600, function () {
        return DB::table('role_permissions')
            ->join('permissions', 'permissions.id', '=', 'role_permissions.permission_id')
            ->where('role_permissions.role', $this->role->value)
            ->pluck('permissions.key')
            ->toArray();
    });

    $key = $permission instanceof PermissionKey
        ? $permission->value
        : $permission;

    return in_array($key, $permissions);
}

// Cache invalidation khi role thay đổi
protected static function booted() {
    static::updated(function (User $user) {
        if ($user->isDirty('role')) {
            Cache::forget("user_permissions_{$user->id}");
        }
    });
}
```

### 3. Permission Management UI

**Hiện Tại:** Chỉ có thể quản lý qua code/seeder

**Cần Thêm:**

- Controller: `PermissionManagementController`
- Views: `resources/views/admin/permissions/index.blade.php`
- Routes: `/admin/permissions`
- Middleware: `can:view-admin`

### 4. Auto-Discovery

**Hiện Tại:** Permissions phải được định nghĩa thủ công trong enum

**Cần Thêm:**

```php
// Artisan command
php artisan permissions:sync

// Scan routes và tự động tạo permissions
class SyncPermissionsCommand extends Command {
    public function handle() {
        $routes = Route::getRoutes();

        foreach ($routes as $route) {
            $middleware = $route->middleware();

            // Tìm permission middleware
            foreach ($middleware as $m) {
                if (str_starts_with($m, 'permission:')) {
                    $permissionKey = str_replace('permission:', '', $m);

                    // Tạo permission nếu chưa có
                    Permission::firstOrCreate([
                        'key' => $permissionKey
                    ], [
                        'name' => $this->generateName($permissionKey),
                        'module' => $this->extractModule($permissionKey)
                    ]);
                }
            }
        }
    }
}
```

### 5. Audit Logging

**Hiện Tại:** Không có audit trail

**Cần Thêm:**

```php
// Model
class AuditLog extends Model {
    protected $fillable = [
        'user_id',
        'action',
        'target_type',
        'target_id',
        'changes',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'changes' => 'array'
    ];
}

// Observer
class PermissionAuditObserver {
    public function updated(RolePermission $rolePermission) {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'permission_updated',
            'target_type' => 'role_permission',
            'target_id' => $rolePermission->id,
            'changes' => $rolePermission->getChanges(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }
}
```

## Permission Matrix (Mục Tiêu Hoàn Chỉnh)

| Module          | Permission     | SUPER_ADMIN | GENERAL | DIRECTOR          | MEMBER            |
| --------------- | -------------- | ----------- | ------- | ----------------- | ----------------- |
| **Members**     | view           | ✓ (all)     | ✓ (all) | ✓ (own community) | ✓ (own community) |
|                 | create         | ✓           | ✓       | ✓                 | ✗                 |
|                 | edit           | ✓           | ✓       | ✓                 | ✗                 |
|                 | delete         | ✓           | ✓       | ✗                 | ✗                 |
|                 | export         | ✓           | ✓       | ✗                 | ✗                 |
| **Financials**  | view           | ✓ (all)     | ✓ (all) | ✓ (own community) | ✗                 |
|                 | create         | ✓           | ✓       | ✓                 | ✗                 |
|                 | approve        | ✓           | ✓       | ✓                 | ✗                 |
|                 | export         | ✓           | ✓       | ✗                 | ✗                 |
|                 | manage         | ✓           | ✓       | ✗                 | ✗                 |
| **Documents**   | view           | ✓ (all)     | ✓ (all) | ✓ (own community) | ✓ (own community) |
|                 | upload         | ✓           | ✓       | ✓                 | ✗                 |
|                 | download       | ✓           | ✓       | ✓                 | ✓                 |
|                 | delete         | ✓           | ✓       | ✓                 | ✗                 |
|                 | manage         | ✓           | ✓       | ✗                 | ✗                 |
| **Communities** | view           | ✓           | ✓       | ✓ (own only)      | ✓ (own only)      |
|                 | create         | ✓           | ✓       | ✗                 | ✗                 |
|                 | edit           | ✓           | ✓       | ✓ (own only)      | ✗                 |
|                 | assign_members | ✓           | ✓       | ✓ (own only)      | ✗                 |
| **Reports**     | view           | ✓           | ✓       | ✓                 | ✗                 |
|                 | generate       | ✓           | ✓       | ✓                 | ✗                 |
|                 | export         | ✓           | ✓       | ✗                 | ✗                 |
|                 | schedule       | ✓           | ✓       | ✗                 | ✗                 |

## Tóm Tắt

**Đã Có (Story 1.4):**

- ✅ Type-safe enums (UserRole, PermissionKey)
- ✅ 3-table schema (users, permissions, role_permissions)
- ✅ Basic permission checking
- ✅ Super admin bypass
- ✅ Community scoping với Global Scopes
- ✅ Basic seeding
- ✅ 95% test coverage

**Cần Hoàn Thiện (Spec Này):**

- 🔲 Complete permission keys cho tất cả modules
- 🔲 Production-ready caching với invalidation
- 🔲 Permission Management UI
- 🔲 Auto-discovery từ routes
- 🔲 Comprehensive audit logging
- 🔲 Integration với tất cả policies
- 🔲 Authorization middleware improvements
- 🔲 Performance monitoring

**Mục Tiêu:**
Biến RBAC system từ "foundation" thành "production-ready" với đầy đủ tính năng quản lý, monitoring, và audit trail.
