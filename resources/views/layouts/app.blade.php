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
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_green.css" />
        <style>
            /* Flatpickr Custom Styling & Dark Mode */
            .flatpickr-calendar {
                font-family: inherit !important;
                border-radius: 0.75rem !important;
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
                border: 1px solid #e5e7eb !important;
            }

            .dark .flatpickr-calendar {
                background: #1f2937 !important;
                border-color: #374151 !important;
                color: #f3f4f6 !important;
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5) !important;
            }

            .dark .flatpickr-calendar .flatpickr-months,
            .dark .flatpickr-calendar .flatpickr-weekdays,
            .dark .flatpickr-calendar .flatpickr-time {
                background: #111827 !important;
                color: #f3f4f6 !important;
            }

            .dark .flatpickr-calendar span.flatpickr-weekday {
                color: #9ca3af !important;
            }

            .dark .flatpickr-calendar .flatpickr-day {
                color: #e5e7eb !important;
            }

            .dark .flatpickr-calendar .flatpickr-day.prevMonthDay,
            .dark .flatpickr-calendar .flatpickr-day.nextMonthDay {
                color: #4b5563 !important;
            }

            .dark .flatpickr-calendar .flatpickr-day:hover {
                background: #374151 !important;
            }

            .dark .flatpickr-calendar .flatpickr-time input {
                color: #f3f4f6 !important;
            }

            .dark .flatpickr-calendar .flatpickr-time input:hover,
            .dark .flatpickr-calendar .flatpickr-time input:focus {
                background: #374151 !important;
            }

            .dark .flatpickr-calendar .numInputWrapper span {
                border-color: #374151 !important;
            }

            .flatpickr-day.selected,
            .flatpickr-day.selected:hover {
                background: #059669 !important;
                border-color: #059669 !important;
                color: #ffffff !important;
            }

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

            /* Geolocalização — marcador do usuário */
            .user-location-pulse {
                animation: location-pulse 2s ease-in-out infinite;
            }

            @keyframes location-pulse {
                0%, 100% { opacity: 0.3; }
                50% { opacity: 0.6; }
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
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/pt.js"></script>
        <script type="module" src="{{ asset('js/map/app.js') }}"></script>
        <script type="module" src="{{ asset('js/map/world-map.js') }}"></script>
    </body>
</html>