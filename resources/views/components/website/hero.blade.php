@props([
    'usersCount' => null,
    'observationsCount' => null,
    'citiesCount' => null,
])

@php
    $usersCount = $usersCount ?? \Illuminate\Support\Facades\Cache::remember('users_count', 60, fn() => \App\Models\User::count());
    $observationsCount = $observationsCount ?? \Illuminate\Support\Facades\Cache::remember('observations_count', 60, fn() => \App\Models\AraucariaObservation::count());
    $citiesCount = $citiesCount ?? \Illuminate\Support\Facades\Cache::remember('cities_count', 60, function () {
        return \App\Models\AraucariaObservation::count() > 0 ? 1 : 0;
    });
@endphp

<section class="relative overflow-hidden hero-gradient">
  <div class="relative z-10 mx-auto max-w-7xl px-4 py-8 sm:px-6 sm:py-12 lg:px-8 lg:py-16">

    <div class="grid grid-cols-1 items-center gap-10 lg:grid-cols-2 lg:gap-12">

      <!-- Coluna esquerda: conteúdo -->
      <div class="max-w-2xl">

        <!-- Eco Badge -->
        <div
          class="mb-6 inline-flex items-center gap-2 rounded-full border border-emerald-300/80 bg-emerald-100/90 px-3.5 py-1.5 text-xs font-bold uppercase tracking-wider text-[#1b4332] shadow-sm dark:border-emerald-700/60 dark:bg-emerald-950/70 dark:text-emerald-300">
          <svg class="h-4 w-4 text-[#1b4332] dark:text-emerald-400" viewBox="0 0 24 24" fill="currentColor">
            <path
              d="M12 2C11.4 2 11 2.4 11 3V5.5C9.2 5.6 7.6 6.4 6.4 7.7C6.1 8.1 6.1 8.7 6.5 9C6.9 9.3 7.5 9.3 7.8 8.9C8.8 7.8 10.3 7.1 11.8 7.1V10C9.8 10.2 8 11.2 6.8 12.8C6.5 13.2 6.6 13.8 7 14.1C7.4 14.4 8 14.3 8.3 13.9C9.2 12.7 10.6 12 12 12C13.4 12 14.8 12.7 15.7 13.9C16 14.3 16.6 14.4 17 14.1C17.4 13.8 17.5 13.2 17.2 12.8C16 11.2 14.2 10.2 12.2 10V7.1C13.7 7.1 15.2 7.8 16.2 8.9C16.5 9.3 17.1 9.3 17.5 9C17.9 8.7 17.9 8.1 17.6 7.7C16.4 6.4 14.8 5.6 13 5.5V3C13 2.4 12.6 2 12 2ZM11 14.5V22H13V14.5H11Z" />
          </svg>
          <span>MAPEAMENTO E CONSERVAÇÃO</span>
        </div>

        <!-- Título -->
        <h1
          class="font-display text-4xl font-extrabold leading-[1.12] tracking-tight text-gray-900 dark:text-white sm:text-5xl lg:text-[3.35rem] xl:text-[3.75rem]">
          Vamos localizar,<br class="hidden sm:inline" />
          monitorar e proteger<br class="hidden sm:inline" />
          as <span class="text-[#1b4332] dark:text-emerald-400">Araucárias?</span>
        </h1>

        <!-- Descrição -->
        <p class="mt-6 max-w-xl text-base leading-relaxed text-gray-600 dark:text-gray-300 sm:text-lg">
          Explore o mapa, veja as observações da comunidade e ajude a construir
          um futuro para essa espécie que é patrimônio do nosso país.
        </p>

        <!-- CTAs -->
        <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center">
          <a href="#mapa-hero" id="btn-explorar-mapa"
            class="inline-flex w-full items-center justify-center gap-2.5 rounded-full bg-[#1b4332] px-7 py-3.5 text-sm font-bold text-white shadow-md shadow-emerald-950/20 transition duration-200 hover:-translate-y-0.5 hover:bg-[#143225] hover:shadow-lg sm:w-auto">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span>Explorar o mapa</span>
          </a>

          <a href="{{ auth()->check() ? url('/dashboard') : route('register') }}"
            class="inline-flex w-full items-center justify-center gap-2 rounded-full border border-gray-200 bg-white px-7 py-3.5 text-sm font-semibold text-gray-800 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:bg-gray-50 hover:shadow-md dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700 sm:w-auto">
            <span class="text-base font-bold leading-none text-emerald-800 dark:text-emerald-400">＋</span>
            <span>Cadastrar observação</span>
          </a>
        </div>

        <!-- Indicadores / Contadores -->
        <div
          class="mt-12 flex flex-wrap items-center gap-6 sm:gap-8 border-t border-gray-200/80 pt-7 dark:border-gray-800">
          <!-- Stat 1: Observações -->
          <div class="flex items-center gap-3">
            <div
              class="flex h-11 w-11 items-center justify-center rounded-full bg-emerald-50 text-[#1b4332] ring-1 ring-emerald-200/60 dark:bg-emerald-950/50 dark:text-emerald-400 dark:ring-emerald-800/40">
              <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
            </div>
            <div>
              <p class="text-xl font-extrabold text-gray-900 dark:text-white leading-tight">
                {{ number_format($observationsCount ?? 0, 0, ',', '.') }}
              </p>
              <p class="text-xs font-medium text-gray-500 dark:text-gray-400 leading-tight mt-0.5">observações no mapa
              </p>
            </div>
          </div>

          <!-- Stat 2: Colaboradores -->
          <div class="flex items-center gap-3">
            <div
              class="flex h-11 w-11 items-center justify-center rounded-full bg-emerald-50 text-[#1b4332] ring-1 ring-emerald-200/60 dark:bg-emerald-950/50 dark:text-emerald-400 dark:ring-emerald-800/40">
              <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
            </div>
            <div>
              <p class="text-xl font-extrabold text-gray-900 dark:text-white leading-tight">
                {{ number_format($usersCount ?? 0, 0, ',', '.') }}
              </p>
              <p class="text-xs font-medium text-gray-500 dark:text-gray-400 leading-tight mt-0.5">colaboradores</p>
            </div>
          </div>

          <!-- Stat 3: Cidades -->
          <div class="flex items-center gap-3">
            <div
              class="flex h-11 w-11 items-center justify-center rounded-full bg-emerald-50 text-[#1b4332] ring-1 ring-emerald-200/60 dark:bg-emerald-950/50 dark:text-emerald-400 dark:ring-emerald-800/40">
              <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
              </svg>
            </div>
            <div>
              <p class="text-xl font-extrabold text-gray-900 dark:text-white leading-tight">
                {{ number_format($citiesCount ?? 0, 0, ',', '.') }}
              </p>
              <p class="text-xs font-medium text-gray-500 dark:text-gray-400 leading-tight mt-0.5">cidades</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Coluna direita: mapa interativo -->
      <div id="mapa-hero" class="relative min-w-0 scroll-mt-24">
        <!-- Card do Mapa com cantos arredondados e sombra -->
        <div
          class="relative overflow-hidden rounded-3xl border border-emerald-900/10 bg-emerald-50/40 shadow-2xl shadow-emerald-950/15 dark:border-gray-800 dark:bg-gray-900"
          style="min-height: 520px;">

          <!-- Mapa Leaflet com altura explícita garantida -->
          <div class="relative w-full h-[480px] sm:h-[520px] lg:h-[560px]" style="min-height: 480px; height: 100%;">
            <x-spinner message="Carregando mapa interativo..." id="mapSpinner" />
            <div id="map" class="h-full w-full" style="height: 100%; min-height: 480px; width: 100%;"></div>
          </div>

          <!-- Overlays Interativos (posicionados acima do canvas Leaflet com z-[1001]) -->
          <div class="pointer-events-none absolute inset-0 z-[1001] p-3 sm:p-4">

            <!-- Top-Left: Search Bar overlay -->
            <div
              class="pointer-events-auto absolute left-3 top-3 sm:left-4 sm:top-4 max-w-[calc(100%-120px)] sm:max-w-none w-64 sm:w-80">
              <div
                class="relative flex items-center rounded-xl border border-gray-200/90 bg-white/95 px-3 py-2.5 shadow-md backdrop-blur-md dark:border-gray-700 dark:bg-gray-900/95 transition-all focus-within:ring-2 focus-within:ring-emerald-500">
                <svg class="mr-2 h-4 w-4 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor"
                  viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" id="map-search-input" placeholder="Buscar por cidade, local ou espécie..."
                  class="w-full border-none bg-transparent p-0 text-xs text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-0 dark:text-gray-100 dark:placeholder-gray-500" />
              </div>
            </div>

            <!-- Left: Custom Zoom & Locate Controls -->
            <div
              class="pointer-events-auto absolute left-3 top-16 sm:left-4 sm:top-20 flex flex-col overflow-hidden rounded-xl border border-gray-200/90 bg-white/95 shadow-md backdrop-blur-md dark:border-gray-700 dark:bg-gray-900/95">
              <button id="btn-map-zoom-in" type="button" aria-label="Aumentar zoom"
                class="flex h-9 w-9 items-center justify-center text-lg font-bold text-gray-700 hover:bg-emerald-50 hover:text-emerald-800 transition dark:text-gray-200 dark:hover:bg-gray-800">
                ＋
              </button>
              <div class="h-px w-full bg-gray-200/80 dark:bg-gray-700"></div>
              <button id="btn-map-zoom-out" type="button" aria-label="Diminuir zoom"
                class="flex h-9 w-9 items-center justify-center text-lg font-bold text-gray-700 hover:bg-emerald-50 hover:text-emerald-800 transition dark:text-gray-200 dark:hover:bg-gray-800">
                −
              </button>
              <div class="h-px w-full bg-gray-200/80 dark:bg-gray-700"></div>
              <button id="btn-map-locate" type="button" aria-label="Minha localização"
                class="flex h-9 w-9 items-center justify-center text-gray-700 hover:bg-emerald-50 hover:text-emerald-800 transition dark:text-gray-200 dark:hover:bg-gray-800">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <circle cx="12" cy="12" r="3" stroke-width="2" />
                  <path stroke-linecap="round" stroke-width="2" d="M12 2v3m0 14v3M2 12h3m14 0h3" />
                </svg>
              </button>
            </div>

            <!-- Top-Right: Layers Card ("Camadas") -->
            <div
              class="pointer-events-auto absolute right-3 top-3 sm:right-4 sm:top-4 w-36 sm:w-48 overflow-hidden rounded-2xl border border-gray-200/90 bg-white/95 p-2.5 sm:p-3 shadow-lg backdrop-blur-md dark:border-gray-700 dark:bg-gray-900/95">
              <div class="mb-2 flex items-center justify-between text-xs font-bold text-gray-800 dark:text-gray-200">
                <span class="flex items-center gap-1.5">
                  <svg class="h-3.5 w-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                  </svg>
                  <span>Camadas</span>
                </span>
                <svg class="h-3.5 w-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </div>

              <div class="space-y-1.5 text-xs text-gray-700 dark:text-gray-300">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                  <input type="checkbox" id="layer-toggle-all" checked
                    class="h-3.5 w-3.5 rounded border-gray-300 text-emerald-700 focus:ring-emerald-500 dark:border-gray-600 dark:bg-gray-800" />
                  <span class="font-medium text-[11px] sm:text-xs">Observações</span>
                </label>

                <label class="flex items-center gap-2 cursor-pointer select-none">
                  <input type="checkbox" id="layer-toggle-araucarias" checked
                    class="h-3.5 w-3.5 rounded border-gray-300 text-emerald-700 focus:ring-emerald-500 dark:border-gray-600 dark:bg-gray-800" />
                  <span class="flex items-center gap-1 text-[11px] sm:text-xs">
                    <span>🌲</span>
                    <span>Normais</span>
                  </span>
                </label>

                <label class="flex items-center gap-2 cursor-pointer select-none">
                  <input type="checkbox" id="layer-toggle-protegidas" checked
                    class="h-3.5 w-3.5 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 dark:border-gray-600 dark:bg-gray-800" />
                  <span class="flex items-center gap-1 text-[11px] sm:text-xs">
                    <span>🛡️</span>
                    <span>Protegidas</span>
                  </span>
                </label>

                <label class="flex items-center gap-2 cursor-pointer select-none">
                  <input type="checkbox" id="layer-toggle-risco" checked
                    class="h-3.5 w-3.5 rounded border-gray-300 text-amber-600 focus:ring-amber-500 dark:border-gray-600 dark:bg-gray-800" />
                  <span class="flex items-center gap-1 text-[11px] sm:text-xs">
                    <span>⚠️</span>
                    <span>Em risco</span>
                  </span>
                </label>

                <label class="flex items-center gap-2 cursor-pointer select-none">
                  <input type="checkbox" id="layer-toggle-doente" checked
                    class="h-3.5 w-3.5 rounded border-gray-300 text-rose-600 focus:ring-rose-500 dark:border-gray-600 dark:bg-gray-800" />
                  <span class="flex items-center gap-1 text-[11px] sm:text-xs">
                    <span>🍃</span>
                    <span>Doentes</span>
                  </span>
                </label>

                <label class="flex items-center gap-2 cursor-pointer select-none">
                  <input type="checkbox" id="layer-toggle-morta" checked
                    class="h-3.5 w-3.5 rounded border-gray-300 text-zinc-800 focus:ring-zinc-600 dark:border-gray-600 dark:bg-gray-800" />
                  <span class="flex items-center gap-1 text-[11px] sm:text-xs">
                    <span>💀</span>
                    <span>Mortas</span>
                  </span>
                </label>
              </div>

              <div
                class="mt-2.5 pt-2 border-t border-gray-200/80 dark:border-gray-700 flex items-center justify-between">
                <span class="text-[10px] sm:text-[11px] font-medium text-gray-600 dark:text-gray-400">Mapa base</span>
                <button id="btn-toggle-tiles" type="button" title="Alternar estilo do mapa"
                  class="flex items-center gap-1 rounded-lg border border-gray-200 bg-gray-50 px-2 py-0.5 sm:py-1 text-[10px] font-semibold text-gray-700 hover:bg-emerald-50 hover:text-emerald-800 transition dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                  <span class="h-2.5 w-2.5 rounded-full bg-emerald-500 border border-emerald-600 inline-block"></span>
                  <span>OSM</span>
                </button>
              </div>
            </div>

            <!-- Bottom-Left: Floating Observation Preview Card (Exclusivo para dados reais) -->
            <div id="hero-floating-obs" style="display: none;"
              class="pointer-events-auto absolute bottom-3 left-3 right-3 sm:bottom-4 sm:left-4 sm:right-auto sm:max-w-xs flex items-center gap-3 rounded-2xl border border-gray-200/90 bg-white/95 p-2.5 shadow-xl backdrop-blur-md transition-all duration-300 hover:shadow-2xl dark:border-gray-700 dark:bg-gray-900/95 cursor-pointer">
              <img id="floating-obs-img" src="" alt="Foto da Araucária"
                class="h-12 w-12 rounded-xl object-cover ring-1 ring-black/5 flex-shrink-0" style="display: none;" />
              <div id="floating-obs-placeholder" class="h-12 w-12 rounded-xl bg-emerald-100/80 dark:bg-emerald-950/70 text-emerald-800 dark:text-emerald-300 flex items-center justify-center text-lg font-bold flex-shrink-0">
                🌲
              </div>
              <div class="flex-1 min-w-0">
                <h4 id="floating-obs-title" class="truncate text-xs font-bold text-gray-900 dark:text-white">Araucária angustifolia</h4>
                <p id="floating-obs-subtitle" class="text-[11px] text-gray-500 dark:text-gray-400">Carregando dados...</p>
                <span id="floating-obs-badge"
                  class="inline-block rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300 mt-0.5">Saudável</span>
              </div>
              <span class="text-lg font-bold text-emerald-700 dark:text-emerald-400 mr-1">›</span>
            </div>

          </div>

        </div>
      </div>

    </div>
  </div>
</section>