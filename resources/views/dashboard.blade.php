<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Painel') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ tab: 'feed' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="flex border-b border-gray-200 dark:border-gray-700 mb-6 space-x-4">
                <button @click="tab = 'feed'; $dispatch('mudar-aba', 'feed')"
                    :class="tab === 'feed' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="py-3 px-4 border-b-2 text-sm font-medium transition duration-200 focus:outline-none">
                    🌲 Feed da Comunidade
                </button>
            
                <button @click="tab = 'my-obs'; $dispatch('mudar-aba', 'my-obs')"
                    :class="tab === 'my-obs' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="py-3 px-4 border-b-2 text-sm font-medium transition duration-200 focus:outline-none">
                    👤 Minhas Observações
                </button>
            
                <button @click="tab = 'create'; $dispatch('mudar-aba', 'create')"
                    :class="tab === 'create' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="py-3 px-4 border-b-2 text-sm font-medium transition duration-200 focus:outline-none bg-emerald-50 dark:bg-emerald-950/30 rounded-t-lg text-emerald-700">
                    ➕ Registrar Araucária
                </button>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">

                <div x-show="tab === 'feed'" x-transition>
                    <x-araucaria.feed :observations="$observations" />
                </div>

                <div x-show="tab === 'my-obs'" x-transition class="p-6 lg:p-8">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Gerenciar Meus Registros</h3>

                    <div class="overflow-x-auto">
                        <x-araucaria.tabela-registros :observations="$observations" />
                    </div>
                </div>

                <div x-show="tab === 'create'" x-transition class="p-6 lg:p-8">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                        Novo Registro
                    </h3>
                    <x-araucaria.registrar />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>