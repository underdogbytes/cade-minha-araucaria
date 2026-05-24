<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Cade minha Araucária') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
        @endif
    </head>

    <body>
        <header>
            <!-- TODO: fazer navbar legal -->
            @if (Route::has('login'))
                <nav>
                    @auth
                        <a href="{{ url('/dashboard') }}">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}">
                            Entrar
                        </a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}">
                                Cadastrar-se
                            </a>
                        @endif
                    @endauth
                </nav>
            @endif
        </header>
        <main>
            <section>
                <!-- TODO: hero -->
                <h1>Cade minha Araucária?</h1>
                <p>
                    Suba registros de araucárias que encontrar pela sua região e
                    ajude a mapear e proteger a espécie!
                </p>
            </section>

            <section>
                <!-- TODO: mapa interativo global ou descrever objetivos do projeto?? -->
            </section>

            <section>
                <!-- TODO: CTA -->
                <a href="{{ route('register') }}">Cadastrar-se</a>
                <a href="{{ route('login') }}">Entrar</a>
            </section>
        </main>

        <footer>
            <!-- TODO: coluna com nome e logo, coluna com links institucionais e coluna com links para contato -->
        </footer>
    </body>

</html>