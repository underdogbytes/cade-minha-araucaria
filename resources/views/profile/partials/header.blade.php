<x-slot name="header">
  {{-- TODO: bg personalizado --}}
  <div class="flex items-center space-x-4 py-6 px-4 sm:px-6 lg:px-8" style="background:rgb(195, 235, 195)">
    <img
      src="{{ $user->profile_photo_url }}"
      alt="{{ $user->name }}"
      style="width: 10rem; height: 10rem; border-radius: 50%; object-fit: cover;"
    />

    <div>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight pb-2">
        {{ $user->name }}
        {{ $user->username ? " || @ {$user->username}" : '' }}
      </h2>
      <span>Seus pinhões: {{ $user->pinhao_balance }}</span>
    </div>
  </div>
</x-slot>