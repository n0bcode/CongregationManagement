<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Community;
use App\Models\Member;
use App\Models\PeriodicEvent;
use App\Models\Role;
use Illuminate\Support\Collection;

class DirectoryReportService
{
    /**
     * Get all communities with their members for full directory report
     */
    public function getDirectoryCommunion(): Collection
    {
        return Community::withoutGlobalScopes()
            ->whereNotNull('code')
            ->with([
                'members' => function ($query) {
                    $query->withoutGlobalScopes()
                        ->with(['assignments.roleModel'])
                        ->orderBy('surname')
                        ->orderBy('given_name');
                }
            ])
            ->orderByRaw("CAST(SUBSTRING(code, 5) AS UNSIGNED)") // Order by AFE number
            ->get()
            ->map(function ($community) {
                return [
                    'code' => $community->code,
                    'name' => $community->name,
                    'location' => $community->location,
                    'phone' => $community->phone,
                    'email' => $community->email,
                    'members' => $community->members->map(function ($member) {
                        $assignment = $member->assignments->first();
                        return [
                            'full_name' => "{$member->surname} {$member->given_name}",
                            'role_code' => $assignment?->roleModel?->code ?? 'n',
                            'role_title' => $assignment?->roleModel?->title ?? 'Novice',
                            'dob' => $member->dob,
                            'first_profession' => $member->first_profession_date,
                            'ordination' => $member->ordination_date,
                        ];
                    })
                ];
            });
    }

    /**
     * Get alphabetical member index
     */
    public function getMemberIndex(): Collection
    {
        return Member::withoutGlobalScopes()
            ->with(['assignments.roleModel', 'assignments.community'])
            ->orderBy('surname')
            ->orderBy('given_name')
            ->get()
            ->map(function ($member) {
                $assignment = $member->assignments->first();
                return [
                    'surname' => $member->surname,
                    'given_name' => $member->given_name,
                    'role_code' => $assignment?->roleModel?->code ?? 'n',
                    'dob' => $member->dob,
                    'first_profession' => $member->first_profession_date,
                    'ordination' => $member->ordination_date,
                    'house_code' => $assignment?->community?->code ?? 'OP',
                ];
            });
    }

    /**
     * Get birthdays grouped by month
     */
    public function getBirthdaysByMonth(): Collection
    {
        $members = Member::withoutGlobalScopes()
            ->whereNotNull('dob')
            ->with(['assignments.community'])
            ->get();

        return collect(range(1, 12))->map(function ($month) use ($members) {
            $monthMembers = $members->filter(function ($member) use ($month) {
                return $member->dob->month === $month;
            })->sortBy(function ($member) {
                return $member->dob->day;
            })->map(function ($member) {
                $assignment = $member->assignments->first();
                return [
                    'day' => $member->dob->day,
                    'surname' => $member->surname,
                    'given_name' => $member->given_name,
                    'dob' => $member->dob,
                    'age' => $member->dob->age,
                    'house_code' => $assignment?->community?->code ?? 'OP',
                ];
            })->values();

            return [
                'month' => $month,
                'month_name' => now()->month($month)->format('F'),
                'members' => $monthMembers,
            ];
        })->filter(function ($month) {
            return $month['members']->isNotEmpty();
        });
    }

    /**
     * Get deceased members
     */
    public function getDeceasedMembers(): Collection
    {
        return Member::withoutGlobalScopes()
            ->where('is_deceased', true)
            ->whereNotNull('date_of_death')
            ->with(['assignments.roleModel', 'assignments.community'])
            ->orderBy('date_of_death', 'desc')
            ->get()
            ->map(function ($member) {
                $assignment = $member->assignments->first();
                $ageAtDeath = $member->dob && $member->date_of_death
                    ? $member->dob->diffInYears($member->date_of_death)
                    : null;

                return [
                    'role_code' => $assignment?->roleModel?->code ?? 'n',
                    'surname' => $member->surname,
                    'given_name' => $member->given_name,
                    'dob' => $member->dob,
                    'date_of_death' => $member->date_of_death,
                    'age_at_death' => $ageAtDeath,
                    'house_code' => $assignment?->community?->code ?? 'OP',
                ];
            });
    }

    /**
     * Get single community detail with members
     */
    public function getCommunityDetail(int $communityId): array
    {
        $community = Community::withoutGlobalScopes()
            ->with([
                'members' => function ($query) {
                    $query->withoutGlobalScopes()
                        ->with(['assignments.roleModel'])
                        ->orderBy('surname')
                        ->orderBy('given_name');
                }
            ])
            ->findOrFail($communityId);

        return [
            'code' => $community->code,
            'name' => $community->name,
            'location' => $community->location,
            'phone' => $community->phone,
            'email' => $community->email,
            'patron_saint' => $community->patron_saint,
            'founded_at' => $community->founded_at,
            'feast_day' => $community->feast_day,
            'members' => $community->members->map(function ($member) {
                $assignment = $member->assignments->first();
                return [
                    'full_name' => "{$member->surname} {$member->given_name}",
                    'role_code' => $assignment?->roleModel?->code ?? 'n',
                    'role_title' => $assignment?->roleModel?->title ?? 'Novice',
                    'dob' => $member->dob,
                    'first_profession' => $member->first_profession_date,
                    'ordination' => $member->ordination_date,
                    'phone' => $member->phone,
                    'email' => $member->email,
                ];
            }),
            'member_count' => $community->members->count(),
        ];
    }

    /**
     * Get statistics for dashboard
     */
    public function getStatistics(): array
    {
        $members = Member::withoutGlobalScopes()->get();
        $communities = Community::whereNotNull('code')->count();

        return [
            'total_members' => $members->count(),
            'total_communities' => $communities,
            'by_role' => Role::withCount(['assignments'])->get()->map(function ($role) {
                return [
                    'code' => $role->code,
                    'title' => $role->title,
                    'count' => $role->assignments_count,
                ];
            }),
            'deceased_count' => $members->where('is_deceased', true)->count(),
            'birthdays_this_month' => $members->filter(function ($member) {
                return $member->dob && $member->dob->month === now()->month;
            })->count(),
        ];
    }
}
