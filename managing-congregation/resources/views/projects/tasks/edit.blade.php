<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="{{ __('Edit Task') }}" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('projects.tasks.update', [$project, $task]) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Title -->
                        <div class="mb-4">
                            <x-forms.input-label for="title" :value="__('Title')" />
                            <x-forms.text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title', $task->title)" required autofocus />
                            <x-forms.input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <x-forms.input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="4">{{ old('description', $task->description) }}</textarea>
                            <x-forms.input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Type -->
                            <div class="mb-4">
                                <x-forms.input-label for="type" :value="__('Type')" />
                                <select id="type" name="type" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="task" {{ old('type', $task->type) == 'task' ? 'selected' : '' }}>Task</option>
                                    <option value="story" {{ old('type', $task->type) == 'story' ? 'selected' : '' }}>Story</option>
                                    <option value="epic" {{ old('type', $task->type) == 'epic' ? 'selected' : '' }}>Epic</option>
                                    <option value="bug" {{ old('type', $task->type) == 'bug' ? 'selected' : '' }}>Bug</option>
                                </select>
                                <x-forms.input-error :messages="$errors->get('type')" class="mt-2" />
                            </div>

                            <!-- Priority -->
                            <div class="mb-4">
                                <x-forms.input-label for="priority" :value="__('Priority')" />
                                <select id="priority" name="priority" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="low" {{ old('priority', $task->priority) == 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="medium" {{ old('priority', $task->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="high" {{ old('priority', $task->priority) == 'high' ? 'selected' : '' }}>High</option>
                                    <option value="urgent" {{ old('priority', $task->priority) == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                </select>
                                <x-forms.input-error :messages="$errors->get('priority')" class="mt-2" />
                            </div>

                            <!-- Status -->
                            <div class="mb-4">
                                <x-forms.input-label for="status" :value="__('Status')" />
                                <select id="status" name="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="todo" {{ old('status', $task->status) == 'todo' ? 'selected' : '' }}>To Do</option>
                                    <option value="in_progress" {{ old('status', $task->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="review" {{ old('status', $task->status) == 'review' ? 'selected' : '' }}>Review</option>
                                    <option value="done" {{ old('status', $task->status) == 'done' ? 'selected' : '' }}>Done</option>
                                </select>
                                <x-forms.input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>

                            <!-- Assignee -->
                            <div class="mb-4">
                                <x-forms.input-label for="assignee_id" :value="__('Assignee')" />
                                <select id="assignee_id" name="assignee_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">Unassigned</option>
                                    @foreach($project->members as $member)
                                        <option value="{{ $member->id }}" {{ old('assignee_id', $task->assignee_id) == $member->id ? 'selected' : '' }}>
                                            {{ $member->first_name }} {{ $member->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-forms.input-error :messages="$errors->get('assignee_id')" class="mt-2" />
                            </div>

                            <!-- Parent Task -->
                            <div class="mb-4">
                                <x-forms.input-label for="parent_id" :value="__('Parent Task (Optional)')" />
                                <select id="parent_id" name="parent_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">None</option>
                                    @foreach($project->tasks as $t)
                                        @if($t->id !== $task->id) <!-- Prevent self-referencing -->
                                            <option value="{{ $t->id }}" {{ old('parent_id', $task->parent_id) == $t->id ? 'selected' : '' }}>
                                                {{ $t->title }} ({{ ucfirst($t->type) }})
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                                <x-forms.input-error :messages="$errors->get('parent_id')" class="mt-2" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Start Date -->
                            <div class="mb-4">
                                <x-forms.input-label for="start_date" :value="__('Start Date')" />
                                <x-forms.text-input id="start_date" class="block mt-1 w-full" type="date" name="start_date" :value="old('start_date', $task->start_date?->format('Y-m-d'))" />
                                <x-forms.input-error :messages="$errors->get('start_date')" class="mt-2" />
                            </div>

                            <!-- Due Date -->
                            <div class="mb-4">
                                <x-forms.input-label for="due_date" :value="__('Due Date')" />
                                <x-forms.text-input id="due_date" class="block mt-1 w-full" type="date" name="due_date" :value="old('due_date', $task->due_date?->format('Y-m-d'))" />
                                <x-forms.input-error :messages="$errors->get('due_date')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-between mt-4">
                             <button type="button" onclick="if(confirm('Are you sure you want to delete this task?')) document.getElementById('delete-task-form').submit()" class="text-red-600 hover:text-red-900">
                                Delete Task
                            </button>

                            <div class="flex items-center">
                                <a href="{{ route('projects.show', $project) }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                                <x-ui.primary-button>
                                    {{ __('Update Task') }}
                                </x-ui.primary-button>
                            </div>
                        </div>
                    </form>

                    <form id="delete-task-form" action="{{ route('projects.tasks.destroy', [$project, $task]) }}" method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
