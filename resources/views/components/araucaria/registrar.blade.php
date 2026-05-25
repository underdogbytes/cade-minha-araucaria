<div class="map-flex-container">

  <div id="map"></div>

  <div id="form-container">
    <p class="text-sm text-gray-600">
      Clique no mapa para definir a localização exata da árvore.
    </p>

    @if ($errors->any())
      <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-800 rounded">
        <ul class="list-disc pl-5">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form id="araucariaForm" method="POST" action="/observations" enctype="multipart/form-data">
      @csrf

      <div class="form-group">
        <label for="latitude">
          Latitude
        </label>

        <input type="text" id="latitude" name="latitude" readonly required class="bg-gray-100">
      </div>

      <div class="form-group">
        <label for="longitude">
          Longitude
        </label>

        <input type="text" id="longitude" name="longitude" readonly required class="bg-gray-100">
      </div>

      <div class="form-group">
        <label for="photo_path">
          Foto da Árvore
        </label>

        <input type="file" id="photo_path" name="photo_path" accept="image/png,image/jpeg,image/webp" required >
      </div>

      <div class="form-group">
        <label for="stage">
          Estágio de Desenvolvimento
        </label>

        <select id="stage" name="stage" required>
          <option value="seedling">Muda</option>
          <option value="sapling">Jovem</option>
          <option value="adult" selected>Adulta</option>
          <option value="dead">Morta/Cortada</option>
        </select>
      </div>

      <div class="form-group">
        <label for="gender">
          Gênero
        </label>

        <select id="gender" name="gender" required>
          <option value="unknown" selected>
            Desconhecido
          </option>

          <option value="male">
            Macho (Produz Pólen)
          </option>

          <option value="female">
            Fêmea (Produz Pinhas)
          </option>
        </select>
      </div>

      <button type="submit"
        class="w-full mt-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded transition disabled:opacity-50">
        Salvar Observação
      </button>
    </form>
  </div>

</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('araucariaForm');
    const input = document.getElementById('photo_path');

    if (!form || !input) return;

    form.addEventListener('submit', function (e) {
      const file = input.files[0];
      if (file && file.size > 10 * 1024 * 1024) {
        e.preventDefault();
        alert('A imagem excede o tamanho máximo permitido (10 MB). Reduza a resolução ou escolha outra imagem.');
      }
    });
  });
</script>