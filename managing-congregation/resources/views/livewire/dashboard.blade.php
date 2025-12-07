<div wire:poll.300s>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-2xl font-semibold text-gray-900 mb-6">Dashboard</h1>
        
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3" id="dashboard-widgets" wire:ignore>
            @foreach($widgets as $widget)
                <div data-id="{{ get_class($widget) }}" class="{{ get_class($widget) === 'App\View\Components\Widgets\RecentActivityWidget' ? 'sm:col-span-2' : '' }}">
                    {{ $widget->render() }}
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
