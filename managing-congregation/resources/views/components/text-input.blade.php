@props(['disabled' => false])

@php
$hasError = false;
if ($attributes->has('name')) {
    $name = $attributes->get('name');
    $errorKey = str_replace(['[', ']'], ['.', ''], $name);
    $hasError = isset($errors) && $errors->has($errorKey);
    // dump(['name' => $name, 'errorKey' => $errorKey, 'hasError' => $hasError, 'errors' => isset($errors) ? $errors->keys() : 'null']);
}
@endphp

<input @disabled($disabled) {{ $attributes->merge([
    'class' => 'border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-sanctuary-blue dark:focus:border-indigo-600 focus:ring-sanctuary-blue dark:focus:ring-indigo-600 rounded-md shadow-sm',
    'aria-invalid' => $hasError ? 'true' : 'false',
    'aria-describedby' => $hasError && isset($name) ? "{$name}-error" : null,
]) }}>
