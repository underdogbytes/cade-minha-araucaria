<div>
  <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
    <span class="font-semibold text-amber-600">{{ ucfirst($report->reason) }}</span>
    <span>•</span>
    <span>Denunciado por
      <a href="/users/{{ $report->user->id }}" class="text-emerald-600 dark:text-emerald-400 hover:underline">
        {{ `$report->user?->name (ver perfil)` ?? 'usuário removido' }}
      </a>
    </span>
  </div>
  <h3 class="mt-2 text-lg font-semibold text-gray-900 dark:text-white">
    <a href="/users/{{ $report->observation?->user?->id }}" target="_blank">
      Usuário denunciado:<br>{{ $report->observation?->user?->name ?? 'Usuário removido' }}
    </a>
  </h3>
  <img src="{{ $report->observation?->photo_path ?? '/images/placeholder.png' }}" alt="Foto denunciada"
    class="h-64 object-cover rounded-lg shadow-md">

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

    <p>
      <span class="font-semibold text-gray-700 dark:text-gray-300">Detalhes:</span>
      {{ $report->details ?? 'Sem detalhes adicionais informados na denúncia.' }}
    </p>
  </div>
</div>