@props(['paginator', 'showPerPage' => true])

@if ($paginator->total() > 0)
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 mt-6">
        <!-- Info Text & Per Page -->
        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-700">
            <!-- Info -->
            <p>
                {{ __('Hiển thị') }} 
                <span class="font-medium">{{ $paginator->firstItem() ?? 0 }}</span> 
                {{ __('đến') }} 
                <span class="font-medium">{{ $paginator->lastItem() ?? 0 }}</span> 
                {{ __('của') }} 
                <span class="font-medium">{{ $paginator->total() }}</span> 
                {{ __('mục') }}
            </p>
            
            <!-- Per Page Dropdown -->
            @if($showPerPage)
                <div class="flex items-center ml-2 border-l pl-4 border-gray-300">
                    <span class="mr-2">{{ __('Hiển thị') }}</span>
                    <select 
                        onchange="window.location.href = this.value" 
                        class="block w-20 px-2 py-1 text-sm border-gray-300 rounded-md focus:border-amber-500 focus:ring-amber-500 shadow-sm"
                    >
                        @foreach([10, 25, 50, 100] as $size)
                            <option 
                                value="{{ request()->fullUrlWithQuery(['perPage' => $size, 'page' => 1]) }}" 
                                {{ request('perPage', $paginator->perPage()) == $size ? 'selected' : '' }}
                            >
                                {{ $size }}
                            </option>
                        @endforeach
                    </select>
                    <span class="ml-2">{{ __('mục') }}</span>
                </div>
            @endif
        </div>

        <!-- Links -->
        @if ($paginator->hasPages())
            <div class="flex-shrink-0">
                {{ $paginator->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endif
