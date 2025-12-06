<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Services\AIProjectService;
use Illuminate\Http\Request;

class AIProjectController extends Controller
{
    protected $aiService;

    public function __construct(AIProjectService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function create()
    {
        return view('projects.ai-wizard');
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'description' => 'required|string|min:10',
            'project_name' => 'required|string|max:255',
        ]);

        $tasks = $this->aiService->generateStructure($validated['description']);

        return view('projects.ai-wizard', [
            'generatedTasks' => $tasks,
            'projectName' => $validated['project_name'],
            'description' => $validated['description'],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_name' => 'required|string|max:255',
            'description' => 'required|string',
            'tasks' => 'required|array',
            'tasks.*.title' => 'required|string',
            'tasks.*.type' => 'required|in:epic,story,task,bug',
            'tasks.*.priority' => 'required|in:low,medium,high,urgent',
        ]);

        $project = Project::create([
            'name' => $validated['project_name'],
            'description' => $validated['description'],
            'status' => 'active',
            'start_date' => now(),
            'manager_id' => auth()->user()->member?->id, // Assign to current user if member
            'community_id' => auth()->user()->community_id ?? 1, // Fallback or required
        ]);

        foreach ($validated['tasks'] as $taskData) {
            Task::create([
                'project_id' => $project->id,
                'title' => $taskData['title'],
                'type' => $taskData['type'],
                'priority' => $taskData['priority'],
                'status' => 'todo',
                'reporter_id' => auth()->user()->member?->id,
            ]);
        }

        return redirect()->route('projects.show', $project)->with('success', 'Project created with AI assistance!');
    }
}
