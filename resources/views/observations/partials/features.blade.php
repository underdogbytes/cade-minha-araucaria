<div>
  <h3 class="text-lg font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-4">
    Características da Árvore
  </h3>

  @php
    $estagios = [
      'seedling' => 'Muda',
      'sapling' => 'Jovem',
      'adult' => 'Adulta',
      'dead' => 'Morta'
    ];
    $generos = [
      'unknown' => 'Não identificado',
      'male' => 'Macho (Dá Pólen)',
      'female' => 'Fêmea (Dá Pinhão)'
    ];
  @endphp

  <div class="border-t border-gray-100 dark:border-gray-700 divide-y divide-gray-100 dark:divide-gray-700 text-sm">
    <div class="py-3 flex justify-between">
      <span class="font-medium text-gray-500">Estágio de Desenvolvimento:</span>
      <span class="font-semibold text-gray-800 dark:text-gray-200">
        {{ $estagios[$observation->stage] ?? $observation->stage }}
      </span>
    </div>
    <div class="py-3 flex justify-between">
      <span class="font-medium text-gray-500">Gênero Biológico:</span>
      <span class="font-semibold text-gray-800 dark:text-gray-200">
        {{ $generos[$observation->gender] ?? $observation->gender }}
      </span>
    </div>
    <div class="py-3 flex justify-between">
      <span class="font-medium text-gray-500">Coordenadas:</span>
      <span class="font-mono text-gray-600 dark:text-gray-400">
        {{ number_format($observation->latitude, 5) }}, {{ number_format($observation->longitude, 5) }}
      </span>
    </div>
    <div class="py-3 flex justify-between">
      <span class="font-medium text-gray-500">Data da Observação:</span>
      <span class="text-gray-800 dark:text-gray-200">
        {{ $observation->observed_at->format('d/m/Y \à\s H:i') }}
      </span>
    </div>
    <div class="py-3 flex justify-between">
      <span class="font-medium text-gray-500">Registrado por:</span>
      <span class="text-gray-800 dark:text-gray-200">
        <a
            href="{{ route('profile.username', $observation->user->username) }}"
            class="text-emerald-600 dark:text-emerald-400 hover:underline">
          {{ $observation->user->username ? '@' . $observation->user->username : $observation->user->name }}
        </a>
      </span>
    </div>
  </div>
</div>