<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', \App\Livewire\Dashboard::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/role', [ProfileController::class, 'updateRole'])->name('profile.role.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('members', \App\Http\Controllers\MemberController::class);
    Route::post('/members/{member}/formation', [\App\Http\Controllers\FormationController::class, 'store'])->name('members.formation.store');

    // Formation Documents
    Route::post('/formation-events/{event}/documents', [\App\Http\Controllers\FormationDocumentController::class, 'store'])->name('formation.documents.store');
    Route::get('/formation-documents/{document}/download', [\App\Http\Controllers\FormationDocumentController::class, 'download'])->name('formation.documents.download');
    Route::delete('/formation-documents/{document}', [\App\Http\Controllers\FormationDocumentController::class, 'destroy'])->name('formation.documents.destroy');

    // Member Transfer
    Route::post('/members/{member}/transfer', [\App\Http\Controllers\MemberTransferController::class, 'store'])->name('members.transfer');

    // Member Photos
    Route::put('/members/{member}/photo', [\App\Http\Controllers\MemberPhotoController::class, 'update'])->name('members.photo.update');
    Route::delete('/members/{member}/photo', [\App\Http\Controllers\MemberPhotoController::class, 'destroy'])->name('members.photo.destroy');

    // Health Records
    Route::post('/members/{member}/health', [\App\Http\Controllers\HealthRecordController::class, 'store'])->name('members.health.store');
    Route::delete('/health-records/{healthRecord}', [\App\Http\Controllers\HealthRecordController::class, 'destroy'])->name('health-records.destroy');

    // Skills
    Route::post('/members/{member}/skills', [\App\Http\Controllers\SkillController::class, 'store'])->name('members.skills.store');
    Route::delete('/skills/{skill}', [\App\Http\Controllers\SkillController::class, 'destroy'])->name('skills.destroy');

    // Service History
    Route::post('/members/{member}/assignments', [\App\Http\Controllers\ServiceHistoryController::class, 'store'])->name('members.assignments.store');
    Route::delete('/assignments/{assignment}', [\App\Http\Controllers\ServiceHistoryController::class, 'destroy'])->name('assignments.destroy');

    // Audit Logs
    Route::get('/audit-logs', [\App\Http\Controllers\AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('/audit-logs/export', [\App\Http\Controllers\AuditLogController::class, 'export'])->name('audit-logs.export');

    // API: Real-time Validation
    Route::post('/api/validate', [\App\Http\Controllers\Api\ValidationController::class, 'validateField'])->name('api.validate');

    // Financials
    Route::get('/financials/dashboard', \App\Livewire\FinancialDashboard::class)->name('financials.dashboard');
    Route::resource('financials', \App\Http\Controllers\FinancialController::class)->parameters([
        'financials' => 'expense',
    ]);
    Route::get('/financials-lock-period', [\App\Http\Controllers\FinancialController::class, 'lockPeriodForm'])->name('financials.lock-period.form');
    Route::post('/financials-lock-period', [\App\Http\Controllers\FinancialController::class, 'lockPeriod'])->name('financials.lock-period');
    Route::get('/financials-monthly-report', [\App\Http\Controllers\FinancialController::class, 'monthlyReport'])->name('financials.monthly-report');
    Route::get('/financials-export-report', [\App\Http\Controllers\FinancialController::class, 'exportMonthlyReport'])->name('financials.export-report');

    // Documents
    Route::resource('documents', \App\Http\Controllers\DocumentController::class);
    Route::get('/documents/{document}/download', [\App\Http\Controllers\DocumentController::class, 'download'])->name('documents.download');

    // Communities
    Route::resource('communities', \App\Http\Controllers\CommunityController::class);

    // Projects
    Route::resource('projects', \App\Http\Controllers\ProjectController::class);
    Route::post('projects/{project}/members', [\App\Http\Controllers\ProjectMemberController::class, 'store'])->name('projects.members.store');
    Route::put('projects/{project}/members/{member}', [\App\Http\Controllers\ProjectMemberController::class, 'update'])->name('projects.members.update');
    Route::delete('projects/{project}/members/{member}', [\App\Http\Controllers\ProjectMemberController::class, 'destroy'])->name('projects.members.destroy');
    
    Route::get('projects/{project}/tasks/create', [\App\Http\Controllers\TaskController::class, 'create'])->name('projects.tasks.create');
    Route::get('projects/{project}/tasks/timeline', [\App\Http\Controllers\TaskController::class, 'timeline'])->name('projects.tasks.timeline');
    Route::post('projects/{project}/tasks', [\App\Http\Controllers\TaskController::class, 'store'])->name('projects.tasks.store');
    Route::get('projects/{project}/tasks/{task}/edit', [\App\Http\Controllers\TaskController::class, 'edit'])->name('projects.tasks.edit');
    Route::put('projects/{project}/tasks/{task}', [\App\Http\Controllers\TaskController::class, 'update'])->name('projects.tasks.update');
    Route::get('projects/{project}/tasks/create', [\App\Http\Controllers\TaskController::class, 'create'])->name('projects.tasks.create');
    Route::post('projects/{project}/tasks', [\App\Http\Controllers\TaskController::class, 'store'])->name('projects.tasks.store');
    Route::get('projects/{project}/tasks/{task}/edit', [\App\Http\Controllers\TaskController::class, 'edit'])->name('projects.tasks.edit');
    Route::put('projects/{project}/tasks/{task}', [\App\Http\Controllers\TaskController::class, 'update'])->name('projects.tasks.update');
    Route::delete('projects/{project}/tasks/{task}', [\App\Http\Controllers\TaskController::class, 'destroy'])->name('projects.tasks.destroy');
    
    // Task status update for drag-and-drop
    Route::patch('projects/{project}/tasks/{task}/status', [\App\Http\Controllers\ProjectController::class, 'updateTaskStatus'])->name('projects.tasks.updateStatus');
    Route::patch('projects/{project}/tasks/{task}/priority', [\App\Http\Controllers\ProjectController::class, 'updateTaskPriority'])->name('projects.tasks.updatePriority');
    
    // Timeline Gantt chart endpoints
    Route::patch('projects/{project}/tasks/{task}/dates', [\App\Http\Controllers\ProjectController::class, 'updateTaskDates'])->name('projects.tasks.updateDates');
    Route::post('projects/{project}/tasks/quick-create', [\App\Http\Controllers\ProjectController::class, 'quickCreateTask'])->name('projects.tasks.quickCreate');

    // My Tasks
    Route::get('projects/ai/create', [\App\Http\Controllers\AIProjectController::class, 'create'])->name('projects.ai.create');
    Route::post('projects/ai/generate', [\App\Http\Controllers\AIProjectController::class, 'generate'])->name('projects.ai.generate');
    Route::post('projects/ai/store', [\App\Http\Controllers\AIProjectController::class, 'store'])->name('projects.ai.store');

    Route::get('my-tasks', [\App\Http\Controllers\MyTaskController::class, 'index'])->name('my-tasks.index');

    // Periodic Events
    Route::resource('periodic-events', \App\Http\Controllers\PeriodicEventController::class);

    // Permission Management (Super Admin & General only)
    Route::middleware('can:view-admin')->prefix('admin')->name('admin.')->group(function () {
        // User Management
        Route::get('/users', [\App\Http\Controllers\Admin\UserManagementController::class, 'index'])->name('users.index');
        Route::patch('/users/{user}/role', [\App\Http\Controllers\Admin\UserManagementController::class, 'updateRole'])->name('users.updateRole');

        // Permission Management
        Route::get('/permissions', [\App\Http\Controllers\Admin\PermissionManagementController::class, 'index'])->name('permissions.index');
        Route::post('/permissions', [\App\Http\Controllers\Admin\PermissionManagementController::class, 'store'])->name('permissions.store');
        Route::post('/permissions/update', [\App\Http\Controllers\Admin\PermissionManagementController::class, 'update'])->name('permissions.update');
        Route::post('/permissions/sync', [\App\Http\Controllers\Admin\PermissionManagementController::class, 'sync'])->name('permissions.sync');
        Route::get('/permissions/audit', [\App\Http\Controllers\Admin\PermissionManagementController::class, 'audit'])->name('permissions.audit');

        // Role Management
        Route::post('/roles', [\App\Http\Controllers\Admin\RoleManagementController::class, 'store'])->name('roles.store');
        Route::delete('/roles/{role}', [\App\Http\Controllers\Admin\RoleManagementController::class, 'destroy'])->name('roles.destroy');

        // System Settings
        Route::get('/settings', [\App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings', [\App\Http\Controllers\SettingsController::class, 'update'])->name('settings.update');
        Route::post('/settings/test-email', [\App\Http\Controllers\SettingsController::class, 'testEmail'])->name('settings.test-email');
        
        // Footer Settings
        Route::get('/settings/footer', [\App\Http\Controllers\SettingsController::class, 'footerEdit'])->name('settings.footer.edit');
        Route::put('/settings/footer', [\App\Http\Controllers\SettingsController::class, 'footerUpdate'])->name('settings.footer.update');
        
        // Backups
        Route::get('/backups', [\App\Http\Controllers\BackupController::class, 'index'])->name('backups.index');
        Route::post('/backups', [\App\Http\Controllers\BackupController::class, 'create'])->name('backups.create');
        Route::get('/backups/{filename}', [\App\Http\Controllers\BackupController::class, 'download'])->name('backups.download');
    });
    // Reports
    Route::get('/reports/demographic', [\App\Http\Controllers\ReportController::class, 'demographic'])->name('reports.demographic');
    Route::get('/reports/advanced', [\App\Http\Controllers\ReportController::class, 'advanced'])->name('reports.advanced');
    Route::get('/reports/community-annual', [\App\Http\Controllers\ReportController::class, 'communityAnnualMembers'])->name('reports.community-annual');
    Route::get('/reports/export/demographic', [\App\Http\Controllers\ReportController::class, 'exportDemographic'])->name('reports.demographic.export');

    // Celebrations
    Route::get('/celebrations', [\App\Http\Controllers\CelebrationController::class, 'index'])->name('celebrations.index');
    Route::get('/celebrations/birthday/{member}/generate', [\App\Http\Controllers\CelebrationController::class, 'generateBirthday'])->name('celebrations.birthday.generate');
    Route::get('/celebrations/birthday/{member}/download', [\App\Http\Controllers\CelebrationController::class, 'downloadBirthday'])->name('celebrations.birthday.download');
    Route::post('/celebrations/birthday/{member}/email', [\App\Http\Controllers\CelebrationController::class, 'emailBirthday'])->name('celebrations.birthday.email');

    // Directory Reports
    Route::prefix('reports/directory')->name('reports.directory.')->group(function () {
        Route::get('/communion/{format}', [\App\Http\Controllers\DirectoryReportController::class, 'communion'])
            ->where('format', 'pdf|docx')
            ->name('communion');
        Route::get('/index/{format}', [\App\Http\Controllers\DirectoryReportController::class, 'index'])
            ->where('format', 'pdf|excel')
            ->name('index');
        Route::get('/birthdays/{format}', [\App\Http\Controllers\DirectoryReportController::class, 'birthdays'])
            ->where('format', 'pdf|excel')
            ->name('birthdays');
        Route::get('/deceased/{format}', [\App\Http\Controllers\DirectoryReportController::class, 'deceased'])
            ->where('format', 'pdf|docx')
            ->name('deceased');
        Route::get('/community/{community}/{format}', [\App\Http\Controllers\DirectoryReportController::class, 'community'])
            ->where('format', 'pdf|docx')
            ->name('community');
        
        // Complete Directory Export (Single File)
        Route::get('/complete/pdf', [\App\Http\Controllers\CompleteDirectoryExportController::class, 'exportPdf'])
            ->name('complete.pdf');
        Route::get('/complete/docx', [\App\Http\Controllers\CompleteDirectoryExportController::class, 'exportDocx'])
            ->name('complete.docx');
    });

    // UI/UX Optimization Components
    Route::get('/reports/builder', \App\Livewire\Reports\ReportBuilder::class)->name('reports.builder');
    Route::get('/notifications', \App\Livewire\Notifications\NotificationCenter::class)->name('notifications.index');
    
    Route::middleware('can:view-admin')->prefix('admin')->name('admin.')->group(function () {
    
    });
});

require __DIR__.'/auth.php';
