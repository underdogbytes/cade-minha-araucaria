<div class="form-group mb-4">
  <label for="photo_path" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1.5">
    Foto da Árvore
  </label>

  <template x-if="idEdicao && editPhotoUrl">
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

  <div class="relative border-2 border-dashed border-gray-300 dark:border-gray-600 hover:border-emerald-500 dark:hover:border-emerald-400 rounded-xl p-4 transition-colors duration-200 bg-gray-50/50 dark:bg-gray-900/50 text-center">
    <input
      type="file"
      id="photo_path"
      name="photo_path"
      class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
      accept="image/png,image/jpeg,image/webp" {{ $attributes }}
      @change="(async () => {
        const file = $event.target.files[0];

        if (file) {
          editPhotoUrl = URL.createObjectURL(file);
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
        <span class="text-xs font-semibold text-emerald-800 dark:text-emerald-400">Clique ou arraste uma foto aqui</span>
        <span class="text-[11px] text-gray-400">Suporta JPG, PNG ou WEBP com coordenadas GPS EXIF</span>
      </div>
  </div>

  <template x-if="idEdicao">
    <p class="text-[11px] text-gray-500 mt-1">Deixe em branco para manter a foto atual.</p>
  </template>
</div>