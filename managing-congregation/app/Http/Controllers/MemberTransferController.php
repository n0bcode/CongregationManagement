<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMemberTransferRequest;
use App\Models\Member;
use App\Models\Assignment;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;

class MemberTransferController extends Controller
{
    public function store(StoreMemberTransferRequest $request, Member $member): RedirectResponse
    {
        // Authorization check (can be moved to Policy later)
        // $this->authorize('update', $member); 

        DB::transaction(function () use ($request, $member) {
            $validated = $request->validated();
            $transferDate = $validated['transfer_date'];
            $newCommunityId = $validated['community_id'];

            // 1. Close the current assignment if it exists
            $currentAssignment = $member->currentAssignment;
            if ($currentAssignment) {
                $currentAssignment->update([
                    'end_date' => $transferDate,
                ]);
            } else {
                // If no assignment exists, create a "historical" one for the current community ending now?
                // Or just assume this is the first tracked assignment.
                // Let's create a closed assignment for the *previous* community if we want to track it.
                // For now, we just ensure the new one starts.
                
                // OPTIONAL: If we want to backfill the history, we could create an assignment for the old community.
                // But we don't know the start date. So we skip.
            }

            // 2. Update the member's current community
            $oldCommunityId = $member->community_id;
            $member->update([
                'community_id' => $newCommunityId,
            ]);

            // 3. Create the new assignment
            Assignment::create([
                'member_id' => $member->id,
                'community_id' => $newCommunityId,
                'start_date' => $transferDate,
                // 'role' => $member->role, // If we tracked roles
            ]);

            // Audit logging is handled by Observers if configured, or we can log explicitly here if needed.
        });

        return redirect()->route('members.show', $member)
            ->with('success', 'Member transferred successfully.');
    }
}
