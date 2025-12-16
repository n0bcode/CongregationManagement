<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="{{ __('Create Task') }}" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('projects.tasks.store', $project) }}" method="POST">
                        @csrf

                        <!-- Title -->
                        <div class="mb-4">
                            <x-forms.input-label for="title" :value="__('Title')" />
                            <x-forms.text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')" required autofocus />
                            <x-forms.input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <x-forms.input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="4">{{ old('description') }}</textarea>
                            <x-forms.input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Type -->
                            <div class="mb-4">
                                <x-forms.input-label for="type" :value="__('Type')" />
                                <select id="type" name="type" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="task" {{ old('type') == 'task' ? 'selected' : '' }}>Task</option>
                                    <option value="story" {{ old('type') == 'story' ? 'selected' : '' }}>Story</option>
                                    <option value="epic" {{ old('type') == 'epic' ? 'selected' : '' }}>Epic</option>
                                    <option value="bug" {{ old('type') == 'bug' ? 'selected' : '' }}>Bug</option>
                                </select>
                                <x-forms.input-error :messages="$errors->get('type')" class="mt-2" />
                            </div>

                            <!-- Priority -->
                            <div class="mb-4">
                                <x-forms.input-label for="priority" :value="__('Priority')" />
                                <select id="priority" name="priority" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                                    <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                </select>
                                <x-forms.input-error :messages="$errors->get('priority')" class="mt-2" />
                            </div>

                            <!-- Status -->
                            <div class="mb-4">
                                <x-forms.input-label for="status" :value="__('Status')" />
                                <select id="status" name="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="todo" {{ old('status') == 'todo' ? 'selected' : '' }}>To Do</option>
                                    <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="review" {{ old('status') == 'review' ? 'selected' : '' }}>Review</option>
                                    <option value="done" {{ old('status') == 'done' ? 'selected' : '' }}>Done</option>
                                </select>
                                <x-forms.input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>

                            <!-- Assignee -->
                            <div class="mb-4">
                                <x-forms.input-label for="assignee_id" :value="__('Assignee')" />
                                <select id="assignee_id" name="assignee_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">Unassigned</option>
                                    @foreach($project->members as $member)
                                        <option value="{{ $member->id }}" {{ old('assignee_id') == $member->id ? 'selected' : '' }}>
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
                                    @foreach($project->tasks as $task)
                                        <option value="{{ $task->id }}" {{ old('parent_id') == $task->id ? 'selected' : '' }}>
                                            {{ $task->title }} ({{ ucfirst($task->type) }})
                                        </option>
                                    @endforeach
                                </select>
                                <x-forms.input-error :messages="$errors->get('parent_id')" class="mt-2" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Start Date -->
                            <div class="mb-4">
                                <x-forms.input-label for="start_date" :value="__('Start Date')" />
                                <x-forms.text-input id="start_date" class="block mt-1 w-full" type="date" name="start_date" :value="old('start_date')" />
                                <x-forms.input-error :messages="$errors->get('start_date')" class="mt-2" />
                            </div>

                            <!-- Due Date -->
                            <div class="mb-4">
                                <x-forms.input-label for="due_date" :value="__('Due Date')" />
                                <x-forms.text-input id="due_date" class="block mt-1 w-full" type="date" name="due_date" :value="old('due_date')" />
                                <x-forms.input-error :messages="$errors->get('due_date')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('projects.show', $project) }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                            <x-ui.primary-button>
                                {{ __('Create Task') }}
                            </x-ui.primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
