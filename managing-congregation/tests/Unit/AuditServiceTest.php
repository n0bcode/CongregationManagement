<?php

namespace Tests\Unit;

use App\Models\AuditLog;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AuditService $auditService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->auditService = new AuditService();
    }

    public function test_log_creates_audit_entry()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $model = User::factory()->create();
        
        $log = $this->auditService->log(
            $model,
            'updated',
            ['name' => 'Old Name'],
            ['name' => 'New Name']
        );

        $this->assertInstanceOf(AuditLog::class, $log);
        $this->assertEquals($user->id, $log->user_id);
        $this->assertEquals('updated', $log->action);
        $this->assertEquals(get_class($model), $log->auditable_type);
        $this->assertEquals($model->id, $log->auditable_id);
        $this->assertEquals(['name' => 'Old Name'], $log->old_values);
        $this->assertEquals(['name' => 'New Name'], $log->new_values);
    }

    public function test_generate_tamper_evident_report()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create some logs
        $model = User::factory()->create();
        $this->auditService->log($model, 'created');
        $this->auditService->log($model, 'updated');

        $report = $this->auditService->generateTamperEvidentReport();

        $this->assertArrayHasKey('entries', $report);
        $this->assertArrayHasKey('report_checksum', $report);
        $this->assertCount(2, $report['entries']);
        
        foreach ($report['entries'] as $entry) {
            $this->assertArrayHasKey('log', $entry);
            $this->assertArrayHasKey('checksum', $entry);
        }
    }

    public function test_verify_report_integrity()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create some logs
        $model = User::factory()->create();
        $this->auditService->log($model, 'created');

        $report = $this->auditService->generateTamperEvidentReport();

        $isValid = $this->auditService->verifyReportIntegrity($report);
        $this->assertTrue($isValid);

        // Tamper with the report
        $entries = $report['entries']->toArray();
        $entries[0]['checksum'] = 'tampered_checksum';
        $report['entries'] = $entries;
        
        $isValid = $this->auditService->verifyReportIntegrity($report);
        $this->assertFalse($isValid);
    }
}
