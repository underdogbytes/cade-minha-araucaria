@props(['modo' => 'criar'])

@php
$sufixo = $modo === 'criar' ? 'create' : 'edit';
@endphp

<div class="map-flex-container rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-xl">

  <div id="map-{{ $sufixo }}" class="relative min-h-[380px]">
    <div class="absolute top-3 left-3 z-10 bg-white/90 dark:bg-gray-900/90 backdrop-blur-md px-3 py-1.5 rounded-lg border border-emerald-500/20 shadow-sm text-xs font-semibold text-emerald-800 dark:text-emerald-300">
      📍 Clique no mapa para marcar a localização
    </div>
  </div>

  <div id="form-container" class="bg-gray-50/50 dark:bg-gray-900/50 p-6 overflow-y-auto">

    <form id="araucariaForm-{{ $sufixo }}" method="POST"
      :action="idEdicao ? '/observations/' + idEdicao : '/observations'" enctype="multipart/form-data" class="space-y-4">
      @csrf

      <template x-if="idEdicao">
        <input type="hidden" name="_method" value="PUT">
      </template>

      <x-araucaria.form.photo ::required="!idEdicao" />

      <div class="p-3 bg-emerald-50/80 dark:bg-emerald-950/40 rounded-xl border border-emerald-200 dark:border-emerald-800/60 flex items-center space-x-3">
        <input
          type="checkbox"
          id="dataexif-{{ $sufixo }}"
          name="dataexif"
          class="rounded border-emerald-300 text-emerald-600 focus:ring-emerald-500 h-4 w-4 cursor-pointer"
          @change="(async () => {
            const sufixo = idEdicao ? 'edit' : 'create';
            const form = document.getElementById(`araucariaForm-${sufixo}`);
            if (!form) return;
            const fileInput = form.querySelector('[name=\'photo_path\']') || form.querySelector('input[type=\'file\']');
            const file = fileInput ? fileInput.files[0] : null;
        
            window.dispatchEvent(new CustomEvent('process-image-exif', {
              detail: { isChecked: $event.target.checked, file, formElement: form, mapId: `map-${sufixo}` }
            }));
            
            setTimeout(() => {
              const latEl = form.querySelector('[name=\'latitude\']');
              const lngEl = form.querySelector('[name=\'longitude\']');
              const obsEl = form.querySelector('[name=\'observed_at\']');
              if (latEl && latEl.value) editLat = latEl.value;
              if (lngEl && lngEl.value) editLng = lngEl.value;
              if (obsEl && obsEl.value) editObservedAt = obsEl.value;
            }, 50);
          })()">
        <label for="dataexif-{{ $sufixo }}" class="text-xs font-semibold text-emerald-900 dark:text-emerald-200 cursor-pointer">
          Usar dados EXIF (Data/GPS) da foto enviada
        </label>
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div class="form-group">
          <label for="latitude-{{ $sufixo }}" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Latitude</label>
          <input type="text" id="latitude-{{ $sufixo }}" name="latitude" required x-model="editLat" class="w-full text-xs font-mono rounded-lg border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100 p-2.5">
        </div>
        
        <div class="form-group">
          <label for="longitude-{{ $sufixo }}" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Longitude</label>
          <input type="text" id="longitude-{{ $sufixo }}" name="longitude" required x-model="editLng" class="w-full text-xs font-mono rounded-lg border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100 p-2.5">
        </div>
      </div>

      <div class="form-group">
        <label for="stage-{{ $sufixo }}" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Estágio de Desenvolvimento</label>
        <select id="stage-{{ $sufixo }}" name="stage" required x-model="editStage" class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2.5 focus:ring-emerald-500 focus:border-emerald-500">
          <option value="seedling">Muda (Plântula)</option>
          <option value="sapling">Jovem (Desenvolvimento)</option>
          <option value="adult">Adulta (Copa Formada)</option>
          <option value="dead">Morta / Cortada</option>
        </select>
      </div>

      <div class="form-group">
        <label for="gender-{{ $sufixo }}" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Gênero</label>
        <select id="gender-{{ $sufixo }}" name="gender" required x-model="editGender" class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2.5 focus:ring-emerald-500 focus:border-emerald-500">
          <option value="unknown">❓ Desconhecido / Não Identificado</option>
          <option value="male">♂️ Macho (Produz Estimais de Pólen)</option>
          <option value="female">♀️ Fêmea (Produz Pinhas/Pinhões)</option>
        </select>
      </div>

      <div class="form-group">
        <label for="observed_at-{{ $sufixo }}" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Data & Hora da Observação</label>
        <input type="datetime-local" id="observed_at-{{ $sufixo }}" name="observed_at" required x-model="editObservedAt" class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2.5 focus:ring-emerald-500 focus:border-emerald-500">
      </div>

      <div class="p-3 bg-gray-100 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600 flex items-center space-x-3">
        <input
          type="checkbox"
          id="is_shared-{{ $sufixo }}"
          name="is_shared"
          value="1"
          checked
          class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 h-4 w-4 cursor-pointer">
        <label for="is_shared-{{ $sufixo }}" class="text-xs font-semibold text-gray-800 dark:text-gray-200 cursor-pointer">
          Permitir que a comunidade anexe fotos de acompanhamento desta árvore
        </label>
      </div>

      <div class="flex gap-2 pt-2">
        <button type="submit"
          class="flex-1 bg-emerald-700 hover:bg-emerald-800 dark:bg-emerald-600 dark:hover:bg-emerald-500 text-white font-bold text-sm py-2.5 px-4 rounded-xl shadow-md shadow-emerald-700/20 transition duration-200 disabled:opacity-50 flex items-center justify-center space-x-2">
          <span x-text="idEdicao ? '💾 Salvar Alterações' : '🌱 Registrar Observação'"></span>
        </button>

        <template x-if="idEdicao">
          <button type="button" @click="subAba = 'tabela'; idEdicao = null;"
            class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-semibold text-xs py-2.5 px-4 rounded-xl transition whitespace-nowrap">
            Cancelar
          </button>
        </template>
      </div>
    </form>
  </div>
</div>