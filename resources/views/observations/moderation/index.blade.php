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

      @if ($groupedReports->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow">
          <p class="text-gray-600 dark:text-gray-300">Nenhuma observação denunciada no momento.</p>
        </div>
      @else
        @foreach ($groupedReports as $observationId => $reportsGroup)
          @php $report = $reportsGroup->first(); @endphp
          <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="grid grid-cols-1 lg:grid-cols-[2fr_1fr] gap-6">
              @include('observations.moderation.partials.details', ['report' => $report, 'reportsGroup' => $reportsGroup])
              @include('observations.moderation.partials.actions', ['report' => $report])
            </div>
          </div>
        @endforeach
      @endif
    </div>
  </div>
</x-app-layout>