@props([
    'disabled' => false,
    'containerClass' => '',
])

@php
    $rawClass = $attributes->get('class', '');
    $tokens = array_filter(preg_split('/\s+/', trim($rawClass)));

    $containerTokens = [];
    $inputTokens = [];

    foreach ($tokens as $token) {
        if (preg_match('/^(m[trblxy]?|w|max-w|min-w|col-span|inline|block|flex|grid)-?/', $token)) {
            $containerTokens[] = $token;
        } else {
            $inputTokens[] = $token;
        }
    }

    $extractedContainerClass = implode(' ', $containerTokens);
    $inputCustomClass = implode(' ', $inputTokens);

    $hasWidth = false;
    foreach ($containerTokens as $token) {
        if (preg_match('/^(w-|max-w-|min-w-)/', $token)) {
            $hasWidth = true;
            break;
        }
    }
    $defaultWidth = $hasWidth ? '' : 'w-full';

    $inputAttributes = $attributes->except(['class', 'type']);
@endphp

<div 
    x-data="{ show: false }" 
    class="relative {{ $defaultWidth }} {{ $extractedContainerClass }} {{ $containerClass }}"
>
    <input 
        {{ $disabled ? 'disabled' : '' }}
        :type="show ? 'text' : 'password'"
        type="password"
        {{ $inputAttributes->merge([
            'class' => 'border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-araucaria-500 dark:focus:border-araucaria-600 focus:ring-araucaria-500 dark:focus:ring-araucaria-600 rounded-lg shadow-sm w-full pr-10 ' . $inputCustomClass
        ]) }}
    />

    <button 
        type="button" 
        @click="show = !show"
        tabindex="-1"
        :aria-label="show ? '{{ __('Ocultar senha') }}' : '{{ __('Mostrar senha') }}'"
        :title="show ? '{{ __('Ocultar senha') }}' : '{{ __('Mostrar senha') }}'"
        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none focus:text-araucaria-600 dark:focus:text-araucaria-400 transition-colors duration-150 cursor-pointer {{ $disabled ? 'opacity-50 pointer-events-none' : '' }}"
    >
        {{-- Ícone quando oculto (clicar para mostrar) --}}
        <svg 
            x-show="!show" 
            class="h-5 w-5" 
            xmlns="http://www.w3.org/2000/svg" 
            fill="none" 
            viewBox="0 0 24 24" 
            stroke-width="1.5" 
            stroke="currentColor" 
            aria-hidden="true"
        >
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
        </svg>

        {{-- Ícone quando visível (clicar para ocultar) --}}
        <svg 
            x-show="show" 
            x-cloak 
            class="h-5 w-5" 
            xmlns="http://www.w3.org/2000/svg" 
            fill="none" 
            viewBox="0 0 24 24" 
            stroke-width="1.5" 
            stroke="currentColor" 
            aria-hidden="true"
        >
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
        </svg>
    </button>
</div>
