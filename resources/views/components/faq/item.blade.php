@props([
    'question',
    'answer' => null,
    'index' => 0,
    'componentId' => 'faq-accordion',
])

@php
    $buttonId = $componentId . '-btn-' . $index;
    $panelId = $componentId . '-panel-' . $index;
@endphp

<div class="py-6 transition-colors duration-200 border-b border-neutral-800/80 last:border-b-0">
    <h3>
        <button
            type="button"
            id="{{ $buttonId }}"
            class="w-full flex items-center justify-between text-left focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 focus-visible:ring-offset-[#1a1d17] rounded-sm group py-1"
            :aria-expanded="isOpen({{ $index }}) ? 'true' : 'false'"
            aria-controls="{{ $panelId }}"
            aria-label="Alternar resposta para: {{ e($question) }}"
            @click="toggle({{ $index }})"
        >
            <span class="text-xl sm:text-2xl font-light text-neutral-100 group-hover:text-emerald-400 transition-colors lowercase tracking-wide">
                {{ $question }}
            </span>
            
            <span class="ml-4 flex-shrink-0 text-neutral-400 group-hover:text-emerald-400 transition-colors duration-300">
                <svg 
                    class="w-5 h-5 transform transition-transform duration-300 ease-in-out"
                    :class="isOpen({{ $index }}) ? 'rotate-180 text-emerald-400' : 'rotate-0'"
                    xmlns="http://www.w3.org/2000/svg" 
                    fill="none" 
                    viewBox="0 0 24 24" 
                    stroke="currentColor"
                    stroke-width="1.5"
                    aria-hidden="true"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                </svg>
            </span>
        </button>
    </h3>

    <div
        id="{{ $panelId }}"
        role="region"
        aria-labelledby="{{ $buttonId }}"
        x-show="isOpen({{ $index }})"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-2 max-h-0"
        x-transition:enter-end="opacity-100 translate-y-0 max-h-[500px]"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 max-h-[500px]"
        x-transition:leave-end="opacity-0 -translate-y-2 max-h-0"
        class="overflow-hidden"
        style="display: none;"
    >
        <div class="pt-4 pb-2 text-sm sm:text-base text-neutral-400 font-light leading-relaxed lowercase whitespace-pre-line">
            @if(isset($slot) && $slot->isNotEmpty())
                {{ $slot }}
            @else
                {!! $answer !!}
            @endif
        </div>
    </div>
</div>
