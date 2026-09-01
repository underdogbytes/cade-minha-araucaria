<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Cadê minha Araucária? — Mapeamento Comunitário de Araucárias Nativas</title>
        <meta name="description" content="Projeto comunitário open-source para mapear, monitorar e proteger as Araucárias nativas (Araucaria angustifolia) da nossa região.">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700|outfit:500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles

        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
        <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
    </head>
    <body class="font-sans antialiased bg-slate-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100 selection:bg-emerald-500 selection:text-white">
        <!-- Top Navigation -->
        <header class="sticky top-0 z-50 bg-white/80 dark:bg-gray-900/80 backdrop-blur-md border-b border-emerald-500/10 dark:border-emerald-500/20 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <a href="{{ url('/') }}" class="flex items-center space-x-3 group">
                    <x-application-mark class="h-9 w-auto transform group-hover:scale-105 transition duration-200" />
                </a>

                <div class="flex items-center space-x-3">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="px-4 py-2 text-sm font-semibold rounded-lg bg-emerald-700 hover:bg-emerald-800 dark:bg-emerald-600 dark:hover:bg-emerald-500 text-white shadow-md shadow-emerald-700/20 transition duration-200">
                                Painel do Usuário →
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-emerald-800 dark:text-emerald-400 hover:text-emerald-900 dark:hover:text-emerald-300 transition">
                                Entrar
                            </a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-semibold rounded-lg bg-emerald-700 hover:bg-emerald-800 dark:bg-emerald-600 dark:hover:bg-emerald-500 text-white shadow-md shadow-emerald-700/20 transition duration-200">
                                    Cadastrar-se
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </header>

        <main>
            <!-- Hero Section -->
            <section class="relative pt-12 pb-16 lg:pt-20 lg:pb-24 overflow-hidden hero-gradient">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
                    
                    <!-- Eco Badge -->
                    <div class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-full bg-emerald-100/80 dark:bg-emerald-950/60 border border-emerald-300/50 dark:border-emerald-700/50 text-emerald-800 dark:text-emerald-300 text-xs font-semibold uppercase tracking-wider mb-6 backdrop-blur-sm">
                        <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>🌲 Mapeamento Comunitário & Conservação Nativista</span>
                    </div>

                    <!-- Main Headline -->
                    <h1 class="font-display text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-gray-900 dark:text-white max-w-4xl mx-auto leading-tight">
                        Mapear, monitorar e proteger as <span class="bg-gradient-to-r from-emerald-700 via-emerald-600 to-teal-600 dark:from-emerald-400 dark:to-teal-300 bg-clip-text text-transparent">Araucárias nativas</span> da nossa terra.
                    </h1>

                    <p class="mt-6 text-lg sm:text-xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto leading-relaxed">
                        Ajude a construir um mapa vivo das nossas florestas. Cadastre observações, fotos e localizações para proteger a *Araucaria angustifolia* e preservar o ecossistema.
                    </p>

                    <!-- CTA Buttons -->
                    <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="w-full sm:w-auto px-8 py-3.5 text-base font-bold rounded-xl bg-emerald-700 hover:bg-emerald-800 dark:bg-emerald-600 dark:hover:bg-emerald-500 text-white shadow-lg shadow-emerald-700/25 hover:shadow-xl hover:-translate-y-0.5 transition duration-200 flex items-center justify-center space-x-2">
                                <span>🌲 Acessar Mapa & Registrar</span>
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-3.5 text-base font-bold rounded-xl bg-emerald-700 hover:bg-emerald-800 dark:bg-emerald-600 dark:hover:bg-emerald-500 text-white shadow-lg shadow-emerald-700/25 hover:shadow-xl hover:-translate-y-0.5 transition duration-200 flex items-center justify-center space-x-2">
                                <span>🌱 Começar a Contribuir</span>
                            </a>
                            <a href="#mapa-ancora" class="w-full sm:w-auto px-8 py-3.5 text-base font-semibold rounded-xl bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200 border border-gray-200 dark:border-gray-700 shadow-sm transition duration-200 flex items-center justify-center space-x-2">
                                <span>🗺️ Ver Mapa Comunitário</span>
                            </a>
                        @endauth
                    </div>
                </div>
            </section>

            <!-- Map Interactive Section -->
            <section class="py-12 bg-white dark:bg-gray-900 border-t border-b border-gray-100 dark:border-gray-800" idmapa-ancora" id="mapa-ancora">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center max-w-3xl mx-auto mb-8">
                        <h2 class="font-display text-3xl font-bold text-gray-900 dark:text-white">
                            Mapa Global de Registros
                        </h2>
                        <p class="mt-2 text-gray-600 dark:text-gray-400">
                            Explore abaixo as árvores catalogadas por cidadãos e pesquisadores da nossa comunidade:
                        </p>
                    </div>

                    <!-- Map Card Frame -->
                    <div class="rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-700 shadow-xl bg-gray-50 dark:bg-gray-800 relative">
                        <div class="bg-gray-100 dark:bg-gray-800 px-6 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block animate-ping"></span>
                                <span class="text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">
                                    Visualização de Geo-Mapeamento ao Vivo
                                </span>
                            </div>
                            <span class="text-xs text-gray-400 hidden sm:inline">Leaflet.js + MarkerCluster</span>
                        </div>

                        <div class="relative w-full h-[520px]">
                            <x-spinner message="Carregando mapa interativo..." id="mapSpinner" />
                            <div id="map" class="w-full h-full"></div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- FAQ Section -->
            <section class="py-16 lg:py-24 bg-slate-50 dark:bg-gray-900/50" id="perguntas-frequentes">
                <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center mb-12">
                        <span class="text-emerald-700 dark:text-emerald-400 text-xs font-bold uppercase tracking-wider">Perguntas Frequentes</span>
                        <h2 class="font-display text-3xl font-bold text-gray-900 dark:text-white mt-1">
                            Tudo o que você precisa saber
                        </h2>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200/80 dark:border-gray-700/80 p-6 sm:p-8">
                        <x-faq.accordion :items="[
                            [
                                'question' => 'O que é o projeto Cadê minha Araucária?',
                                'answer' => '<b>Cadê minha Araucária</b> é uma plataforma cidadã aberta desenvolvida para mapear, monitorar e promover a conservação das Araucárias nativas (<i>Araucaria angustifolia</i>) e da Mata dos Pinhais.'
                            ],
                            [
                                'question' => 'Por que é vital monitorar as Araucárias?',
                                'answer' => 'Resta uma fração mínima da cobertura original de Mata de Araucárias. O registro individual nos permite mapear espécimes adultas reprodutoras, mudas e áreas com risco de desmatamento ilegal.'    
                            ],
                            [
                                'question' => 'Quanto resta da floresta original?',
                                'answer' => 'Estudos recentes (2025) estimam que restam apenas <b>4,3%</b> da floresta original de Araucárias.<br><br>Leia a pesquisa completa:<br><a href=\'https://www.sciencedirect.com/science/article/pii/S0006320724002854\' target=\'_blank\' class=\'text-emerald-600 dark:text-emerald-400 underline font-medium\'>How much Araucaria Mixed Forest remains? Novel perspectives on conservation status (ScienceDirect)</a>'
                            ],
                            [
                                'question' => 'Como posso enviar fotos com coordenadas GPS automaticamente?',
                                'answer' => 'Ao tirar fotos com seu celular com o GPS/Localização ativado, o arquivo da imagem armazena dados EXIF de latitude e longitude. Nossa plataforma lê estes dados automaticamente ao enviar a imagem!'
                            ]
                        ]" />
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <div class="flex justify-center items-center space-x-2 mb-4">
                    <x-application-mark class="h-6 w-auto opacity-75" />
                    <span class="font-display font-bold text-gray-700 dark:text-gray-300">Cadê minha Araucária?</span>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 max-w-md mx-auto mb-4">
                    Projeto comunitário e de código aberto dedicado à proteção ambiental e à biodiversidade nativa.
                </p>
                <div class="flex items-center justify-center space-x-4 text-xs text-gray-500 dark:text-gray-400">
                    <span>&copy; {{ date('Y') }} Todos os direitos reservados.</span>
                    <span>•</span>
                    <a href="https://github.com/underdogbytes/cade-minha-araucaria" target="_blank" class="text-emerald-700 dark:text-emerald-400 font-semibold hover:underline flex items-center space-x-1">
                        <span>Código fonte no GitHub</span>
                    </a>
                </div>
            </div>
        </footer>

        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
        <script type="module" src="{{ asset('js/map/world-map.js') }}"></script>
        @livewireScripts
    </body>
</html>