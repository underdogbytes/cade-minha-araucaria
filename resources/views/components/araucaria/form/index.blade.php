@props(['modo' => 'criar'])

@php
$sufixo = $modo === 'criar' ? 'create' : 'edit';
@endphp

<div class="map-flex-container">

  <div id="map-{{ $sufixo }}"></div>

  <div id="form-container">
    <p class="text-sm text-gray-600">
      Clique no mapa para definir a localização exata da árvore.
    </p>

    <form id="araucariaForm-{{ $sufixo }}" method="POST"
      :action="idEdicao ? '/observations/' + idEdicao : '/observations'" enctype="multipart/form-data">
      @csrf

      <template x-if="idEdicao">
        <input type="hidden" name="_method" value="PUT">
      </template>

      <x-araucaria.form.photo ::required="!idEdicao" />

      <div class="form-group">
        <label for="dataexif-{{ $sufixo }}" class="font-medium text-gray-700 dark:text-gray-300 cursor-pointer">
          Preencher usando dados EXIF da foto?
        </label>
        <input
          type="checkbox"
          id="dataexif-{{ $sufixo }}"
          name="dataexif"
          class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" style="max-width: 1.5rem; max-height: 1.5rem;"
          @change="(async () => {
            const sufixo = idEdicao ? 'edit' : 'create';
            const form = document.getElementById(`araucariaForm-${sufixo}`);
            if (!form) return;
            const fileInput = form.querySelector('[name=\'photo_path\']') || form.querySelector('input[type=\'file\']');
            const file = fileInput ? fileInput.files[0] : null;
        
            if (window.handleSelecaoImagem) {
              await window.handleSelecaoImagem($event.target.checked, file, form, `map-${sufixo}`);
              
              const latEl = form.querySelector('[name=\'latitude\']');
              const lngEl = form.querySelector('[name=\'longitude\']');
              const obsEl = form.querySelector('[name=\'observed_at\']');
              editLat = latEl ? latEl.value : '';
              editLng = lngEl ? lngEl.value : '';
              editObservedAt = obsEl ? obsEl.value : '';
            }
          })()">
        <span>Aceito usar os dados EXIF da foto</span>
      </div>

      <div class="form-group">
        <label for="latitude-{{ $sufixo }}">Latitude</label>
        <input type="text" id="latitude-{{ $sufixo }}" name="latitude" required x-model="editLat" class="bg-gray-100">
      </div>
      
      <div class="form-group">
        <label for="longitude-{{ $sufixo }}">Longitude</label>
        <input type="text" id="longitude-{{ $sufixo }}" name="longitude" required x-model="editLng" class="bg-gray-100">
      </div>

      <div class="form-group">
        <label for="stage-{{ $sufixo }}">Estágio de Desenvolvimento</label>
        <select id="stage-{{ $sufixo }}" name="stage" required x-model="editStage">
          <option value="seedling">Muda</option>
          <option value="sapling">Jovem</option>
          <option value="adult">Adulta</option>
          <option value="dead">Morta/Cortada</option>
        </select>
      </div>

      <div class="form-group">
        <label for="gender-{{ $sufixo }}">Gênero</label>
        <select id="gender-{{ $sufixo }}" name="gender" required x-model="editGender">
          <option value="unknown">Desconhecido</option>
          <option value="male">Macho (Produz Pólen)</option>
          <option value="female">Fêmea (Produz Pinhas)</option>
        </select>
      </div>

      <div class="form-group">
        <label for="observed_at-{{ $sufixo }}">Data da Observação</label>
        <input type="datetime-local" id="observed_at-{{ $sufixo }}" name="observed_at" required x-model="editObservedAt">
      </div>

      <div class="flex gap-2 mt-2">
        <button type="submit"
          class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded transition disabled:opacity-50">
          <span x-text="idEdicao ? 'Salvar Alterações' : 'Salvar Observação'"></span>
        </button>

        <template x-if="idEdicao">
          <button type="button" @click="subAba = 'tabela'; idEdicao = null;"
            class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded transition whitespace-nowrap">
            Cancelar
          </button>
        </template>
      </div>
    </form>
  </div>
</div>