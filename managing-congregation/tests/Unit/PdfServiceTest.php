<?php

namespace Tests\Unit;

use App\Models\Member;
use App\Services\PdfService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PdfServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PdfService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PdfService;
    }

    public function test_generate_financial_report_creates_download_response()
    {
        $mockResponse = \Mockery::mock(\Illuminate\Http\Response::class);

        Pdf::shouldReceive('loadView')
            ->once()
            ->with('financials.pdf.monthly-report', \Mockery::on(function ($data) {
                return isset($data['report']) && isset($data['communityName']);
            }))
            ->andReturnSelf();

        Pdf::shouldReceive('setPaper')
            ->once()
            ->with('a4', 'portrait')
            ->andReturnSelf();

        Pdf::shouldReceive('download')
            ->once()
            ->with(\Mockery::pattern('/financial-report-.*\.pdf/'))
            ->andReturn($mockResponse);

        $report = [
            'period' => ['year' => 2023, 'month' => 1],
            'summary' => [],
        ];

        $response = $this->service->generateFinancialReport($report, 'Test Community');

        $this->assertSame($mockResponse, $response);
    }

    public function test_generate_member_profile_creates_download_response()
    {
        $member = Member::factory()->create();
        $mockResponse = \Mockery::mock(\Illuminate\Http\Response::class);

        Pdf::shouldReceive('loadView')
            ->once()
            ->with('members.pdf.profile', \Mockery::on(function ($data) use ($member) {
                return isset($data['member']) && $data['member']->id === $member->id;
            }))
            ->andReturnSelf();

        Pdf::shouldReceive('setPaper')
            ->once()
            ->with('a4', 'portrait')
            ->andReturnSelf();

        Pdf::shouldReceive('download')
            ->once()
            ->with(\Mockery::pattern('/member-profile-.*\.pdf/'))
            ->andReturn($mockResponse);

        $response = $this->service->generateMemberProfile($member);

        $this->assertSame($mockResponse, $response);
    }
}
