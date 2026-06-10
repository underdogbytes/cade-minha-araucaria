<div id="{{ $id ?? 'spinner' }}"
  class="absolute inset-0 bg-white/70 z-[1000] flex flex-col items-center justify-center transition-opacity duration-300">
  <div class="animate-spin rounded-full h-10 w-10 border-4 border-emerald-500 border-t-transparent"></div>
  <p class="text-emerald-800 font-semibold mt-2 text-sm">{{ $message ?? '' }}</p>
</div>