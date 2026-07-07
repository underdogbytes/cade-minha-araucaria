<x-app-layout>
  <x-slot name="header">
    <div
      x-data="{ alerta: { mostrar: {{ session('status') ? 'true' : 'false' }}, tipo: 'success', mensagem: '{{ session('status') ?? '' }}' } }"
      @observation-saved.window="alerta.mostrar = true; alerta.tipo = 'success'; alerta.mensagem = $event.detail.message; setTimeout(() => alerta.mostrar = false, 5000);"
      @observation-error.window="alerta.mostrar = true; alerta.tipo = 'error'; alerta.mensagem = $event.detail.message; setTimeout(() => alerta.mostrar = false, 5000);"
      x-init="if (alerta.mostrar) setTimeout(() => alerta.mostrar = false, 5000)">
      <div x-show="alerta.mostrar" x-transition :class="alerta.tipo === 'success' ? 'bg-emerald-500' : 'bg-red-500'"
        class="fixed top-5 right-5 z-50 text-white px-6 py-3 rounded-lg shadow-xl font-semibold flex items-center space-x-2">
        <span x-text="alerta.tipo === 'success' ? '✅' : '❌'"></span>
        <span x-text="alerta.mensagem"></span>
      </div>
    </div>
    <div class="flex justify-between items-center py-6">
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

      @if ($groupedReports->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow">
          <p class="text-gray-600 dark:text-gray-300">Nenhuma observação denunciada no momento.</p>
        </div>
      @else
        @foreach ($groupedReports as $observationId => $reportsGroup)
          @php $report = $reportsGroup->first(); @endphp
          <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="grid grid-cols-1 lg:grid-cols-[2fr_1fr] gap-6">
              @include('moderation.partials.details', ['report' => $report, 'reportsGroup' => $reportsGroup])
              @include('moderation.partials.actions', ['report' => $report])
            </div>
          </div>
        @endforeach
      @endif
    </div>
  </div>
</x-app-layout>