<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        {{-- Heading --}}
        <div class="mb-6">
            <h2 class="text-2xl font-display font-bold text-gray-900 dark:text-white">
                Bem-vindo de volta
            </h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Entre na sua conta para continuar mapeando araucárias.
            </p>
        </div>

        <x-validation-errors class="mb-4" />

        @session('status')
            <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                {{ $value }}
            </div>
        @endsession

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <x-label for="email" value="{{ __('E-mail') }}" />
                <x-input id="email" placeholder="Digite seu e-mail" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Senha') }}" />
                <x-utils.password-input 
                    id="password" 
                    name="password" 
                    class="block mt-1 w-full" 
                    required 
                    autocomplete="current-password" 
                    placeholder="Digite sua senha" 
                />
            </div>

            <div class="flex items-center justify-between mt-4">
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember" />
                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Permanecer conectado') }}</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-sm text-araucaria-600 dark:text-araucaria-400 hover:text-araucaria-800 dark:hover:text-araucaria-200 font-medium transition-colors" href="{{ route('password.request') }}">
                        {{ __('Esqueceu sua senha?') }}
                    </a>
                @endif
            </div>

            <div class="mt-6">
                <x-button class="w-full justify-center">
                    {{ __('Entrar') }}
                </x-button>
            </div>

            <div class="mt-6 text-center">
                <span class="text-sm text-gray-500 dark:text-gray-400">Não tem conta?</span>
                <a class="text-sm text-araucaria-600 dark:text-araucaria-400 hover:text-araucaria-800 dark:hover:text-araucaria-200 font-medium transition-colors ms-1" href="{{ route('register') }}">
                    Cadastre-se
                </a>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
