<?php

namespace App\Http\Controllers;

use App\Models\Community;
use App\Models\Member;
use App\Services\PdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct(
        protected PdfService $pdfService
    ) {}

    /**
     * Display demographic report
     */
    public function demographic(Request $request): View
    {
        $this->checkAuthorization('viewReports');

        // Get filters
        $communityId = $request->input('community_id');
        $status = $request->input('status');

        // Base query
        $query = Member::query();

        // Apply filters
        if ($communityId) {
            $query->where('community_id', $communityId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        // Get statistics
        $totalMembers = $query->count();

        // Age distribution
        $ageDistribution = $query->select(
            DB::raw('CASE 
                WHEN TIMESTAMPDIFF(YEAR, dob, CURDATE()) < 30 THEN "Under 30"
                WHEN TIMESTAMPDIFF(YEAR, dob, CURDATE()) BETWEEN 30 AND 50 THEN "30-50"
                WHEN TIMESTAMPDIFF(YEAR, dob, CURDATE()) BETWEEN 51 AND 70 THEN "51-70"
                ELSE "Over 70"
            END as age_group'),
            DB::raw('COUNT(*) as count')
        )
            ->groupBy('age_group')
            ->get()
            ->pluck('count', 'age_group')
            ->toArray();

        // Formation stage breakdown
        $formationStages = DB::table('members')
            ->join('formation_events', 'members.id', '=', 'formation_events.member_id')
            ->select('formation_events.stage', DB::raw('COUNT(DISTINCT members.id) as count'))
            ->when($communityId, fn ($q) => $q->where('members.community_id', $communityId))
            ->when($status, fn ($q) => $q->where('members.status', $status))
            ->groupBy('formation_events.stage')
            ->pluck('count', 'stage')
            ->toArray();

        // Community distribution
        $communityDistribution = Member::select('community_id', DB::raw('COUNT(*) as count'))
            ->when($communityId, fn ($q) => $q->where('community_id', $communityId))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->groupBy('community_id')
            ->with('community:id,name')
            ->get()
            ->mapWithKeys(function ($item) {
                /** @var \App\Models\Member $item */
                return [$item->community->name ?? 'Unknown' => $item->count];
            })
            ->toArray();

        // Status distribution
        $statusDistribution = Member::select('status', DB::raw('COUNT(*) as count'))
            ->when($communityId, fn ($q) => $q->where('community_id', $communityId))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->groupBy('status')
            ->toBase()
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        // Get communities for filter
        $communities = Community::orderBy('name')->get();

        return view('reports.demographic', compact(
            'totalMembers',
            'ageDistribution',
            'formationStages',
            'communityDistribution',
            'statusDistribution',
            'communities',
            'communityId',
            'status'
        ));
    }

    /**
     * Export demographic report as PDF
     */
    public function exportDemographic(Request $request): \Illuminate\Http\Response
    {
        $this->checkAuthorization('exportReports');

        // Get same data as demographic view
        $communityId = $request->input('community_id');
        $status = $request->input('status');

        $query = Member::query();

        if ($communityId) {
            $query->where('community_id', $communityId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $totalMembers = $query->count();

        $ageDistribution = $query->select(
            DB::raw('CASE 
                WHEN TIMESTAMPDIFF(YEAR, dob, CURDATE()) < 30 THEN "Under 30"
                WHEN TIMESTAMPDIFF(YEAR, dob, CURDATE()) BETWEEN 30 AND 50 THEN "30-50"
                WHEN TIMESTAMPDIFF(YEAR, dob, CURDATE()) BETWEEN 51 AND 70 THEN "51-70"
                ELSE "Over 70"
            END as age_group'),
            DB::raw('COUNT(*) as count')
        )
            ->groupBy('age_group')
            ->get()
            ->pluck('count', 'age_group')
            ->toArray();

        $formationStages = DB::table('members')
            ->join('formation_events', 'members.id', '=', 'formation_events.member_id')
            ->select('formation_events.stage', DB::raw('COUNT(DISTINCT members.id) as count'))
            ->when($communityId, fn ($q) => $q->where('members.community_id', $communityId))
            ->when($status, fn ($q) => $q->where('members.status', $status))
            ->groupBy('formation_events.stage')
            ->pluck('count', 'stage')
            ->toArray();

        $communityDistribution = Member::select('community_id', DB::raw('COUNT(*) as count'))
            ->when($communityId, fn ($q) => $q->where('community_id', $communityId))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->groupBy('community_id')
            ->with('community:id,name')
            ->get()
            ->mapWithKeys(function ($item) {
                /** @var \App\Models\Member $item */
                return [$item->community->name ?? 'Unknown' => $item->count];
            })
            ->toArray();

        $statusDistribution = Member::select('status', DB::raw('COUNT(*) as count'))
            ->when($communityId, fn ($q) => $q->where('community_id', $communityId))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->groupBy('status')
            ->toBase()
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        $data = compact(
            'totalMembers',
            'ageDistribution',
            'formationStages',
            'communityDistribution',
            'statusDistribution',
            'communityId',
            'status'
        );

        return $this->pdfService->generateDemographicReport($data);
    }

    /**
     * Display advanced statistics
     */
    public function advanced(): View
    {
        $this->checkAuthorization('viewReports');

        // Skills Distribution
        $skillsDistribution = \App\Models\Skill::select('name', DB::raw('count(*) as count'))
            ->groupBy('name')
            ->orderByDesc('count')
            ->limit(10)
            ->pluck('count', 'name')
            ->toArray();

        // Age Demographics (Detailed)
        $ageDemographics = Member::select(
            DB::raw('CASE 
                WHEN TIMESTAMPDIFF(YEAR, dob, CURDATE()) < 20 THEN "Under 20"
                WHEN TIMESTAMPDIFF(YEAR, dob, CURDATE()) BETWEEN 20 AND 29 THEN "20-29"
                WHEN TIMESTAMPDIFF(YEAR, dob, CURDATE()) BETWEEN 30 AND 39 THEN "30-39"
                WHEN TIMESTAMPDIFF(YEAR, dob, CURDATE()) BETWEEN 40 AND 49 THEN "40-49"
                WHEN TIMESTAMPDIFF(YEAR, dob, CURDATE()) BETWEEN 50 AND 59 THEN "50-59"
                WHEN TIMESTAMPDIFF(YEAR, dob, CURDATE()) BETWEEN 60 AND 69 THEN "60-69"
                ELSE "70+"
            END as age_group'),
            DB::raw('COUNT(*) as count')
        )
            ->groupBy('age_group')
            ->orderBy('age_group')
            ->get()
            ->pluck('count', 'age_group')
            ->toArray();

        // Upcoming Ordination Anniversaries (Next 30 days)
        $upcomingOrdinations = \App\Models\Ordination::with('member')
            ->whereRaw('DATE_ADD(date, INTERVAL YEAR(CURDATE())-YEAR(date) YEAR) BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)')
            ->orderByRaw('DATE_ADD(date, INTERVAL YEAR(CURDATE())-YEAR(date) YEAR)')
            ->get();

        return view('reports.advanced', compact(
            'skillsDistribution',
            'ageDemographics',
            'upcomingOrdinations'
        ));
    }

    /**
     * Display community annual members report
     */
    public function communityAnnualMembers(Request $request): View
    {
        $this->checkAuthorization('viewReports');

        $communityId = $request->input('community_id');
        $year = $request->input('year', now()->year);

        $communities = Community::orderBy('name')->get();
        $members = collect([]);

        if ($communityId) {
            if ($year == now()->year) {
                // If checking for current year, just return members currently in this community
                $members = \App\Models\Member::where('community_id', $communityId)
                    ->with(['assignments' => function ($query) use ($communityId) {
                        $query->where('community_id', $communityId)->latest('start_date');
                    }])
                    ->get()
                    ->map(function ($member) {
                        $member->historical_role = $member->assignments->first()?->role ?? 'Member';
                        return $member;
                    });
            } else {
                // Get members who had an active assignment in this community during the specified year
                // Logic: Assignment Start <= EndOfYear AND (Assignment End IS NULL OR Assignment End >= StartOfYear)
                $startOfYear = \Carbon\Carbon::createFromDate($year, 1, 1)->startOfDay();
                $endOfYear = \Carbon\Carbon::createFromDate($year, 12, 31)->endOfDay();
    
                $members = \App\Models\Member::whereHas('assignments', function ($query) use ($communityId, $startOfYear, $endOfYear) {
                    $query->where('community_id', $communityId)
                        ->where('start_date', '<=', $endOfYear)
                        ->where(function ($q) use ($startOfYear) {
                            $q->whereNull('end_date')
                                ->orWhere('end_date', '>=', $startOfYear);
                        });
                })
                    ->with(['assignments' => function ($query) use ($communityId) {
                        $query->where('community_id', $communityId)->latest('start_date');
                    }])
                    ->get()
                    ->map(function ($member) {
                        // Attach the relevant role for that period
                        $member->historical_role = $member->assignments->first()?->role ?? 'Member';
                        return $member;
                    });
            }
        }

        return view('reports.community-annual', compact('communities', 'members', 'communityId', 'year'));
    }

    /**
     * Check authorization for reports
     */
    protected function checkAuthorization(string $ability): void
    {
        $user = auth()->user();

        $allowed = match ($ability) {
            'viewReports' => in_array($user->role->value, [\App\Enums\UserRole::SUPER_ADMIN->value, \App\Enums\UserRole::GENERAL->value, \App\Enums\UserRole::DIRECTOR->value]),
            'exportReports' => in_array($user->role->value, [\App\Enums\UserRole::SUPER_ADMIN->value, \App\Enums\UserRole::GENERAL->value, \App\Enums\UserRole::DIRECTOR->value]),
            default => false,
        };

        if (! $allowed) {
            abort(403, 'Unauthorized action.');
        }
    }
}
