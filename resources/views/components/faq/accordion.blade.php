@props([
    'title' => "faq's",
    'subtitle' => null,
    'items' => [],
    'allowMultiple' => false,
    'defaultOpen' => 0,
    'id' => null,
])

@php
    $componentId = $id ?? 'faq-accordion-' . uniqid();
@endphp

<section 
    id="{{ $componentId }}"
    class="w-full mx-auto py-12 px-4 sm:px-6 lg:px-8 bg-[#1a1d17] text-neutral-200 shadow-2xl"
    role="region"
    aria-labelledby="{{ $componentId }}-heading"
>
    <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-12 gap-8 lg:gap-12 items-start "
         x-data="{
             openItems: {{ Js::from(is_numeric($defaultOpen) ? [(int)$defaultOpen] : (is_array($defaultOpen) ? $defaultOpen : [])) }},
             allowMultiple: {{ Js::from((bool)$allowMultiple) }},
             
             isOpen(index) {
                 return this.openItems.includes(index);
             },
             
             toggle(index) {
                 if (this.allowMultiple) {
                     if (this.isOpen(index)) {
                         this.openItems = this.openItems.filter(i => i !== index);
                     } else {
                         this.openItems.push(index);
                     }
                 } else {
                     this.openItems = this.isOpen(index) ? [] : [index];
                 }
             }
         }">
        
        <div class="md:col-span-4 lg:col-span-5 pr-4">
            <h2 id="{{ $componentId }}-heading" class="text-5xl sm:text-6xl font-light tracking-tight text-white font-serif lowercase">
                {{ $title }}
            </h2>
            @if($subtitle)
                <p class="mt-4 text-sm sm:text-base text-neutral-400 font-light leading-relaxed">
                    {{ $subtitle }}
                </p>
            @endif
        </div>

        <div class="md:col-span-8 lg:col-span-7 divide-y divide-neutral-800/80 border-t border-b border-neutral-800/80">
            @forelse($items as $index => $item)
                @php
                    $question = is_array($item) ? ($item['question'] ?? $item['quesiton'] ?? '') : ($item->question ?? '');
                    $answer = is_array($item) ? ($item['answer'] ?? '') : ($item->answer ?? '');
                @endphp

                <x-faq.item 
                    :question="$question"
                    :answer="$answer"
                    :index="$index"
                    :componentId="$componentId"
                />
            @empty
                @if(isset($slot) && $slot->isNotEmpty())
                    {{ $slot }}
                @else
                    <div class="py-6 text-neutral-500 text-sm italic">
                        Nenhuma pergunta frequente cadastrada.
                    </div>
                @endif
            @endforelse
        </div>
    </div>
</section>
