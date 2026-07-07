@props(['observation'])

<div class="py-3" x-data="{ openReport: false }">
  <button
    id="report-toggle"
    type="button"
    @click="openReport = !openReport"
    class="text-white hover:text-gray-200 text-sm font-medium transition bg-red-600 hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 rounded-lg px-3 py-1.5">
    🚩 Denunciar*
  </button>
  <br>
  <span class="text-xs text-gray-500 dark:text-gray-400">
    * Caso ela viole as regras da plataforma ou contenha conteúdo impróprio.
    A moderação irá analisar a denúncia e tomar as medidas cabíveis.
  </span>

  <div class="space-y-3 mt-3" id="report-form-container" x-show="openReport" style="display: none;">
    <form
      id="report-form"
      action="{{ route('observations.report', $observation) }}"
      method="POST"
      class="space-y-3"
      onsubmit="return confirm('Você tem certeza que deseja denunciar?');">
      @csrf

      <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">
        Motivo da denúncia
        <select
          name="reason"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
          <option value="inappropriate_image">Imagem imprópria</option>
          <option value="ownership">Autoria</option>
          <option value="other">Outros</option>
        </select>
      </label>

      <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">
        Detalhes
        <textarea
          name="details"
          maxlength="144"
          rows="3"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
          placeholder="Máximo 144 caracteres"></textarea>
      </label>

      <button
        type="submit"
        class="text-white hover:text-gray-200 text-sm font-medium transition bg-red-600 hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 rounded-lg px-3 py-1.5">
        Denunciar observação
      </button>
    </form>
  </div>
</div>
