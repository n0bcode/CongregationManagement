@props(['if', 'values' => []])

<div x-data="{ 
    show: false,
    checkCondition() {
        const field = document.querySelector('[name=\'{{ $if }}\']');
        if (!field) return;
        
        const value = field.type === 'checkbox' ? field.checked : field.value;
        const targetValues = {{ json_encode($values) }};
        
        if (Array.isArray(targetValues) && targetValues.length > 0) {
            this.show = targetValues.includes(value);
        } else {
            this.show = !!value;
        }
    }
}" 
x-init="checkCondition(); $el.closest('form').addEventListener('change', () => checkCondition())"
x-show="show"
x-transition
class="space-y-4"
style="display: none;">
    {{ $slot }}
</div>
