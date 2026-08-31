export function atualizaDataHora(formElement, dataHora) {
  if (!formElement) return;

  const dateInput = formElement.querySelector('[name="observed_at"]') || formElement.querySelector('[id^="observed_at"]');
  if (dateInput) {
    dateInput.value = dataHora;
    dateInput.dispatchEvent(new Event('input', { bubbles: true }));
  }
}

export function limparCampo(formElement, itemId) {
  if (!formElement) return;

  const elemento = formElement.querySelector(`[name="${itemId}"]`) || formElement.querySelector(`#${itemId}`) || formElement.querySelector(`[id^="${itemId}"]`);
  if (!elemento) return;

  elemento.value = '';
  elemento.dispatchEvent(new Event('input', { bubbles: true }));
}