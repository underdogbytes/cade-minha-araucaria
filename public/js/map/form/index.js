import { createObservation, deleteObservation } from '../api.js';
import { addNewObservationToMap, clearClickMarker } from '../map.js';
import { dispatchAlert } from '../utils/alerts.js';
import { validarImagem } from './imagem.js';

let isSubmitting = false;

export function setupFormListener() {
  window.removeEventListener('submit', handleGlobalSubmit);
  window.addEventListener('submit', handleGlobalSubmit);
  window.removeEventListener('submit', handleReportSubmit);
  window.addEventListener('submit', handleReportSubmit);
}

async function handleGlobalSubmit(event) {
  const form = event.target.closest('form');

  if (!form || !form.id || !form.id.startsWith('araucariaForm-')) {
    return;
  }

  event.preventDefault();

  if (isSubmitting) {
    // TODO: mostrar mensagem de "Aguarde, salvando..." ou "Envio bloqueado: já existe uma requisição em andamento."
    return;
  }

  const submitButton = form.querySelector('button[type="submit"]');

  try {
    isSubmitting = true;
    toggleSubmitButton(submitButton, true);
    validarImagem(form);

    // Garante que o input oculto observed_at esteja com valor ISO antes de enviar
    const displayInput = form.querySelector('[id^="observed_at_display"]');
    const hiddenInput = form.querySelector('[name="observed_at"]');
    if (displayInput && hiddenInput && displayInput.value && !hiddenInput.value) {
      const match = displayInput.value.trim().match(/^(\d{2})\/(\d{2})\/(\d{4})(?:\s+(\d{2}):(\d{2}))?$/);
      if (match) {
        const [_, day, month, year, hours = '12', minutes = '00'] = match;
        hiddenInput.value = `${year}-${month}-${day}T${hours}:${minutes}`;
      }
    }

    const response = await createObservation(form);
    const observation = response.data || response;
    const successMessage = response.message || 'Observação salva com sucesso!';

    if (observation) {
      addNewObservationToMap(observation, 'map');
    }

    dispatchAlert('saved', successMessage);

    // ✨ Se for CRIAÇÃO, limpa tudo para o próximo registro
    if (form.id === 'araucariaForm-create') {
      form.reset();
      window.dispatchEvent(new CustomEvent('reset-form-photos'));
      clearClickMarker('map-create');

      const latInput = form.querySelector('#latitude-create') || form.querySelector('[name="latitude"]');
      const lngInput = form.querySelector('#longitude-create') || form.querySelector('[name="longitude"]');
      if (latInput) { latInput.value = ''; latInput.dispatchEvent(new Event('input', { bubbles: true })); }
      if (lngInput) { lngInput.value = ''; lngInput.dispatchEvent(new Event('input', { bubbles: true })); }

      const obsInput = form.querySelector('#observed_at-create') || form.querySelector('[name="observed_at"]');
      if (obsInput) { obsInput.value = ''; obsInput.dispatchEvent(new Event('input', { bubbles: true })); }
      const obsDisplay = form.querySelector('#observed_at_display-create');
      if (obsDisplay) { obsDisplay.value = ''; obsDisplay.dispatchEvent(new Event('input', { bubbles: true })); }
    } else {
      window.dispatchEvent(new CustomEvent('reset-form-photos'));
      clearClickMarker('map-edit');
    }

  } catch (error) {
    let message = error.message || 'Erro inesperado.';
    dispatchAlert('error', message);
  } finally {
    isSubmitting = false;
    toggleSubmitButton(submitButton, false);
  }
}

function toggleSubmitButton(button, loading) {
  if (!button) return;
  button.disabled = loading;
  button.textContent = loading ? 'Salvando...' : 'Salvar Observação';
}

async function handleReportSubmit(event) {
  const form = event.target.closest('form');

  if (!form || form.id !== 'report-form') {
    return;
  }

  event.preventDefault();

  const submitButton = form.querySelector('button[type="submit"]');
  const originalLabel = submitButton?.textContent ?? 'Denunciar';
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

  submitButton.disabled = true;
  submitButton.textContent = 'Enviando...';

  try {
    const response = await fetch(form.action, {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': csrfToken,
      },
      body: new FormData(form),
      credentials: 'same-origin',
    });

    const data = await response.json().catch(() => ({}));

    if (!response.ok) {
      throw new Error(data.message || 'Não foi possível enviar a denúncia.');
    }

    dispatchAlert('saved', data.message || 'Denúncia enviada com sucesso!');
    form.reset();
    const toggleButton = document.getElementById('report-toggle');
    toggleButton?.click();
  } catch (error) {
    dispatchAlert('error', error.message || 'Erro ao enviar a denúncia.');
  } finally {
    submitButton.disabled = false;
    submitButton.textContent = originalLabel;
  }
}

export async function deletarObservacao(id, elementoLinha) {
  try {
    const response = await deleteObservation(id);
    const successMessage = response.message || 'Registro excluído com sucesso!';

    dispatchAlert('saved', successMessage);

    if (elementoLinha) {
      elementoLinha.style.transition = 'all 0.5s ease';
      elementoLinha.style.opacity = '0';
      elementoLinha.style.transform = 'scale(0.95)';

      setTimeout(() => {
        elementoLinha.remove();
      }, 500);
    }

  } catch (error) {
    let message = error.message || 'Não foi possível excluir.';
    dispatchAlert('error', message);
  }
}

if (typeof window !== 'undefined') {
  window.addEventListener('deletar-observacao', async (event) => {
    const { id, elementoLinha } = event.detail || {};
    if (id) {
      await deletarObservacao(id, elementoLinha);
    }
  });
}