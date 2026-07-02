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
                  <span>Denunciado por {{ $report->user?->name ?? 'usuário removido' }}</span>
                </div>
                <h3 class="mt-2 text-lg font-semibold text-gray-900 dark:text-white">
                  {{ $report->observation?->user?->name ?? 'Observação removida' }}
                </h3>
                <p class="mt-3 text-sm text-gray-600 dark:text-gray-300">
                  {{ $report->details ?? 'Sem detalhes adicionais.' }}
                </p>
              </div>

              <div class="space-y-3">
                <form method="POST" action="{{ route('observations.moderation.delete', $report) }}">
                  @csrf
                  <button type="submit" class="w-full rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">
                    Deletar imagem
                  </button>
                </form>

                <form method="POST" action="{{ route('observations.moderation.assign', $report) }}" class="space-y-2">
                  @csrf
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">
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
              </div>
            </div>
          </div>
        @endforeach
      @endif
    </div>
  </div>
</x-app-layout>
