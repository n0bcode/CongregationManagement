<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiProjectPlanService
{
    private ?string $apiKey;
    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key') ?? env('GEMINI_API_KEY');
    }

    /**
     * Generate a detailed project plan from a description.
     *
     * @param string $description User's project requirements
     * @param string|null $manualApiKey Optional API Key provided by user
     * @return array The parsed JSON structure containing project details and tasks
     * @throws \Exception If API call fails or JSON is invalid
     */
    public function generatePlan(string $description, ?string $manualApiKey = null): array
    {
        $apiKey = $manualApiKey ?: $this->apiKey;

        if (empty($apiKey)) {
            throw new \Exception('Gemini API Key is not configured. Please provide an API Key.');
        }

        $prompt = $this->buildSystemPrompt($description);

        try {
            $response = Http::timeout(60)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'x-goog-api-key' => $apiKey,
                ])->post($this->baseUrl, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 8192,
                    'responseMimeType' => 'application/json',
                ]
            ]);

            if ($response->failed()) {
                Log::error('Gemini API Error', ['status' => $response->status(), 'body' => $response->body()]);
                throw new \Exception('Failed to communicate with AI service: ' . $response->reason());
            }

            $jsonResponse = $response->json();
            $generatedText = $jsonResponse['candidates'][0]['content']['parts'][0]['text'] ?? '';

            return $this->cleanAndParseJson($generatedText);

        } catch (\Exception $e) {
            Log::error('Project Plan Generation Failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    private function buildSystemPrompt(string $userInput): string
    {
        return <<<EOT
You are an expert Senior Project Manager. Your task is to create a comprehensive, production-ready project plan based on the user's request.

User Request: "{$userInput}"

requirements:
1.  **Structure**: Break the project down into logical **Epics** (major phases) and **Tasks**.
2.  **Output Format**: Return strictly **valid JSON** only. No Markdown code fences (```json). No commentary.
3.  **Schema**: The JSON must exactly match this structure:
    {
        "project_name": "Suggested Professional Name",
        "description": "Professional executive summary of the project",
        "tasks": [
            {
                "title": "Phase 1: Foundation (Header Task)",
                "type": "epic",
                "priority": "high",
                "estimated_hours": 40
            },
            {
                "title": "Setup Development Environment",
                "type": "task",
                "priority": "high",
                "status": "todo",
                "estimated_hours": 4
            }
        ]
    }

4.  **Rules**:
    - `type` must be one of: "epic", "story", "task", "bug".
    - `priority` must be one of: "low", "medium", "high", "urgent".
    - `status` defaults to "todo".
    - Use "epic" type for grouping phases (e.g., "Planning", "Development", "Testing").
    - Ensure logical flow of tasks.
    - valid JSON string only.
EOT;
    }

    private function cleanAndParseJson(string $rawContent): array
    {
        // 1. Remove Markdown code fences if present (Gemini might still add them despite instructions)
        $cleanContent = preg_replace('/^```json\s*/m', '', $rawContent);
        $cleanContent = preg_replace('/^```\s*/m', '', $cleanContent);
        
        // 2. Decode JSON
        $data = json_decode($cleanContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('JSON Parse Error', ['raw' => $rawContent, 'error' => json_last_error_msg()]);
            throw new \Exception('AI returned invalid data format. Please try again.');
        }

        // 3. Basic Validation
        if (!isset($data['project_name']) || !isset($data['tasks']) || !is_array($data['tasks'])) {
            throw new \Exception('AI response missing required project structure.');
        }

        return $data;
    }
}
