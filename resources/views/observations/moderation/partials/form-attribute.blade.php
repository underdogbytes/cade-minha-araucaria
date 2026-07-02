<form 
  method="POST"
  action="{{ route('observations.moderation.assign', $report) }}"
  class="space-y-2"
  onsubmit="return confirm('Você tem certeza que deseja atribuir a outro usuário?');">
  @csrf
  <label class="block text-md font-medium text-gray-700 dark:text-gray-200">
    Atribuir a outro usuário
    <select name="user_id"
      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
      @foreach (\App\Models\User::orderBy('name')->get() as $user)
      <option value="{{ $user->id }}">{{ $user->name }}</option>
      @endforeach
    </select>
  </label>
  <button type="submit"
    class="w-full rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
    Atribuir
  </button>
</form>