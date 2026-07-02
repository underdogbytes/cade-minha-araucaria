<form
  method="POST"
  action="{{ route('observations.moderation.delete', $report) }}"
  class="space-y-2"
  onsubmit="return confirm('Você tem certeza que deseja exluir?');">
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