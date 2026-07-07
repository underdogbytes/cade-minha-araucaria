<x-app-layout>
  <x-slot name="header">
    <x-page-header title="Registro de Araucária — {{ $observation->created_at->format('d/m/Y') }}" />
  </x-slot>

  <div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
      <x-toast-alert />

      <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div
            class="flex flex-col items-center justify-top bg-gray-50 dark:bg-gray-900 rounded-xl p-2 border border-gray-100 dark:border-gray-700">
            @if($observation->photo_path)
              <img src="{{ $observation->photo_path }}" alt="Foto da Araucária"
                class="w-full h-auto max-h-[450px] object-cover rounded-lg shadow-md">
            @else
              <div class="text-gray-400 p-12 text-center">Nenhuma imagem registrada</div>
            @endif
          </div>

          <div class="flex flex-col justify-between space-y-6">
            @include('observations.partials.features', ['observation' => $observation])
            @include('observations.partials.do-u-know')
            <x-araucaria.report-form :observation="$observation" />
          </div>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>