@php
  $estagiosTraducao = [
    'seedling' => 'Muda',
    'sapling' => 'Jovem',
    'adult' => 'Adulta',
    'dead' => 'Morta'
  ];
@endphp

<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm text-left">
  <thead class="bg-gray-50 dark:bg-gray-900 text-gray-700 dark:text-gray-300 uppercase text-xs">
    <tr>
      <th class="px-6 py-3">ID</th>
      <th class="px-6 py-3">Miniatura</th>
      <th class="px-6 py-3">Data</th>
      <th class="px-6 py-3">Estágio</th>
      <th class="px-6 py-3 text-right">Ações</th>
    </tr>
  </thead>
  <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-gray-900 dark:text-gray-100">
    @forelse($observations->where('user_id', auth()->id()) as $obs)
    <tr>
      <td class="px-6 py-4">
        {{ $obs->id }}
      </td>
      <td class="px-6 py-4">
        <img src="{{ $obs->photo_path }}" class="w-12 h-12 object-cover rounded-md border">
      </td>
      <td class="px-6 py-4">{{ $obs->created_at->format('d/m/Y') }}</td>
      <td class="px-6 py-4 uppercase text-xs"><span
          class="px-2 py-1 bg-gray-100 dark:bg-gray-800 rounded">{{ $estagiosTraducao[$obs->stage] ?? $obs->stage }}</span>
      </td>
      <td class="px-6 py-4 text-right space-x-2" x-data="{ confirmandoExclusao: false }">
        <div x-show="!confirmandoExclusao">
          <a href="{{ route('observations.show', $obs->id) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400">Ver</a>
          <button type="button" @click="
                  subAba = 'editar';
                  idEdicao = '{{ $obs->id }}';
                  editLat = '{{ $obs->latitude }}';
                  editLng = '{{ $obs->longitude }}';
                  editStage = '{{ $obs->stage }}';
                  editGender = '{{ $obs->gender }}';
                  editPhotoUrl = '{{ $obs->photo_path }}';
                  editObservedAt = '{{ \Carbon\Carbon::parse($obs->observed_at)->format('Y-m-d\TH:i') }}';
                  $dispatch('mudar-aba', 'edit'); 
              " class="text-emerald-600 hover:text-emerald-900 font-semibold">
            Editar
          </button>
          <button
            type="button"
            @click="confirmandoExclusao = true"
            class="text-red-600 hover:text-red-900 font-semibold">
            Excluir
          </button>
        </div>

        <div x-show="confirmandoExclusao"
          class="flex justify-end items-center space-x-2 text-xs bg-red-50 dark:bg-red-950/30 p-1.5 rounded" x-transition>
          <span class="text-red-700 dark:text-red-300 font-medium">Tem certeza?</span>
        
          <button type="button" @click="deletarObservacao('{{ $obs->id }}', $el.closest('tr'))"
            class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded font-bold transition shadow-sm">
            Sim
          </button>
        
          <button type="button" @click="confirmandoExclusao = false"
            class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-2 py-1 rounded font-bold transition">
            Não
          </button>
        </div>
      </td>
    </tr>
    @empty
    <tr>
      <td colspan="4" class="px-6 py-8 text-center text-gray-500">
        Você ainda não registrou nenhuma araucária :(
      </td>
    </tr>
    @endforelse
  </tbody>
</table>