@props([
    'disabled' => false,
    'containerClass' => '',
])

<x-utils.password-input :disabled="$disabled" :containerClass="$containerClass" {{ $attributes }}>
    {{ $slot ?? '' }}
</x-utils.password-input>
