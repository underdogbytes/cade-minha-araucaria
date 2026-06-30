<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Painel') }}
        </h2>
    </x-slot>

    <div x-data="{ alerta: { mostrar: false, tipo: 'success', mensagem: '' } }" @observation-saved.window="
            alerta.mostrar = true;
            alerta.tipo = 'success';
            alerta.mensagem = $event.detail.message;
            setTimeout(() => alerta.mostrar = false, 5000);
         " @observation-error.window="
            alerta.mostrar = true;
            alerta.tipo = 'error';
            alerta.mensagem = $event.detail.message;
            setTimeout(() => alerta.mostrar = false, 5000);
         ">
    
        <div x-show="alerta.mostrar" x-transition :class="alerta.tipo === 'success' ? 'bg-emerald-500' : 'bg-red-500'"
            class="fixed top-5 right-5 z-50 text-white px-6 py-3 rounded-lg shadow-xl font-semibold flex items-center space-x-2">
            <span x-text="alerta.tipo === 'success' ? '✅' : '❌'"></span>
            <span x-text="alerta.mensagem"></span>
        </div>
    </div>
    
    <div class="py-12" x-data="{ 
        tab: 'feed', 
        subAba: 'tabela', 
        idEdicao: null,
        editLat: '',
        editLng: '',
        editStage: 'adult',
        editGender: 'unknown',
        editPhotoUrl: '',
        editObservedAt: '',
        exifLat: null,
        exifLng: null,
        exifDate: null,
        showAlert: false,
        alertMessage: '',
        alertType: 'success'
    }"
    @observation-saved="showAlert = true; alertMessage = 'Araucária salva com sucesso!'; alertType = 'success'; setTimeout(() => showAlert = false, 3000);"
    @observation-error="showAlert = true; alertMessage = $event.detail.message; alertType = 'error'; setTimeout(() => showAlert = false, 3000);">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Alert Container -->
            <div x-show="showAlert" x-transition class="mb-4 p-4 rounded-lg"
                :class="alertType === 'success' ? 'bg-emerald-100 border border-emerald-400 text-emerald-700' : 'bg-red-100 border border-red-400 text-red-700'">
                <span x-text="alertMessage"></span>
            </div>
    
            <div class="flex border-b border-gray-200 dark:border-gray-700 mb-6 space-x-4">
                <button @click="tab = 'feed'; $dispatch('mudar-aba', 'feed')"
                    :class="tab === 'feed' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="py-3 px-4 border-b-2 text-sm font-medium transition duration-200 focus:outline-none">
                    🌲 Feed da Comunidade
                </button>
    
                <button @click="tab = 'my-obs'; subAba = 'tabela'; $dispatch('mudar-aba', 'my-obs')"
                    :class="tab === 'my-obs' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="py-3 px-4 border-b-2 text-sm font-medium transition duration-200 focus:outline-none">
                    👤 Minhas Observações
                </button>
    
                <button @click="
                    tab = 'create';
                    idEdicao = null;
                    editLat = '';
                    editLng = '';
                    editStage = 'adult';
                    editGender = 'unknown';
                    editPhotoUrl = '';
                    document.getElementById('araucariaForm')?.reset();
                    $dispatch('mudar-aba', 'create')"
                    :class="tab === 'create' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="py-3 px-4 border-b-2 text-sm font-medium transition duration-200 focus:outline-none">
                    ➕ Registrar Araucária
                </button>
            </div>
    
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
    
                <div x-show="tab === 'feed'" x-transition>
                    <x-araucaria.feed :observations="$observations" />
                </div>
    
                <div x-show="tab === 'my-obs'" x-transition class="p-6 lg:p-8">
    
                    <div x-show="subAba === 'tabela'">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Gerenciar Meus Registros</h3>
                        <div class="overflow-x-auto">
                            <x-araucaria.tabela-registros :observations="$observations" />
                        </div>
                    </div>
    
                    <div x-show="subAba === 'editar'" x-cloak x-effect="if (subAba === 'editar') { setTimeout(() => $dispatch('mudar-aba', 'edit'), 50); }">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                Editar Registro #<span x-text="idEdicao"></span>
                            </h3>
                            <button @click="subAba = 'tabela'; idEdicao = null; editLat = ''; editLng = ''; editStage = 'adult'; editGender = 'unknown';"
                                class="text-sm bg-gray-500 hover:bg-gray-600 text-white font-bold py-1 px-3 rounded transition">
                                Voltar para Tabela
                            </button>
                        </div>

                        <x-araucaria.form modo="editar" />
                    </div>
                </div>
    
                <div x-show="tab === 'create'" x-transition class="p-6 lg:p-8">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Novo Registro</h3>
                    <x-araucaria.form modo="criar" />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>