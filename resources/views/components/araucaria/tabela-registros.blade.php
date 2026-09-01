@props(['observations' => null, 'myObservations' => null])

@php
  $estagiosConfig = [
    'seedling' => ['label' => 'Muda', 'class' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300'],
    'sapling'  => ['label' => 'Jovem', 'class' => 'bg-teal-100 text-teal-800 dark:bg-teal-950 dark:text-teal-300'],
    'adult'    => ['label' => 'Adulta', 'class' => 'bg-emerald-800 text-white dark:bg-emerald-600 dark:text-white'],
    'dead'     => ['label' => 'Morta', 'class' => 'bg-amber-100 text-amber-900 dark:bg-amber-950 dark:text-amber-300'],
  ];
  $lista = $myObservations ?? ($observations ? $observations->where('user_id', auth()->id()) : collect());
@endphp

<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm text-left">
  <thead class="bg-slate-100/80 dark:bg-gray-900/80 text-gray-500 dark:text-gray-400 uppercase text-[11px] font-bold tracking-wider">
    <tr>
      <th class="px-6 py-4">ID & Foto</th>
      <th class="px-6 py-4">Data do Registro</th>
      <th class="px-6 py-4">Estágio Ecológico</th>
      <th class="px-6 py-4">Coordenadas</th>
      <th class="px-6 py-4 text-right">Ações</th>
    </tr>
  </thead>
  <tbody class="divide-y divide-gray-200 dark:divide-gray-700/80 text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800">
    @forelse($lista as $obs)
    @php
      $stage = $estagiosConfig[$obs->stage] ?? ['label' => $obs->stage, 'class' => 'bg-gray-100 text-gray-800'];
    @endphp
    <tr class="hover:bg-emerald-50/40 dark:hover:bg-emerald-950/20 transition-colors duration-150">
      <td class="px-6 py-4">
        <div class="flex items-center space-x-3">
          <span class="font-mono text-xs font-bold text-gray-400">#{{ $obs->id }}</span>
          <div class="relative w-12 h-12 rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 shadow-sm shrink-0">
            <img src="{{ $obs->photo_path }}" alt="Miniatura" class="w-full h-full object-cover">
          </div>
        </div>
      </td>
      <td class="px-6 py-4 whitespace-nowrap text-xs font-medium text-gray-600 dark:text-gray-300">
        {{ $obs->created_at->format('d/m/Y H:i') }}
      </td>
      <td class="px-6 py-4 whitespace-nowrap">
        <span class="px-2.5 py-1 text-xs font-bold rounded-lg shadow-xs {{ $stage['class'] }}">
          {{ $stage['label'] }}
        </span>
      </td>
      <td class="px-6 py-4 whitespace-nowrap text-xs font-mono text-gray-500 dark:text-gray-400">
        {{ number_format($obs->latitude, 4) }}, {{ number_format($obs->longitude, 4) }}
      </td>
      <td class="px-6 py-4 text-right whitespace-nowrap" x-data="{ confirmandoExclusao: false }">
        <div x-show="!confirmandoExclusao" class="flex justify-end items-center space-x-2">
          <a href="{{ route('observations.show', $obs->id) }}" 
             class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 transition">
            Ver
          </a>
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
              " class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-emerald-100 hover:bg-emerald-200 dark:bg-emerald-900/60 dark:hover:bg-emerald-800/80 text-emerald-800 dark:text-emerald-300 transition">
            Editar
          </button>
          <button
            type="button"
            @click="confirmandoExclusao = true"
            class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/50 dark:hover:bg-rose-900/60 text-rose-700 dark:text-rose-300 transition">
            Excluir
          </button>
        </div>

        <div x-show="confirmandoExclusao"
          class="flex justify-end items-center space-x-2 text-xs bg-rose-50 dark:bg-rose-950/80 border border-rose-200 dark:border-rose-800 p-1.5 rounded-lg shadow-sm" x-transition>
          <span class="text-rose-800 dark:text-rose-200 font-semibold">Excluir?</span>
        
          <button type="button" @click="$dispatch('deletar-observacao', { id: '{{ $obs->id }}', elementoLinha: $el.closest('tr') })"
            class="bg-rose-600 hover:bg-rose-700 text-white px-2.5 py-1 rounded-md font-bold transition shadow-sm">
            Sim
          </button>
        
          <button type="button" @click="confirmandoExclusao = false"
            class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-2 py-1 rounded-md font-bold transition">
            Não
          </button>
        </div>
      </td>
    </tr>
    @empty
    <tr>
      <td colspan="5" class="px-6 py-12 text-center">
        <div class="max-w-xs mx-auto text-center">
          <span class="text-3xl block mb-2">🌲</span>
          <p class="text-gray-600 dark:text-gray-300 font-medium">Você ainda não registrou nenhuma araucária.</p>
          <button type="button" @click="tab = 'create'; $dispatch('mudar-aba', 'create');" 
                  class="mt-3 inline-flex items-center space-x-1 text-xs font-bold text-emerald-700 dark:text-emerald-400 hover:underline">
            <span>➕ Clique aqui para fazer seu primeiro registro</span>
          </button>
        </div>
      </td>
    </tr>
    @endforelse
  </tbody>
</table>