@props([
    'stickyHeader' => false, // NEW: Sticky table header
    'striped' => false, // NEW: Zebra striping
])

@php
    $tableClasses = 'min-w-full divide-y divide-stone-200';
    
    if ($striped) {
        $tableClasses .= ' [&_tbody_tr:nth-child(even)]:bg-stone-50';
    }
@endphp

<div class="overflow-x-auto -mx-4 sm:mx-0">
    <div class="inline-block min-w-full align-middle">
        <div class="shadow-sm border border-stone-200 rounded-lg overflow-hidden {{ $stickyHeader ? 'max-h-[600px] overflow-y-auto' : '' }}">
            <table {{ $attributes->merge(['class' => $tableClasses]) }}>
                {{ $slot }}
            </table>
        </div>
    </div>
</div>
