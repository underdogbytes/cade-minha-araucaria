<x-app-layout>
  <x-slot name="header">
    <x-page-header title="Registro de Araucária — {{ $observation->created_at->format('d/m/Y') }}" />
  </x-slot>

  <div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
      <x-toast-alert />

      <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div
            class="flex flex-col items-center justify-top bg-gray-50 dark:bg-gray-900 rounded-xl p-2 border border-gray-100 dark:border-gray-700">
            @if($observation->photo_path)
              <img src="{{ $observation->photo_path }}" alt="Foto da Araucária"
                class="w-full h-auto max-h-[450px] object-cover rounded-lg shadow-md">
            @else
              <div class="text-gray-400 p-12 text-center">Nenhuma imagem registrada</div>
            @endif
          </div>

          <div class="flex flex-col justify-between space-y-6">
            <div>
              <h3 class="text-lg font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-4">
                Características da Árvore
              </h3>

              @php
                $estagios = [
                  'seedling' => 'Muda',
                  'sapling' => 'Jovem',
                  'adult' => 'Adulta',
                  'dead' => 'Morta / Cortada'
                ];
                $generos = [
                  'unknown' => 'Não identificado',
                  'male' => 'Macho (Dá Pólen)',
                  'female' => 'Fêmea (Dá Pinhão)'
                ];
              @endphp

              <div
                class="border-t border-gray-100 dark:border-gray-700 divide-y divide-gray-100 dark:divide-gray-700 text-sm">
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
                    <a href="{{ route('profile.show', $observation->user) }}"
                      class="text-emerald-600 dark:text-emerald-400 hover:underline">
                      {{ $observation->user->username ? '@' . $observation->user->username : $observation->user->name }}
                    </a>
                  </span>
                </div>
              </div>
            </div>

            <div
              class="bg-emerald-50 dark:bg-emerald-950/20 p-4 rounded-lg border border-emerald-100 dark:border-emerald-900 text-xs text-emerald-800 dark:text-emerald-300">
              💡 <strong>Você sabia?</strong> As araucárias são protegidas pela
              Lei da Mata Atlântica (Lei Federal nº 11.428/2006) e pela Lei de Crimes Ambientais (Lei Federal nº
              9.605/1998).
              O mapeamento colaborativo ajuda pesquisadores e a comunidade a monitorar a preservação da espécie :D
            </div>

            <div class="py-3" x-data="{ openReport: false }">
              <button id="report-toggle" type="button" @click="openReport = !openReport"
                class="ml-2 text-sm font-medium text-amber-600 hover:text-amber-800 dark:text-amber-400 dark:hover:text-amber-300 transition">
                🚩 Denunciar*
              </button>
              <br>
              <span class="text-xs text-gray-500 dark:text-gray-400">
                * Caso ela viole as regras da plataforma ou contenha conteúdo impróprio.
                A moderação irá analisar a denúncia e tomar as medidas cabíveis.
              </span>

              <div class="space-y-3 mt-3" id="report-form-container" x-show="openReport" style="display: none;">
                <form id="report-form" action="{{ route('observations.report', $observation) }}" method="POST"
                  class="space-y-3" onsubmit="return confirm('Você tem certeza que deseja denunciar?');">
                  @csrf
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                    Motivo da denúncia
                    <select name="reason"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                      <option value="inappropriate_image">Imagem imprópria</option>
                      <option value="ownership">Autoria</option>
                      <option value="other">Outros</option>
                    </select>
                  </label>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                    Detalhes
                    <textarea name="details" maxlength="144" rows="3"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                      placeholder="Máximo 144 caracteres"></textarea>
                  </label>
                  <button type="submit"
                    class="text-red-600 hover:text-red-800 dark:hover:text-red-400 text-sm font-medium transition">
                    Denunciar observação
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>