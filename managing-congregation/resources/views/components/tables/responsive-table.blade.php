<div class="w-full">
    <!-- Desktop View (Table) -->
    <div class="hidden md:block bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                {{ $thead }}
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                {{ $tbody }}
            </tbody>
        </table>
    </div>

    <!-- Mobile View (Cards) -->
    <div class="md:hidden space-y-4">
        {{ $mobileContent }}
    </div>
</div>
