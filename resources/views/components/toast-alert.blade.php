@props(['sessionKey' => 'status'])

<div
  x-data="{
    alerta: {
      mostrar: {{ session($sessionKey) ? 'true' : 'false' }},
      tipo: 'success',
      mensagem: '{{ session($sessionKey) ?? '' }}'
    }
  }"
  @observation-saved.window="alerta.mostrar = true; alerta.tipo = 'success'; alerta.mensagem = $event.detail.message; setTimeout(() => alerta.mostrar = false, 5000);"
  @observation-error.window="alerta.mostrar = true; alerta.tipo = 'error'; alerta.mensagem = $event.detail.message; setTimeout(() => alerta.mostrar = false, 5000);"
  x-init="if (alerta.mostrar) setTimeout(() => alerta.mostrar = false, 5000)">

  <div
    x-show="alerta.mostrar"
    x-transition
    :class="alerta.tipo === 'success' ? 'bg-emerald-500' : 'bg-red-500'"
    class="fixed top-5 right-5 z-50 text-white px-6 py-3 rounded-lg shadow-xl font-semibold flex items-center space-x-2">
    <span x-text="alerta.tipo === 'success' ? '✅' : '❌'"></span>
    <span x-text="alerta.mensagem"></span>
  </div>
</div>
