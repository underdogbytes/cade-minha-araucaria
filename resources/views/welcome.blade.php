<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Cadê minha Araucária? — Mapeamento Comunitário de Araucárias Nativas</title>
        <meta name="description"
            content="Projeto comunitário open-source para mapear, monitorar e proteger as Araucárias nativas (Araucaria angustifolia) da nossa região.">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link
            href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700|outfit:500,600,700,800&display=swap"
            rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles

        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
        <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />

        <style>
            /* Geolocalização — marcador do usuário */
            .user-location-pulse {
                animation: location-pulse 2s ease-in-out infinite;
            }

            @keyframes location-pulse {

                0%,
                100% {
                    opacity: 0.3;
                }

                50% {
                    opacity: 0.6;
                }
            }

            .user-locate-control .user-locate-button {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 34px;
                height: 34px;
                font-size: 18px;
                line-height: 34px;
                text-decoration: none;
                cursor: pointer;
                background: white;
            }

            .user-locate-control .user-locate-button:hover {
                background: #f4f4f4;
            }

            .user-location-tooltip {
                font-size: 12px;
                font-weight: 600;
                color: #1a73e8;
            }
        </style>
    </head>

    <body
        class="font-sans antialiased bg-slate-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100 selection:bg-emerald-500 selection:text-white">
        <!-- Top Navigation -->
        <header
            class="sticky top-0 z-50 bg-white/80 dark:bg-gray-900/80 backdrop-blur-md border-b border-emerald-500/10 dark:border-emerald-500/20 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <a href="{{ url('/') }}" class="flex items-center space-x-3 group">
                    <x-application-mark class="h-9 w-auto transform group-hover:scale-105 transition duration-200" />
                </a>

                <div class="flex items-center space-x-3">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}"
                                class="px-4 py-2 text-sm font-semibold rounded-lg bg-emerald-700 hover:bg-emerald-800 dark:bg-emerald-600 dark:hover:bg-emerald-500 text-white shadow-md shadow-emerald-700/20 transition duration-200">
                                Painel do Usuário →
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                                class="px-4 py-2 text-sm font-medium text-emerald-800 dark:text-emerald-400 hover:text-emerald-900 dark:hover:text-emerald-300 transition">
                                Entrar
                            </a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                    class="px-4 py-2 text-sm font-semibold rounded-lg bg-emerald-700 hover:bg-emerald-800 dark:bg-emerald-600 dark:hover:bg-emerald-500 text-white shadow-md shadow-emerald-700/20 transition duration-200">
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
            <x-website.hero 
                :users-count="$usersCount" 
                :observations-count="$observationsCount" 
                :cities-count="$citiesCount" 
            />

            <!-- FAQ Section -->
            <section id="perguntas-frequentes">
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
            </section>

            <!-- Apoiadores Section -->
            <section class="py-16 bg-slate-50 dark:bg-gray-950 border-t border-gray-100 dark:border-gray-800">
                <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">

                    <!-- Section Label -->
                    <div>
                        <span>Nossos</span>
                    </div>

                    <h2 class="font-display text-3xl font-bold text-gray-900 dark:text-white mb-3">
                        Apoiadores
                    </h2>

                    <!-- Logos -->
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-10 sm:gap-16">
                        <!-- Gralharia -->
                        <div
                            class="group flex items-center justify-center bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md hover:-translate-y-1 transition duration-300 px-10 py-8 min-w-[200px]">
                            <a href="https://gralharia.com.br" target="_blank">
                                <img src="{{ asset('images/apoiadores/gralharia.png') }}" alt="Gralharia"
                                    class="max-h-20 w-auto object-contain grayscale group-hover:grayscale-0 opacity-70 group-hover:opacity-100 transition duration-300" />
                            </a>
                        </div>

                        <!-- Design CWB -->
                        <div
                            class="group flex items-center justify-center bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md hover:-translate-y-1 transition duration-300 px-10 py-8 min-w-[200px]">
                            <a href="https://design.curitiba.br/" target="_blank">
                                <img src="{{ asset('images/apoiadores/logo-dsgn-cwb-horizontal.png') }}"
                                    alt="Design CWB"
                                    class="max-h-20 w-auto object-contain grayscale group-hover:grayscale-0 opacity-70 group-hover:opacity-100 transition duration-300" />
                            </a>
                        </div>

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
                    <a href="https://github.com/underdogbytes/cade-minha-araucaria" target="_blank"
                        class="text-emerald-700 dark:text-emerald-400 font-semibold hover:underline flex items-center space-x-1">
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