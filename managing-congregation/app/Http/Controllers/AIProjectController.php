<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Services\GeminiProjectPlanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AIProjectController extends Controller
{
    protected $geminiService;

    public function __construct(GeminiProjectPlanService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    public function create()
    {
        $hasApiKey = !empty(config('services.gemini.key')) || !empty(env('GEMINI_API_KEY'));
        return view('projects.ai-wizard', compact('hasApiKey'));
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'description' => 'required|string|min:10',
            'api_key' => 'nullable|string',
        ]);

        try {
            $plan = $this->geminiService->generatePlan(
                $validated['description'], 
                $validated['api_key'] ?? null
            );
            
            return response()->json([
                'success' => true,
                'data' => $plan
            ]);
        } catch (\Exception $e) {
            Log::error('AI Generation Failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
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
            'tasks.*.estimated_hours' => 'nullable|numeric',
        ]);

        try {
            DB::beginTransaction();

            $project = Project::create([
                'name' => $validated['project_name'],
                'description' => $validated['description'],
                'status' => 'planned', // Default to planned
                'start_date' => now(),
                'manager_id' => auth()->user()->member?->id,
                'community_id' => auth()->user()->community_id ?? 1,
            ]);

            foreach ($validated['tasks'] as $taskData) {
                Task::create([
                    'project_id' => $project->id,
                    'title' => $taskData['title'],
                    'type' => $taskData['type'],
                    'priority' => $taskData['priority'],
                    'status' => 'todo',
                    // 'estimated_hours' => $taskData['estimated_hours'] ?? 0, // Assuming migration has this, if not we skip
                    'reporter_id' => auth()->user()->member?->id,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'redirect_url' => route('projects.show', $project)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Project Creation Failed', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to save project. Please try again.'
            ], 500);
        }
    }
}
