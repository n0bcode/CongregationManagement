<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 10);
        if (!in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        $projects = \App\Models\Project::with(['community', 'manager'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();
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
        // Load expenses with pagination (named paginator)
        // [TESTING] Reduced to 1 to show pagination
        $expenses = $project->expenses()
            ->latest()
            ->with(['category', 'recordedBy'])
            ->paginate(1, ['*'], 'expenses_page');

        return view('projects.show', compact('project', 'expenses'));
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

    public function updateTaskStatus(Request $request, \App\Models\Project $project, \App\Models\Task $task)
    {
        // Validate task belongs to project
        if ($task->project_id !== $project->id) {
            return response()->json(['success' => false, 'message' => 'Task not found'], 404);
        }
        
        $validated = $request->validate([
            'status' => 'required|in:todo,in_progress,review,done'
        ]);
        
        $task->update(['status' => $validated['status']]);
        
        return response()->json([
            'success' => true, 
            'task' => $task,
            'message' => 'Task status updated successfully'
        ]);
    }

    public function updateTaskDates(Request $request, \App\Models\Project $project, \App\Models\Task $task)
    {
        // Validate task belongs to project
        if ($task->project_id !== $project->id) {
            return response()->json(['success' => false, 'message' => 'Task not found'], 404);
        }
        
        $validated = $request->validate([
            'start_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:start_date'
        ]);
        
        $task->update($validated);
        
        return response()->json([
            'success' => true,
            'task' => $task->fresh(),
            'message' => 'Task dates updated successfully'
        ]);
    }

    public function quickCreateTask(Request $request, \App\Models\Project $project)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:epic,story,task,bug',
            'parent_id' => 'nullable|exists:tasks,id',
            'start_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:start_date',
            'assignee_id' => 'nullable|exists:members,id'
        ]);
        
        $validated['project_id'] = $project->id;
        $validated['status'] = 'todo';
        $validated['priority'] = 'medium';
        
        $task = \App\Models\Task::create($validated);
        
        return response()->json([
            'success' => true,
            'task' => $task->load(['assignee', 'parent', 'children']),
            'message' => 'Task created successfully'
        ]);
    }
    
    public function updateTaskPriority(Request $request, Project $project, Task $task)
    {
        // Verify task belongs to project
        if ($task->project_id !== $project->id) {
            return response()->json(['success' => false, 'message' => 'Task not found'], 404);
        }
        
        $validated = $request->validate([
            'priority' => 'required|in:low,medium,high'
        ]);
        
        $task->update(['priority' => $validated['priority']]);
        
        return response()->json([
            'success' => true,
            'task' => $task->load(['assignee', 'parent']),
            'message' => 'Priority updated successfully'
        ]);
    }
    
    public function destroy(\App\Models\Project $project)
    {
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Project deleted successfully.');
    }
}
