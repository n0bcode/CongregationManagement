<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = \App\Models\Project::with(['community', 'manager'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        $communities = \App\Models\Community::all();
        $members = \App\Models\Member::all(); // Should filter by active/eligible
        return view('projects.create', compact('communities', 'members'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'community_id' => 'required|exists:communities,id',
            'manager_id' => 'nullable|exists:members,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:planned,active,completed,suspended',
            'budget' => 'required|numeric|min:0',
        ]);

        \App\Models\Project::create($validated);

        return redirect()->route('projects.index')->with('success', 'Project created successfully.');
    }

    public function show(\App\Models\Project $project)
    {
        $project->load(['community', 'manager', 'expenses']);
        return view('projects.show', compact('project'));
    }

    public function edit(\App\Models\Project $project)
    {
        $communities = \App\Models\Community::all();
        $members = \App\Models\Member::all();
        return view('projects.edit', compact('project', 'communities', 'members'));
    }

    public function update(Request $request, \App\Models\Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'community_id' => 'required|exists:communities,id',
            'manager_id' => 'nullable|exists:members,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:planned,active,completed,suspended',
            'budget' => 'required|numeric|min:0',
        ]);

        $project->update($validated);

        return redirect()->route('projects.index')->with('success', 'Project updated successfully.');
    }

    public function destroy(\App\Models\Project $project)
    {
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Project deleted successfully.');
    }
}
