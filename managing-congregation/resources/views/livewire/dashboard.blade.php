<div wire:poll.300s>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Dashboard</h1>
            <a href="{{ route('financials.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Financial Overview
            </a>
        </div>
        
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3" id="dashboard-widgets" wire:ignore>
            @foreach($widgets as $widget)
                <div data-id="{{ get_class($widget) }}" class="{{ get_class($widget) === 'App\View\Components\Widgets\RecentActivityWidget' ? 'sm:col-span-2' : '' }}">
                    {!! $widget->render() !!}
                </div>
            @endforeach
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            var el = document.getElementById('dashboard-widgets');
            var sortable = Sortable.create(el, {
                animation: 150,
                onEnd: function (evt) {
                    var list = [];
                    el.querySelectorAll('[data-id]').forEach(function (node, index) {
                        list.push({
                            value: node.getAttribute('data-id'),
                            order: index + 1
                        });
                    });
                    @this.call('updateWidgetOrder', list);
                }
            });
        });
    </script>
</div>
