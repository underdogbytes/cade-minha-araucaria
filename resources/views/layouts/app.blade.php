<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Cadê minha Araucária?</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles

        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <style>
            .map-flex-container {
                display: flex;
                flex-direction: column;
                height: 550px;
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
                min-height: 350px;
                height: 100%;
                border-radius: 8px 0 0 8px;
                z-index: 1;
            }
        
            #form-container {
                flex: 1;
                padding: 20px;
                background: white;
                overflow-y: auto;
                border-radius: 0 8px 8px 0;
            }
        
            .form-group {
                margin-bottom: 15px;
            }
        
            .form-group label {
                display: block;
                margin-bottom: 5px;
                font-weight: bold;
            }
        
            .form-group input,
            .form-group select {
                width: 100%;
                padding: 8px;
                box-sizing: border-box;
                border-radius: 4px;
                border: 1px solid #ccc;
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <x-banner />

        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @livewire('navigation-menu')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
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
        <script type="module" src="{{ asset('js/map/app.js') }}"></script>
    </body>
</html>