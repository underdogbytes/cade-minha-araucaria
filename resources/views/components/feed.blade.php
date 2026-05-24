@props(['observations'])

<div
    class="p-6 lg:p-8 bg-white dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent border-b border-gray-200 dark:border-gray-700">
    <h1 class="mt-8 text-2xl font-medium text-gray-900 dark:text-white">
        Feed das Araucárias
    </h1>

    <p class="mt-6 text-gray-500 dark:text-gray-400 leading-relaxed">
        Últimos registros da comunidade:
    </p>
</div>

<div class="bg-gray-200 dark:bg-gray-800 bg-opacity-25 grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8 p-6 lg:p-8">

    @forelse($observations as $obs)
    <div
        class="bg-white dark:bg-gray-900 overflow-hidden shadow-xl sm:rounded-lg flex flex-col justify-between border border-gray-100 dark:border-gray-700">

        <div class="w-full h-48 overflow-hidden bg-gray-100 dark:bg-gray-800">
            <img src="{{ Storage::url($obs.photo_url) }}" alt="Foto da Araucária"
                class="w-full h-full object-cover hover:scale-105 transition duration-300">
        </div>

        <div class="p-6 flex-1 flex flex-col justify-between">
            <div>
                <div class="flex justify-between items-center text-xs text-gray-400 dark:text-gray-500 mb-3">
                    <span class="font-semibold text-emerald-600 dark:text-emerald-400">
                        🌲 @_{{ $obs->user->username }}
                    </span>
                    <span>
                        {{ $obs->created_at->format('d/m/Y H:i') }}
                    </span>
                </div>

                <div class="flex flex-wrap gap-2 mt-2">
                    <span
                        class="px-2.5 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 uppercase tracking-wider">
                        {{ [
                                'seedling' => 'Muda',
                                'sapling' => 'Jovem',
                                'adult' => 'Adulta',
                                'dead' => 'Morta/Cortada'
                            ][$obs->stage] ?? $obs->stage }}
                    </span>

                    <span
                        class="px-2.5 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200 uppercase tracking-wider">
                      {{ [
                          'male' => '♂️ Macho',
                          'female' => '♀️ Fêmea',
                          'unknown' => '❓ Desconhecido'
                      ][$obs->gender] ?? $obs->gender }}
                  </span>
                </div>
            </div>

            <div
                class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-800 text-xs text-gray-500 dark:text-gray-400 flex justify-between">
                <span>Lat: {{ number_format($obs->latitude, 4) }}</span>
                <span>Lng: {{ number_format($obs->longitude, 4) }}</span>
            </div>
        </div>

    </div>
    @empty
    <div class="col-span-1 md:col-span-2 text-center py-12">
        <p class="text-gray-500 dark:text-gray-400">Nenhuma araucária registrada no feed até o momento.</p>
    </div>
    @endforelse
</div>