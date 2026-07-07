<x-app-layout>
  <x-slot name="header">
    <x-page-header title="Painel de Moderação" />
  </x-slot>

  <x-toast-alert />

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