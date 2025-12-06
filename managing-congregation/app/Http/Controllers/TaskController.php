<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function create(Project $project)
    {
        return view('projects.tasks.create', compact('project'));
    }

    public function timeline(Project $project)
    {
        return view('projects.tasks.timeline', compact('project'));
    }

    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:epic,story,task,bug',
            'status' => 'required|in:todo,in_progress,review,done',
            'priority' => 'required|in:low,medium,high,urgent',
            'assignee_id' => 'nullable|exists:members,id',
            'parent_id' => 'nullable|exists:tasks,id',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $task = $project->tasks()->create([
            ...$validated,
            'reporter_id' => auth()->id() ?? 1, // Fallback to system user if not logged in
        ]);

        // In a real app, send notification to assignee here

        return redirect()->route('projects.show', $project)->with('success', 'Task created successfully.');
    }

    public function edit(Project $project, Task $task)
    {
        return view('projects.tasks.edit', compact('project', 'task'));
    }

    public function update(Request $request, Project $project, Task $task)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:epic,story,task,bug',
            'status' => 'required|in:todo,in_progress,review,done',
            'priority' => 'required|in:low,medium,high,urgent',
            'assignee_id' => 'nullable|exists:members,id',
            'parent_id' => 'nullable|exists:tasks,id',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $task->update($validated);

        return redirect()->route('projects.show', $project)->with('success', 'Task updated successfully.');
    }

    public function destroy(Project $project, Task $task)
    {
        $task->delete();

        return redirect()->route('projects.show', $project)->with('success', 'Task deleted successfully.');
    }
}
