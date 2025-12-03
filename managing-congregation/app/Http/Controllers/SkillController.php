<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Skill;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SkillController extends Controller
{
    public function store(Request $request, Member $member): RedirectResponse
    {
        Gate::authorize('update', $member);

        $validated = $request->validate([
            'category' => 'required|in:pastoral,practical,special',
            'name' => 'required|string|max:255',
            'proficiency' => 'required|in:beginner,intermediate,advanced,expert',
            'notes' => 'nullable|string',
        ]);

        $member->skills()->create($validated);

        return redirect()->route('members.show', $member)
            ->with('status', 'Skill added successfully.');
    }

    public function destroy(Skill $skill): RedirectResponse
    {
        Gate::authorize('update', $skill->member);

        $member = $skill->member;
        $skill->delete();

        return redirect()->route('members.show', $member)
            ->with('status', 'Skill deleted successfully.');
    }
}
