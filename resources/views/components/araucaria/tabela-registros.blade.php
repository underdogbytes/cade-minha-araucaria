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
          class="px-2 py-1 bg-gray-100 dark:bg-gray-800 rounded">{{ $obs->stage }}</span>
      </td>
      <td class="px-6 py-4 text-right space-x-2">
        <a href="{{ route('observations.show', $obs->id) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400">Ver</a>
        <button type="button" @click="
                subAba = 'editar';
                idEdicao = '{{ $obs->id }}';
                editLat = '{{ $obs->latitude }}';
                editLng = '{{ $obs->longitude }}';
                editStage = '{{ $obs->stage }}';
                editGender = '{{ $obs->gender }}';
                editPhotoUrl = '{{ $obs->photo_path }}';
                $dispatch('mudar-aba', 'edit'); 
            " class="text-emerald-600 hover:text-emerald-900 font-semibold">
          Editar
        </button>
        <button class="text-red-600 hover:text-red-900 dark:text-red-400 font-medium">Excluir</button>
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