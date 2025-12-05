<?php

namespace Tests\Feature;

use App\Models\Community;
use App\Models\Member;
use App\Models\User;
use App\Services\PdfService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_demographic_report_accessible_by_authorized_users()
    {
        $authorizedRoles = [
            \App\Enums\UserRole::SUPER_ADMIN,
            \App\Enums\UserRole::GENERAL,
            \App\Enums\UserRole::DIRECTOR,
        ];

        foreach ($authorizedRoles as $role) {
            $user = User::factory()->create(['role' => $role]);

            $response = $this->actingAs($user)->get(route('reports.demographic'));

            $response->assertStatus(200);
            $response->assertViewIs('reports.demographic');
        }
    }

    public function test_demographic_report_forbidden_for_unauthorized_users()
    {
        $user = User::factory()->create(['role' => \App\Enums\UserRole::MEMBER]);

        $response = $this->actingAs($user)->get(route('reports.demographic'));

        $response->assertStatus(403);
    }

    public function test_demographic_report_filters_data()
    {
        $user = User::factory()->create(['role' => \App\Enums\UserRole::SUPER_ADMIN]);
        $community1 = Community::factory()->create(['name' => 'Community A']);
        $community2 = Community::factory()->create(['name' => 'Community B']);

        Member::factory()->create(['community_id' => $community1->id, 'status' => 'active']);
        Member::factory()->create(['community_id' => $community2->id, 'status' => 'active']);
        Member::factory()->create(['community_id' => $community1->id, 'status' => 'inactive']);

        // Filter by Community A
        $response = $this->actingAs($user)->get(route('reports.demographic', ['community_id' => $community1->id]));
        $response->assertOk();
        $response->assertViewHas('totalMembers', 2); // 1 active + 1 inactive in Com A

        // Filter by Status Active
        $response = $this->actingAs($user)->get(route('reports.demographic', ['status' => 'active']));
        $response->assertOk();
        $response->assertViewHas('totalMembers', 2); // 1 in Com A + 1 in Com B

        // Filter by Both
        $response = $this->actingAs($user)->get(route('reports.demographic', [
            'community_id' => $community1->id,
            'status' => 'active',
        ]));
        $response->assertOk();
        $response->assertViewHas('totalMembers', 1);
    }

    public function test_export_demographic_generates_pdf()
    {
        $user = User::factory()->create(['role' => \App\Enums\UserRole::SUPER_ADMIN]);

        $this->mock(PdfService::class, function ($mock) {
            $mock->shouldReceive('generateDemographicReport')
                ->once()
                ->andReturn(new \Illuminate\Http\Response('PDF content', 200, ['Content-Type' => 'application/pdf']));
        });

        $response = $this->actingAs($user)->get(route('reports.demographic.export'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf'); // StreamedResponse defaults might vary but usually used for download
    }
}
