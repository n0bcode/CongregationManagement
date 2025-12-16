<div class="overflow-x-auto -mx-4 sm:mx-0">
    <div class="inline-block min-w-full align-middle">
        <div class="shadow-sm border border-stone-200 rounded-lg overflow-hidden">
            <table {{ $attributes->merge(['class' => 'min-w-full divide-y divide-stone-200']) }}>
                {{ $slot }}
            </table>
        </div>
    </div>
</div>
