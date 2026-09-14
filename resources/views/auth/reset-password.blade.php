<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="block">
                <x-label for="email" value="{{ __('E-mail') }}" />
                <x-input id="email" placeholder="Digite seu e-mail"  class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Senha') }}" />
                <x-utils.password-input 
                    id="password" 
                    name="password" 
                    class="block mt-1 w-full" 
                    required 
                    autocomplete="new-password" 
                />
            </div>

            <div class="mt-4">
                <x-label for="password_confirmation" value="{{ __('Confirmar Senha') }}" />
                <x-utils.password-input 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    class="block mt-1 w-full" 
                    required 
                    autocomplete="new-password" 
                />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-button>
                    {{ __('Trocar senha') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
