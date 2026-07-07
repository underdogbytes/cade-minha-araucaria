@props([
    'title',
    'backHref' => '/dashboard',
    'backLabel' => '← Voltar ao Início',
])

<div class="flex justify-between items-center py-6">
  <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
    {{ $title }}
  </h2>
  <a href="{{ $backHref }}"
    class="text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded-md font-medium transition">
    {{ $backLabel }}
  </a>
</div>
