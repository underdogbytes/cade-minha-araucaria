<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Cade minha Araucária?</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles

        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <link rel="stylesheet" href="https://app.unpkg.com/leaflet.markercluster@1.4.1/files/dist/MarkerCluster.css" />
        <link rel="stylesheet" href="https://app.unpkg.com/leaflet.markercluster@1.4.1/files/dist/MarkerCluster.Default.css" />
        <style>
            .btn {
                display: inline-block;
                margin: 10px 5px;
                padding: 10px 20px;
                background: #1e4620;
                color: white;
                text-decoration: none;
                border-radius: 4px;
                font-weight: 500;
            }

           .btn-primary {
                background: #336336;
            }

            .btn-primary:hover {
                background: #35763a;
            }

            .btn-secondary {
                background: #3b518e;
            }

            .btn-secondary:hover {
                background: #4861a7;
            }

            h1 {
                font-size: 2rem;
                margin-bottom: 10px;
                color: #1b4332;
                padding: 0 20px;
            }

            .map-section {
                padding: 60px 0 0 0;
                /* Remove padding lateral */
                background: #f4f9f4;
                text-align: center;
            }
        
            .map-section p {
                color: #555;
                padding: 0 20px;
            }
        
            .map-wrapper {
                width: 100%;
                height: 500px;
                margin-top: 40px;
                box-shadow: inset 0 8px 10px -10px rgba(0, 0, 0, 0.15), inset 0 -8px 10px -10px rgba(0, 0, 0, 0.15);
            }
        
            #map {
                width: 100%;
                height: 100%;
            }
        </style>
    </head>
    <body>
        <main>
            <section class="map-section" id="mapa-ancora">
                <h1>Cadê minha Araucária?</h1>
                <p>
                    Projeto para mapear, monitorar e proteger as Araucárias nativas da nossa região.
                    <br>
                    Cadastre-se para contribuir com observações, fotos e informações sobre as araucárias da sua região.
                </p>

                @if (Route::has('login'))
                    @auth
                    <a class="btn btn-primary" href="{{ url('/dashboard') }}">
                        Acessar página interna
                    </a>
                    @else
                    <a class="btn btn-primary" href="{{ route('login') }}">
                        Entrar
                    </a>
                
                    @if (Route::has('register'))
                    <a class="btn btn-secondary" href="{{ route('register') }}">
                        Cadastrar-se
                    </a>
                    @endif
                    @endauth
                @endif

                {{-- // TODO: unificar mapa global --}}
                <p>Veja abaixo todas as árvores já registradas pela nossa comunidade:</p>

                <div class="map-wrapper">
                    <x-spinner message="Carregando mapa..." id="mapSpinner" />
                    <div id="map"></div>
                </div>
            </section>

            <section id="perguntas-frequentes">
                <x-faq.accordion :items="[
                    [
                        'question' => 'O que esse site faz?',
                        'answer' => '<b>Cade minha Araucária</b> é um projeto para mapear, monitorar e proteger as Araucárias nativas da nossa região.'
                    ],
                    [
                        'question' => 'Por quê é importante cuidar das Araucárias?',
                        'answer' => 'Resta realmente muito pouco da mata original e a gente precisa fazer algo. Cuide da Araucária da sua vizinhança e registre ela aqui para que possamos conhecê-la e monitorar contigo!'    
                    ],
                    [
                        'question' => 'Quanto resta da mata original?',
                        'answer' => 'Segundo uma pesquisa publicada em 2025, restam 4,3% da cobertura de mata original.
                        <br>Leia mais em:<br>
                        How much Araucaria Mixed Forest remains? Novel perspectives on conservation status based on satellite imagery and policy review
                        <a href=\'https://www.sciencedirect.com/science/article/pii/S0006320724002854\' target=\'_blank\'>
                            https://www.sciencedirect.com/science/article/pii/S0006320724002854</a>'
                    ]
                ]" />
            </section>
        </main>

        <footer>
            <p style="text-align: center; padding: 20px; color: #666;">
                &copy; {{ date('Y') }} Cade minha Araucária.
                Projeto de código aberto.
                <br>
                Contribua em:
                <a href="github.com/underdogbytes/cade-minha-araucaria" target="_blank" style="color: #1e4620; text-decoration: none;">
                    github.com/underdogbytes/cade-minha-araucaria
                </a>  
            </p>
        </footer>

        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
        <script type="module" src="{{ asset('js/map/world-map.js') }}"></script>
        @livewireScripts
    </body>
</html>