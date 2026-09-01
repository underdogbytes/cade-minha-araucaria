<div class="form-group mb-4"
     x-data="{ previewPhotos: [] }"
     @reset-form-photos.window="previewPhotos = []; editPhotoUrl = '';"
     @reset="previewPhotos = []; editPhotoUrl = '';">
  <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1.5 flex items-center justify-between">
    <span>Fotos da Árvore</span>
    <span class="text-[11px] font-normal text-emerald-600 dark:text-emerald-400">Você pode enviar 1 ou mais fotos</span>
  </label>

  <template x-if="idEdicao && editPhotoUrl && previewPhotos.length === 0">
    <div class="mb-3 flex flex-col items-start">
      <span class="text-xs text-gray-500 mb-1">Foto atual registrada:</span>
      <div class="relative inline-block bg-gray-100 dark:bg-gray-700 p-1.5 rounded-xl border border-gray-200 dark:border-gray-600 shadow-sm">
        <img
          :src="editPhotoUrl"
          alt="Miniatura da Araucária atual"
          class="w-36 h-36 object-cover rounded-lg shadow-sm">
      </div>
    </div>
  </template>

  <!-- Previews das fotos selecionadas -->
  <template x-if="previewPhotos.length > 0">
    <div class="mb-3 space-y-1.5">
      <div class="flex items-center justify-between">
        <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">
          Fotos selecionadas (<span x-text="previewPhotos.length"></span>):
        </span>
        <button type="button"
                @click="previewPhotos = []; editPhotoUrl = ''; $el.closest('.form-group').querySelector('input[type=file]').value = ''"
                class="text-[11px] font-semibold text-rose-600 hover:text-rose-700 dark:text-rose-400 hover:underline">
          ✕ Remover seleção
        </button>
      </div>
      <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
        <template x-for="(photo, index) in previewPhotos" :key="index">
          <div class="relative group rounded-xl overflow-hidden border border-emerald-500/40 bg-gray-100 dark:bg-gray-800 shadow-xs aspect-square">
            <img :src="photo.url" class="w-full h-full object-cover">
            <span class="absolute top-1 left-1 bg-black/60 backdrop-blur-xs text-white text-[9px] font-bold px-1.5 py-0.5 rounded-md" x-text="index === 0 ? 'Principal' : '#' + (index + 1)"></span>
          </div>
        </template>
      </div>
    </div>
  </template>

  <div class="relative border-2 border-dashed border-gray-300 dark:border-gray-600 hover:border-emerald-500 dark:hover:border-emerald-400 rounded-xl p-4 transition-colors duration-200 bg-gray-50/50 dark:bg-gray-900/50 text-center">
    <input
      type="file"
      name="photos[]"
      multiple
      class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
      accept="image/png,image/jpeg,image/webp" {{ $attributes }}
      @change="(async () => {
        const files = Array.from($event.target.files);
        if (files.length > 0) {
          previewPhotos = files.map(f => ({ url: URL.createObjectURL(f), name: f.name }));
          editPhotoUrl = previewPhotos[0].url;
          const file = files[0];

          const sufixo = idEdicao ? 'edit' : 'create';
          const currentMapId = `map-${sufixo}`;
          
          const form = document.getElementById(`araucariaForm-${sufixo}`);
          if (!form) return;

          const checkboxAtual = form.querySelector('[name=\'dataexif\']') || form.querySelector('#dataexif');
          const isChecked = checkboxAtual ? checkboxAtual.checked : false;

          window.dispatchEvent(new CustomEvent('process-image-exif', {
            detail: { isChecked, file, formElement: form, mapId: currentMapId }
          }));
          
          setTimeout(() => {
            const latEl = form.querySelector('[name=\'latitude\']');
            const lngEl = form.querySelector('[name=\'longitude\']');
            const obsEl = form.querySelector('[name=\'observed_at\']');
            if (latEl && latEl.value) editLat = latEl.value;
            if (lngEl && lngEl.value) editLng = lngEl.value;
            if (obsEl && obsEl.value) editObservedAt = obsEl.value;
          }, 50);
        }
      })()"
      >
      <div class="flex flex-col items-center justify-center space-y-1">
        <span class="text-2xl">🖼️</span>
        <span class="text-xs font-semibold text-emerald-800 dark:text-emerald-400">Clique ou arraste fotos aqui</span>
        <span class="text-[11px] text-gray-400">Seleção múltipla suportada (JPG, PNG, WEBP)</span>
      </div>
  </div>

  <template x-if="idEdicao">
    <p class="text-[11px] text-gray-500 mt-1">Deixe em branco para manter as fotos atuais.</p>
  </template>
</div>