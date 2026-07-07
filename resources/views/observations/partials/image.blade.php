@if($observation->photo_path)
  <img
    src="{{ $observation->photo_path }}"
    alt="Foto da Araucária"
    class="w-full h-auto max-h-[450px] object-cover rounded-lg shadow-md">
@else
  <div class="text-gray-400 p-12 text-center">Nenhuma imagem registrada</div>
@endif