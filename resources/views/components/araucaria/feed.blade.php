@props(['observations'])

<div class="p-6 lg:p-8 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-emerald-900/5 via-transparent to-transparent">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center space-x-2 px-2.5 py-1 rounded-md bg-emerald-100 dark:bg-emerald-950/80 text-emerald-800 dark:text-emerald-300 text-xs font-semibold uppercase tracking-wider mb-2">
                <span>🌲 Observações em Tempo Real</span>
            </div>
            <h1 class="text-2xl font-bold font-display text-gray-900 dark:text-white">
                Feed das Araucárias
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Acompanhe as espécimes registradas pelos membros da comunidade.
            </p>
        </div>
        <div class="self-start sm:self-center">
            <span class="px-3 py-1.5 rounded-full text-xs font-bold bg-white dark:bg-gray-700 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 shadow-sm">
                {{ count($observations) }} {{ count($observations) === 1 ? 'Araucária listada' : 'Araucárias listadas' }}
            </span>
        </div>
    </div>
</div>

<div class="bg-slate-50/60 dark:bg-gray-900/60 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6 lg:p-8">

    @forelse($observations as $obs)
    @php
        $stageInfo = [
            'seedling' => ['label' => 'Muda', 'class' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300 border-emerald-200'],
            'sapling'  => ['label' => 'Jovem', 'class' => 'bg-teal-100 text-teal-800 dark:bg-teal-950/80 dark:text-teal-300 border-teal-200'],
            'adult'    => ['label' => 'Adulta', 'class' => 'bg-emerald-800 text-white dark:bg-emerald-600 dark:text-white border-emerald-700'],
            'dead'     => ['label' => 'Morta/Cortada', 'class' => 'bg-amber-100 text-amber-900 dark:bg-amber-950/80 dark:text-amber-300 border-amber-200'],
        ][$obs->stage] ?? ['label' => $obs->stage, 'class' => 'bg-gray-100 text-gray-800'];

        $genderInfo = [
            'male'    => '♂️ Macho',
            'female'  => '♀️ Fêmea',
            'unknown' => '❓ Desconhecido',
        ][$obs->gender] ?? $obs->gender;
    @endphp

    <div class="group bg-white dark:bg-gray-900 rounded-2xl border border-gray-200/80 dark:border-gray-700/80 overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition duration-300 flex flex-col justify-between">

        <!-- Image Container with Badge Overlay -->
        <div class="relative w-full h-52 overflow-hidden bg-gray-100 dark:bg-gray-800">
            <img src="{{ $obs->photo_path }}" alt="Foto da Araucária"
                class="w-full h-full object-cover group-hover:scale-105 transition duration-500 ease-out">
            
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-black/20 opacity-80 group-hover:opacity-60 transition duration-300"></div>

            <!-- Top Overlay Badges -->
            <div class="absolute top-3 left-3 flex flex-wrap gap-1.5">
                <span class="px-2.5 py-1 text-xs font-bold rounded-lg shadow-sm border backdrop-blur-md {{ $stageInfo['class'] }}">
                    {{ $stageInfo['label'] }}
                </span>
                @if($obs->is_shared)
                    <span class="px-2.5 py-1 text-[11px] font-bold rounded-lg shadow-sm border backdrop-blur-md bg-emerald-900/80 text-white border-emerald-700">
                        Colaborativa
                    </span>
                @endif
            </div>

            <!-- Bottom Left Author Overlay -->
            <div class="absolute bottom-3 left-3 right-3 flex justify-between items-center text-xs text-white">
                <div class="flex items-center space-x-2 drop-shadow-md">
                    <div class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-[10px]">
                        {{ strtoupper(substr($obs->user->name ?? 'A', 0, 1)) }}
                    </div>
                    <span class="font-semibold text-white truncate max-w-[140px]">
                        {{ $obs->user->username ? '@'.$obs->user->username : $obs->user->name }}
                    </span>
                </div>
                <span class="text-[11px] text-gray-200 font-medium drop-shadow-md">
                    {{ $obs->created_at->diffForHumans() }}
                </span>
            </div>
        </div>

        <!-- Content Area -->
        <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
            <div>
                <div class="flex items-center justify-between text-xs mb-2">
                    <span class="px-2 py-0.5 rounded bg-purple-50 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300 font-medium">
                        {{ $genderInfo }}
                    </span>

                    <span class="text-[11px] text-gray-400">
                        Obs #{{ $obs->id }}
                    </span>
                </div>

                <div class="text-xs text-gray-500 dark:text-gray-400 flex items-center space-x-1 mt-3">
                    <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="font-mono text-gray-600 dark:text-gray-300">
                        {{ number_format($obs->latitude, 4) }}, {{ number_format($obs->longitude, 4) }}
                    </span>
                </div>
            </div>

            <!-- Footer Action -->
            <div class="pt-3 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between">
                <span class="text-[11px] text-gray-400">
                    {{ $obs->created_at->format('d/m/Y') }}
                </span>
                <a href="/observations/{{ $obs->id }}"
                    class="inline-flex items-center space-x-1 text-xs font-bold text-emerald-700 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-300 group-hover:translate-x-0.5 transition duration-200">
                    <span>Ver Detalhes</span>
                    <span>→</span>
                </a>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-1 md:col-span-2 lg:col-span-3 text-center py-16 bg-white dark:bg-gray-900 rounded-2xl border border-dashed border-gray-300 dark:border-gray-700">
        <span class="text-4xl block mb-2">🌲</span>
        <p class="text-gray-600 dark:text-gray-300 font-semibold">Nenhuma araucária registrada no feed até o momento.</p>
        <p class="text-xs text-gray-400 mt-1">Seja o primeiro membro da comunidade a registrar uma espécime!</p>
    </div>
    @endforelse
</div>

<div class="p-6 lg:p-8 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
    {{ $observations->links() }}
</div>