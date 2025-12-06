<div class="flex flex-col h-full">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-medium text-gray-900">Task Board</h3>
        <a href="{{ route('projects.tasks.create', $project) }}" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
            Create Task
        </a>
    </div>

    <div class="flex space-x-4 overflow-x-auto pb-4">
        @foreach(['todo' => 'To Do', 'in_progress' => 'In Progress', 'review' => 'Review', 'done' => 'Done'] as $status => $label)
            <div class="flex-shrink-0 w-80 bg-gray-100 rounded-lg p-4">
                <h4 class="font-semibold text-gray-700 mb-3 flex justify-between items-center">
                    {{ $label }}
                    <span class="bg-gray-200 text-gray-600 text-xs px-2 py-1 rounded-full">
                        {{ $tasks->where('status', $status)->count() }}
                    </span>
                </h4>
                
                <div class="space-y-3">
                    @forelse($tasks->where('status', $status) as $task)
                        <div class="bg-white p-3 rounded shadow-sm border border-gray-200 hover:shadow-md transition-shadow cursor-pointer">
                            <div class="flex justify-between items-start mb-2">
                                <span class="text-xs font-semibold px-2 py-0.5 rounded 
                                    {{ $task->type === 'bug' ? 'bg-red-100 text-red-800' : 
                                       ($task->type === 'epic' ? 'bg-purple-100 text-purple-800' : 
                                       ($task->type === 'story' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800')) }}">
                                    {{ ucfirst($task->type) }}
                                </span>
                                <div class="flex space-x-1">
                                    <a href="{{ route('projects.tasks.edit', [$project, $task]) }}" class="text-gray-400 hover:text-gray-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </a>
                                </div>
                            </div>
                            
                            <h5 class="text-sm font-medium text-gray-900 mb-1">{{ $task->title }}</h5>
                            
                            @if($task->parent)
                                <div class="text-xs text-gray-500 mb-2 flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                    {{ $task->parent->title }}
                                </div>
                            @endif

                            <div class="flex justify-between items-center mt-3">
                                <div class="flex items-center">
                                    @if($task->assignee)
                                        <img class="h-6 w-6 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($task->assignee->first_name . ' ' . $task->assignee->last_name) }}&size=24" title="{{ $task->assignee->first_name }}">
                                    @else
                                        <span class="h-6 w-6 rounded-full bg-gray-200 flex items-center justify-center text-xs text-gray-500">?</span>
                                    @endif
                                </div>
                                <span class="text-xs font-medium 
                                    {{ $task->priority === 'urgent' ? 'text-red-600' : 
                                       ($task->priority === 'high' ? 'text-orange-600' : 'text-gray-500') }}">
                                    {{ ucfirst($task->priority) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <!-- Empty state for column -->
                        <div class="text-center py-4 border-2 border-dashed border-gray-200 rounded-lg">
                            <p class="text-xs text-gray-400">No tasks</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</div>
