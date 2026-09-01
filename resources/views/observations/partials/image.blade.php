@php
  $photos = ($observation->photos && $observation->photos->isNotEmpty())
    ? $observation->photos->pluck('photo_path')->toArray()
    : ($observation->photo_path ? [$observation->photo_path] : []);
@endphp

@if(count($photos) > 0)
  <div x-data="{ activePhoto: '{{ addslashes($photos[0]) }}' }" class="w-full flex flex-col items-center space-y-3">
    <div class="relative w-full min-h-[300px] max-h-[450px] overflow-hidden rounded-xl shadow-md border border-gray-200 dark:border-gray-700 bg-gray-900/90 flex items-center justify-center">
      <img
        :src="activePhoto"
        alt="Foto da Araucária"
        class="w-full h-auto max-h-[450px] object-contain rounded-xl transition-all duration-300">
    </div>

    @if(count($photos) > 1)
      <div class="w-full flex items-center justify-center space-x-2 overflow-x-auto py-1">
        @foreach($photos as $index => $photo)
          <button
            type="button"
            @click="activePhoto = '{{ addslashes($photo) }}'"
            :class="activePhoto === '{{ addslashes($photo) }}' ? 'ring-2 ring-emerald-500 scale-105 opacity-100' : 'opacity-60 hover:opacity-100'"
            class="relative w-14 h-14 rounded-lg overflow-hidden border border-gray-300 dark:border-gray-600 transition shrink-0">
            <img src="{{ $photo }}" alt="Foto {{ $index + 1 }}" class="w-full h-full object-cover">
            <span class="absolute bottom-0 right-0 bg-black/70 text-[9px] text-white px-1 font-mono font-bold">{{ $index + 1 }}</span>
          </button>
        @endforeach
      </div>
    @endif
  </div>
@else
  <div class="text-gray-400 p-12 text-center">Nenhuma imagem registrada</div>
@endif