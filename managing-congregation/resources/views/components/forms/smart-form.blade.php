<form 
    action="{{ $action }}" 
    method="{{ $method === 'GET' ? 'GET' : 'POST' }}"
    @if($hasFiles) enctype="multipart/form-data" @endif
    id="{{ $id }}"
    {{ $attributes->merge(['class' => 'space-y-6']) }}
    x-data="smartForm"
    @submit="isSubmitting = true"
    @change="hasUnsavedChanges = true"
>
    @csrf
    @if(!in_array($method, ['GET', 'POST']))
        @method($method)
    @endif

    {{ $slot }}

    <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
        {{ $actions ?? '' }}
    </div>
</form>
