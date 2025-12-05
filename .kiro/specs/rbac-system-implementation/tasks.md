# Implementation Plan

- [x] 1. Extend Permission Keys for All Modules

  - Update `app/Enums/PermissionKey.php` to include all module permissions
  - Add Members module permissions (view, create, edit, delete, export)
  - Add Financials module permissions (view, create, approve, export, manage)
  - Add Documents module permissions (view, upload, download, delete, manage)
  - Add Communities module permissions (view, create, edit, assign_members)
  - Add Reports module permissions (view, generate, export, schedule)
  - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5_

- [x] 1.1 Write unit tests for PermissionKey enum completeness

  - **Property 1: All required module permissions exist**
  - **Validates: Requirements 1.1, 1.2, 1.3, 1.4, 1.5**

- [x] 2. Update Permission Seeder with New Permissions

  - Update `database/seeders/PermissionSeeder.php` to seed all new permissions
  - Update default role-permission assignments for all modules
  - Ensure seeder remains idempotent
  - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5_

- [x] 2.1 Write tests for permission seeder completeness

  - Test that all enum permissions are seeded
  - Test that default role assignments are correct
  - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5_

- [x] 3. Implement Cache Manager Service

  - Create `app/Services/CacheManager.php` with CacheManagerInterface
  - Implement getUserPermissions() method
  - Implement cacheUserPermissions() method with 1-hour TTL
  - Implement invalidateUserCache() method
  - Implement invalidateRoleCache() method
  - Add cache hit/miss metrics tracking
  - _Requirements: 2.1, 2.2, 2.3, 2.5_

- [ ]\* 3.1 Write property test for cache consistency

  - **Property 1: Permission cache consistency**
  - **Validates: Requirements 2.1**

- [ ]\* 3.2 Write property test for cache invalidation on role change

  - **Property 2: Cache invalidation on role change**
  - **Validates: Requirements 2.2**

- [ ]\* 3.3 Write property test for bulk cache invalidation

  - **Property 3: Bulk cache invalidation on role permission update**
  - **Validates: Requirements 2.3**

- [x] 4. Update User Model with Caching

  - Update `app/Models/User.php` hasPermission() method to use CacheManager
  - Add cache invalidation in booted() method when role changes
  - Add graceful fallback to database on cache errors
  - _Requirements: 2.1, 2.2_

- [ ]\* 4.1 Write unit tests for User::hasPermission with caching

  - Test cache hit scenario
  - Test cache miss scenario
  - Test cache fallback on error
  - _Requirements: 2.1_

- [x] 5. Checkpoint - Ensure all tests pass

  - Ensure all tests pass, ask the user if questions arise.

- [x] 6. Create Audit Logger Service

  - Create `app/Services/AuditLogger.php` with AuditLoggerInterface
  - Implement logPermissionChange() method
  - Implement logRoleChange() method
  - Implement getRoleAuditHistory() method
  - Implement getUserAuditHistory() method
  - _Requirements: 8.1, 8.2, 8.3, 8.5_

- [x] 7. Create AuditLog Model and Migration

  - Create migration for `audit_logs` table
  - Add columns: user_id, action, target_type, target_id, changes, ip_address, user_agent, timestamps
  - Create `app/Models/AuditLog.php` model
  - Add relationships and scopes
  - _Requirements: 8.1, 8.2, 8.3, 8.4_

- [ ]\* 7.1 Write property test for audit trail completeness

  - **Property 12: Audit trail completeness**
  - **Validates: Requirements 6.5, 8.1, 8.2, 8.3**

- [x] 8. Update Permission Service with Audit Logging

  - Update `app/Services/PermissionService.php` to inject AuditLogger
  - Add audit logging to assignPermissionsToRole() method
  - Add cache invalidation calls
  - Add error handling and logging
  - _Requirements: 5.2, 6.5, 8.1_

- [ ]\* 8.1 Write property test for permission assignment idempotence

  - **Property 8: Permission assignment idempotence**
  - **Validates: Requirements 5.2**

- [ ]\* 8.2 Write property test for permission revocation completeness

  - **Property 9: Permission revocation completeness**
  - **Validates: Requirements 5.3**

- [ ]\* 8.3 Write property test for invalid permission rejection

  - **Property 10: Invalid permission rejection**
  - **Validates: Requirements 5.5**

- [ ]\* 8.4 Write property test for permission update atomicity

  - **Property 11: Permission update atomicity**
  - **Validates: Requirements 6.3**

- [x] 9. Create Permission Management Controller

  - Create `app/Http/Controllers/Admin/PermissionManagementController.php`
  - Implement index() method to display permission matrix
  - Implement update() method to handle permission changes
  - Implement sync() method to trigger route scanning
  - Implement audit() method to display audit logs
  - Add request validation
  - _Requirements: 6.1, 6.2, 6.3, 6.5_

- [x] 10. Create Permission Management Views

  - Create `resources/views/admin/permissions/index.blade.php`
  - Build permission matrix UI with role selector
  - Add permission checkboxes grouped by module
  - Implement AJAX for real-time updates
  - Add loading states and success messages
  - Create `resources/views/admin/permissions/audit.blade.php` for audit logs
  - _Requirements: 6.1, 6.2, 6.4_

- [x] 11. Add Permission Management Routes

  - Add routes to `routes/web.php` with auth and can:view-admin middleware
  - Route for index page
  - Route for update action
  - Route for sync command
  - Route for audit log viewer
  - _Requirements: 6.1, 6.3_

- [ ]\* 11.1 Write integration test for permission management workflow

  - Test complete workflow: view → update → verify → audit
  - Test authorization (only super admin can access)
  - _Requirements: 6.1, 6.2, 6.3, 6.5_

- [x] 12. Checkpoint - Ensure all tests pass

  - Ensure all tests pass, ask the user if questions arise.

- [x] 13. Create Route Scanner Service

  - Create `app/Services/RouteScanner.php` with RouteScannerInterface
  - Implement scanRoutes() method to extract permissions from routes
  - Implement extractPermissionFromMiddleware() method
  - Implement generatePermissionMetadata() method
  - Add module extraction logic
  - _Requirements: 7.1, 7.2_

- [x] 14. Create Permission Sync Command

  - Create `app/Console/Commands/SyncPermissions.php` artisan command
  - Implement handle() method using RouteScanner
  - Add logic to create new permissions
  - Add logic to mark removed permissions as inactive
  - Generate sync report with statistics
  - _Requirements: 7.1, 7.2, 7.3, 7.4, 7.5_

- [ ]\* 14.1 Write property test for route permission discovery

  - **Property 13: Route permission discovery**
  - **Validates: Requirements 7.1, 7.2**

- [ ]\* 14.2 Write property test for soft delete on removed permissions

  - **Property 14: Soft delete for removed permissions**
  - **Validates: Requirements 7.3**

- [x] 15. Create CheckPermission Middleware

  - Create `app/Http/Middleware/CheckPermission.php` middleware
  - Implement handle() method to check permissions
  - Return 403 Forbidden for unauthorized users
  - Redirect to login for unauthenticated users
  - Support multiple permission requirements
  - _Requirements: 9.1, 9.2, 9.3, 9.4_

- [ ]\* 15.1 Write property test for authorization failure response

  - **Property 15: Authorization failure response**
  - **Validates: Requirements 9.2**

- [ ]\* 15.2 Write property test for unauthenticated redirect

  - **Property 16: Unauthenticated redirect**
  - **Validates: Requirements 9.3**

- [x] 16. Update All Application Policies

  - Update `app/Policies/MemberPolicy.php` to use RBAC permissions
  - Update `app/Policies/FinancialPolicy.php` to use RBAC permissions
  - Update `app/Policies/DocumentPolicy.php` to use RBAC permissions
  - Update `app/Policies/CommunityPolicy.php` to use RBAC permissions
  - Ensure all policies check permissions via User::hasPermission()
  - Maintain super admin bypass in before() methods
  - _Requirements: 11.1, 11.2, 11.3, 11.4_

- [ ]\* 16.1 Write property test for community scoping

  - **Property 4: Community scoping for Directors**
  - **Validates: Requirements 3.1, 3.2**

- [ ]\* 16.2 Write property test for no scoping for elevated roles

  - **Property 5: No community scoping for elevated roles**
  - **Validates: Requirements 3.3**

- [ ]\* 16.3 Write property test for super admin universal access

  - **Property 6: Super admin universal access**
  - **Validates: Requirements 4.2**

- [ ]\* 16.4 Write property test for permission-based authorization

  - **Property 7: Permission-based authorization**
  - **Validates: Requirements 4.3**

- [ ]\* 16.5 Write property test for policy integration consistency

  - **Property 17: Policy integration consistency**
  - **Validates: Requirements 11.1, 11.2, 11.3, 11.4**

- [x] 17. Create Base Policy Class

  - Create `app/Policies/BasePolicy.php` with common authorization patterns
  - Implement before() method with super admin bypass
  - Add helper methods for permission checking
  - Add helper methods for community scoping
  - Update all policies to extend BasePolicy
  - _Requirements: 11.5_

- [x] 18. Add Error Handling and Logging

  - Create custom exception classes (PermissionNotFoundException, CacheException, etc.)
  - Add try-catch blocks in all service methods
  - Implement graceful degradation for cache failures
  - Add comprehensive logging at appropriate levels
  - Add error monitoring integration points
  - _Requirements: Error Handling section_

- [x] 19. Performance Optimization

  - Add database indexes for permission queries
  - Implement eager loading to prevent N+1 queries
  - Configure Redis cache driver for production
  - Add query result caching where appropriate
  - Run performance benchmarks and optimize bottlenecks
  - _Requirements: Performance Testing section_

- [ ]\* 19.1 Write performance tests

  - Test permission check speed (cached < 1ms)
  - Test permission check speed (uncached < 10ms)
  - Test cache invalidation speed (< 100ms for 50 users)
  - _Requirements: Performance Testing section_

- [x] 20. Final Checkpoint - Ensure all tests pass

  - Ensure all tests pass, ask the user if questions arise.

- [x] 21. Documentation and Deployment
  - Update permission matrix documentation
  - Create admin user guide for permission management
  - Update deployment checklist
  - Add monitoring dashboard for cache metrics
  - Prepare training materials
  - _Requirements: Implementation Notes section_
