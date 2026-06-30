export function atualizaDataHora(formElement, dataHora) {
  const dateInput = formElement.querySelector('[name="observed_at"]') || document.getElementById('observed_at');
  if (dateInput) {
    dateInput.value = dataHora;
    dateInput.dispatchEvent(new Event('input', { bubbles: true }));
  }
}

export function limparCampo(formElement, itemId) {
  if (!formElement) return;

  const elemento = formElement.querySelector(`#${itemId}`) || formElement.querySelector(`[name="${itemId}"]`);
  if (!elemento) return;

  if (elemento) elemento.value = '';

  elemento?.dispatchEvent(new Event('input', { bubbles: true }))
}