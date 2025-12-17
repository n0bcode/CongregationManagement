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
            <div class="space-y-2 relative" id="timeline-rows">
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

                @php
                    // Epic-centric view: Only show epics and their children
                    $epics = $project->tasks->where('type', 'epic')->sortBy('start_date');
                    $orphanTasks = $project->tasks->whereNull('parent_id')->where('type', '!=', 'epic')->sortBy('start_date');
                    
                    // Calculate epic date ranges based on children
                    foreach ($epics as $epic) {
                        if ($epic->children->count() > 0) {
                            $childrenWithDates = $epic->children->filter(fn($c) => $c->start_date && $c->due_date);
                            if ($childrenWithDates->count() > 0) {
                                $minStart = $childrenWithDates->min('start_date');
                                $maxEnd = $childrenWithDates->max('due_date');
                                
                                // Update epic dates to span children if not set or children extend beyond
                                if (!$epic->start_date || $minStart < $epic->start_date) {
                                    $epic->calculated_start = $minStart;
                                } else {
                                    $epic->calculated_start = $epic->start_date;
                                }
                                
                                if (!$epic->due_date || $maxEnd > $epic->due_date) {
                                    $epic->calculated_end = $maxEnd;
                                } else {
                                    $epic->calculated_end = $epic->due_date;
                                }
                            }
                        }
                    }
                @endphp

                @foreach($epics as $epic)
                    {{-- Epic Row --}}
                    @php
                        // Use calculated dates if available (spans children)
                        $epicStart = $epic->calculated_start ?? $epic->start_date;
                        $epicEnd = $epic->calculated_end ?? $epic->due_date;
                        
                        $taskStart = $epicStart ? $epicStart->timestamp : $projectStart->timestamp;
                        $taskEnd = $epicEnd ? $epicEnd->timestamp : ($taskStart + 86400);
                        $startOffset = max(0, ($taskStart - $projectStart->timestamp));
                        $duration = max(86400, ($taskEnd - $taskStart));
                        $leftPercent = ($startOffset / $totalDuration) * 100;
                        $widthPercent = ($duration / $totalDuration) * 100;
                        
                        $childCount = $epic->children->count();
                    @endphp

                    <div class="relative flex items-center group z-10 hover:bg-gray-50 rounded-lg -mx-2 px-2 py-2 transition-colors epic-row" data-epic-id="{{ $epic->id }}">
                        {{-- Task Info --}}
                        <div class="w-64 flex-shrink-0 pr-4 border-r border-gray-100 mr-0 flex items-center">
                            @if($epic->children->count() > 0)
                                <button class="expand-toggle mr-2 text-gray-500 hover:text-gray-700 focus:outline-none" data-epic-id="{{ $epic->id }}">
                                    <svg class="w-4 h-4 transform transition-transform" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            @else
                                <span class="w-4 mr-2"></span>
                            @endif
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-semibold text-gray-900 truncate" title="{{ $epic->title }}">{{ $epic->title }}</div>
                                <div class="flex items-center space-x-2 mt-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                        Epic
                                    </span>
                                    @if($childCount > 0)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700">
                                            {{ $childCount }} {{ Str::plural('task', $childCount) }}
                                        </span>
                                    @endif
                                    @if($epic->assignee)
                                        <span class="text-xs text-gray-500">{{ $epic->assignee->first_name }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Timeline Bar Container --}}
                        <div class="flex-1 h-10 relative flex items-center timeline-container">
                            <div class="timeline-bar epic-bar absolute h-6 rounded-md bg-purple-500 border-purple-600 border shadow-sm opacity-80 hover:opacity-100 transition-all cursor-move group-hover:shadow-md"
                                 style="left: {{ min($leftPercent, 98) }}%; width: {{ min($widthPercent, 100 - min($leftPercent, 98)) }}%; min-width: 4px;"
                                 data-task-id="{{ $epic->id }}"
                                 data-start="{{ $epic->start_date?->format('Y-m-d') }}"
                                 data-end="{{ $epic->due_date?->format('Y-m-d') }}"
                                 title="{{ $epic->title }}&#013;Start: {{ $epic->start_date?->format('M d') }}&#013;Due: {{ $epic->due_date?->format('M d') }}">
                                 
                                 @if($widthPercent > 10)
                                    <span class="text-[10px] text-white font-semibold px-2 truncate block w-full leading-6 pointer-events-none">
                                        {{ $epic->title }}
                                    </span>
                                 @endif
                            </div>
                        </div>
                    </div>

                    {{-- Child Tasks --}}
                    @if($epic->children->count() > 0)
                        <div class="epic-children" data-parent="{{ $epic->id }}">
                            @foreach($epic->children->sortBy('start_date') as $index => $child)
                                @php
                                    $taskStart = $child->start_date ? $child->start_date->timestamp : $projectStart->timestamp;
                                    $taskEnd = $child->due_date ? $child->due_date->timestamp : ($taskStart + 86400);
                                    $startOffset = max(0, ($taskStart - $projectStart->timestamp));
                                    $duration = max(86400, ($taskEnd - $taskStart));
                                    $leftPercent = ($startOffset / $totalDuration) * 100;
                                    $widthPercent = ($duration / $totalDuration) * 100;
                                    $colorClass = match($child->type) {
                                        'story' => 'bg-green-500 border-green-600',
                                        'bug' => 'bg-red-500 border-red-600',
                                        default => 'bg-blue-500 border-blue-600',
                                    };
                                    $isLast = $index === $epic->children->count() - 1;
                                @endphp

                                <div class="relative flex items-center group z-10 hover:bg-gray-50 rounded-lg -mx-2 px-2 py-1 transition-colors child-row">
                                    {{-- Task Info --}}
                                    <div class="w-64 flex-shrink-0 pr-4 border-r border-gray-100 mr-0 flex items-center pl-6">
                                        <span class="text-gray-400 mr-2 text-xs">{{ $isLast ? '└─' : '├─' }}</span>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-sm font-medium text-gray-900 truncate" title="{{ $child->title }}">{{ $child->title }}</div>
                                            <div class="flex items-center space-x-2 mt-1">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                    {{ $child->type === 'story' ? 'bg-green-100 text-green-800' : 
                                                       ($child->type === 'bug' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                                                    {{ ucfirst($child->type) }}
                                                </span>
                                                @if($child->assignee)
                                                    <span class="text-xs text-gray-500">{{ $child->assignee->first_name }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Timeline Bar Container --}}
                                    <div class="flex-1 h-8 relative flex items-center timeline-container">
                                        <div class="timeline-bar child-bar absolute h-4 rounded-md {{ $colorClass }} border shadow-sm opacity-80 hover:opacity-100 transition-all cursor-move group-hover:shadow-md"
                                             style="left: {{ min($leftPercent, 98) }}%; width: {{ min($widthPercent, 100 - min($leftPercent, 98)) }}%; min-width: 4px;"
                                             data-task-id="{{ $child->id }}"
                                             data-start="{{ $child->start_date?->format('Y-m-d') }}"
                                             data-end="{{ $child->due_date?->format('Y-m-d') }}"
                                             title="{{ $child->title }}&#013;Start: {{ $child->start_date?->format('M d') }}&#013;Due: {{ $child->due_date?->format('M d') }}">
                                             
                                             @if($widthPercent > 10)
                                                <span class="text-[10px] text-white font-medium px-2 truncate block w-full leading-4 pointer-events-none">
                                                    {{ $child->title }}
                                                </span>
                                             @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @endforeach

                {{-- Orphan Tasks (no parent) - Collapsible Section --}}
                @if($orphanTasks->count() > 0)
                    <div class="mt-6 border-t border-gray-200 pt-4">
                        <button class="flex items-center space-x-2 text-sm font-medium text-gray-700 hover:text-gray-900 mb-2" 
                                onclick="document.getElementById('orphan-tasks').classList.toggle('hidden')">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                            <span>Unassigned Tasks ({{ $orphanTasks->count() }})</span>
                        </button>
                        
                        <div id="orphan-tasks" class="space-y-2">
                            @foreach($orphanTasks as $task)
                                @php
                                    $taskStart = $task->start_date ? $task->start_date->timestamp : $projectStart->timestamp;
                                    $taskEnd = $task->due_date ? $task->due_date->timestamp : ($taskStart + 86400);
                                    $startOffset = max(0, ($taskStart - $projectStart->timestamp));
                                    $duration = max(86400, ($taskEnd - $taskStart));
                                    $leftPercent = ($startOffset / $totalDuration) * 100;
                                    $widthPercent = ($duration / $totalDuration) * 100;
                                    $colorClass = match($task->type) {
                                        'story' => 'bg-green-500 border-green-600',
                                        'bug' => 'bg-red-500 border-red-600',
                                        default => 'bg-blue-500 border-blue-600',
                                    };
                                @endphp

                                <div class="relative flex items-center group z-10 hover:bg-gray-50 rounded-lg -mx-2 px-2 py-1 transition-colors">
                                    {{-- Task Info --}}
                                    <div class="w-64 flex-shrink-0 pr-4 truncate border-r border-gray-100 mr-0">
                                        <div class="text-sm font-medium text-gray-900 truncate" title="{{ $task->title }}">{{ $task->title }}</div>
                                        <div class="flex items-center space-x-2 mt-1">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                {{ $task->type === 'story' ? 'bg-green-100 text-green-800' : 
                                                   ($task->type === 'bug' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                                                {{ ucfirst($task->type) }}
                                            </span>
                                            @if($task->assignee)
                                                <span class="text-xs text-gray-500">{{ $task->assignee->first_name }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Timeline Bar Container --}}
                                    <div class="flex-1 h-8 relative flex items-center timeline-container">
                                        <div class="timeline-bar absolute h-5 rounded-md {{ $colorClass }} border shadow-sm opacity-80 hover:opacity-100 transition-all cursor-move group-hover:shadow-md"
                                             style="left: {{ min($leftPercent, 98) }}%; width: {{ min($widthPercent, 100 - min($leftPercent, 98)) }}%; min-width: 4px;"
                                             data-task-id="{{ $task->id }}"
                                             data-start="{{ $task->start_date?->format('Y-m-d') }}"
                                             data-end="{{ $task->due_date?->format('Y-m-d') }}"
                                             title="{{ $task->title }}&#013;Start: {{ $task->start_date?->format('M d') }}&#013;Due: {{ $task->due_date?->format('M d') }}">
                                             
                                             @if($widthPercent > 10)
                                                <span class="text-[10px] text-white font-medium px-2 truncate block w-full leading-5 pointer-events-none">
                                                    {{ $task->title }}
                                                </span>
                                             @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($epics->count() === 0 && $orphanTasks->count() === 0)
                    <div class="text-center text-gray-500 py-8">No tasks with dates found.</div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Interact.js Library -->
<script src="https://cdn.jsdelivr.net/npm/interactjs@1.10.19/dist/interact.min.js"></script>

<!-- Custom Styles -->
<style>
.timeline-bar {
    touch-action: none;
    user-select: none;
}

.timeline-bar:hover {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2) !important;
}

.timeline-bar.dragging {
    opacity: 0.7 !important;
    z-index: 100;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3) !important;
}

/* Hierarchy Styles */
.epic-row {
    background-color: #f9fafb;
}

.epic-row:hover {
    background-color: #f3f4f6;
}

.epic-bar {
    height: 24px !important;
}

.child-row {
    background-color: #ffffff;
}

.child-bar {
    height: 16px !important;
}

/* Expand/collapse button */
.expand-toggle {
    transition: transform 0.2s;
}

.expand-toggle:hover {
    transform: scale(1.1);
}

.expand-toggle.collapsed svg {
    transform: rotate(-90deg);
}

/* Smooth expand/collapse animation */
.epic-children {
    overflow: hidden;
    transition: all 0.3s ease-out;
}

.epic-children.collapsed {
    max-height: 0;
    opacity: 0;
}

/* Phase 3A: Quick Actions Styles */
.quick-create-btn {
    transition: opacity 0.2s, background-color 0.2s;
}

.status-dropdown {
    font-size: 0.75rem;
    transition: border-color 0.2s;
}

.status-dropdown:focus {
    outline: none;
    border-color: #6366f1;
}

.priority-high {
    border-left: 4px solid #ef4444 !important;
}

.priority-medium {
    border-left: 4px solid #f59e0b !important;
}

.priority-low {
    border-left: 4px solid #10b981 !important;
}
</style>

<!-- Drag & Drop Logic -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const projectStart = {{ $projectStart->timestamp }};
    const totalDuration = {{ $totalDuration }};
    const projectId = {{ $project->id }};
    
    // ===== Expand/Collapse Logic =====
    const storageKey = `timeline-collapsed-epics-${projectId}`;
    
    // Load collapsed state from localStorage
    function getCollapsedEpics() {
        try {
            return JSON.parse(localStorage.getItem(storageKey) || '[]');
        } catch (e) {
            return [];
        }
    }
    
    function saveCollapsedEpics(epicIds) {
        localStorage.setItem(storageKey, JSON.stringify(epicIds));
    }
    
    function toggleEpic(epicId) {
        const button = document.querySelector(`[data-epic-id="${epicId}"].expand-toggle`);
        const children = document.querySelector(`[data-parent="${epicId}"]`);
        
        if (!children || !button) return;
        
        const collapsedEpics = getCollapsedEpics();
        const isCollapsed = collapsedEpics.includes(epicId);
        
        if (isCollapsed) {
            // Expand
            children.classList.remove('collapsed');
            button.classList.remove('collapsed');
            const newCollapsed = collapsedEpics.filter(id => id !== epicId);
            saveCollapsedEpics(newCollapsed);
        } else {
            // Collapse
            children.classList.add('collapsed');
            button.classList.add('collapsed');
            collapsedEpics.push(epicId);
            saveCollapsedEpics(collapsedEpics);
        }
    }
    
    // Apply initial collapsed state
    const collapsedEpics = getCollapsedEpics();
    collapsedEpics.forEach(epicId => {
        const children = document.querySelector(`[data-parent="${epicId}"]`);
        const button = document.querySelector(`[data-epic-id="${epicId}"].expand-toggle`);
        if (children) children.classList.add('collapsed');
        if (button) button.classList.add('collapsed');
    });
    
    // Handle toggle button clicks
    document.querySelectorAll('.expand-toggle').forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            const epicId = parseInt(this.dataset.epicId);
            toggleEpic(epicId);
        });
    });
    
    // ===== Drag & Drop Logic =====
    // Make timeline bars draggable and resizable
    interact('.timeline-bar')
        .draggable({
            axis: 'x',
            modifiers: [
                interact.modifiers.restrict({
                    restriction: 'parent',
                    elementRect: { left: 0, right: 1 }
                })
            ],
            listeners: {
                start: function(event) {
                    event.target.classList.add('dragging');
                },
                move: function(event) {
                    const target = event.target;
                    const x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx;
                    
                    target.style.transform = `translateX(${x}px)`;
                    target.setAttribute('data-x', x);
                },
                end: function(event) {
                    const target = event.target;
                    target.classList.remove('dragging');
                    
                    const x = parseFloat(target.getAttribute('data-x')) || 0;
                    
                    if (Math.abs(x) < 5) {
                        // Too small movement, ignore
                        target.style.transform = '';
                        target.removeAttribute('data-x');
                        return;
                    }
                    
                    // Calculate new dates
                    const parent = target.closest('.timeline-container');
                    const parentWidth = parent.offsetWidth;
                    const percentShift = (x / parentWidth) * 100;
                    const timeShift = (percentShift / 100) * totalDuration;
                    
                    const taskId = target.dataset.taskId;
                    const oldStart = new Date(target.dataset.start);
                    const oldEnd = new Date(target.dataset.end);
                    
                    // Validate dates
                    if (!target.dataset.start || !target.dataset.end || isNaN(oldStart.getTime()) || isNaN(oldEnd.getTime())) {
                        target.style.transform = '';
                        target.removeAttribute('data-x');
                        showToast('Cannot move task - invalid dates', 'error');
                        return;
                    }
                    
                    const newStart = new Date(oldStart.getTime() + timeShift * 1000);
                    const newEnd = new Date(oldEnd.getTime() + timeShift * 1000);
                    
                    // Reset transform immediately to prevent sticking
                    target.style.transform = '';
                    target.removeAttribute('data-x');
                    
                    // Update via AJAX
                    updateTaskDates(taskId, newStart, newEnd, target);
                }
            }
        })
        .resizable({
            edges: { left: true, right: true },
            modifiers: [
                interact.modifiers.restrictSize({
                    min: { width: 20 }
                })
            ],
            listeners: {
                start: function(event) {
                    event.target.classList.add('dragging');
                },
                move: function(event) {
                    const target = event.target;
                    let x = parseFloat(target.getAttribute('data-x')) || 0;
                    
                    // Update position and width
                    if (event.edges.left) {
                        x += event.deltaRect.left;
                    }
                    
                    const newWidth = event.rect.width;
                    
                    target.style.width = `${newWidth}px`;
                    target.style.transform = `translateX(${x}px)`;
                    target.setAttribute('data-x', x);
                },
                end: function(event) {
                    const target = event.target;
                    target.classList.remove('dragging');
                    
                    const x = parseFloat(target.getAttribute('data-x')) || 0;
                    const newWidth = parseFloat(target.style.width);
                    const oldWidth = target.offsetWidth;
                    
                    // Calculate new dates
                    const parent = target.closest('.timeline-container');
                    const parentWidth = parent.offsetWidth;
                    
                    const leftShiftPercent = (x / parentWidth) * 100;
                    const widthChangePercent = ((newWidth - oldWidth) / parentWidth) * 100;
                    
                    const taskId = target.dataset.taskId;
                    const oldStart = new Date(target.dataset.start);
                    const oldEnd = new Date(target.dataset.end);
                    
                    // Validate dates
                    if (!target.dataset.start || !target.dataset.end || isNaN(oldStart.getTime()) || isNaN(oldEnd.getTime())) {
                        target.style.transform = '';
                        target.style.width = '';
                        target.removeAttribute('data-x');
                        showToast('Cannot resize task - invalid dates', 'error');
                        return;
                    }
                    
                    const startShift = (leftShiftPercent / 100) * totalDuration;
                    const durationChange = (widthChangePercent / 100) * totalDuration;
                    
                    let newStart = new Date(oldStart.getTime() + startShift * 1000);
                    let newEnd = new Date(oldEnd.getTime() + durationChange * 1000);
                    
                    // Ensure end is after start
                    if (newEnd <= newStart) {
                        newEnd = new Date(newStart.getTime() + 86400000); // +1 day
                    }
                    
                    // Reset transform and width immediately to prevent sticking
                    target.style.transform = '';
                    target.style.width = '';
                    target.removeAttribute('data-x');
                    
                    // Update via AJAX
                    updateTaskDates(taskId, newStart, newEnd, target);
                }
            }
        });
    
    function updateTaskDates(taskId, startDate, endDate, element) {
        // Validate dates
        if (!startDate || !endDate || isNaN(startDate.getTime()) || isNaN(endDate.getTime())) {
            showToast('Invalid dates - task may not have start/end dates set', 'error');
            element.style.transform = '';
            element.style.width = '';
            element.removeAttribute('data-x');
            return;
        }
        
        fetch(`/projects/${projectId}/tasks/${taskId}/dates`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                start_date: startDate.toISOString().split('T')[0],
                due_date: endDate.toISOString().split('T')[0]
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Task dates updated successfully', 'success');
                // Reload to refresh positions
                setTimeout(() => location.reload(), 800);
            } else {
                throw new Error(data.message || 'Failed to update');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast(error.message || 'Failed to update task dates', 'error');
            // Revert visual changes
            element.style.transform = '';
            element.style.width = '';
            element.removeAttribute('data-x');
        });
    }
    
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white z-50 ${
            type === 'success' ? 'bg-green-500' : 'bg-red-500'
        }`;
        toast.textContent = message;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 0.3s';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
});
</script>
