<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
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

    // Financials
    Route::resource('financials', \App\Http\Controllers\FinancialController::class)->parameters([
        'financials' => 'expense'
    ]);
    Route::get('/financials-lock-period', [\App\Http\Controllers\FinancialController::class, 'lockPeriodForm'])->name('financials.lock-period.form');
    Route::post('/financials-lock-period', [\App\Http\Controllers\FinancialController::class, 'lockPeriod'])->name('financials.lock-period');
    Route::get('/financials-monthly-report', [\App\Http\Controllers\FinancialController::class, 'monthlyReport'])->name('financials.monthly-report');
    Route::get('/financials-export-report', [\App\Http\Controllers\FinancialController::class, 'exportMonthlyReport'])->name('financials.export-report');

    // Documents
    Route::resource('documents', \App\Http\Controllers\DocumentController::class);
    Route::get('/documents/{document}/download', [\App\Http\Controllers\DocumentController::class, 'download'])->name('documents.download');

    // Permission Management (Super Admin & General only)
    Route::middleware('can:view-admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/permissions', [\App\Http\Controllers\Admin\PermissionManagementController::class, 'index'])->name('permissions.index');
        Route::post('/permissions/update', [\App\Http\Controllers\Admin\PermissionManagementController::class, 'update'])->name('permissions.update');
        Route::post('/permissions/sync', [\App\Http\Controllers\Admin\PermissionManagementController::class, 'sync'])->name('permissions.sync');
        Route::get('/permissions/audit', [\App\Http\Controllers\Admin\PermissionManagementController::class, 'audit'])->name('permissions.audit');
    });
});

require __DIR__.'/auth.php';

    // Reports
    Route::get('/reports/demographic', [\App\Http\Controllers\ReportController::class, 'demographic'])->name('reports.demographic');
    Route::get('/reports/demographic/export', [\App\Http\Controllers\ReportController::class, 'exportDemographic'])->name('reports.demographic.export');

    // Celebrations
    Route::get('/celebrations', [\App\Http\Controllers\CelebrationController::class, 'index'])->name('celebrations.index');
    Route::get('/celebrations/birthday/{member}/generate', [\App\Http\Controllers\CelebrationController::class, 'generateBirthday'])->name('celebrations.birthday.generate');
    Route::get('/celebrations/birthday/{member}/download', [\App\Http\Controllers\CelebrationController::class, 'downloadBirthday'])->name('celebrations.birthday.download');
