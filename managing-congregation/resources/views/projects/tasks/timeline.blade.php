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
        <div class="min-w-[800px] relative">
            @php
                $projectStart = $project->start_date ? $project->start_date->startOfMonth() : now()->subMonth()->startOfMonth();
                $projectEnd = $project->end_date ? $project->end_date->endOfMonth() : now()->addMonth()->endOfMonth();
                
                // Ensure at least 3 months window
                if ($projectStart->diffInMonths($projectEnd) < 2) {
                    $projectEnd = $projectStart->copy()->addMonths(2)->endOfMonth();
                }

                $totalDuration = $projectEnd->timestamp - $projectStart->timestamp;
                $months = [];
                $current = $projectStart->copy();
                
                while ($current <= $projectEnd) {
                    $months[] = $current->copy();
                    $current->addMonth();
                }
            @endphp

            {{-- Timeline Header --}}
            <div class="flex border-b border-gray-200 pb-2 mb-4">
                <div class="w-64 flex-shrink-0 font-semibold text-gray-500 pl-2">Task</div>
                <div class="flex-1 relative h-6">
                    @foreach($months as $month)
                        @php
                            $left = (($month->timestamp - $projectStart->timestamp) / $totalDuration) * 100;
                        @endphp
                        @if($left < 98)
                            <div class="absolute text-xs text-gray-400 font-medium border-l border-gray-300 pl-1" 
                                 style="left: {{ $left }}%; top: 0;">
                                {{ $month->format('M Y') }}
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Timeline Rows --}}
            <div class="space-y-4 relative">
                {{-- Timeline Background (Grid & Today) --}}
                <div class="absolute top-0 bottom-0 right-0 left-64 pointer-events-none z-0">
                    {{-- Grid Lines --}}
                    @foreach($months as $month)
                        @php
                            $left = (($month->timestamp - $projectStart->timestamp) / $totalDuration) * 100;
                        @endphp
                        <div class="absolute top-0 bottom-0 border-l border-gray-100" style="left: {{ $left }}%"></div>
                    @endforeach

                    @php
                        // Calculate Today's position
                        $today = now()->timestamp;
                        $todayPercent = (($today - $projectStart->timestamp) / $totalDuration) * 100;
                    @endphp

                    {{-- Today Line --}}
                    @if($todayPercent >= 0 && $todayPercent <= 100)
                        <div class="absolute top-0 bottom-0 border-l-2 border-red-400 border-dashed z-10" 
                             style="left: {{ $todayPercent }}%">
                            <span class="absolute -top-6 -left-1/2 transform -translate-x-1/2 bg-red-100 text-red-800 text-[10px] px-1 rounded whitespace-nowrap">Today</span>
                        </div>
                    @endif
                </div>

                @forelse($project->tasks->sortBy('start_date') as $task)
                    <div class="relative flex items-center group z-10 hover:bg-gray-50 rounded-lg -mx-2 px-2 py-1 transition-colors">
                        {{-- Task Info --}}
                        <div class="w-64 flex-shrink-0 pr-4 truncate border-r border-gray-100 mr-0">
                            <div class="text-sm font-medium text-gray-900 truncate" title="{{ $task->title }}">{{ $task->title }}</div>
                            <div class="flex items-center space-x-2 mt-1">
                                @if($task->assignee)
                                    <img class="h-4 w-4 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($task->assignee->first_name) }}&size=16" alt="">
                                @endif
                                <span class="text-xs text-gray-500">{{ $task->assignee ? $task->assignee->first_name : 'Unassigned' }}</span>
                            </div>
                        </div>

                        {{-- Timeline Bar Container --}}
                        <div class="flex-1 h-8 relative flex items-center">
                            @php
                                $taskStart = $task->start_date ? $task->start_date->timestamp : $projectStart->timestamp;
                                $taskEnd = $task->due_date ? $task->due_date->timestamp : ($taskStart + 86400);
                                
                                // Clamp to project window
                                $startOffset = max(0, ($taskStart - $projectStart->timestamp));
                                $duration = max(86400, ($taskEnd - $taskStart));
                                
                                $leftPercent = ($startOffset / $totalDuration) * 100;
                                $widthPercent = ($duration / $totalDuration) * 100;
                                
                                $colorClass = match($task->type) {
                                    'epic' => 'bg-purple-500 border-purple-600',
                                    'bug' => 'bg-red-500 border-red-600',
                                    'story' => 'bg-green-500 border-green-600',
                                    default => 'bg-blue-500 border-blue-600',
                                };
                            @endphp

                            <div class="absolute h-5 rounded-md {{ $colorClass }} border shadow-sm opacity-80 hover:opacity-100 transition-all cursor-pointer group-hover:shadow-md"
                                 style="left: {{ min($leftPercent, 98) }}%; width: {{ min($widthPercent, 100 - min($leftPercent, 98)) }}%; min-width: 4px;"
                                 title="{{ $task->title }}&#013;Start: {{ $task->start_date?->format('M d') }}&#013;Due: {{ $task->due_date?->format('M d') }}">
                                 
                                 {{-- Label inside bar if wide enough --}}
                                 @if($widthPercent > 10)
                                    <span class="text-[10px] text-white font-medium px-2 truncate block w-full leading-5">
                                        {{ $task->title }}
                                    </span>
                                 @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-gray-500 py-8">No tasks with dates found.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
