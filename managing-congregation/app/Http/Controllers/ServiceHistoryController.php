<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssignmentRequest;
use App\Models\Assignment;
use App\Models\Member;
use Illuminate\Http\RedirectResponse;

class ServiceHistoryController extends Controller
{
    public function store(StoreAssignmentRequest $request, Member $member): RedirectResponse
    {
        $member->assignments()->create($request->validated());

        return back()->with('success', 'Service record added successfully.');
    }

    public function destroy(Assignment $assignment): RedirectResponse
    {
        $this->authorize('update', $assignment->member);

        $assignment->delete();

        return back()->with('success', 'Service record deleted successfully.');
    }
}
