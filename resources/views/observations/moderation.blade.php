<x-app-layout>
  <x-slot name="header">
    <div class="flex justify-between items-center">
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Painel de Moderação
      </h2>
      <a href="/dashboard"
        class="text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded-md font-medium transition">
        ← Voltar ao Início
      </a>
    </div>
  </x-slot>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
      @if (session('status'))
        <div class="rounded-md bg-emerald-50 p-4 text-sm text-emerald-700">
          {{ session('status') }}
        </div>
      @endif

      @if ($reports->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow">
          <p class="text-gray-600 dark:text-gray-300">Nenhuma observação denunciada no momento.</p>
        </div>
      @else
        @foreach ($reports as $report)
          <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="grid grid-cols-1 lg:grid-cols-[2fr_1fr] gap-6">
              <div>
                <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                  <span class="font-semibold text-amber-600">{{ ucfirst($report->reason) }}</span>
                  <span>•</span>
                  <span>Denunciado por
                    <a href="/users/{{ $report->user->id }}"
                      class="text-emerald-600 dark:text-emerald-400 hover:underline">
                      {{ `$report->user?->name (ver perfil)` ?? 'usuário removido' }}
                    </a>
                  </span>
                </div>
                <h3 class="mt-2 text-lg font-semibold text-gray-900 dark:text-white">
                  <a href="/users/{{ $report->observation?->user?->id }}" target="_blank">
                    Usuário denunciado:<br>{{ $report->observation?->user?->name ?? 'Usuário removido' }}
                  </a>
                </h3>
                <img
                  src="{{ $report->observation?->photo_path ?? '/images/placeholder.png' }}"
                  alt="Foto denunciada"
                  class="h-64 object-cover rounded-lg shadow-md"
                >

                <div class="mt-4 space-y-2 text-gray-700 dark:text-gray-300">
                  <p>
                    <span class="font-semibold text-gray-700 dark:text-gray-300">ID da Observação:</span>
                    {{ $report->observation?->id ?? 'N/A' }}
                  </p>
                  
                  <p>
                    <span class="font-semibold text-gray-700 dark:text-gray-300">Link da Observação:</span>
                    <a href="/observations/{{ $report->observation?->id }}" target="_blank"
                      class="text-emerald-600 dark:text-emerald-400 hover:underline">
                      {{ $report->observation?->id ? 'Ver Detalhes' : 'N/A' }}
                    </a>
                  </p>
                  
                  <p>
                    <span class="font-semibold text-gray-700 dark:text-gray-300">Registrado em:</span>
                    {{ $report->observation?->created_at?->format('d/m/Y H:i') ?? 'Data não disponível' }}
                  </p>
                  
                  <p>
                    <span class="font-semibold text-gray-700 dark:text-gray-300">Detalhes:</span>
                    {{ $report->details ?? 'Sem detalhes adicionais informados na denúncia.' }}
                  </p>
                </div>
              </div>

              <div class="space-y-3">
                <h4>Ações</h4>
                <span class="text-gray-500 dark:text-gray-400">
                  Escolha uma ação para esta observação.
                </span>

                <form method="POST" action="{{ route('observations.moderation.assign', $report) }}" class="space-y-2">
                  @csrf
                  <label class="block text-md font-medium text-gray-700 dark:text-gray-200">
                    Atribuir a outro usuário
                    <select name="user_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                      @foreach (\App\Models\User::orderBy('name')->get() as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                      @endforeach
                    </select>
                  </label>
                  <button type="submit" class="w-full rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
                    Atribuir
                  </button>
                </form>

                <form method="POST" action="{{ route('observations.moderation.delete', $report) }}" class="space-y-2">
                  @csrf
                  <label class="block text-md font-medium text-gray-700 dark:text-gray-200">
                    Deletar imagem
                  </label>
                  <span class="text-gray-500 dark:text-gray-400">
                    ATENÇÃO: ação permanentemente no sistema.
                  </span>
                  <button type="submit" class="w-full rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">
                    Deletar imagem
                  </button>
                </form>
              </div>
            </div>
          </div>
        @endforeach
      @endif
    </div>
  </div>
</x-app-layout>
