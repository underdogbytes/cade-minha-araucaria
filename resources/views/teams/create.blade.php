<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create Team') }}
        </h2>
    </x-slot>

    <div>
        <div class="max-w-screen-2xl mx-auto">
            @livewire('teams.create-team-form')
        </div>
    </div>
</x-app-layout>