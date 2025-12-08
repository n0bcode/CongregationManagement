<x-app-layout>
    <x-slot name="header">
        <h2 class="text-3xl font-bold text-stone-800">
            {{ __('AI Project Wizard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(!isset($generatedTasks))
                        {{-- Step 1: Input Description --}}
                        <form action="{{ route('projects.ai.generate') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label for="project_name" class="block text-sm font-medium text-gray-700">Project Name</label>
                                <input type="text" name="project_name" id="project_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                            </div>
                            <div class="mb-4">
                                <label for="description" class="block text-sm font-medium text-gray-700">Describe your project</label>
                                <p class="text-sm text-gray-500 mb-2">e.g., "A mobile app for tracking fitness goals with social sharing features."</p>
                                <textarea name="description" id="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required></textarea>
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    Generate Structure
                                </button>
                            </div>
                        </form>
                    @else
                        {{-- Step 2: Review and Create --}}
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Review Generated Structure</h3>
                        <form action="{{ route('projects.ai.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="project_name" value="{{ $projectName }}">
                            <input type="hidden" name="description" value="{{ $description }}">
                            
                            <div class="space-y-4 mb-6">
                                @foreach($generatedTasks as $index => $task)
                                    <div class="flex items-center space-x-4 border p-4 rounded-md bg-gray-50">
                                        <div class="flex-1">
                                            <label class="block text-sm font-medium text-gray-700">Task Title</label>
                                            <input type="text" name="tasks[{{ $index }}][title]" value="{{ $task['title'] }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        </div>
                                        <div class="w-32">
                                            <label class="block text-sm font-medium text-gray-700">Type</label>
                                            <select name="tasks[{{ $index }}][type]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                <option value="epic" {{ $task['type'] == 'epic' ? 'selected' : '' }}>Epic</option>
                                                <option value="story" {{ $task['type'] == 'story' ? 'selected' : '' }}>Story</option>
                                                <option value="task" {{ $task['type'] == 'task' ? 'selected' : '' }}>Task</option>
                                                <option value="bug" {{ $task['type'] == 'bug' ? 'selected' : '' }}>Bug</option>
                                            </select>
                                        </div>
                                        <div class="w-32">
                                            <label class="block text-sm font-medium text-gray-700">Priority</label>
                                            <select name="tasks[{{ $index }}][priority]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                <option value="low" {{ $task['priority'] == 'low' ? 'selected' : '' }}>Low</option>
                                                <option value="medium" {{ $task['priority'] == 'medium' ? 'selected' : '' }}>Medium</option>
                                                <option value="high" {{ $task['priority'] == 'high' ? 'selected' : '' }}>High</option>
                                                <option value="urgent" {{ $task['priority'] == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                            </select>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="flex justify-end space-x-3">
                                <a href="{{ route('projects.ai.create') }}" class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    Start Over
                                </a>
                                <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    Create Project
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
