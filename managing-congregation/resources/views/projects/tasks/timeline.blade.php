<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h3 class="text-lg font-medium text-gray-900">Project Timeline</h3>
        <div class="flex space-x-2">
            <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-800">Story</span>
            <span class="px-2 py-1 text-xs rounded bg-purple-100 text-purple-800">Epic</span>
            <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-800">Bug</span>
        </div>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-md p-4 overflow-x-auto">
        <div class="min-w-[800px]">
            {{-- Timeline Header (Months/Weeks - Simplified for MVP) --}}
            <div class="flex border-b border-gray-200 pb-2 mb-4">
                <div class="w-1/4 font-semibold text-gray-500">Task</div>
                <div class="w-3/4 flex justify-between text-xs text-gray-400">
                    <span>Start</span>
                    <span>Duration</span>
                    <span>Due</span>
                </div>
            </div>

            {{-- Timeline Rows --}}
            <div class="space-y-4">
                @forelse($project->tasks->sortBy('start_date') as $task)
                    <div class="relative flex items-center group">
                        {{-- Task Info --}}
                        <div class="w-1/4 pr-4 truncate">
                            <div class="text-sm font-medium text-gray-900">{{ $task->title }}</div>
                            <div class="text-xs text-gray-500">{{ $task->assignee ? $task->assignee->first_name : 'Unassigned' }}</div>
                        </div>

                        {{-- Timeline Bar Container --}}
                        <div class="w-3/4 h-8 bg-gray-50 rounded relative flex items-center">
                            @php
                                // Calculate position and width based on project duration or fixed window
                                // For MVP, we'll just show a bar with start/end dates as text if visual calc is too complex without JS lib
                                // Let's try a simple percentage based on project start/end if available, else default
                                $projectStart = $project->start_date ? $project->start_date->timestamp : now()->subMonth()->timestamp;
                                $projectEnd = $project->end_date ? $project->end_date->timestamp : now()->addMonth()->timestamp;
                                $totalDuration = max($projectEnd - $projectStart, 86400); // Avoid div by zero

                                $taskStart = $task->start_date ? $task->start_date->timestamp : $projectStart;
                                $taskEnd = $task->due_date ? $task->due_date->timestamp : ($taskStart + 86400);
                                
                                // Clamp to project window for display
                                $startOffset = max(0, ($taskStart - $projectStart));
                                $duration = max(86400, ($taskEnd - $taskStart));
                                
                                $leftPercent = ($startOffset / $totalDuration) * 100;
                                $widthPercent = ($duration / $totalDuration) * 100;
                                
                                // Color based on type
                                $colorClass = match($task->type) {
                                    'epic' => 'bg-purple-500',
                                    'bug' => 'bg-red-500',
                                    default => 'bg-blue-500',
                                };
                            @endphp

                            <div class="absolute h-6 rounded {{ $colorClass }} opacity-75 hover:opacity-100 transition-opacity cursor-pointer"
                                 style="left: {{ min($leftPercent, 95) }}%; width: {{ min($widthPercent, 100 - min($leftPercent, 95)) }}%; min-width: 10px;"
                                 title="{{ $task->title }} ({{ $task->start_date?->format('M d') }} - {{ $task->due_date?->format('M d') }})">
                            </div>
                            
                            {{-- Date Labels for context --}}
                            <div class="absolute left-0 text-[10px] text-gray-400 -bottom-4">{{ $task->start_date?->format('M d') }}</div>
                            <div class="absolute right-0 text-[10px] text-gray-400 -bottom-4">{{ $task->due_date?->format('M d') }}</div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-gray-500 py-8">No tasks with dates found.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
