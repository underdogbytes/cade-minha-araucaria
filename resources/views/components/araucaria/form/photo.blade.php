<div class="form-group">
  <label for="photo_path" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
    Foto da Árvore
  </label>

  <template x-if="idEdicao && editPhotoUrl">
    <div class="mb-3 flex flex-col items-start mt-2">
      <p class="text-xs font-medium text-gray-500 mb-1">Foto atual registrada:</p>
      <div
        class="relative inline-block bg-gray-100 dark:bg-gray-700 p-1 rounded border border-gray-200 dark:border-gray-600">
        <img
          :src="editPhotoUrl"
          alt="Miniatura da Araucária atual"
          class="w-32 h-32 object-cover rounded shadow-sm">
      </div>
    </div>
  </template>

  <input
    type="file"
    id="photo_path"
    name="photo_path"
    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100"
    accept="image/png,image/jpeg,image/webp" {{ $attributes }}
    @change="(async () => {
      const file = $event.target.files[0];

      if (file) {
        editPhotoUrl = URL.createObjectURL(file);
        const currentMapId = idEdicao ? 'map-edit' : 'map-create';
        
        const sufixo = idEdicao ? 'edit' : 'create';
        const checkboxAtual = document.querySelector(`#araucariaForm-${sufixo} #dataexif`);
        const isChecked = checkboxAtual ? checkboxAtual.checked : false;

        if (window.processPhotoExif) {
          await window.processPhotoExif(isChecked, file, currentMapId);
          
          if (isChecked) {
            editLat = document.getElementById('latitude').value;
            editLng = document.getElementById('longitude').value;
            editObservedAt = document.getElementById('observed_at').value;
          }
        }
      }
    })()"
    >

  <template x-if="idEdicao">
    <p class="text-xs text-gray-500 mt-1">Deixe em branco para manter a foto atual.</p>
  </template>
</div>