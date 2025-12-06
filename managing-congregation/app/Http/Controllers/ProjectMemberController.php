<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectMember;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProjectMemberController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'member_id' => [
                'required',
                'exists:members,id',
                Rule::unique('project_members')->where(function ($query) use ($project) {
                    return $query->where('project_id', $project->id);
                }),
            ],
            'role' => 'required|in:admin,member,viewer',
        ]);

        $project->members()->attach($validated['member_id'], [
            'role' => $validated['role'],
            'status' => 'pending', // Default to pending until accepted
        ]);

        // In a real app, send notification/email here

        return back()->with('success', 'Member invited successfully.');
    }

    public function update(Request $request, Project $project, ProjectMember $member)
    {
        $validated = $request->validate([
            'role' => 'required|in:admin,member,viewer',
            'status' => 'required|in:pending,active',
        ]);

        $member->update($validated);

        return back()->with('success', 'Member updated successfully.');
    }

    public function destroy(Project $project, ProjectMember $member)
    {
        $member->delete();

        return back()->with('success', 'Member removed successfully.');
    }
}
