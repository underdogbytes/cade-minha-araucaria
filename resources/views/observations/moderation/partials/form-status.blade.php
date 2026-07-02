<form
  method="POST"
  action="{{ route('observations.moderation.update-status', $report) }}"
  class="space-y-2"
  onsubmit="return confirm('Você tem certeza que deseja atribuir esse status?');">
  @csrf
    <label class="block text-md font-medium text-gray-700 dark:text-gray-200">
      Atribuir outro status a denúncia
    </label>
    <select
      name="status"
      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
    >
      @foreach (['pending' => 'Pendente', 'in_progress' => 'Em andamento', 'resolved' => 'Resolvido'] as $value => $label)
        <option value="{{ $value }}" {{ $report->status === $value ? 'selected' : '' }}>{{ $label }}</option>
      @endforeach
    </select>
    <button
      type="submit"
      class="w-full rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
      Mudar status
    </button>
</form>