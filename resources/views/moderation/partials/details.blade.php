<div>
  <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
    <span class="font-semibold text-amber-600">Total de Denúncias: {{ $reportsGroup->count() }}</span>
  </div>
  <h3 class="mt-2 text-lg font-semibold text-gray-900 dark:text-white">
    <a href="{{ route('profile.username', $report->observation?->user->username) }}" target="_blank">
      Usuário denunciado:<br>{{ $report->observation?->user?->name ?? 'Usuário removido' }}
    </a>
  </h3>
  <img src="{{ $report->observation?->photo_path ?? '/images/placeholder.png' }}" alt="Foto denunciada"
    class="h-64 object-cover rounded-lg shadow-md mt-4">

  <div class="mt-4 space-y-2 text-gray-700 dark:text-gray-300">
    <p>
      <span class="font-semibold text-gray-700 dark:text-gray-300">ID da Observação:</span>
      {{ $report->observation?->id ?? 'N/A' }}
    </p>

    <p>
      <span class="font-semibold text-gray-700 dark:text-gray-300">Link da Observação:</span>
      <a href="/observations/{{ $report->observation?->id }}" target="_blank"
        class="text-emerald-600 dark:text-emerald-400 hover:underline">
        {{ $report->observation?->id ? 'Ver Detalhes' : 'N/A' }}
      </a>
    </p>

    <p>
      <span class="font-semibold text-gray-700 dark:text-gray-300">Registrado em:</span>
      {{ $report->observation?->created_at?->format('d/m/Y H:i') ?? 'Data não disponível' }}
    </p>

    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
      <h4 class="font-semibold text-gray-700 dark:text-gray-300 mb-2">Denunciado por:</h4>
      <ul class="space-y-3">
        @foreach($reportsGroup as $r)
          <li class="bg-gray-50 dark:bg-gray-900 p-3 rounded-md text-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400 mb-1">
              <span class="font-semibold text-amber-600">{{ ucfirst($r->reason) }}</span>
              <span>•</span>
              <a href="{{ route('profile.username', $r->user->username) }}" class="text-emerald-600 dark:text-emerald-400 hover:underline">
                {{ $r->user?->name ?? 'usuário removido' }}
              </a>
            </div>
            @if($r->details)
              <p class="text-gray-700 dark:text-gray-300 italic">"{{ $r->details }}"</p>
            @else
              <p class="text-gray-500 dark:text-gray-500 italic text-xs">Sem detalhes adicionais</p>
            @endif
          </li>
        @endforeach
      </ul>
    </div>
  </div>
</div>