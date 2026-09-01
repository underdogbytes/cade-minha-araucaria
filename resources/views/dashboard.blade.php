<x-app-layout>
    <x-toast-alert />
    
    <div class="py-8 sm:py-10" x-data="{ 
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
        editLat = ''; editLng = ''; editPhotoUrl = '';
        refreshFeed();
        refreshMyObs();
        refreshMap();
    "
    @observation-error="showAlert = true; alertMessage = $event.detail.message; alertType = 'error'; setTimeout(() => showAlert = false, 3000);">
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Alert Container -->
            <div x-show="showAlert" x-transition class="mb-6 p-4 rounded-xl shadow-sm border"
                :class="alertType === 'success' ? 'bg-emerald-50 border-emerald-300 text-emerald-800 dark:bg-emerald-950/60 dark:border-emerald-700 dark:text-emerald-200' : 'bg-red-50 border-red-300 text-red-800 dark:bg-red-950/60 dark:border-red-700 dark:text-red-200'">
                <div class="flex items-center space-x-2">
                    <span x-text="alertType === 'success' ? '✅' : '⚠️'"></span>
                    <span class="font-medium text-sm" x-text="alertMessage"></span>
                </div>
            </div>
    
            <!-- Segmented Control Tab Pills Bar -->
            <div class="mb-8 p-1.5 bg-gray-200/70 dark:bg-gray-800/70 backdrop-blur-md rounded-2xl border border-gray-200/80 dark:border-gray-700/80 shadow-inner flex overflow-x-auto no-scrollbar space-x-1 sm:space-x-2">
                <button @click="tab = 'feed'; $dispatch('mudar-aba', 'feed'); refreshFeed();"
                    :class="tab === 'feed' ? 'bg-white dark:bg-emerald-700 text-emerald-900 dark:text-white shadow-md font-bold' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-white/40 dark:hover:bg-gray-700/40'"
                    class="flex-1 min-w-[130px] flex items-center justify-center space-x-2 py-2.5 px-4 rounded-xl text-xs sm:text-sm transition duration-200 focus:outline-none">
                    <span>🌲</span>
                    <span>Feed da Comunidade</span>
                </button>
    
                <button @click="tab = 'mapa-mundi'; $dispatch('mudar-aba', 'mapa-mundi'); refreshMap();"
                    :class="tab === 'mapa-mundi' ? 'bg-white dark:bg-emerald-700 text-emerald-900 dark:text-white shadow-md font-bold' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-white/40 dark:hover:bg-gray-700/40'"
                    class="flex-1 min-w-[130px] flex items-center justify-center space-x-2 py-2.5 px-4 rounded-xl text-xs sm:text-sm transition duration-200 focus:outline-none">
                    <span>🌎</span>
                    <span>Araucárias do Mundo</span>
                </button>

                <button @click="tab = 'my-obs'; subAba = 'tabela'; $dispatch('mudar-aba', 'my-obs'); refreshMyObs();"
                    :class="tab === 'my-obs' ? 'bg-white dark:bg-emerald-700 text-emerald-900 dark:text-white shadow-md font-bold' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-white/40 dark:hover:bg-gray-700/40'"
                    class="flex-1 min-w-[130px] flex items-center justify-center space-x-2 py-2.5 px-4 rounded-xl text-xs sm:text-sm transition duration-200 focus:outline-none">
                    <span>👤</span>
                    <span>Minhas Observações</span>
                </button>
    
                <button @click="
                    tab = 'create';
                    idEdicao = null;
                    editLat = '';
                    editLng = '';
                    editStage = 'adult';
                    editGender = 'unknown';
                    editPhotoUrl = '';
                    document.getElementById('araucariaForm-create')?.reset();
                    window.dispatchEvent(new CustomEvent('reset-form-photos'));
                    $dispatch('mudar-aba', 'create')"
                    :class="tab === 'create' ? 'bg-white dark:bg-emerald-700 text-emerald-900 dark:text-white shadow-md font-bold' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-white/40 dark:hover:bg-gray-700/40'"
                    class="flex-1 min-w-[130px] flex items-center justify-center space-x-2 py-2.5 px-4 rounded-xl text-xs sm:text-sm transition duration-200 focus:outline-none">
                    <span>➕</span>
                    <span>Registrar Araucária</span>
                </button>
            </div>
    
            <!-- Main Content Container -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200/80 dark:border-gray-700/80 overflow-hidden">
    
                <!-- Aba: Feed -->
                <div x-show="tab === 'feed'" x-transition class="relative">
                    <div x-show="loadingFeed" class="absolute inset-0 bg-white/80 dark:bg-gray-800/80 z-20 flex items-center justify-center backdrop-blur-sm rounded-2xl" x-transition>
                        <x-spinner message="Atualizando feed da comunidade..." id="feedSpinner" />
                    </div>
                    <div id="feed-container">
                        <x-araucaria.feed :observations="$observations" />
                    </div>
                </div>

                <!-- Aba: Mapa Mundi -->
                <div x-show="tab === 'mapa-mundi'" x-transition class="p-6 lg:p-8">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-xl font-bold font-display text-gray-900 dark:text-white">Todas as Araucárias Mapeadas</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Navegue pelo mapa para visualizar a localização de todos os registros da comunidade.</p>
                        </div>
                    </div>
                    
                    <div class="rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 shadow-md h-[550px] relative">
                        <x-spinner message="Carregando mapa..." id="mapSpinner" />
                        <div id="map" class="w-full h-full"></div>
                    </div>
                </div>
    
                <!-- Aba: Minhas Observações -->
                <div x-show="tab === 'my-obs'" x-transition class="p-6 lg:p-8">
    
                    <div x-show="subAba === 'tabela'" class="relative">
                        <div x-show="loadingMyObs" class="absolute inset-0 bg-white/80 dark:bg-gray-800/80 z-20 flex items-center justify-center backdrop-blur-sm rounded-xl" x-transition>
                            <x-spinner message="Atualizando seus registros..." id="myObsSpinner" />
                        </div>
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h3 class="text-xl font-bold font-display text-gray-900 dark:text-white">Meus Registros</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Gerencie as observações de Araucárias que você cadastrou no sistema.</p>
                            </div>
                        </div>
                        <div id="tabela-registros-container" class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                            <x-araucaria.tabela-registros :observations="$observations" :myObservations="$myObservations" />
                        </div>
                    </div>
    
                    <div x-show="subAba === 'editar'" x-cloak x-effect="if (subAba === 'editar') { setTimeout(() => $dispatch('mudar-aba', 'edit'), 50); }">
                        <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                            <div>
                                <h3 class="text-xl font-bold font-display text-gray-900 dark:text-white">
                                    Editar Registro #<span x-text="idEdicao"></span>
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Atualize as informações da observação ou altere a localização no mapa.</p>
                            </div>
                            <button @click="subAba = 'tabela'; idEdicao = null; editLat = ''; editLng = ''; editStage = 'adult'; editGender = 'unknown'; $dispatch('destroy-map', { mapId: 'map-edit' });"
                                class="text-xs font-semibold bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 py-2 px-4 rounded-lg transition border border-gray-300 dark:border-gray-600 flex items-center space-x-1">
                                <span>← Voltar para Tabela</span>
                            </button>
                        </div>

                        <x-araucaria.form modo="editar" />
                    </div>
                </div>
    
                <!-- Aba: Registrar Araucária -->
                <div x-show="tab === 'create'" x-transition class="p-6 lg:p-8">
                    <div class="mb-6">
                        <h3 class="text-xl font-bold font-display text-gray-900 dark:text-white">Registrar Nova Araucária</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Marque a localização no mapa, adicione uma foto da árvore e informe seu estágio ecológico.</p>
                    </div>
                    <x-araucaria.form modo="criar" />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>