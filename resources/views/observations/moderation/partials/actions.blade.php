<div class="space-y-3">
  <h4>Ações</h4>
  <span class="text-gray-500 dark:text-gray-400">
    Escolha uma ação para esta observação.
  </span>

  @include('observations.moderation.partials.form-attribute', ['report' => $report])
  @include('observations.moderation.partials.form-delete', ['report' => $report])
</div>