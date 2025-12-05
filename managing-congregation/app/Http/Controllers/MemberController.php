<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function index(Request $request): View
    {
        $query = \App\Models\Member::query();

        if ($request->has('search')) {
            $query->search($request->input('search'));
        }

        $members = $query->paginate(20)->appends($request->query());

        return view('members.index', compact('members'));
    }

    public function create(): View
    {
        \Illuminate\Support\Facades\Gate::authorize('create', \App\Models\Member::class);

        $communities = \App\Models\Community::all();

        return view('members.create', compact('communities'));
    }

    public function store(\App\Http\Requests\StoreMemberRequest $request)
    {
        \Illuminate\Support\Facades\Gate::authorize('create', \App\Models\Member::class);
        $data = $request->validated();

        // Handle community_id
        // If provided in request (e.g. by Super Admin), use it.
        // Otherwise, fall back to the authenticated user's community_id.
        if (! isset($data['community_id'])) {
            $data['community_id'] = \Illuminate\Support\Facades\Auth::user()->community_id;
        }

        $member = \App\Models\Member::create($data);

        return redirect()->route('members.show', $member)->with('status', 'Member created successfully.');
    }

    public function show(\App\Models\Member $member): View
    {
        \Illuminate\Support\Facades\Gate::authorize('view', $member);

        $member->load(['formationEvents', 'assignments.community', 'healthRecords', 'skills']);

        // Calculate projected future events based on most recent formation event
        $projectedEvents = [];
        $latestEvent = $member->formationEvents->sortByDesc('started_at')->first();

        if ($latestEvent) {
            $formationService = app(\App\Services\FormationService::class);
            $nextStageDate = $formationService->calculateNextStageDate($latestEvent->stage, $latestEvent->started_at);

            if ($nextStageDate) {
                // Determine next stage based on current stage
                $nextStage = match ($latestEvent->stage) {
                    \App\Enums\FormationStage::Novitiate => \App\Enums\FormationStage::FirstVows,
                    \App\Enums\FormationStage::FirstVows => \App\Enums\FormationStage::FinalVows,
                    default => null,
                };

                if ($nextStage) {
                    $projectedEvents[] = [
                        'stage' => $nextStage,
                        'date' => $nextStageDate,
                    ];
                }
            }
        }

        $communities = \App\Models\Community::all();

        return view('members.show', compact('member', 'projectedEvents', 'communities'));
    }

    public function edit(\App\Models\Member $member): View
    {
        \Illuminate\Support\Facades\Gate::authorize('update', $member);

        return view('members.edit', [
            'member' => $member,
            'statuses' => \App\Enums\MemberStatus::cases(),
        ]);
    }

    public function update(\App\Http\Requests\UpdateMemberRequest $request, \App\Models\Member $member)
    {
        \Illuminate\Support\Facades\Gate::authorize('update', $member);

        $member->update($request->validated());

        return redirect()->route('members.show', $member)->with('status', 'Member updated successfully.');
    }
}
