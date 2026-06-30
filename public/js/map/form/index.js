import { createObservation, deleteObservation } from '../api.js';
import { addNewObservationToMap, clearClickMarker } from '../map.js';
import { dispatchAlert } from '../utils/alerts.js';
import { validarImagem } from './imagem.js';

let isSubmitting = false;

export function setupFormListener() {
  window.removeEventListener('submit', handleGlobalSubmit);
  window.addEventListener('submit', handleGlobalSubmit);
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

    const response = await createObservation(form);
    const observation = response.data || response;
    const successMessage = response.message || 'Observação salva com sucesso!';

    if (observation) {
      const currentMapId = form.id === 'araucariaForm-edit' ? 'map-edit' : 'map-create';
      addNewObservationToMap(observation, currentMapId);
    }

    dispatchAlert('saved', successMessage);

    // ✨ Se for CRIAÇÃO, limpa tudo para o próximo registro
    if (form.id === 'araucariaForm-create') {
      form.reset();
      clearClickMarker('map-create');

      const latInput = form.querySelector('#latitude');
      const lngInput = form.querySelector('#longitude');
      if (latInput) latInput.value = '';
      if (lngInput) lngInput.value = '';
    }
    // Se for EDIÇÃO, não resetamos o form! 
    // Limpa o estado de clique antigo se houver, mantendo os dados salvos na tela

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

window.deletarObservacao = async function (id, elementoLinha) {
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
};