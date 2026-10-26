@php
  $photos = ($observation->photos && $observation->photos->isNotEmpty())
    ? $observation->photos->pluck('photo_path')->values()->toArray()
    : ($observation->photo_path ? [$observation->photo_path] : []);
  $photosCount = count($photos);
@endphp

@if($photosCount > 0)
  <div
    x-data="{
      originalPhotos: {{ Js::from($photos) }},
      photos: {{ Js::from($photos) }},
      currentIndex: 0,
      isLightboxOpen: false,
      touchStartX: 0,
      touchStartY: 0,
      isDragging: false,
      openLightbox(index = 0) {
        if (typeof index === 'number' && index >= 0 && index < this.photos.length) {
          this.currentIndex = index;
        }
        this.isLightboxOpen = true;
      },
      closeLightbox() {
        this.isLightboxOpen = false;
        if (this.photos !== this.originalPhotos) {
          this.photos = [...this.originalPhotos];
        }
      },
      nextPhoto() {
        if (this.photos.length > 1) {
          this.currentIndex = (this.currentIndex + 1) % this.photos.length;
        }
      },
      prevPhoto() {
        if (this.photos.length > 1) {
          this.currentIndex = (this.currentIndex - 1 + this.photos.length) % this.photos.length;
        }
      },
      handleStart(e) {
        this.isDragging = true;
        const clientX = e.touches ? e.touches[0].clientX : e.clientX;
        const clientY = e.touches ? e.touches[0].clientY : e.clientY;
        this.touchStartX = clientX;
        this.touchStartY = clientY;
      },
      handleEnd(e) {
        if (!this.isDragging) return;
        this.isDragging = false;
        const clientX = e.changedTouches ? e.changedTouches[0].clientX : e.clientX;
        const clientY = e.changedTouches ? e.changedTouches[0].clientY : e.clientY;
        const diffX = clientX - this.touchStartX;
        const diffY = clientY - this.touchStartY;
        if (Math.abs(diffX) > 40 && Math.abs(diffX) > Math.abs(diffY)) {
          if (diffX > 0) {
            this.prevPhoto();
          } else {
            this.nextPhoto();
          }
        }
      }
    }"
    x-effect="document.body.classList.toggle('overflow-hidden', isLightboxOpen)"
    @keydown.escape.window="if (isLightboxOpen) closeLightbox()"
    @keydown.arrow-left.window="if (isLightboxOpen) prevPhoto()"
    @keydown.arrow-right.window="if (isLightboxOpen) nextPhoto()"
    @open-zoom-modal.window="
      if ($event.detail && $event.detail.photos) {
        photos = $event.detail.photos;
        currentIndex = $event.detail.index || 0;
        isLightboxOpen = true;
      } else if ($event.detail && $event.detail.photo) {
        photos = [$event.detail.photo];
        currentIndex = 0;
        isLightboxOpen = true;
      }
    "
    class="w-full flex flex-col items-center space-y-3">

    <!-- Card da Imagem Principal -->
    <div
      class="relative w-full min-h-[300px] max-h-[460px] overflow-hidden rounded-xl shadow-md border border-gray-200 dark:border-gray-700 bg-gray-900/90 flex items-center justify-center cursor-zoom-in group select-none"
      @click="openLightbox(currentIndex)"
      @touchstart="handleStart($event)"
      @touchend="handleEnd($event)">

      <img
        :src="photos[currentIndex]"
        draggable="false"
        alt="Foto da Araucária"
        class="w-full h-auto max-h-[460px] object-contain rounded-xl transition-transform duration-300 group-hover:scale-[1.015]">

      <!-- Ícone no canto da imagem indicando que é possível zoom -->
      <button
        type="button"
        @click.stop="openLightbox(currentIndex)"
        class="absolute top-3 right-3 z-10 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-black/60 hover:bg-black/80 active:scale-95 text-white text-xs font-semibold backdrop-blur-md shadow-lg border border-white/20 transition-all duration-200 cursor-pointer group/btn"
        title="Clique para ampliar a foto (Zoom)"
        aria-label="Ampliar foto em tela cheia">
        <svg class="w-4 h-4 text-emerald-400 group-hover/btn:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7" />
        </svg>
        <span class="text-[11px] font-medium tracking-wide">Ampliar</span>
      </button>

      <!-- Badge de Contagem no canto inferior esquerdo da imagem -->
      <span
        class="absolute bottom-3 left-3 z-10 bg-black/60 backdrop-blur-md text-white text-[11px] font-semibold px-2.5 py-1 rounded-lg border border-white/10 pointer-events-none">
        {{ $photosCount > 1 ? $photosCount . ' Fotos Registradas' : 'Foto Original do Registro' }}
      </span>

      <!-- Indicador numérico em mobile quando há múltiplas fotos -->
      <template x-if="photos.length > 1">
        <div class="sm:hidden absolute bottom-3 right-3 z-10 bg-black/60 backdrop-blur-md text-white text-[10px] font-mono font-semibold px-2 py-0.5 rounded-md border border-white/10 pointer-events-none">
          <span x-text="(currentIndex + 1) + ' / ' + photos.length"></span>
        </div>
      </template>
    </div>

    <!-- Miniaturas de seleção (caso tenha mais de uma foto) -->
    @if($photosCount > 1)
      <div class="w-full flex items-center justify-center space-x-2 overflow-x-auto py-1">
        @foreach($photos as $index => $photo)
          <button
            type="button"
            @click="currentIndex = {{ $index }}"
            :class="currentIndex === {{ $index }} ? 'ring-2 ring-emerald-500 scale-105 opacity-100 shadow-md' : 'opacity-60 hover:opacity-100 hover:scale-102'"
            class="relative w-14 h-14 rounded-lg overflow-hidden border border-gray-300 dark:border-gray-600 transition shrink-0 cursor-pointer">
            <img src="{{ $photo }}" alt="Foto {{ $index + 1 }}" class="w-full h-full object-cover">
            <span class="absolute bottom-0 right-0 bg-black/70 text-[9px] text-white px-1 font-mono font-bold">{{ $index + 1 }}</span>
          </button>
        @endforeach
      </div>
    @endif

    <!-- Modal Lightbox em Tela Cheia (Fundo semitransparente preto, centralizado, pegando toda a altura) -->
    <template x-teleport="body">
      <div
        x-show="isLightboxOpen"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="closeLightbox()"
        @touchstart="handleStart($event)"
        @touchend="handleEnd($event)"
        @mousedown="handleStart($event)"
        @mouseup="handleEnd($event)"
        class="fixed inset-0 z-[100] bg-black/90 sm:bg-black/95 backdrop-blur-xs flex items-center justify-center select-none p-3 sm:p-6"
        style="margin: 0;">

        <!-- Botão X no topo direito para fechar -->
        <button
          type="button"
          @click.stop="closeLightbox()"
          class="absolute top-4 right-4 sm:top-6 sm:right-6 z-50 p-2.5 sm:p-3 rounded-full bg-white/10 hover:bg-white/25 active:scale-95 text-white/90 hover:text-white transition-all backdrop-blur-md border border-white/20 shadow-2xl focus:outline-none cursor-pointer"
          title="Fechar (Esc)"
          aria-label="Fechar visualização">
          <svg class="w-6 h-6 sm:w-7 sm:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>

        <!-- Seta para a Esquerda (Foto Anterior) caso tenha mais de uma -->
        <template x-if="photos.length > 1">
          <button
            type="button"
            @click.stop="prevPhoto()"
            class="absolute left-2 sm:left-6 top-1/2 -translate-y-1/2 z-50 p-2.5 sm:p-4 rounded-full bg-black/50 hover:bg-black/80 active:scale-90 text-white/90 hover:text-white transition-all backdrop-blur-md border border-white/20 shadow-2xl focus:outline-none cursor-pointer group"
            title="Foto anterior (Seta esquerda ou deslize para a direita)"
            aria-label="Foto anterior">
            <svg class="w-6 h-6 sm:w-8 sm:h-8 transition-transform group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
            </svg>
          </button>
        </template>

        <!-- Seta para a Direita (Próxima Foto) caso tenha mais de uma -->
        <template x-if="photos.length > 1">
          <button
            type="button"
            @click.stop="nextPhoto()"
            class="absolute right-2 sm:right-6 top-1/2 -translate-y-1/2 z-50 p-2.5 sm:p-4 rounded-full bg-black/50 hover:bg-black/80 active:scale-90 text-white/90 hover:text-white transition-all backdrop-blur-md border border-white/20 shadow-2xl focus:outline-none cursor-pointer group"
            title="Próxima foto (Seta direita ou deslize para a esquerda)"
            aria-label="Próxima foto">
            <svg class="w-6 h-6 sm:w-8 sm:h-8 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
            </svg>
          </button>
        </template>

        <!-- Imagem Expandida Centralizada, preenchendo toda a altura da página -->
        <img
          :src="photos[currentIndex]"
          @click.stop
          draggable="false"
          alt="Foto da Araucária Expandida"
          class="max-w-full max-h-[85vh] sm:max-h-[92vh] sm:max-w-[90vw] w-auto h-auto object-contain rounded-lg shadow-2xl transition-all duration-200 select-none">

        <!-- Indicador de Posição e Marcadores caso tenha mais de uma foto -->
        <template x-if="photos.length > 1">
          <div
            @click.stop
            class="absolute bottom-4 left-1/2 -translate-x-1/2 z-50 flex items-center space-x-2 bg-black/60 backdrop-blur-md px-4 py-2 rounded-full border border-white/20 text-white shadow-xl pointer-events-auto">
            <span class="text-xs font-bold tracking-wider font-mono" x-text="(currentIndex + 1) + ' / ' + photos.length"></span>
            <div class="flex items-center space-x-1.5 ml-2">
              <template x-for="(p, i) in photos" :key="i">
                <button
                  type="button"
                  @click.stop="currentIndex = i"
                  :class="currentIndex === i ? 'w-5 bg-emerald-400' : 'w-1.5 bg-white/40 hover:bg-white/80'"
                  class="h-1.5 rounded-full transition-all duration-200 cursor-pointer"
                  :aria-label="'Ir para foto ' + (i + 1)">
                </button>
              </template>
            </div>
          </div>
        </template>

      </div>
    </template>
  </div>
@else
  <div class="text-gray-400 p-12 text-center">Nenhuma imagem registrada</div>
@endif