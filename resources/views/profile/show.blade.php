<x-app-layout>
    @include('profile.partials.header', ['user' => $user])

    @auth
        <div x-data="{ tab: 'feed' }" class="mt-4 max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
        <!-- Tabs -->
        <ul class="flex border-b mb-6">
            <li @click="tab = 'feed'"
                :class="tab === 'feed' ? 'border-b-2 border-indigo-500' : ''"
                class="cursor-pointer py-2 px-4">
                Fotos
            </li>
            <li @click="tab = 'settings'"
                :class="tab === 'settings' ? 'border-b-2 border-indigo-500' : ''"
                class="cursor-pointer py-2 px-4 mr-4">
                Configurações
            </li>
        </ul>
    
        <!-- Tab Contents -->
        <div x-show="tab === 'feed'">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
                <x-profile.post :observations="$user->araucariaObservations" />
            </div>
        </div>
        <div x-show="tab === 'settings'">
            @include('profile.partials.settings')
        </div>
    </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
            <x-profile.post :observations="$user->araucariaObservations" />
        </div>
    @endauth
</x-app-layout>