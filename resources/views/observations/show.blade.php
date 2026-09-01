<x-app-layout>
  <x-slot name="header">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
      <x-page-header title="Registro de Araucária — #{{ $observation->id }}" />
      
      <div class="flex items-center space-x-3">
        @if($observation->is_shared)
          <span class="inline-flex items-center space-x-1 px-3 py-1 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 text-xs font-bold border border-emerald-300 dark:border-emerald-700">
            <span>👥 Árvore Colaborativa</span>
          </span>
        @else
          <span class="inline-flex items-center space-x-1 px-3 py-1 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-xs font-bold border border-gray-300 dark:border-gray-600">
            <span>🔒 Registro Privado</span>
          </span>
        @endif

        @if(auth()->id() === $observation->user_id || in_array(auth()->user()->role ?? 'user', ['admin', 'staff']))
          <form method="POST" action="{{ route('observations.toggle-shared', $observation->id) }}">
            @csrf
            @method('PATCH')
            <button type="submit" class="text-xs font-semibold px-3 py-1 rounded-lg bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 transition">
              {{ $observation->is_shared ? 'Tornar Privada' : 'Tornar Colaborativa' }}
            </button>
          </form>
        @endif
      </div>
    </div>
  </x-slot>

  <div class="py-8 sm:py-12" x-data="{ showModal: false, previewPhotoUrl: null }">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
      <x-toast-alert />

      <!-- Card Principal de Detalhes -->
      <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-2xl border border-gray-200/80 dark:border-gray-700/80 p-6 lg:p-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div class="flex flex-col items-center justify-center bg-gray-50 dark:bg-gray-900 rounded-xl p-3 border border-gray-100 dark:border-gray-700 relative group">
            @include('observations.partials.image', ['observation' => $observation])
            <span class="absolute bottom-5 left-5 bg-black/60 backdrop-blur-md text-white text-[11px] font-semibold px-2.5 py-1 rounded-lg">
              Foto Original do Registro
            </span>
          </div>

          <div class="flex flex-col justify-between space-y-6">
            @include('observations.partials.features', ['observation' => $observation])
            @include('observations.partials.do-u-know')
            
            <div class="pt-4 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
              <x-araucaria.report-form :observation="$observation" />

              @if($observation->is_shared || auth()->id() === $observation->user_id)
                <button @click="showModal = true" type="button" class="inline-flex items-center space-x-2 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs py-2.5 px-4 rounded-xl shadow-md transition transform hover:-translate-y-0.5">
                  <span>📸 Anexar Foto de Cuidado</span>
                </button>
              @endif
            </div>
          </div>
        </div>
      </div>

      <!-- Seção da Linha do Tempo e Cuidados Colaborativos da Comunidade -->
      <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-2xl border border-gray-200/80 dark:border-gray-700/80 p-6 lg:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b border-gray-200 dark:border-gray-700 gap-4">
          <div>
            <div class="inline-flex items-center space-x-1.5 text-xs font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider mb-1">
              <span>🌱 Mapeamento Colaborativo</span>
            </div>
            <h3 class="text-xl font-bold font-display text-gray-900 dark:text-white">
              Linha do Tempo & Cuidados da Comunidade
            </h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
              Acompanhe a evolução histórica e fotos enviadas por pessoas que cuidam desta Araucária.
            </p>
          </div>

          @if($observation->is_shared || auth()->id() === $observation->user_id)
            <button @click="showModal = true" type="button" class="self-start sm:self-center inline-flex items-center space-x-1.5 bg-emerald-100 hover:bg-emerald-200 dark:bg-emerald-950 dark:hover:bg-emerald-900 text-emerald-800 dark:text-emerald-300 font-bold text-xs py-2 px-3.5 rounded-xl transition border border-emerald-300 dark:border-emerald-700">
              <span>➕ Anexar Foto (+1 Pinhão 🌲)</span>
            </button>
          @endif
        </div>

        <!-- Lista da Linha do Tempo -->
        <div class="mt-8 space-y-6">

          <!-- Nó Inicial: Foto de Cadastro -->
          <div class="relative pl-6 border-l-2 border-emerald-500 dark:border-emerald-600 pb-4">
            <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full bg-emerald-500 border-4 border-white dark:border-gray-800"></div>
            <div class="bg-emerald-50/50 dark:bg-emerald-950/20 p-4 rounded-xl border border-emerald-200/60 dark:border-emerald-900/60 flex flex-col sm:flex-row items-start gap-4">
              <div class="w-24 h-24 rounded-lg overflow-hidden shrink-0 border border-gray-200 dark:border-gray-700 shadow-sm">
                <img src="{{ $observation->photo_path }}" alt="Foto Inicial" class="w-full h-full object-cover">
              </div>
              <div class="flex-1">
                <div class="flex items-center justify-between text-xs mb-1">
                  <span class="font-bold text-emerald-900 dark:text-emerald-300">📌 Mapeamento Inicial Registrado</span>
                  <span class="text-gray-400 font-mono">{{ $observation->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <p class="text-xs text-gray-600 dark:text-gray-300">
                  Registro oficial fundado por 
                  <a href="{{ route('profile.username', $observation->user->username) }}" class="font-bold text-emerald-700 dark:text-emerald-400 hover:underline">
                    {{ $observation->user->username ? '@'.$observation->user->username : $observation->user->name }}
                  </a>.
                </p>
              </div>
            </div>
          </div>

          <!-- Fotos de Acompanhamento Anexadas -->
          @forelse($observation->updates as $update)
            <div class="relative pl-6 border-l-2 border-gray-200 dark:border-gray-700 pb-4">
              <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full bg-emerald-600 border-4 border-white dark:border-gray-800"></div>
              
              <div class="bg-gray-50/80 dark:bg-gray-900/60 p-4 rounded-xl border border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row items-start justify-between gap-4">
                <div class="flex flex-col sm:flex-row items-start gap-4 flex-1">
                  <div class="w-28 h-28 rounded-xl overflow-hidden shrink-0 border border-gray-200 dark:border-gray-700 shadow-sm relative group">
                    <img src="{{ $update->photo_path }}" alt="Foto de Acompanhamento" class="w-full h-full object-cover">
                  </div>

                  <div class="space-y-1.5 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                      <span class="text-xs font-bold text-gray-900 dark:text-white">
                        📷 Atualização por {{ $update->user->username ? '@'.$update->user->username : $update->user->name }}
                      </span>
                      @if($update->stage)
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 border border-emerald-200">
                          Estágio: {{ $update->stage }}
                        </span>
                      @endif
                    </div>

                    <p class="text-xs text-gray-400 font-mono">
                      Data da foto: {{ \Carbon\Carbon::parse($update->observed_at)->format('d/m/Y H:i') }}
                    </p>

                    @if($update->notes)
                      <div class="p-2.5 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-xs text-gray-700 dark:text-gray-300 italic">
                        "{{ $update->notes }}"
                      </div>
                    @endif
                  </div>
                </div>

                @if(auth()->id() === $update->user_id || auth()->id() === $observation->user_id || in_array(auth()->user()->role ?? 'user', ['admin', 'staff']))
                  <form method="POST" action="{{ route('observations.updates.destroy', $update->id) }}" onsubmit="return confirm('Deseja remover esta foto da linha do tempo?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs font-semibold text-rose-600 dark:text-rose-400 hover:underline">
                      Excluir
                    </button>
                  </form>
                @endif
              </div>
            </div>
          @empty
            <div class="text-center py-8 bg-gray-50 dark:bg-gray-900/40 rounded-xl border border-dashed border-gray-200 dark:border-gray-700">
              <span class="text-3xl block mb-2">📸</span>
              <p class="text-xs font-semibold text-gray-600 dark:text-gray-300">Ainda não há fotos de acompanhamento anexadas.</p>
              <p class="text-[11px] text-gray-400 mt-1">Se você visitou ou cuida desta árvore, anexe uma nova foto para registrar sua história!</p>
            </div>
          @endforelse

        </div>
      </div>

    </div>

    <!-- Modal Alpine: Anexar Foto de Cuidado -->
    <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
      <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        
        <!-- Backdrop -->
        <div @click="showModal = false" class="fixed inset-0 transition-opacity bg-gray-900/60 backdrop-blur-xs"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-200 dark:border-gray-700 p-6">
          
          <div class="flex justify-between items-center pb-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-bold font-display text-gray-900 dark:text-white flex items-center space-x-2">
              <span>🌱 Anexar Foto de Cuidado</span>
            </h3>
            <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-lg">
              ✕
            </button>
          </div>

          <form action="{{ route('observations.updates.store', $observation->id) }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
            @csrf

            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1.5">
                📸 Foto de Acompanhamento
              </label>
              <input type="file" name="photo_path" required accept="image/png,image/jpeg,image/webp"
                @change="const file = $event.target.files[0]; if(file) previewPhotoUrl = URL.createObjectURL(file)"
                class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer">
              
              <template x-if="previewPhotoUrl">
                <div class="mt-2">
                  <img :src="previewPhotoUrl" class="w-32 h-32 object-cover rounded-xl border border-gray-200">
                </div>
              </template>
            </div>

            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">
                Data da Observação
              </label>
              <input type="datetime-local" name="observed_at" value="{{ now()->format('Y-m-d\TH:i') }}"
                class="w-full text-xs rounded-xl border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2.5">
            </div>

            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">
                Atualização de Estágio (Opcional)
              </label>
              <select name="stage" class="w-full text-xs rounded-xl border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2.5">
                <option value="">Manter estágio atual</option>
                <option value="seedling">🌱 Muda (Plântula)</option>
                <option value="sapling">🌿 Jovem (Desenvolvimento)</option>
                <option value="adult">🌲 Adulta (Copa Formada)</option>
                <option value="dead">🪵 Morta / Cortada</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">
                Relato de Cuidado / Observação (Opcional)
              </label>
              <textarea name="notes" rows="3" placeholder="Ex: Poda de galhos secos realizada, copa desenvolvendo novas acículas..."
                class="w-full text-xs rounded-xl border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2.5"></textarea>
            </div>

            <div class="pt-3 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-2">
              <button @click="showModal = false" type="button" class="px-4 py-2 text-xs font-semibold rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                Cancelar
              </button>
              <button type="submit" class="px-4 py-2 text-xs font-bold rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white shadow-md">
                Anexar Foto (+1 Pinhão 🌲)
              </button>
            </div>

          </form>

        </div>
      </div>
    </div>

  </div>
</x-app-layout>