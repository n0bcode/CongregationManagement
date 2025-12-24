<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="{{ __('AI Project Creation Tutorial') }}" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <!-- Header Section -->
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">
                            🎥 How to Create Projects Using AI
                        </h2>
                        <p class="text-gray-600 leading-relaxed">
                            Watch this comprehensive tutorial to learn how to leverage the power of Google Gemini AI 
                            to automatically plan and structure your projects. This video demonstrates the complete 
                            workflow from initial description to fully organized tasks and milestones.
                        </p>
                    </div>

                    <!-- Video Section -->
                    <div class="mb-8 bg-gray-50 rounded-lg p-6">
                        <div class="aspect-w-16 aspect-h-9 bg-gray-900 rounded-lg overflow-hidden shadow-lg">
                            <video controls class="w-full h-auto max-h-[600px]">
                                <source src="{{ asset('videos/AI_to_create_projects.mp4') }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                        <p class="text-sm text-gray-500 mt-4 text-center">
                            💡 Tip: Click the fullscreen button for better viewing experience
                        </p>
                    </div>

                    <!-- Key Features Section -->
                    <div class="mb-8">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">
                            ✨ What You'll Learn
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex items-start space-x-3 p-4 bg-indigo-50 rounded-lg">
                                <svg class="w-6 h-6 text-indigo-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <h4 class="font-medium text-gray-900">Describe Your Vision</h4>
                                    <p class="text-sm text-gray-600">Learn how to write effective project descriptions that AI can understand</p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-3 p-4 bg-green-50 rounded-lg">
                                <svg class="w-6 h-6 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                <div>
                                    <h4 class="font-medium text-gray-900">AI-Powered Planning</h4>
                                    <p class="text-sm text-gray-600">See how AI generates comprehensive project plans with tasks and priorities</p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-3 p-4 bg-purple-50 rounded-lg">
                                <svg class="w-6 h-6 text-purple-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                <div>
                                    <h4 class="font-medium text-gray-900">Review & Customize</h4>
                                    <p class="text-sm text-gray-600">Adjust task types, priorities, and details before creating the project</p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-3 p-4 bg-yellow-50 rounded-lg">
                                <svg class="w-6 h-6 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <div>
                                    <h4 class="font-medium text-gray-900">Create & Execute</h4>
                                    <p class="text-sm text-gray-600">One-click project creation with all tasks ready for execution</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Start Guide -->
                    <div class="mb-8 border-t pt-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">
                            🚀 Quick Start Steps
                        </h3>
                        <ol class="space-y-3">
                            <li class="flex items-start space-x-3">
                                <span class="flex-shrink-0 w-8 h-8 bg-indigo-600 text-white rounded-full flex items-center justify-center font-bold text-sm">1</span>
                                <div>
                                    <h4 class="font-medium text-gray-900">Access AI Project Manager</h4>
                                    <p class="text-sm text-gray-600">Navigate to Projects → AI Project Manager from the main menu</p>
                                </div>
                            </li>
                            <li class="flex items-start space-x-3">
                                <span class="flex-shrink-0 w-8 h-8 bg-indigo-600 text-white rounded-full flex items-center justify-center font-bold text-sm">2</span>
                                <div>
                                    <h4 class="font-medium text-gray-900">Describe Your Project</h4>
                                    <p class="text-sm text-gray-600">Write a detailed description of what you want to build - be specific about features and goals</p>
                                </div>
                            </li>
                            <li class="flex items-start space-x-3">
                                <span class="flex-shrink-0 w-8 h-8 bg-indigo-600 text-white rounded-full flex items-center justify-center font-bold text-sm">3</span>
                                <div>
                                    <h4 class="font-medium text-gray-900">Generate Plan with AI</h4>
                                    <p class="text-sm text-gray-600">Click the "Generate Plan with AI" button and wait for the AI to create your project structure</p>
                                </div>
                            </li>
                            <li class="flex items-start space-x-3">
                                <span class="flex-shrink-0 w-8 h-8 bg-indigo-600 text-white rounded-full flex items-center justify-center font-bold text-sm">4</span>
                                <div>
                                    <h4 class="font-medium text-gray-900">Review and Refine</h4>
                                    <p class="text-sm text-gray-600">Edit task names, types, and priorities to match your preferences</p>
                                </div>
                            </li>
                            <li class="flex items-start space-x-3">
                                <span class="flex-shrink-0 w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center font-bold text-sm">5</span>
                                <div>
                                    <h4 class="font-medium text-gray-900">Approve & Create</h4>
                                    <p class="text-sm text-gray-600">Click "Approve & Create Project" to finalize and start managing your project</p>
                                </div>
                            </li>
                        </ol>
                    </div>

                    <!-- Tips & Best Practices -->
                    <div class="mb-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-blue-900 mb-4 flex items-center">
                            <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            Tips for Better AI Results
                        </h3>
                        <ul class="space-y-2 text-sm text-blue-800">
                            <li class="flex items-start">
                                <span class="mr-2">•</span>
                                <span>Be specific about your project goals, target audience, and required features</span>
                            </li>
                            <li class="flex items-start">
                                <span class="mr-2">•</span>
                                <span>Mention technology stack preferences if you have any (e.g., "using Laravel and Vue.js")</span>
                            </li>
                            <li class="flex items-start">
                                <span class="mr-2">•</span>
                                <span>Include timeline expectations and any constraints (budget, team size, etc.)</span>
                            </li>
                            <li class="flex items-start">
                                <span class="mr-2">•</span>
                                <span>Review and modify the AI-generated tasks - they're a starting point, not final</span>
                            </li>
                            <li class="flex items-start">
                                <span class="mr-2">•</span>
                                <span>Use task types appropriately: Epic for large features, Story for user stories, Task for technical work</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-center space-x-4 pt-6 border-t">
                        <a href="{{ route('projects.ai.create') }}" class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-6 py-3 text-base font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            Try AI Project Manager Now
                        </a>
                        <a href="{{ route('projects.index') }}" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-6 py-3 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            View All Projects
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
