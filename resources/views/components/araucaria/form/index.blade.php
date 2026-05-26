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

      <div class="form-group">
        <label for="latitude">Latitude</label>
        <input type="text" id="latitude" name="latitude" readonly required x-model="editLat" class="bg-gray-100">
      </div>

      <div class="form-group">
        <label for="longitude">Longitude</label>
        <input type="text" id="longitude" name="longitude" readonly required x-model="editLng" class="bg-gray-100">
      </div>

      <x-araucaria.form.photo ::required="!idEdicao" />

      <div class="form-group">
        <label for="stage">Estágio de Desenvolvimento</label>
        <select id="stage" name="stage" required x-model="editStage">
          <option value="seedling">Muda</option>
          <option value="sapling">Jovem</option>
          <option value="adult">Adulta</option>
          <option value="dead">Morta/Cortada</option>
        </select>
      </div>

      <div class="form-group">
        <label for="gender">Gênero</label>
        <select id="gender" name="gender" required x-model="editGender">
          <option value="unknown">Desconhecido</option>
          <option value="male">Macho (Produz Pólen)</option>
          <option value="female">Fêmea (Produz Pinhas)</option>
        </select>
      </div>

      <div class="form-group">
        <label for="observed_at">Data da Observação</label>
        <input type="datetime-local" id="observed_at" name="observed_at" required x-model="editObservedAt">
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