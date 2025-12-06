<?php

namespace App\Services;

class AIProjectService
{
    /**
     * Generate a project structure from a description.
     * 
     * @param string $description
     * @return array
     */
    public function generateStructure(string $description): array
    {
        // Mock implementation for MVP
        // In a real app, this would call OpenAI/Gemini API
        
        $tasks = [];
        
        // Simple heuristic: split by lines or keywords
        // For demo purposes, we'll return a fixed structure based on keywords
        
        if (str_contains(strtolower($description), 'website')) {
            $tasks = [
                ['title' => 'Design Homepage', 'type' => 'story', 'priority' => 'high'],
                ['title' => 'Implement Authentication', 'type' => 'story', 'priority' => 'high'],
                ['title' => 'Setup Database', 'type' => 'task', 'priority' => 'medium'],
                ['title' => 'Deploy to Production', 'type' => 'task', 'priority' => 'medium'],
            ];
        } elseif (str_contains(strtolower($description), 'mobile app')) {
            $tasks = [
                ['title' => 'Design UI/UX', 'type' => 'story', 'priority' => 'high'],
                ['title' => 'Setup React Native', 'type' => 'task', 'priority' => 'medium'],
                ['title' => 'Implement API Integration', 'type' => 'story', 'priority' => 'high'],
                ['title' => 'App Store Submission', 'type' => 'task', 'priority' => 'low'],
            ];
        } else {
            // Generic tasks
            $tasks = [
                ['title' => 'Planning Phase', 'type' => 'epic', 'priority' => 'medium'],
                ['title' => 'Execution Phase', 'type' => 'epic', 'priority' => 'medium'],
                ['title' => 'Review & Launch', 'type' => 'story', 'priority' => 'high'],
            ];
        }
        
        return $tasks;
    }
}
