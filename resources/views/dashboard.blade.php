<x-app-layout>
    <x-toast-alert />
    
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
        alertType: 'success',
        loadingFeed: false,
        loadingMyObs: false,
        async refreshFeed() {
            this.loadingFeed = true;
            try {
                const res = await fetch('/dashboard/feed-partial');
                if (res.ok) {
                    const html = await res.text();
                    const el = document.getElementById('feed-container');
                    if (el) el.innerHTML = html;
                }
            } catch (e) {
                console.error('Erro ao atualizar feed:', e);
            } finally {
                this.loadingFeed = false;
            }
        },
        async refreshMyObs() {
            this.loadingMyObs = true;
            try {
                const res = await fetch('/dashboard/my-obs-partial');
                if (res.ok) {
                    const html = await res.text();
                    const el = document.getElementById('tabela-registros-container');
                    if (el) el.innerHTML = html;
                }
            } catch (e) {
                console.error('Erro ao atualizar minhas observações:', e);
            } finally {
                this.loadingMyObs = false;
            }
        },
        refreshMap() {
            window.dispatchEvent(new CustomEvent('reload-map', { detail: { mapId: 'map' } }));
        }
    }"
    @observation-saved="
        showAlert = true; alertMessage = 'Araucária salva com sucesso!'; alertType = 'success'; setTimeout(() => showAlert = false, 3000);
        refreshFeed();
        refreshMyObs();
        refreshMap();
    "
    @observation-error="showAlert = true; alertMessage = $event.detail.message; alertType = 'error'; setTimeout(() => showAlert = false, 3000);">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Alert Container -->
            <div x-show="showAlert" x-transition class="mb-4 p-4 rounded-lg"
                :class="alertType === 'success' ? 'bg-emerald-100 border border-emerald-400 text-emerald-700' : 'bg-red-100 border border-red-400 text-red-700'">
                <span x-text="alertMessage"></span>
            </div>
    
            <div class="flex overflow-x-auto whitespace-nowrap border-b border-gray-200 dark:border-gray-700 mb-6 space-x-1 sm:space-x-4 scrollbar-none [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                <button @click="tab = 'feed'; $dispatch('mudar-aba', 'feed'); refreshFeed();"
                    :class="tab === 'feed' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="flex-shrink-0 whitespace-nowrap py-3 px-3 sm:px-4 border-b-2 text-xs sm:text-sm font-medium transition duration-200 focus:outline-none">
                    🌲 Feed da Comunidade
                </button>
    
                <button @click="tab = 'mapa-mundi'; $dispatch('mudar-aba', 'mapa-mundi'); refreshMap();"
                    :class="tab === 'mapa-mundi' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="flex-shrink-0 whitespace-nowrap py-3 px-3 sm:px-4 border-b-2 text-xs sm:text-sm font-medium transition duration-200 focus:outline-none">
                    🌎 Araucárias do Mundo
                </button>

                <button @click="tab = 'my-obs'; subAba = 'tabela'; $dispatch('mudar-aba', 'my-obs'); refreshMyObs();"
                    :class="tab === 'my-obs' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="flex-shrink-0 whitespace-nowrap py-3 px-3 sm:px-4 border-b-2 text-xs sm:text-sm font-medium transition duration-200 focus:outline-none">
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
                    class="flex-shrink-0 whitespace-nowrap py-3 px-3 sm:px-4 border-b-2 text-xs sm:text-sm font-medium transition duration-200 focus:outline-none">
                    ➕ Registrar Araucária
                </button>
            </div>
    
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
    
                <div x-show="tab === 'feed'" x-transition class="relative">
                    <div x-show="loadingFeed" class="absolute inset-0 bg-white/70 dark:bg-gray-800/70 z-10 flex items-center justify-center rounded-lg" x-transition>
                        <x-spinner message="Atualizando feed..." id="feedSpinner" />
                    </div>
                    <div id="feed-container">
                        <x-araucaria.feed :observations="$observations" />
                    </div>
                </div>

                <div x-show="tab === 'mapa-mundi'" x-transition class="p-6 lg:p-8">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Todas as Araucárias</h3>
                    
                    {{--// TODO: unificar mapa global --}}
                    <div class="map-wrapper">
                        <x-spinner message="Carregando mapa..." id="mapSpinner" />
                        <div id="map"></div>
                    </div>
                </div>
    
                <div x-show="tab === 'my-obs'" x-transition class="p-6 lg:p-8">
    
                    <div x-show="subAba === 'tabela'" class="relative">
                        <div x-show="loadingMyObs" class="absolute inset-0 bg-white/70 dark:bg-gray-800/70 z-10 flex items-center justify-center rounded-lg" x-transition>
                            <x-spinner message="Atualizando registros..." id="myObsSpinner" />
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Gerenciar Meus Registros</h3>
                        <div id="tabela-registros-container" class="overflow-x-auto">
                            <x-araucaria.tabela-registros :observations="$observations" :myObservations="$myObservations" />
                        </div>
                    </div>
    
                    <div x-show="subAba === 'editar'" x-cloak x-effect="if (subAba === 'editar') { setTimeout(() => $dispatch('mudar-aba', 'edit'), 50); }">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                Editar Registro #<span x-text="idEdicao"></span>
                            </h3>
                            <button @click="subAba = 'tabela'; idEdicao = null; editLat = ''; editLng = ''; editStage = 'adult'; editGender = 'unknown'; $dispatch('destroy-map', { mapId: 'map-edit' });"
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