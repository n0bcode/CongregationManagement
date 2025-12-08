<x-app-layout>
    <x-slot name="header">
        <h2 class="text-3xl font-bold text-stone-800">
            {{ __('Project Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">{{ $project->name }}</h3>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ $project->community->name }} | 
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ ucfirst($project->status) }}
                                </span>
                            </p>
                        </div>
                        <div class="flex space-x-3">
                            <a href="{{ route('projects.edit', $project) }}" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                                Edit Project
                            </a>
                        </div>
                    </div>

                    <div x-data="{ activeTab: 'overview' }">
                        <div class="border-b border-gray-200 mb-6">
                            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                                <button @click="activeTab = 'overview'" 
                                    :class="{'border-indigo-500 text-indigo-600': activeTab === 'overview', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'overview'}"
                                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                    Overview
                                </button>
                                <button @click="activeTab = 'tasks'" 
                                    :class="{'border-indigo-500 text-indigo-600': activeTab === 'tasks', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'tasks'}"
                                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                    Tasks
                                </button>
                                <button @click="activeTab = 'timeline'" 
                                    :class="{'border-indigo-500 text-indigo-600': activeTab === 'timeline', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'timeline'}"
                                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                    Timeline
                                </button>
                                <button @click="activeTab = 'members'" 
                                    :class="{'border-indigo-500 text-indigo-600': activeTab === 'members', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'members'}"
                                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                    Members
                                </button>
                            </nav>
                        </div>
            <div class="mt-6">
                <div x-show="activeTab === 'overview'">
                    <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
                        <div class="px-4 py-5 sm:px-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Project Details</h3>
                        </div>
                        <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
                            <dl class="sm:divide-y sm:divide-gray-200">
                                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Description</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $project->description }}</dd>
                                </div>
                                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $project->status === 'active' ? 'bg-green-100 text-green-800' : 
                                               ($project->status === 'completed' ? 'bg-blue-100 text-blue-800' : 
                                               ($project->status === 'on_hold' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) }}">
                                            {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                        </span>
                                    </dd>
                                </div>
                                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Dates</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        {{ $project->start_date ? $project->start_date->format('M j, Y') : 'N/A' }} - 
                                        {{ $project->end_date ? $project->end_date->format('M j, Y') : 'N/A' }}
                                    </dd>
                                </div>
                                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Manager</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        {{ $project->manager ? $project->manager->first_name . ' ' . $project->manager->last_name : 'Unassigned' }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    {{-- Expenses Section --}}
                    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Project Expenses</h3>
                            <span class="text-sm text-gray-500">Budget: ${{ number_format($project->budget, 2) }}</span>
                        </div>
                        <div class="border-t border-gray-200">
                            <ul role="list" class="divide-y divide-gray-200">
                                @forelse($project->expenses as $expense)
                                    <li class="px-4 py-4 sm:px-6">
                                        <div class="flex items-center justify-between">
                                            <div class="text-sm font-medium text-indigo-600 truncate">
                                                {{ $expense->description }}
                                            </div>
                                            <div class="ml-2 flex-shrink-0 flex">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    ${{ number_format($expense->amount, 2) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="mt-2 sm:flex sm:justify-between">
                                            <div class="sm:flex">
                                                <p class="flex items-center text-sm text-gray-500">
                                                    {{ $expense->expense_date->format('M j, Y') }}
                                                </p>
                                            </div>
                                        </div>
                                    </li>
                                @empty
                                    <li class="px-4 py-4 sm:px-6 text-center text-gray-500">No expenses recorded.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>

                <div x-show="activeTab === 'tasks'" style="display: none;">
                    @include('projects.tasks.board', ['tasks' => $project->tasks, 'project' => $project])
                </div>

                <div x-show="activeTab === 'timeline'" style="display: none;">
                    @include('projects.tasks.timeline', ['project' => $project])
                </div>

                <div x-show="activeTab === 'members'" style="display: none;">
                    @include('projects.members.index', ['project' => $project])
                </div>
            </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
