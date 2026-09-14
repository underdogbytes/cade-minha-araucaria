<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        {{-- Heading --}}
        <div class="mb-6">
            <h2 class="text-2xl font-display font-bold text-gray-900 dark:text-white">
                Crie sua conta
            </h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Junte-se à comunidade e ajude a proteger nossas araucárias.
            </p>
        </div>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div>
                <x-label for="name" value="{{ __('Nome') }}" />
                <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required
                    autofocus autocomplete="name" />
            </div>

            <div class="mt-4">
                <x-label for="username" value="{{ __('Username') }}" />
                <x-input id="username" class="block mt-1 w-full" type="text" name="username" :value="old('username')"
                    required autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="email" value="{{ __('E-mail') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required
                    autocomplete="email" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Senha') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required
                    autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <x-label for="password_confirmation" value="{{ __('Confirmar Senha') }}" />
                <x-input id="password_confirmation" class="block mt-1 w-full" type="password"
                    name="password_confirmation" required autocomplete="new-password" />
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4">
                    <x-label for="terms">
                        <div class="flex items-center">
                            <x-checkbox name="terms" id="terms" required />

                            <div class="ms-2">
                                {!! __('Eu concordo com os :terms_of_service e :privacy_policy', [
                                    'terms_of_service' => '<a target="_blank" href="' . route('terms.show') . '" class="underline text-sm text-araucaria-600 dark:text-araucaria-400 hover:text-araucaria-800 dark:hover:text-araucaria-200 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-araucaria-500 dark:focus:ring-offset-gray-800">' . __('Terms of Service') . '</a>',
                                    'privacy_policy' => '<a target="_blank" href="' . route('policy.show') . '" class="underline text-sm text-araucaria-600 dark:text-araucaria-400 hover:text-araucaria-800 dark:hover:text-araucaria-200 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-araucaria-500 dark:focus:ring-offset-gray-800">' . __('Privacy Policy') . '</a>',
                                ]) !!}
                            </div>
                        </div>
                    </x-label>
                </div>
            @endif

            <div class="mt-6">
                <x-button class="w-full justify-center">
                    {{ __('Registrar') }}
                </x-button>
            </div>

            <div class="mt-6 text-center">
                <span class="text-sm text-gray-500 dark:text-gray-400">Já tem conta?</span>
                <a class="text-sm text-araucaria-600 dark:text-araucaria-400 hover:text-araucaria-800 dark:hover:text-araucaria-200 font-medium transition-colors ms-1"
                    href="{{ route('login') }}">
                    Entre aqui
                </a>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>