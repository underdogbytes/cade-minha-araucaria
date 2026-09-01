<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Cadê minha Araucária?</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700|outfit:500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles

        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <style>
            .map-flex-container {
                display: flex;
                flex-direction: column;
                min-height: 520px;
                border-radius: 1rem;
                overflow: hidden;
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            }
        
            @media (min-width: 768px) {
                .map-flex-container {
                    flex-direction: row;
                }
            }
        
            #map,
            #map-create,
            #map-edit {
                flex: 2;
                min-height: 380px;
                height: 100%;
                z-index: 1;
            }
        
            #form-container {
                flex: 1;
                padding: 1.5rem;
                overflow-y: auto;
            }
        
            .form-group {
                margin-bottom: 1rem;
            }
        
            .form-group label {
                display: block;
                margin-bottom: 0.375rem;
                font-weight: 600;
                font-size: 0.875rem;
            }
        
            .form-group input,
            .form-group select {
                width: 100%;
                padding: 0.625rem 0.875rem;
                box-sizing: border-box;
                border-radius: 0.5rem;
                border: 1px solid #d1d5db;
                transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            }
            .form-group input:focus,
            .form-group select:focus {
                outline: none;
                border-color: #327a55;
                box-shadow: 0 0 0 3px rgba(50, 122, 85, 0.2);
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <x-banner />

        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @auth
                @livewire('navigation-menu')
            @endauth

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        @stack('modals')

        @livewireScripts

        <script src="https://cdn.jsdelivr.net/npm/exifreader@4.41.0/dist/exif-reader.min.js"></script>
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
        <script type="module" src="{{ asset('js/map/app.js') }}"></script>
        <script type="module" src="{{ asset('js/map/world-map.js') }}"></script>
    </body>
</html>